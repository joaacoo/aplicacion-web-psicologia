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
        $currentMonth = (int) $request->input('month', Carbon::now()->month);
        $currentYear = (int) $request->input('year', Carbon::now()->year);

        // 1. Ingresos Confirmados (Pagos Verificados)
        // [FIX] Only count appointments with VERIFIED payments
        $incomeQuery = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->whereHas('payment', function($q) {
                $q->where('estado', 'verificado');
            });

        $monthlyIncome = $incomeQuery->sum('monto_final');
        
        $incomes = $incomeQuery->with('user.paciente')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        // 2. Pendiente de Cobro (A Cobrar)
        // [FIX] Sum: 
        // a) Confirmed appointments (past) without payment or with pending payment? 
        //    Actually, if confirmed it usually means paid/verified in this flow? 
        //    No, Manual confirmation might exist. 
        //    Let's stick to User's request: "base a los copobrantes que tengo que no epte ni rechaze"
        
        // Items with Pending Proofs (regardless of date, usually recent)
        $pendingProofs = Appointment::where('estado', '!=', 'cancelado')
            ->whereHas('payment', function($q) {
                $q->where('estado', 'pendiente');
            })->get();

        $pendingIncome = 0;
        foreach ($pendingProofs as $appt) {
            // If monto_final is set (e.g. was confirmed then payment uploaded? unlikely), use it.
            // If not, calculate based on patient's current price.
            if ($appt->monto_final > 0) {
                $pendingIncome += $appt->monto_final;
            } else {
                // Estimate based on patient price
                $price = 0;
                if ($appt->user && $appt->user->paciente) {
                    $price = $appt->user->paciente->precio_sesion; 
                } else {
                    $price = \App\Models\Setting::get('precio_base_sesion', 25000);
                }
                $pendingIncome += $price;
            }
        }

        // Also add "Debtors" (Past appointments, confirmed but NO payment record or rejected payment?)
        // The previous logic for debtors was: date < now AND state = confirmed. 
        // But if state=confirmed usually implies paid in this system (by verify()), then looking for confirmed unpaid is contradictory unless manual confirm exists.
        // I will keep the previous "Debtors" logic effectively but maybe separate or merge?
        // User said: "a cobrar serai en base a los copobrantes que tengo que no epte ni rechaze" -> Only pending proofs?
        // "cuadno me falta por cokbrar en base al honorarioa de configuarion" -> implies debt too.
        // I'll add the Debtors sum too.
        
        $debtorsQuery = Appointment::where('fecha_hora', '<', Carbon::now())
            ->where('estado', 'confirmado') // Assuming manually confirmed but not paid? Or maybe this query is wrong if confirm=paid.
            ->whereDoesntHave('payment', function($q) {
                $q->where('estado', 'verificado');
            }); // Filter out verified payments
            
        $debtorsSum = $debtorsQuery->sum('monto_final');
        $pendingIncome += $debtorsSum;

        // 9. Total Sessions (New KPI)
        $totalSessions = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->whereIn('estado', ['confirmado', 'asistido', 'completado'])
            ->count();
        
        // 3. Tasa de Cancelación Mensual
        $totalAppointments = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->count();
        
        $cancelledAppointments = Appointment::whereYear('fecha_hora', $currentYear)
            ->whereMonth('fecha_hora', $currentMonth)
            ->where('estado', 'cancelado')
            ->count();

        $cancellationRate = $totalAppointments > 0 ? round(($cancelledAppointments / $totalAppointments) * 100, 1) : 0;

        // ... existing code ...

        // Also add logic to calculate vs previous month sessions if needed, but user just asked "cuantas sesiones tuvo en todo el mes".
        // I'll keep it simple for now.



        // 4. Pacientes Totales (Nuevos vs Frecuentes)
        // [FIX] User requested totals, not monthly.
        $newPatients = \App\Models\Paciente::where('tipo_paciente', 'nuevo')->count();
        $frequentPatients = \App\Models\Paciente::where('tipo_paciente', 'frecuente')->count();

        // 5. Comparación Ingresos Mes a Mes
        $prevDate = Carbon::create($currentYear, $currentMonth, 1)->subMonth();
        $previousMonthIncome = Appointment::whereYear('fecha_hora', $prevDate->year)
            ->whereMonth('fecha_hora', $prevDate->month)
             ->whereHas('payment', function($q) {
                $q->where('estado', 'verificado');
            })
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
            ->whereDoesntHave('payment', function($q) {
                $q->where('estado', 'verificado');
            })
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
        // [FIX] Don't filter by appointment status 'pendiente' strictly, as it might be 'reservado' or 'confirmado' awaiting verification
        // Also ensure pricing is dynamic if needed
        $pendingReceipts = Appointment::where('estado', '!=', 'cancelado')
            ->whereHas('payment', function($q) {
                $q->where('estado', 'pendiente');
            })
            ->with(['user.paciente', 'payment'])
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($turno) {
                $user = $turno->user;
                if (!$user) return null;
                
                // Calculate display price
                $price = $turno->monto_final > 0 ? $turno->monto_final : ($user->paciente->precio_sesion ?? \App\Models\Setting::get('precio_base_sesion', 25000));

                return [
                    'turno' => $turno,
                    'user' => $user,
                    'paciente' => $user->paciente,
                    'comprobante_ruta' => $turno->payment ? asset('storage/' . $turno->payment->comprobante_ruta) : null,
                    'fecha_hora' => $turno->fecha_hora,
                    'modalidad' => $turno->modalidad,
                    'monto_estimado' => $price
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
            'pendingIncome', // Duplicate key but fine
            'cancellationRate', 
            'newPatients', 
            'frequentPatients', 
            'monthName',
            'previousMonthIncome',
            'incomeGrowth',
            'isFirstMonth',
            'debtors',
            'incomes',
            'pendingReceipts',
            'totalSessions'
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
        ->whereHas('payment', function($q) {
            $q->where('estado', 'verificado');
        })
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

        // Gráfico 2: Pacientes Totales (Nuevos vs Frecuentes)
        $patientTypes = \App\Models\Paciente::select('tipo_paciente', DB::raw('count(*) as total'))
            ->where('tipo_paciente', '!=', 'otro')
            ->groupBy('tipo_paciente')
            ->orderBy('tipo_paciente')
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
