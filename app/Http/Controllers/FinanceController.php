<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinanzasExport;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getFinanceData($request);
        return view('dashboard.admin.finanzas', $data);
    }

    public function report(Request $request)
    {
        $data = $this->getFinanceData($request);
        
        if ($request->get('export') === 'excel') {
            $filename = 'Reporte_Financiero_' . $data['monthName'] . '_' . $data['year'] . '.xlsx';
            return Excel::download(new FinanzasExport($data), $filename);
        }

        return view('dashboard.admin.finanzas-report', $data);
    }

    // ... getFinanceData and getChartData remain unchanged ...

    private function getFinanceData(Request $request)
    {
        // ... implementation kept in cache by replace_file_content if not targeting it ...
        // Re-declaring for context if needed, but since we use replace_file_content on specific chunks or entire file.
        // I will target the end of the file to replace the redirect methods.
        // Wait, replace_file_content is for contiguous blocks.
        // I'll do this in two steps or Replace the whole file content if easier, but it's large.
        // Let's replace the specific methods.
        
        $currentMonth = (int) $request->input('month', Carbon::now()->month);
        $currentYear = (int) $request->input('year', Carbon::now()->year);

        // 1. Ingresos Confirmados
        $incomeQuery = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado']);

        $monthlyIncome = $incomeQuery->sum('monto_final');
        
        $incomes = $incomeQuery->with('user.paciente')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        // 2. Pendiente de Cobro
        $pendingIncome = Appointment::where('fecha_hora', '<', Carbon::now())
            ->where('estado', 'confirmado')
            ->sum('monto_final');

        // 3. Tasa de Cancelación Mensual
        $totalAppointments = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->count();
        
        $cancelledAppointments = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->where('estado', 'cancelado')
            ->count();

        $cancellationRate = $totalAppointments > 0 ? round(($cancelledAppointments / $totalAppointments) * 100, 1) : 0;

        // 4. Pacientes Nuevos vs Frecuentes
        $patientsThisMonth = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->with(['user.paciente'])
            ->get()
            ->groupBy('usuario_id');

        $newPatients = 0;
        $frequentPatients = 0;

        foreach ($patientsThisMonth as $userId => $appts) {
            $patient = $appts->first()->user->paciente ?? null;
            if ($patient) {
                if ($patient->tipo_paciente === 'nuevo') $newPatients++;
                else $frequentPatients++;
            }
        }

        // 5. Comparación Ingresos Mes a Mes
        $prevDate = Carbon::create($currentYear, $currentMonth, 1)->subMonth();
        $previousMonthIncome = Appointment::whereYear('fecha_hora', $prevDate->year)
            ->whereMonth('fecha_hora', $prevDate->month)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->sum('monto_final');

        $incomeGrowth = 0;
        $isFirstMonth = false;

        if ($previousMonthIncome > 0) {
            $incomeGrowth = (($monthlyIncome - $previousMonthIncome) / $previousMonthIncome) * 100;
        } elseif ($monthlyIncome > 0 && $previousMonthIncome == 0) {
             $prevAppointments = Appointment::whereYear('fecha_hora', $prevDate->year)
                ->whereMonth('fecha_hora', $prevDate->month)
                ->exists();
             
             if (!$prevAppointments) {
                 $isFirstMonth = true;
             } else {
                 $incomeGrowth = 100;
             }
        }

        // 6. Lista de Deudores (Optimized)
        // Aggregating in Database instead of fetching all rows
        $debtors = Appointment::where('fecha_hora', '<', Carbon::now())
            ->where('estado', 'confirmado')
            ->select(
                'usuario_id', 
                DB::raw('count(*) as sessions_count'), 
                DB::raw('sum(monto_final) as total_debt'), 
                DB::raw('MAX(fecha_hora) as last_session_date')
            )
            ->groupBy('usuario_id')
            ->with(['user.paciente'])
            ->get()
            ->map(function ($item) {
                // $item is an Appointment instance but with aggregated extra attributes
                $user = $item->user;
                if (!$user) return null;

                $paciente = $user->paciente;
                $phone = $paciente ? preg_replace('/[^0-9]/', '', $paciente->telefono) : ($user->telefono_legacy ? preg_replace('/[^0-9]/', '', $user->telefono_legacy) : '');
                
                $mensaje = "Hola {$user->nombre}, ¿cómo estás? Te escribo para regularizar el saldo de nuestras últimas {$item->sessions_count} sesiones ($" . number_format($item->total_debt, 0, ',', '.') . "). ¡Cualquier duda me avisás! Saludos.";
                $whatsappLink = $phone ? "https://wa.me/" . $phone . "?text=" . urlencode($mensaje) : '#';

                return [
                    'user' => $user,
                    'is_frequent' => $paciente && $paciente->tipo_paciente === 'frecuente',
                    'sessions_count' => $item->sessions_count,
                    'total_debt' => $item->total_debt,
                    'last_session_date' => $item->last_session_date,
                    'whatsapp_link' => $whatsappLink
                ];
            })
            ->filter()
            ->values();

        $monthName = ucfirst(Carbon::create()->month($currentMonth)->locale('es')->translatedFormat('F'));

        // 7. Gastos del Mes
        $gastos = Gasto::whereYear('fecha', $currentYear)
            ->whereMonth('fecha', $currentMonth)
            ->orderBy('fecha', 'desc')
            ->get();

        $totalGastos = $gastos->sum('monto');
        $realProfit = $monthlyIncome - $totalGastos;

        // 8. Comprobantes Pendientes de Aprobación
        $pendingReceipts = Appointment::where('estado', 'pendiente')
            ->whereHas('payment', function($q) {
                $q->where('estado', 'pendiente');
            })
            ->with(['user.paciente', 'payment'])
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($turno) {
                $user = $turno->user;
                if (!$user) return null;

                return [
                    'turno' => $turno,
                    'user' => $user,
                    'paciente' => $user->paciente,
                    'comprobante_ruta' => $turno->payment ? asset('storage/' . $turno->payment->comprobante_ruta) : null,
                    'fecha_hora' => $turno->fecha_hora,
                    'modalidad' => $turno->modalidad,
                ];
            })
            ->filter()
            ->values();

        return compact(
            'monthlyIncome', 
            'pendingIncome', 
            'totalGastos',
            'gastos',
            'realProfit',
            'pendingIncome', 
            'cancellationRate', 
            'newPatients', 
            'frequentPatients', 
            'monthName',
            'previousMonthIncome',
            'incomeGrowth',
            'isFirstMonth',
            'debtors',
            'incomes',
            'pendingReceipts'
        ) + ['year' => $currentYear];
    }
    
    public function getChartData(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $date = Carbon::create($year, $month, 1);

        // Gráfico 1: Evolución de Ingresos (Últimos 6 meses hasta el mes seleccionado)
        // Se toman 5 meses atrás + el actual
        $startPeriod = $date->copy()->subMonths(5)->startOfMonth();
        $endPeriod = $date->copy()->endOfMonth();

        $incomes = Appointment::select(
            DB::raw('sum(monto_final) as total'), 
            DB::raw("DATE_FORMAT(fecha_hora, '%Y-%m') as mes")
        )
        ->whereBetween('fecha_hora', [$startPeriod, $endPeriod])
        ->whereIn('estado', ['completado', 'asistido'])
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

        // Gráfico 2: Pacientes Nuevos vs Frecuentes (De los turnos del mes seleccionado)
        $patientTypes = Appointment::join('pacientes', 'turnos.usuario_id', '=', 'pacientes.user_id')
            ->select('pacientes.tipo_paciente', DB::raw('count(*) as total'))
            ->whereYear('fecha_hora', $year)
            ->whereMonth('fecha_hora', $month)
            ->groupBy('pacientes.tipo_paciente')
            ->get();

        return response()->json([
            'incomes' => $incomes,
            'patientTypes' => $patientTypes
        ]);
    }

    public function updatePrices(Request $request)
    {
        // Ajuste Masivo
        $request->validate([
            'porcentaje' => 'required|numeric|min:1',
        ]);

        $factor = 1 + ($request->porcentaje / 100);

        // Actualizar tabla Pacientes
        \App\Models\Paciente::query()->update([
            'honorario_pactado' => DB::raw("honorario_pactado * $factor")
        ]);

        return redirect()->back()->with('success', "Precios actualizados un {$request->porcentaje}% correctamente.")->withFragment('honorarios');
    }

    public function updatePatientHonorario(Request $request, $id)
    {
        $request->validate([
            'honorario_pactado' => 'nullable|numeric|min:0'
        ]);
        
        $paciente = \App\Models\Paciente::firstOrCreate(
            ['user_id' => $id],
            ['tipo_paciente' => 'nuevo']
        );

        if ($request->has('use_custom_price')) {
            if (is_null($request->honorario_pactado)) {
                 return redirect()->back()->with('error', 'Debés ingresar un monto si activás el precio personalizado.')->withFragment('honorarios');
            }
            $paciente->precio_personalizado = $request->honorario_pactado;
        } else {
            $paciente->precio_personalizado = null;
        }

        $paciente->save();

        return redirect()->back()->with('success', 'Honorario actualizado correctamente.')->withFragment('honorarios');
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'categoria' => 'required|string',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Gasto::create([
            'user_id' => auth()->id(),
            'fecha' => $request->fecha,
            'categoria' => $request->categoria,
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'es_recurrente' => 0
        ]);

        return redirect()->back()->with('success', 'Gasto registrado correctamente.')->withFragment('gastos');
    }

    public function destroyExpense($id)
    {
        $gasto = Gasto::findOrFail($id);
        $gasto->delete();

        return redirect()->back()->with('success', 'Gasto eliminado correctamente.')->withFragment('gastos');
    }
}
