@extends('layouts.app')

@section('title', 'Finanzas - Admin')
@section('header_title', 'Panel de Finanzas')

@section('content')
<style>
@media (max-width: 768px) {
        /* Stack Filters */
        div[style*="display: flex; flex-wrap: wrap"] {
            flex-direction: column;
            align-items: stretch !important;
        }
        div[style*="display: flex; flex-wrap: wrap"] > div {
            width: 100%;
        }
        .filter-control {
            width: auto; /* Let flex handle width */
            flex: 1;    /* Share space */
        }
        /* Target the specific form */
        form[action*="finanzas"] {
            width: 100%;
        }
        
        /* Stack Tabs */
        .finance-tabs {
            flex-direction: column;
            border-bottom: none !important;
            gap: 0.5rem;
            margin-bottom: 2rem !important;
        }
        .tab-btn {
            width: 100%;
            text-align: center;
            border: 2px solid #000 !important;
            margin: 0 !important;
            border-radius: 50px; /* Pill style on mobile for better touch */
        }
        .tab-btn.active {
            border-bottom: 2px solid #000 !important;
            transform: scale(1.02);
        }
        
        /* Stack Grids */
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important; /* Force single column on all grids */
            gap: 1.5rem !important;
        }
        
        /* Adjust Chart height */
        canvas {
            max-height: 250px;
        }
        
        /* Card Adjustments */
        .neobrutalist-card {
            padding: 1.2rem !important;
        }
        
        /* MOBILE: Convert Debtors Table to Cards */
        .debtors-table-container {
            overflow-x: visible !important;
        }
        
        .debtors-table {
            display: block !important;
        }
        
        .debtors-table thead {
            display: none !important;
        }
        
        .debtors-table tbody {
            display: block !important;
        }
        
        .debtors-table tr {
            display: block !important;
            border: 3px solid #000 !important;
            border-radius: 12px !important;
            margin-bottom: 1rem !important;
            padding: 1rem !important;
            background: white !important;
            box-shadow: 4px 4px 0px #000 !important;
        }
        
        .debtors-table td {
            display: block !important;
            text-align: left !important;
            padding: 0.5rem 0 !important;
            border: none !important;
        }
        
        .debtors-table td:before {
            content: attr(data-label);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.7rem;
            color: #666;
            display: block;
            margin-bottom: 0.3rem;
        }
        
        .debtors-table td:last-child {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px dashed #eee !important;
        }
        
        /* MOBILE: Stack Modal Footer Buttons */
        #proofModal .modal-footer {
            flex-direction: column !important;
            gap: 0.8rem !important;
        }
        
        #proofModal .modal-footer > div {
            width: 100% !important;
            justify-content: center !important;
        }
        
        #proofModal .modal-footer button,
        #proofModal .modal-footer a {
            flex: 1 !important;
            min-width: 120px !important;
        }
        
        /* MOBILE: Fit Modal Without Scroll */
        #proofModal > div {
            max-height: 95vh !important;
            height: auto !important;
        }
        
        #proofModal img {
            max-height: 35vh !important;
        }
        
        #proofModal > div > div:first-child {
            padding: 1rem !important;
        }
        
        #proofModal > div > div:nth-child(2) {
            padding: 1rem !important;
        }
        
        #proofModal .modal-footer {
            padding: 0.8rem !important;
        }
        
        #proofModal h3 {
            font-size: 1rem !important;
        }
        
        #proofModal p {
            font-size: 0.7rem !important;
        }
    }
    /* Safer Minmax for desktop */
    .dashboard-grid {
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 1rem; 
        margin-bottom: 1.5rem;
    }
    .dashboard-grid {
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 1rem; 
        margin-bottom: 1.5rem;
    }
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
    /* Subtle Bell Interaction */
    .notification-bell-container {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .notification-bell-container:hover {
        /* No hover effect: keep static */
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
    /* Custom Scrollbar for Carousel */
    .carousel-container::-webkit-scrollbar {
        height: 8px;
    }
    .carousel-container::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .carousel-container::-webkit-scrollbar-thumb {
        background: #ccc; 
        border-radius: 4px;
    }
    .carousel-container::-webkit-scrollbar-thumb:hover {
        background: #888; 
    }
</style>

<div style="padding: 1.5rem;">

    <!-- Top Bar: Title & Filters (Aligned) -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem;">

        
        <div id="financeFilters" style="display: flex; align-items: stretch; gap: 0.5rem; height: 42px;">
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
            <form action="{{ route('admin.finanzas') }}" method="GET" style="display: flex; gap: 0.5rem; margin: 0; align-items: stretch; position: relative; z-index: 50;">
                <!-- Fixed Width for Month Selector to show full name -->
                <select name="month" class="neobrutalist-input filter-control" style="border: 2px solid #000; text-transform: capitalize; cursor: pointer; line-height: 1.2; font-size: 0.9rem; min-width: 150px; border-radius: 8px; outline: none !important; box-shadow: none !important;" onfocus="this.style.borderColor='#000'" onblur="this.style.borderColor='#000'" onchange="showFinanceLoader(); this.form.submit()">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ ucfirst(\Carbon\Carbon::create(null, $m)->locale('es')->translatedFormat('F')) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="neobrutalist-input filter-control" style="border: 2px solid #000; cursor: pointer; line-height: 1.2; font-size: 0.9rem; min-width: 120px; width: auto; margin-left: 5px; outline: none !important; box-shadow: none !important; border-radius: 8px;" onfocus="this.style.borderColor='#000'" onblur="this.style.borderColor='#000'" onchange="showFinanceLoader(); this.form.submit()">
                    @for($y = 2026; $y <= 2030; $y++)
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
    </div>

    <!-- TAB 1: DASHBOARD -->
    <div id="dashboard" class="tab-pane active">
        <!-- Section 1: KPIs -->
        <div class="dashboard-grid">
            
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
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase;">Ganancia Real ({{ $monthName }})</span>
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
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase;">Pacientes Totales</span>
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
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ $frequentPatients }} Frecuentes
                    </span>
                </div>
            </div>

            <!-- Total Sessions (New) -->
            <div class="neobrutalist-card" style="background: white; border: 3px solid #000; padding: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 4px 4px 0px rgba(0,0,0,1);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <span style="font-size: 0.75rem; color: #666; font-weight: 800; text-transform: uppercase;">Sesiones ({{ $monthName }})</span>
                    <div style="background: #e3f2fd; padding: 5px; border-radius: 6px; border: 2px solid #000;">
                        <i class="fa-solid fa-calendar-check" style="font-size: 1.1rem; color: #1565c0;"></i>
                    </div>
                </div>
                <h2 style="margin: 0; font-size: 1.8rem; font-weight: 900; color: #000;">{{ $totalSessions }}</h2>
                <div style="font-size: 0.75rem; font-weight: 700; color: #666;">
                    Confirmadas / Realizadas
                </div>
                <!-- Optional: Link to Agenda if needed -->
                 <a href="{{ route('admin.agenda') }}" style="margin-top: auto; font-size: 0.75rem; font-weight: 800; color: #000; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                    Ver agenda <i class="fa-solid fa-arrow-right" style="font-size: 0.7rem;"></i>
                </a>
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
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 800; color: #000; text-transform: uppercase;"><i class="fa-solid fa-chart-pie"></i> Distribución de Pacientes (Total)</h3>
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
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;" class="admin-finance-grid">
            
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
        
        {{-- NEW: Pending Receipts Section (Full Width) --}}
        <div class="neobrutalist-card" style="background: #fff3e0; border: 3px solid #ffb74d; padding: 1.5rem; box-shadow: 5px 5px 0px rgba(255, 183, 77, 0.4); margin-bottom: 2rem;">
            <h3 style="margin: 0 0 1.5rem 0; font-size: 0.95rem; font-weight: 900; color: #ef6c00; text-transform: uppercase;">
                <i class="fa-solid fa-file-invoice"></i> Comprobantes Pendientes de Aprobación
            </h3>
            
            @if($pendingReceipts->isEmpty())
                <div style="text-align: center; padding: 2rem; background: white; border: 2px dashed #ffb74d; border-radius: 8px;">
                    <p style="color: #ef6c00; font-weight: 700; margin: 0;">No hay comprobantes pendientes de revisión.</p>
                </div>
            @else
                <!-- Carousel Container -->
                <div class="carousel-container" id="receiptsCarousel" style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; cursor: grab;">
                    @foreach($pendingReceipts as $receipt)
                        <div class="carousel-item" style="flex: 0 0 300px; scroll-snap-align: start; background: white; border: 3px solid #000; border-radius: 12px; padding: 1.5rem; box-shadow: 4px 4px 0px #000; display: flex; flex-direction: column; justify-content: space-between; user-select: none;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <div style="flex: 1;">
                                <div style="font-weight: 800; color: #000;">
                                    {{ $receipt['user']->nombre }}
                                    @if($receipt['paciente']->precio_personalizado)
                                        <i class="fa-solid fa-tag" style="color: #ef6c00; font-size: 0.7rem;" title="Precio personalizado"></i>
                                    @endif
                                </div>
                                <div style="font-size: 0.8rem; color: #666;">
                                    {{ \Carbon\Carbon::parse($receipt['fecha_hora'])->format('d/m H:i') }} - {{ ucfirst($receipt['modalidad']) }}
                                </div>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #2e7d32;">
                                    ${{ number_format($receipt['monto_estimado'], 0, ',', '.') }}
                                </div>
                            </div>
                                    @if($receipt['paciente'])
                                        <span style="background: {{ $receipt['paciente']->tipo_paciente === 'nuevo' ? '#ede7f6' : '#e8f5e9' }}; 
                                                     color: {{ $receipt['paciente']->tipo_paciente === 'nuevo' ? '#5e35b1' : '#2e7d32' }}; 
                                                     padding: 0.2rem 0.5rem; border: 2px solid #000; border-radius: 6px; font-weight: 800; font-size: 0.7rem; height: fit-content;">
                                            {{ $receipt['paciente']->tipo_paciente === 'nuevo' ? 'NUEVO' : 'FREC' }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if($receipt['comprobante_ruta'])
                                    <div style="margin: 1rem 0;">
                                        <button type="button" class="neobrutalist-btn" 
                                                onclick="openProofModalHelper('{{ route('payments.showProof', $receipt['turno']->payment->id) }}', '{{ $receipt['user']->nombre }}', '{{ \Carbon\Carbon::parse($receipt['fecha_hora'])->format('d/m H:i') }}')"
                                           style="width: 100%; background: #60a5fa; color: white; padding: 0.6rem; font-size: 0.85rem; border: 2px solid #000; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 2px 2px 0px #000;">
                                            <i class="fa-solid fa-file-image"></i> Ver Comprobante
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; margin-top: auto;">
                                <form action="{{ route('admin.appointments.confirm', $receipt['turno']->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <button type="submit" class="neobrutalist-btn" 
                                            style="width: 100%; background: #10b981; color: white; padding: 0.6rem; font-size: 0.85rem; border: 2px solid #000; box-shadow: 2px 2px 0px #000; display: flex; justify-content: center; align-items: center;">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <form id="reject-form-{{ $receipt['turno']->payment->id }}" action="{{ route('admin.payments.reject', $receipt['turno']->payment->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn" 
                                            onclick="showConfirm('¿Rechazar comprobante?', function() { document.getElementById('reject-form-{{ $receipt['turno']->payment->id }}').submit(); })"
                                            style="width: 100%; background: #ef4444; color: white; padding: 0.6rem; font-size: 0.85rem; border: 2px solid #000; box-shadow: 2px 2px 0px #000; display: flex; justify-content: center; align-items: center;">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
            
            {{-- REMOVED: Old Pending Receipts Section Location --}}


            {{-- REMOVED: Debtors Table --}}


            <!-- Link to Honorarios Configuration -->
            <div class="neobrutalist-card" style="background: #e3f2fd; border: 3px solid #1565c0; padding: 1.5rem; box-shadow: 4px 4px 0px rgba(21, 101, 192, 0.3); align-self: start; max-width: 500px; width: 100%;">
                <div style="display: flex; gap: 0.8rem; align-items: center; margin-bottom: 1rem;">
                     <div style="background: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 3px solid #1565c0;">
                         <i class="fa-solid fa-money-bill-trend-up" style="color: #0d47a1; font-size: 1.1rem;"></i>
                     </div>
                     <h3 style="margin: 0; color: #0d47a1; font-weight: 900; font-size: 1.1rem; text-transform: uppercase;">Configurar Honorarios</h3>
                </div>
                
                <p style="color: #0d47a1; margin-bottom: 1.2rem; font-size: 0.8rem; line-height: 1.4; font-weight: 600;">
                    Accedé a la configuración de honorarios y sesiones para ajustar precios y parámetros.
                </p>
                
                <a href="{{ route('admin.configuracion') }}#honorarios" class="neobrutalist-btn" style="background: #1565c0; color: white; margin: 0; font-size: 0.9rem; padding: 0.8rem 1.5rem; border: 2px solid #000; box-shadow: 3px 3px 0px rgba(0,0,0,0.4); display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; width: 100%;">
                    <i class="fa-solid fa-gear"></i> Ir a Configuración
                </a>
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
        
        // Hide/Show filters based on tab
        const filters = document.getElementById('financeFilters');
        if (tabName === 'honorarios') {
            filters.style.display = 'none';
        } else {
            filters.style.display = 'flex';
        }
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
                if (legendContainer) {
                    legendContainer.innerHTML = `
                        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                             <span class="badge-new" style="background: #ede7f6; color: #5e35b1; border-color: #d1c4e9;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Nuevos
                            </span>
                            <span class="badge-new" style="background: #e8f5e9; color: #2e7d32; border-color: #c8e6c9;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> Frecuentes
                            </span>
                        </div>
                    `;
                }
            }); // Close fetch
        } // Close if check

        // Check for URL Hash to activate tab
        if(window.location.hash) {
            const hash = window.location.hash.substring(1); // remove #
            const tabBtn = document.querySelector(`.tab-btn[onclick*="'${hash}'"]`);
            if (tabBtn) {
                // Mock event object for openTab
                openTab({currentTarget: tabBtn}, hash);
            }
        }

        // Drag to scroll functionality for carousel
        const slider = document.getElementById('receiptsCarousel');
        if (slider) {
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                slider.classList.add('active');
                slider.style.cursor = 'grabbing';
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener('mouseleave', () => {
                isDown = false;
                slider.style.cursor = 'grab';
            });

            slider.addEventListener('mouseup', () => {
                isDown = false;
                slider.style.cursor = 'grab';
            });

            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 2; // Scroll-fast
                slider.scrollLeft = scrollLeft - walk;
            });
        }
    });
</script>


<!-- Modal de Comprobante (Moved to end) -->
<div id="proofModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: white; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; border: 4px solid #000; border-radius: 12px; box-shadow: 8px 8px 0px #000;">
        <div style="padding: 1.5rem; border-bottom: 3px solid #000; display: flex; justify-content: space-between; align-items: center; background: #f0f0f0;">
            <div>
                <h3 id="proofModalTitle" style="margin: 0; font-weight: 900; font-size: 1.2rem;">Comprobante de Pago</h3>
                <p id="proofModalSubtitle" style="margin: 0.2rem 0 0 0; font-size: 0.8rem; color: #666;"></p>
            </div>
            <button onclick="closeProofModal()" class="neobrutalist-btn" style="background: #ff5252; color: white; padding: 0.4rem 0.8rem; font-size: 1rem;"><i class="fa-solid fa-times"></i></button>
        </div>
        <div style="padding: 2rem; text-align: center; background: #333; min-height: 200px;">
            <div id="proofLoader" style="display: none; padding: 2rem; text-align: center;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: white;"></i>
            </div>
            {{-- Image for regular photos --}}
            <img id="proofModalImg" src="" alt="Comprobante" style="max-width: 100%; max-height: 60vh; border: 2px solid #fff; box-shadow: 0 0 15px rgba(0,0,0,0.5); display: none; margin: 0 auto;" onload="document.getElementById('proofLoader').style.display='none'; this.style.display='block'; this.style.animation='fadeIn 0.3s ease-in-out';">
            {{-- Iframe for PDFs --}}
            <iframe id="proofModalPdf" src="" style="width: 100%; height: 65vh; border: 2px solid #fff; display: none; margin: 0 auto; background: white;"></iframe>
        </div>
        <div class="modal-footer" style="padding: 1rem; border-top: 3px solid #000; background: white; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <!-- Navigation Buttons -->
            <div style="display: flex; gap: 0.5rem;">
                <button id="proofModalPrev" onclick="navigateProof(-1)" class="neobrutalist-btn" style="background: white; color: #000; padding: 0.6rem 1rem; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button id="proofModalNext" onclick="navigateProof(1)" class="neobrutalist-btn" style="background: white; color: #000; padding: 0.6rem 1rem; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 0.5rem; flex: 1; justify-content: center;">
                <button id="proofModalApprove" onclick="approveFromModal()" class="neobrutalist-btn" style="background: #10b981; color: white; padding: 0.6rem 1.5rem; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-check"></i> Aprobar
                </button>
                <button id="proofModalReject" onclick="rejectFromModal()" class="neobrutalist-btn" style="background: #ef4444; color: white; padding: 0.6rem 1.5rem; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-times"></i> Rechazar
                </button>
            </div>
            
            <!-- Download Button (Icon Only) -->
            <a id="proofModalDownload" href="#" download class="neobrutalist-btn bg-amarillo" style="text-decoration: none; padding: 0.6rem 1rem;" title="Descargar Imagen">
                <i class="fa-solid fa-download"></i>
            </a>
        </div>
    </div>
</div>

<script>
    let currentProofIndex = 0;
    let allProofs = [];

    function openProofModalHelper(url, userName, date, appointmentId) {
        // Reset state
        document.getElementById('proofLoader').style.display = 'block';
        document.getElementById('proofModalImg').style.display = 'none';
        
        // Collect all proofs from the carousel
        const carouselItems = document.querySelectorAll('.carousel-item');
        allProofs = Array.from(carouselItems).map((item, index) => {
            const viewButton = item.querySelector('button[onclick*="openProofModalHelper"]');
            if (viewButton) {
                const onclickAttr = viewButton.getAttribute('onclick');
                const match = onclickAttr.match(/openProofModalHelper\('([^']+)',\s*'([^']+)',\s*'([^']+)'(?:,\s*(\d+))?\)/);
                if (match) {
                    const approveForm = item.querySelector('form[action*="confirm"]');
                    const rejectForm = item.querySelector('form[action*="reject"]');
                    return {
                        url: match[1],
                        userName: match[2],
                        date: match[3],
                        appointmentId: approveForm ? approveForm.action.match(/\/(\d+)\/confirm/)[1] : null,
                        approveAction: approveForm ? approveForm.action : null,
                        rejectAction: rejectForm ? rejectForm.action : null
                    };
                }
            }
            return null;
        }).filter(p => p !== null);
        
        // Find current index
        currentProofIndex = allProofs.findIndex(p => p.url === url);
        if (currentProofIndex === -1) currentProofIndex = 0;
        
        loadProofAtIndex(currentProofIndex);
        
        document.getElementById('proofModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function loadProofAtIndex(index) {
        if (index < 0 || index >= allProofs.length) return;
        
        const proof = allProofs[index];
        const imgEl  = document.getElementById('proofModalImg');
        const pdfEl  = document.getElementById('proofModalPdf');
        const loader = document.getElementById('proofLoader');

        // Reset both viewers
        imgEl.style.display = 'none';
        pdfEl.style.display = 'none';
        imgEl.src = '';
        pdfEl.src = '';
        loader.style.display = 'block';

        document.getElementById('proofModalTitle').innerText = 'Comprobante: ' + proof.userName;
        document.getElementById('proofModalSubtitle').innerText = 'Subido el ' + proof.date;
        document.getElementById('proofModalDownload').href = proof.url;
        
        // Navigation buttons
        document.getElementById('proofModalPrev').disabled = (index === 0);
        document.getElementById('proofModalNext').disabled = (index === allProofs.length - 1);
        document.getElementById('proofModalPrev').style.opacity = (index === 0) ? '0.5' : '1';
        document.getElementById('proofModalNext').style.opacity = (index === allProofs.length - 1) ? '0.5' : '1';

        // Approve / reject actions
        document.getElementById('proofModalApprove').setAttribute('data-approve-action', proof.approveAction || '');
        document.getElementById('proofModalReject').setAttribute('data-reject-action', proof.rejectAction || '');

        // Detect if file is PDF via HEAD request
        fetch(proof.url, { method: 'HEAD' })
            .then(res => {
                const ct = res.headers.get('Content-Type') || '';
                if (ct.includes('pdf')) {
                    // PDF: show iframe
                    loader.style.display = 'none';
                    pdfEl.src = proof.url;
                    pdfEl.style.display = 'block';
                    pdfEl.style.animation = 'fadeIn 0.3s ease-in-out';
                } else {
                    // Image: show img (onload will hide loader)
                    imgEl.src = proof.url;
                    imgEl.style.animation = 'fadeIn 0.3s ease-in-out';
                }
            })
            .catch(() => {
                // Fallback: try as image
                imgEl.src = proof.url;
                imgEl.style.animation = 'fadeIn 0.3s ease-in-out';
            });
    }
    
    function navigateProof(direction) {
        const newIndex = currentProofIndex + direction;
        if (newIndex >= 0 && newIndex < allProofs.length) {
            currentProofIndex = newIndex;
            loadProofAtIndex(currentProofIndex);
        }
    }
    
    function approveFromModal() {
        const action = document.getElementById('proofModalApprove').getAttribute('data-approve-action');
        if (action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function rejectFromModal() {
        showConfirm('¿Rechazar comprobante?', function() {
            const action = document.getElementById('proofModalReject').getAttribute('data-reject-action');
            if (action) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function closeProofModal() {
        document.getElementById('proofModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('proofModalImg').src = '';
        document.getElementById('proofModalImg').style.display = 'none';
        document.getElementById('proofModalPdf').src = '';
        document.getElementById('proofModalPdf').style.display = 'none';
        allProofs = [];
        currentProofIndex = 0;
    }
    
    // Close on outside click
    document.getElementById('proofModal').addEventListener('click', function(e) {
        if (e.target === this) closeProofModal();
    });
</script>
@endsection
