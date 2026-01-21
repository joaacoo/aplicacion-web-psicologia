@extends('layouts.app')

@section('title', 'Finanzas - Admin')
@section('header_title', 'Panel de Finanzas')

@section('content')
<style>
    /* Tabs Styling */
    .finance-tabs {
        display: flex;
        gap: 1rem;
        border-bottom: 2px solid #000;
        margin-bottom: 4rem;
        flex-wrap: wrap;
        /* Removed overflow-x: auto per request to remove internal scroll */
    }
    .tab-btn {
        padding: 0.8rem 1.5rem;
        background: white;
        border: 2px solid #000;
        margin-right: -2px; /* Connect borders */
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        color: #000;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
    }
    .tab-btn:hover {
        background: #f0f0f0;
    }
    .tab-btn.active {
        background: #000;
        color: white;
        border-bottom: 2px solid #000;
    }
    .tab-pane {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .tab-pane.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Fixed Height Controls */
    .filter-control {
        height: 42px !important; 
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    select.filter-control {
        padding: 0 2rem 0 0.8rem !important;
    }
    
    /* New Badge Color */
    .badge-new {
        background: #ede7f6;
        color: #5e35b1;
        border: 1px solid #d1c4e9;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Export Button */
    .btn-export-excel {
        background: #1D6F42;
        color: white;
        text-decoration: none;
        padding: 1rem 2rem;
        border-radius: 8px; /* Slightly softer than pure brutalist for this button */
        border: 2px solid #000;
        font-weight: 800;
        box-shadow: 4px 4px 0px rgba(0,0,0,1);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-export-excel:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0px rgba(0,0,0,1);
        color: white;
    }
    .btn-export-excel:active {
        transform: translate(0, 0);
        box-shadow: 2px 2px 0px rgba(0,0,0,1);
    }
</style>

<div style="padding: 1.5rem; max-width: 1400px; margin: 0 auto; margin-bottom: 40px;">

    <!-- Top Bar: Title & Filters (Aligned) -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem;">

        
        <div style="display: flex; align-items: stretch; gap: 0.5rem; height: 42px;">
            @php
                $currMonth = request('month', now()->month);
                $currYear = request('year', now()->year);
                $dateObj = \Carbon\Carbon::create($currYear, $currMonth, 1);
                
                $prevDate = $dateObj->copy()->subMonth();
                $nextDate = $dateObj->copy()->addMonth();
                
                $disablePrev = ($currYear <= 2024 && $currMonth <= 1);
            @endphp
            
            <!-- Prev Button -->
            <a href="{{ $disablePrev ? '#' : route('admin.finanzas', ['month' => $prevDate->month, 'year' => $prevDate->year]) }}" 
               class="neobrutalist-btn filter-control {{ $disablePrev ? 'disabled' : '' }}" 
               onclick="if(!this.classList.contains('disabled')) showFinanceLoader()"
               style="background: {{ $disablePrev ? '#eee' : 'white' }}; color: {{ $disablePrev ? '#999' : '#000' }}; border: 2px solid #000; padding: 0 1rem; text-decoration: none; {{ $disablePrev ? 'pointer-events: none;' : '' }}">
                <i class="fa-solid fa-chevron-left"></i>
            </a>

            <!-- Form -->
            <form action="{{ route('admin.finanzas') }}" method="GET" style="display: flex; gap: 0.5rem; margin: 0; align-items: stretch;">
                <!-- Fixed Width for Month Selector to show full name -->
                <select name="month" class="neobrutalist-input filter-control" style="border: 2px solid #000; text-transform: capitalize; cursor: pointer; line-height: 1.2; font-size: 0.9rem; min-width: 150px;" onchange="showFinanceLoader(); this.form.submit()">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ ucfirst(\Carbon\Carbon::create(null, $m)->locale('es')->translatedFormat('F')) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="neobrutalist-input filter-control" style="border: 2px solid #000; cursor: pointer; line-height: 1.2; font-size: 0.9rem;" onchange="showFinanceLoader(); this.form.submit()">
                    @for($y = 2024; $y <= 2030; $y++)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            <!-- Next Button -->
            <a href="{{ route('admin.finanzas', ['month' => $nextDate->month, 'year' => $nextDate->year]) }}" 
               class="neobrutalist-btn filter-control" 
               onclick="showFinanceLoader()"
               style="background: white; color: #000; border: 2px solid #000; padding: 0 1rem; text-decoration: none;">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Loader Overlay -->
    <div id="finance-loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.7); backdrop-filter: blur(2px); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border: 3px solid #000; padding: 2rem; border-radius: 15px; box-shadow: 5px 5px 0px #000; text-align: center;">
             <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
             <p style="font-weight: 800; margin: 0; font-family: 'Syne', sans-serif;">Actualizando Finanzas...</p>
        </div>
    </div>

    <script>
        function showFinanceLoader() {
            document.getElementById('finance-loader').style.display = 'flex';
        }
    </script>

    <!-- Navigation Tabs -->
    <div class="finance-tabs">
        <button class="tab-btn active" onclick="openTab(event, 'dashboard')">Dashboard</button>
        <button class="tab-btn" onclick="openTab(event, 'gastos')">Gastos</button>
        <button class="tab-btn" onclick="openTab(event, 'honorarios')">Salarios / Honorarios</button>
        <button class="tab-btn" onclick="openTab(event, 'reportes')">Reportes</button>
    </div>

    <!-- TAB 1: DASHBOARD -->
    <div id="dashboard" class="tab-pane active">
        <!-- Section 1: KPIs -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            
            <!-- Monthly Income -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 4px 4px 0px rgba(0,0,0,1);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Ingresos ({{ $monthName }})</span>
                    <div style="background: #e8f5e9; padding: 5px; border-radius: 6px; border: 2px solid #000;">
                        <i class="fa-solid fa-sack-dollar" style="font-size: 1.1rem; color: #2e7d32;"></i>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #000;">${{ number_format($monthlyIncome, 0, ',', '.') }}</h2>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    @if($isFirstMonth)
                        <span style="font-size: 0.75rem; font-weight: 700; background: #e3f2fd; color: #1565c0; padding: 3px 8px; border-radius: 4px; border: 1px solid #1565c0;">
                            <i class="fa-solid fa-rocket"></i> Mes Base
                        </span>
                    @else
                        @if($incomeGrowth >= 0)
                            <span style="font-size: 0.8rem; font-weight: 800; color: #2e7d32;">
                                <i class="fa-solid fa-arrow-trend-up"></i> {{ number_format($incomeGrowth, 1) }}%
                            </span>
                        @else
                            <span style="font-size: 0.8rem; font-weight: 800; color: #c62828;">
                                <i class="fa-solid fa-arrow-trend-down"></i> {{ number_format(abs($incomeGrowth), 1) }}%
                            </span>
                        @endif
                        <span style="font-size: 0.75rem; color: #666; font-weight: 600;">vs mes anterior</span>
                    @endif
                </div>
            </div>

            <!-- Real Profit (New) -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 4px 4px 0px rgba(0,0,0,1);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase;">Ganancia Real</span>
                    <div style="background: #fff8e1; padding: 5px; border-radius: 6px; border: 2px solid #000;">
                        <i class="fa-solid fa-wallet" style="font-size: 1.1rem; color: #fbc02d;"></i>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #000;">${{ number_format($realProfit, 0, ',', '.') }}</h2>
                <div style="font-size: 0.75rem; font-weight: 700; color: #666;">
                    (Ingresos - Gastos)
                </div>
            </div>

            <!-- Pending Income -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 4px 4px 0px rgba(0,0,0,1);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase;">A Cobrar</span>
                    <div style="background: {{ $pendingIncome > 0 ? '#fff3e0' : '#e8f5e9' }}; padding: 5px; border-radius: 6px; border: 2px solid #000;">
                        <i class="fa-solid {{ $pendingIncome > 0 ? 'fa-hourglass-half' : 'fa-check' }}" style="font-size: 1.1rem; color: {{ $pendingIncome > 0 ? '#ef6c00' : '#2e7d32' }};"></i>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #000;">${{ number_format($pendingIncome, 0, ',', '.') }}</h2>
                @if($pendingIncome > 0)
                    <div style="font-size: 0.75rem; font-weight: 700; color: #ef6c00;">
                        <i class="fa-solid fa-circle-exclamation"></i> Pendiente de regularizar
                    </div>
                @else
                    <div style="font-size: 0.75rem; font-weight: 800; color: #2e7d32;">
                        <i class="fa-solid fa-check"></i> ¡Todo al día!
                    </div>
                @endif
            </div>

            <!-- Patients Stats -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 4px 4px 0px rgba(0,0,0,1);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase;">Pacientes Activos</span>
                    <div style="background: #e1f5fe; padding: 5px; border-radius: 6px; border: 2px solid #000;">
                        <i class="fa-solid fa-users" style="font-size: 1.1rem; color: #0277bd;"></i>
                    </div>
                </div>
                <div style="display: flex; align-items: baseline; gap: 5px;">
                    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #000;">{{ $newPatients + $frequentPatients }}</h2>
                    <span style="font-size: 0.8rem; color: #666; font-weight: 700;">total</span>
                </div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #666; display: flex; gap: 10px; margin-top: auto;">
                    <!-- Updated New Patient Badge -->
                    <span class="badge-new">
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ $newPatients }} Nuevos
                    </span>
                    <span style="color: #4caf50; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ $frequentPatients }} Frec.
                    </span>
                </div>
            </div>
        </div>

        <!-- Section 2: Charts -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Income Chart -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.5rem; box-shadow: 5px 5px 0px rgba(0,0,0,1);">
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: #000; text-transform: uppercase;"><i class="fa-solid fa-chart-line"></i> Evolución de Ingresos</h3>
                <div style="height: 200px; width: 100%;">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>

            <!-- Patient Type Chart -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.5rem; box-shadow: 5px 5px 0px rgba(0,0,0,1);">
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: #000; text-transform: uppercase;"><i class="fa-solid fa-chart-pie"></i> Distribución de Pacientes</h3>
                <div style="width: 100%; display: flex; flex-direction: column; align-items: center;">
                    <div style="height: 200px; width: 100%; position: relative;">
                        <canvas id="patientChart"></canvas>
                    </div>
                    <div id="patientChartLegend" style="margin-top: 1rem; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAB: GASTOS -->
    <div id="gastos" class="tab-pane">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            
            <!-- Expenses List -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.5rem; box-shadow: 5px 5px 0px rgba(0,0,0,1);">
                <h3 style="margin: 0 0 1.5rem 0; font-size: 1.1rem; font-weight: 900; Color: #000; text-transform: uppercase;">
                    <i class="fa-solid fa-list"></i> Detalle de Gastos
                </h3>

                @if($gastos->isEmpty())
                    <p style="text-align: center; color: #999; font-style: italic;">No hay gastos registrados este mes.</p>
                @else
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee;">
                                <th style="text-align: left; padding: 0.5rem; font-size: 0.8rem;">Fecha</th>
                                <th style="text-align: left; padding: 0.5rem; font-size: 0.8rem;">Categoría</th>
                                <th style="text-align: left; padding: 0.5rem; font-size: 0.8rem;">Descripción</th>
                                <th style="text-align: right; padding: 0.5rem; font-size: 0.8rem;">Monto</th>
                                <th style="text-align: right; padding: 0.5rem; font-size: 0.8rem;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gastos as $gasto)
                                <tr style="border-bottom: 1px solid #f9f9f9;">
                                    <td style="padding: 0.8rem 0.5rem; font-size: 0.9rem;">{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m') }}</td>
                                    <td style="padding: 0.8rem 0.5rem;">
                                        <span style="background: #eee; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">
                                            {{ ucfirst($gasto->categoria) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.8rem 0.5rem; font-size: 0.9rem; color: #666;">{{ $gasto->descripcion ?? '-' }}</td>
                                    <td style="padding: 0.8rem 0.5rem; text-align: right; font-weight: 800;">${{ number_format($gasto->monto, 0, ',', '.') }}</td>
                                    <td style="padding: 0.8rem 0.5rem; text-align: right;">
                                        <form action="{{ route('admin.finanzas.destroy-expense', $gasto->id) }}" method="POST" onsubmit="return confirm('¿Eliminar gasto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; color: #ff5252; cursor: pointer;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="border-top: 2px solid #000;">
                                <td colspan="3" style="padding: 1rem 0.5rem; font-weight: 800; text-align: right;">TOTAL:</td>
                                <td style="padding: 1rem 0.5rem; font-weight: 900; text-align: right; font-size: 1.1rem;">${{ number_format($totalGastos, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>

            <!-- Add Expense Form -->
            <div class="neobrutalist-card" style="background: #fff3e0; border: 3px solid #ffb74d; padding: 1.5rem; box-shadow: 4px 4px 0px rgba(255, 183, 77, 0.4); height: fit-content;">
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 900; color: #ef6c00; text-transform: uppercase;">
                    <i class="fa-solid fa-plus-circle"></i> Nuevo Gasto
                </h3>
                
                <form action="{{ route('admin.finanzas.store-expense') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; margin-bottom: 4px; display: block;">Fecha</label>
                        <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" class="neobrutalist-input" style="width: 100%; border: 2px solid #ffb74d; cursor: pointer;" onclick="this.showPicker()">
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; margin-bottom: 4px; display: block;">Categoría</label>
                        <select name="categoria" class="neobrutalist-input" style="width: 100%; border: 2px solid #ffb74d;">
                            <option value="matricula">Matrícula / Colegio</option>
                            <option value="supervision">Supervisión</option>
                            <option value="impuestos">Monotributo / IIBB</option>
                            <option value="consultorio">Alquiler Consultorio</option>
                            <option value="publicidad">Publicidad</option>
                            <option value="otros">Otros Gastos</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; margin-bottom: 4px; display: block;">Monto</label>
                        <input type="number" name="monto" placeholder="0.00" step="0.01" class="neobrutalist-input" style="width: 100%; border: 2px solid #ffb74d;">
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; margin-bottom: 4px; display: block;">Descripción (Opcional)</label>
                        <input type="text" name="descripcion" placeholder="Ej: Pago de Febrero" class="neobrutalist-input" style="width: 100%; border: 2px solid #ffb74d;">
                    </div>

                    <button type="submit" class="neobrutalist-btn" style="background: #ef6c00; color: white; border: 2px solid #000; margin-top: 0.5rem;">Guardar Gasto</button>
                </form>
            </div>
        </div>
    </div>
    <div id="honorarios" class="tab-pane">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
            
            <!-- Smart Debtors Table (Seguimiento) -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.5rem; box-shadow: 5px 5px 0px rgba(0,0,0,1);">
                <h3 style="margin: 0 0 1.5rem 0; font-size: 1.1rem; font-weight: 900; color: #000; text-transform: uppercase;">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Seguimiento de Sesiones (Por Cobrar)
                </h3>
                
                @if($debtors->isEmpty())
                    <div style="text-align: center; padding: 2rem; background: #f9f9f9; border: 2px dashed #ccc; border-radius: 8px;">
                        <p style="color: #666; font-weight: 700; margin: 0;">¡Todo al día! No hay sesiones pendientes de cobro.</p>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-family: 'Inter', sans-serif;">
                            <thead>
                                <tr style="border-bottom: 3px solid #000; text-align: left;">
                                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 800; color: #000; text-transform: uppercase;">Paciente</th>
                                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 800; color: #000; text-transform: uppercase;">Última Sesión</th>
                                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 800; color: #000; text-transform: uppercase; text-align: center;">Cant.</th>
                                    <th style="padding: 1rem; font-size: 0.8rem; font-weight: 800; color: #000; text-transform: uppercase;">Total</th>
                                    <th style="padding: 1rem; text-align: right; font-size: 0.8rem; font-weight: 800; color: #000; text-transform: uppercase;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debtors as $dept)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 1rem; font-weight: 700; color: #333;">
                                            {{ $dept['user']->nombre }}
                                            @if($dept['is_frequent'])
                                                <span style="font-size: 0.65rem; background: #e8f5e9; color: #2e7d32; padding: 2px 5px; border-radius: 4px; border: 1px solid #c8e6c9;">FREC</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1rem; color: #555;">{{ \Carbon\Carbon::parse($dept['last_session_date'])->format('d/m/Y') }}</td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <span style="background: #fff3e0; border: 2px solid #ffb74d; padding: 2px 8px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; color: #ef6c00;">{{ $dept['sessions_count'] }}</span>
                                        </td>
                                        <td style="padding: 1rem; font-weight: 800; color: #000;">${{ number_format($dept['total_debt'], 0, ',', '.') }}</td>
                                        <td style="padding: 1rem; text-align: right;">
                                            <a href="{{ $dept['whatsapp_link'] }}" target="_blank" 
                                               class="neobrutalist-btn" 
                                               style="background: #25D366; color: white; padding: 6px 15px; font-size: 0.8rem; text-decoration: none; border: 2px solid #000; display: inline-flex; align-items: center; gap: 5px; box-shadow: 2px 2px 0px #000; transition: all 0.2s;"
                                               onclick="this.style.background='#128C7E'; this.innerHTML='<i class=\'fa-solid fa-check\'></i> Enviado';">
                                                <i class="fa-brands fa-whatsapp"></i> Recordar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Inflation Tool -->
            <div class="neobrutalist-card" style="background: #e3f2fd; border: 3px solid #1565c0; padding: 1.5rem; box-shadow: 4px 4px 0px rgba(21, 101, 192, 0.3); align-self: start;">
                <div style="display: flex; gap: 0.8rem; align-items: center; margin-bottom: 1rem;">
                     <div style="background: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 3px solid #1565c0;">
                         <i class="fa-solid fa-money-bill-trend-up" style="color: #0d47a1; font-size: 1.1rem;"></i>
                     </div>
                     <h3 style="margin: 0; color: #0d47a1; font-weight: 900; font-size: 1.1rem; text-transform: uppercase;">Ajuste Inflación</h3>
                </div>
                
                <p style="color: #0d47a1; margin-bottom: 1.2rem; font-size: 0.8rem; line-height: 1.4; font-weight: 600;">
                    Aumenta el <strong>Honorario Pactado</strong> de todos tus pacientes activos automáticamente.
                </p>
                
                <form action="{{ route('admin.finance.update-prices') }}" method="POST" style="display: flex; gap: 0.5rem; width: 100%;">
                    @csrf
                    <div style="flex: 1;">
                        <input type="number" name="porcentaje" min="1" step="0.1" placeholder="% Aumento" class="neobrutalist-input" style="width: 100%; margin: 0; padding: 0.6rem; border: 2px solid #1565c0; font-weight: 700; height: 45px;">
                    </div>
                    <button type="submit" class="neobrutalist-btn" style="background: #1565c0; color: white; margin: 0; font-size: 0.85rem; padding: 0 1.2rem; border: 2px solid #000; box-shadow: 3px 3px 0px rgba(0,0,0,0.4); height: 45px; display: flex; align-items: center; justify-content: center;" onclick="return confirm('¿Confirmás el aumento masivo?')">
                        Aplicar
                    </button>
                </form>
            </div>

        </div>
    </div>
    
    <!-- TAB 3: REPORTES -->
    <div id="reportes" class="tab-pane">
        <div style="background: white; border: 3px solid #000; padding: 2rem; border-radius: 8px; box-shadow: 5px 5px 0px rgba(0,0,0,1); display: flex; flex-direction: column; align-items: center; text-align: center; max-width: 600px; margin: 0 auto;">
            <div style="width: 80px; height: 80px; background: #e8f5e9; border: 3px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 2.5rem; color: #2e7d32; display: block;"></i>
            </div>
            
            <h3 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 0.5rem; color: #333;">Exportar Reporte Mensual</h3>
            <p style="color: #666; font-size: 1rem; margin-bottom: 2rem; max-width: 400px;">
                Generá un archivo Excel con el detalle de ingresos, pacientes y deudas del periodo <strong>{{ $monthName }} {{ $year }}</strong>.
            </p>
            
            <a href="{{ route('admin.finanzas.report', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn-export-excel">
                <i class="fa-solid fa-file-excel" style="font-size: 1.2rem;"></i> DESCARGAR EXCEL
            </a>
            
            <p style="margin-top: 1.5rem; font-size: 0.8rem; color: #999;">
                Formato compatible con Microsoft Excel y Google Sheets.
            </p>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Tabs Logic
    function openTab(evt, tabName) {
        // Hide all tab content
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-pane");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
            tabcontent[i].style.display = "none";
        }

        // Remove active class from buttons
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }

        // Show current tab and add active class to button
        document.getElementById(tabName).style.display = "block";
        // Small delay to allow display block to apply before opacity animation if needed, 
        // but CSS class switching handles it mostly.
        setTimeout(() => {
             document.getElementById(tabName).classList.add("active");
        }, 10);
       
        evt.currentTarget.classList.add("active");
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Charts Logic
        const incomeEl = document.getElementById('incomeChart');
        const patientEl = document.getElementById('patientChart');

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#333';

        if (incomeEl && patientEl) {
            const ctxIncome = incomeEl.getContext('2d');
            const ctxPatient = patientEl.getContext('2d');

            fetch('{{ route("admin.finance.chart-data", request()->query()) }}')
                .then(response => response.json())
                .then(data => {
                    
                    // 1. Income Chart
                    new Chart(ctxIncome, {
                        type: 'line',
                        data: {
                            labels: data.incomes.map(item => item.mes),
                            datasets: [{
                                label: 'Ingresos',
                                data: data.incomes.map(item => item.total),
                                borderColor: '#2e7d32',
                                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                                borderWidth: 3,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#2e7d32',
                                pointBorderWidth: 3,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: { 
                                    backgroundColor: '#000',
                                    titleFont: { family: 'Inter', size: 13 },
                                    bodyFont: { family: 'Inter', size: 13, weight: 'bold' },
                                    padding: 10,
                                    callbacks: { 
                                        label: function(c) { return '$' + new Intl.NumberFormat('es-AR').format(c.raw); } 
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f0f0f0' },
                                    ticks: { font: { size: 11, weight: '600' } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11, weight: '600' } }
                                }
                            }
                        }
                    });

                    // 2. Patient Types Chart
                new Chart(ctxPatient, {
                    type: 'doughnut',
                    data: {
                        labels: data.patientTypes.map(item => item.tipo_paciente.charAt(0).toUpperCase() + item.tipo_paciente.slice(1)),
                        datasets: [{
                            data: data.patientTypes.map(item => item.total),
                            backgroundColor: ['#d1c4e9', '#66bb6a', '#42a5f5'], // Violet (New), Green (Frec), Blue (Other)
                            borderColor: '#000',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        layout: { padding: 10 },
                        plugins: {
                            legend: { display: false } // Custom legend below
                        }
                    }
                });
                
                // Custom Legend Generation
                const legendContainer = document.getElementById('patientChartLegend');
                if(legendContainer) {
                    legendContainer.innerHTML = `
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 10px; flex-wrap: wrap;">
                            <span class="badge-new" style="background: #ede7f6; color: #5e35b1; border-color: #d1c4e9;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Nuevos
                            </span>
                             <span class="badge-new" style="background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Frecuentes
                            </span>
                        </div>
                    `;
                }
                });
        }

        // Check for URL Hash to activate tab
        if(window.location.hash) {
            const hash = window.location.hash.substring(1); // remove #
            const tabBtn = document.querySelector(`.tab-btn[onclick*="'${hash}'"]`);
            if (tabBtn) {
                // Mock event object for openTab
                openTab({currentTarget: tabBtn}, hash);
            }
        }
    });
</script>
@endsection
