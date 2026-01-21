@php
    $isExcel = request('export') === 'excel';
    $borderColor = $isExcel ? '#000000' : '#000';
    $headerBg = $isExcel ? '#000000' : '#000';
    $headerText = $isExcel ? '#ffffff' : '#fff';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero - {{ $monthName }} {{ $year }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; padding: 20px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 10px; font-size: 14px; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .kpi-box { border: 2px solid #000; padding: 15px; text-align: center; background: #fff; }
        .kpi-title { font-size: 10px; text-transform: uppercase; color: #555; font-weight: bold; margin-bottom: 5px; }
        .kpi-value { font-size: 24px; font-weight: 900; color: #000; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .kpi-box { border: 2px solid #000 !important; }
        }
    </style>
</head>
<body @if(!$isExcel) onload="setTimeout(() => window.print(), 500)" @endif>

    @if(!$isExcel)
    <div class="no-print" style="margin-bottom: 20px; font-family: sans-serif; background: #f0f0f0; padding: 10px; border-radius: 8px;">
        <p style="margin:0;">🖨️ Vista de Impresión. Para Excel, usá el botón "Descargar Excel" del panel.</p>
    </div>
    @endif

    <!-- Header Table -->
    <table style="border: none; margin-bottom: 40px;">
        <tr>
            <td style="border: none; width: 60%; vertical-align: top;">
                <h1 style="margin: 0; font-size: 24px; text-transform: uppercase; font-weight: 900;">Reporte Financiero</h1>
                <h2 style="margin: 5px 0 0; font-size: 16px; color: #555;">Lic. Nazarena De Luca</h2>
            </td>
            <td style="border: none; width: 40%; text-align: right; vertical-align: top;">
                <p style="margin: 0; font-size: 12px; color: #555;">
                    <strong>PERIODO:</strong> {{ $monthName }} {{ $year }}<br>
                    <strong>GENERADO:</strong> {{ now()->format('d/m/Y H:i') }}
                </p>
            </td>
        </tr>
    </table>

    <!-- KPIs using Table for Excel compatibility -->
    <table style="border: none; margin-bottom: 30px;">
        <tr>
            <td style="border: none; padding: 5px; width: 25%;">
                <div class="kpi-box" style="border: 2px solid #000; padding: 15px; background: #e8f5e9;">
                    <div class="kpi-title" style="color: #1b5e20;">INGRESOS TOTALES</div>
                    <div class="kpi-value" style="color: #1b5e20;">${{ number_format($monthlyIncome, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="border: none; padding: 5px; width: 25%;">
                <div class="kpi-box" style="border: 2px solid #000; padding: 15px; background: #ffebee;">
                    <div class="kpi-title" style="color: #b71c1c;">GASTOS TOTALES</div>
                    <div class="kpi-value" style="color: #b71c1c;">${{ number_format($totalGastos, 0, ',', '.') }}</div>
                </div>
            </td>
             <td style="border: none; padding: 5px; width: 25%;">
                <div class="kpi-box" style="border: 2px solid #000; padding: 15px; background: #e3f2fd;">
                    <div class="kpi-title" style="color: #0d47a1;">GANANCIA NETA</div>
                    <div class="kpi-value" style="color: #0d47a1;">${{ number_format($realProfit, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="border: none; padding: 5px; width: 25%;">
                <div class="kpi-box" style="border: 2px solid #000; padding: 15px; background: #fff;">
                    <div class="kpi-title">PENDIENTE COBRO</div>
                    <div class="kpi-value" style="color: #f57f17;">${{ number_format($pendingIncome, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>


    <!-- INGRESOS Section -->
    <h3 style="border-bottom: 2px solid #000; padding-bottom: 10px; text-transform: uppercase; font-family: sans-serif; margin-top: 40px; margin-bottom: 20px;">
        Detalle de Ingresos
    </h3>
    @if($incomes->isEmpty())
        <p>No hay ingresos registrados en este periodo.</p>
    @else
        <table border="{{ $isExcel ? '1' : '0' }}" cellpadding="0" cellspacing="0">
            <thead>
                <tr style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: left;">FECHA</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: left;">PACIENTE</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: center;">ESTADO</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: right;">MONTO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $income)
                <tr>
                    <td style="border-bottom: 1px solid #ccc;">{{ \Carbon\Carbon::parse($income->fecha_hora)->format('d/m/Y') }}</td>
                    <td style="border-bottom: 1px solid #ccc;">{{ $income->user->nombre ?? 'Desconocido' }}</td>
                    <td style="border-bottom: 1px solid #ccc; text-align: center;">
                         <span style="font-size: 10px; padding: 2px 6px; border: 1px solid #333; border-radius: 4px; text-transform: uppercase;">{{ $income->estado }}</span>
                    </td>
                    <td style="text-align: right; border-bottom: 1px solid #ccc;">${{ number_format($income->monto_final, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f0f0f0;">
                    <td colspan="3" style="text-align: right; font-weight: bold; border-top: 2px solid #000;">TOTAL INGRESOS</td>
                    <td style="text-align: right; font-weight: bold; border-top: 2px solid #000;">${{ number_format($monthlyIncome, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- GASTOS Section -->
    <h3 style="border-bottom: 2px solid #000; padding-bottom: 10px; text-transform: uppercase; font-family: sans-serif; margin-top: 40px; margin-bottom: 20px;">
        Detalle de Gastos
    </h3>
    @if($gastos->isEmpty())
        <p>No hay gastos registrados en este periodo.</p>
    @else
        <table border="{{ $isExcel ? '1' : '0' }}" cellpadding="0" cellspacing="0">
            <thead>
                <tr style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: left;">FECHA</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: left;">CATEGORÍA</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: left;">DESCRIPCIÓN</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: right;">MONTO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gastos as $gasto)
                <tr>
                    <td style="border-bottom: 1px solid #ccc;">{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                    <td style="border-bottom: 1px solid #ccc;">{{ ucfirst($gasto->categoria) }}</td>
                    <td style="border-bottom: 1px solid #ccc;">{{ $gasto->descripcion ?? '-' }}</td>
                    <td style="text-align: right; border-bottom: 1px solid #ccc;">${{ number_format($gasto->monto, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f0f0f0;">
                    <td colspan="3" style="text-align: right; font-weight: bold; border-top: 2px solid #000;">TOTAL GASTOS</td>
                    <td style="text-align: right; font-weight: bold; border-top: 2px solid #000;">${{ number_format($totalGastos, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- DEUDORES Section -->
    <h3 style="border-bottom: 2px solid #000; padding-bottom: 10px; text-transform: uppercase; font-family: sans-serif; margin-top: 40px; margin-bottom: 20px;">
        Pacientes con Deuda (Seguimiento)
    </h3>
    @if($debtors->isEmpty())
        <p>No hay deudas registradas.</p>
    @else
        <table border="{{ $isExcel ? '1' : '0' }}" cellpadding="0" cellspacing="0">
            <thead>
                <tr style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: left;">PACIENTE</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: center;">TIPO</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: center;">ÚLT. SESIÓN</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: center;">CANT. SESIONES</th>
                    <th style="background-color: {{ $headerBg }}; color: {{ $headerText }}; text-align: right;">DEUDA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($debtors as $dept)
                <tr>
                    <td style="border-bottom: 1px solid #ccc;">{{ $dept['user']->nombre }}</td>
                    <td style="border-bottom: 1px solid #ccc; text-align: center;">
                        <span style="font-size: 10px; padding: 2px 6px; border: 1px solid #333; border-radius: 4px;">{{ $dept['is_frequent'] ? 'FRECUENTE' : 'NUEVO' }}</span>
                    </td>
                    <td style="border-bottom: 1px solid #ccc; text-align: center;">{{ \Carbon\Carbon::parse($dept['last_session_date'])->format('d/m/Y') }}</td>
                    <td style="border-bottom: 1px solid #ccc; text-align: center;">{{ $dept['sessions_count'] }}</td>
                    <td style="border-bottom: 1px solid #ccc; text-align: right; font-weight: bold;">${{ number_format($dept['total_debt'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f0f0f0;">
                    <td colspan="4" style="text-align: right; font-weight: bold; border-top: 2px solid #000;">TOTAL PENDIENTE</td>
                    <td style="text-align: right; font-weight: bold; border-top: 2px solid #000;">${{ number_format($debtors->sum('total_debt'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

</body>
</html>
