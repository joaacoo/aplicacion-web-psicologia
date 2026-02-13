<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirección inteligente por rol
            if (Auth::user()->rol === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('patient.dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios', // Tabla usuarios
            'telefono' => 'required|string|max:20',
            'password' => [
                'required',
                'confirmed',
                Password::min(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $user = User::create([
            'nombre' => ucwords(strtolower($request->name)),
            'email' => $request->email,
            // 'telefono' => $request->telefono, // Moved to Paciente
            'password' => Hash::make($request->password),
            'rol' => 'paciente', // Default role in Spanish
        ]);

        // Create associated Patient record
        $user->paciente()->create([
            'tipo_paciente' => 'nuevo',
            'honorario_pactado' => 0,
            'telefono' => $request->telefono
        ]);

        Auth::login($user);
        
        // Enviar mail de bienvenida
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RegistrationConfirmation($user->nombre));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al enviar mail de bienvenida: " . $e->getMessage());
        }
        
        // Seteamos una bandera para mostrar el prompt de sesión recordada
        session()->flash('show_session_prompt', true);

        return redirect()->route('patient.dashboard');
    }

    public function rememberSession(Request $request)
    {
        Auth::login(Auth::user(), true);
        return back()->with('success', 'Sesión recordada. No tendrás que loguearte de nuevo pronto.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function destroyPatient($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->rol !== 'paciente') {
            return back()->with('error', 'No se puede eliminar a un administrador.');
        }

        // Se eliminan turnos y pagos por cascada si está configurado en la DB, 
        // sino los eliminamos manualmente aquí o confiamos en el delete() si hay relaciones.
        $user->turnos()->each(function($turno) {
            if ($turno->payment) {
                $turno->payment->delete();
            }
            $turno->delete();
        });

        $user->delete();

        return back()->with('success', 'Paciente ' . $user->nombre . ' y todos sus registros eliminados correctamente.');
    }

    public function destroyAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.']);
        }
        
        Auth::logout();

        $user->turnos()->each(function($turno) {
            if ($turno->payment) {
                $turno->payment->delete();
            }
            $turno->delete();
        });

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Tu cuenta ha sido eliminada correctamente.');
    }

    public function updatePatientType(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'tipo_paciente' => 'required|in:nuevo,frecuente'
        ]);

        // Update Paciente relationship
        $user->paciente()->updateOrCreate(
            ['user_id' => $user->id],
            ['tipo_paciente' => $request->tipo_paciente]
        );

        return back()->with('success', 'Paciente ' . $user->nombre . ' actualizado a ' . ucfirst($request->tipo_paciente) . ' correctamente.');
    }

    public function updateMeetLink(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'meet_link' => 'nullable|url'
        ]);

        $user->update([
            'meet_link' => $request->meet_link
        ]);

        return back()->with('success', 'Link de Meet actualizado correctamente para ' . $user->nombre);
    }

    public function sendManualReminder(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Notificar por DB
        \App\Models\Notification::create([
            'usuario_id' => $user->id,
            'mensaje' => 'La Lic. Nazarena De Luca te ha enviado un recordatorio. Por favor, revisá tus turnos pendientes.',
            'link' => route('patient.dashboard')
        ]);

        // Enviar mail genérico de recordatorio
        try {
            \Illuminate\Support\Facades\Mail::raw("Hola {$user->nombre}, la Lic. Nazarena De Luca te envía este recordatorio acerca de tus sesiones. Por favor, ingresá al portal para más detalles.", function($msg) use ($user) {
                $msg->to($user->email)->subject('Recordatorio - Lic. Nazarena De Luca');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error enviando recordatorio manual: " . $e->getMessage());
        }

        return back()->with('success', 'Recordatorio enviado con éxito a ' . $user->nombre);
    }

    // Password Reset Methods
    public function showForgotForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Te enviamos un link para restablecer tu contraseña a tu email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Contraseña restablecida correctamente. Ahora podés ingresar.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
