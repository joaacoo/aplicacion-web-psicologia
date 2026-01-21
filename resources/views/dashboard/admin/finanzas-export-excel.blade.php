<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="4" style="font-weight: bold; font-size: 16px; text-align: center;">Reporte Financiero - {{ $monthName }} {{ $year }}</th>
            </tr>
            <tr>
                <th colspan="4" style="text-align: center;">Generado: {{ now()->format('d/m/Y H:i') }}</th>
            </tr>
            <tr></tr> <!-- Empty Row -->
            
            <!-- Resumen -->
            <tr>
                <td colspan="2" style="font-weight: bold; background-color: #e8f5e9;">INGRESOS TOTALES</td>
                <td colspan="2" style="font-weight: bold; background-color: #e8f5e9;">${{ number_format($monthlyIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; background-color: #ffebee;">GASTOS TOTALES</td>
                <td colspan="2" style="font-weight: bold; background-color: #ffebee;">${{ number_format($totalGastos, 0, ',', '.') }}</td>
            </tr>
             <tr>
                <td colspan="2" style="font-weight: bold; background-color: #e3f2fd;">GANANCIA NETA</td>
                <td colspan="2" style="font-weight: bold; background-color: #e3f2fd;">${{ number_format($realProfit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; background-color: #fff3e0;">PENDIENTE COBRO</td>
                <td colspan="2" style="font-weight: bold; background-color: #fff3e0;">${{ number_format($pendingIncome, 0, ',', '.') }}</td>
            </tr>
            <tr></tr>

            <!-- Detalle Ingresos -->
            <tr>
                <th colspan="4" style="font-weight: bold; background-color: #000000; color: #ffffff;">DETALLE DE INGRESOS</th>
            </tr>
            <tr>
                <th style="font-weight: bold; border: 1px solid #000000;">FECHA</th>
                <th style="font-weight: bold; border: 1px solid #000000;">PACIENTE</th>
                <th style="font-weight: bold; border: 1px solid #000000;">ESTADO</th>
                <th style="font-weight: bold; border: 1px solid #000000;">MONTO</th>
            </tr>
        </thead>
        <tbody>
            @if($incomes->isEmpty())
                <tr><td colspan="4">No hay ingresos registrados</td></tr>
            @else
                @foreach($incomes as $income)
                <tr>
                    <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($income->fecha_hora)->format('d/m/Y') }}</td>
                    <td style="border: 1px solid #000000;">{{ $income->user->nombre ?? 'Desconocido' }}</td>
                    <td style="border: 1px solid #000000;">{{ ucfirst($income->estado) }}</td>
                    <td style="border: 1px solid #000000;">{{ $income->monto_final }}</td>
                </tr>
                @endforeach
            @endif
            <tr></tr>

             <!-- Detalle Gastos -->
             <tr>
                <th colspan="4" style="font-weight: bold; background-color: #000000; color: #ffffff;">DETALLE DE GASTOS</th>
            </tr>
            <tr>
                <th style="font-weight: bold; border: 1px solid #000000;">FECHA</th>
                <th style="font-weight: bold; border: 1px solid #000000;">CATEGORÍA</th>
                <th style="font-weight: bold; border: 1px solid #000000;">DESCRIPCIÓN</th>
                <th style="font-weight: bold; border: 1px solid #000000;">MONTO</th>
            </tr>
        </tbody>
        <tbody>
            @if($gastos->isEmpty())
                <tr><td colspan="4">No hay gastos registrados</td></tr>
            @else
                @foreach($gastos as $gasto)
                <tr>
                    <td style="border: 1px solid #000000;">{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                    <td style="border: 1px solid #000000;">{{ ucfirst($gasto->categoria) }}</td>
                    <td style="border: 1px solid #000000;">{{ $gasto->descripcion }}</td>
                    <td style="border: 1px solid #000000;">{{ $gasto->monto }}</td>
                </tr>
                @endforeach
            @endif
             <tr></tr>

            <!-- Deudas -->
            <tr>
                <th colspan="4" style="font-weight: bold; background-color: #000000; color: #ffffff;">CONTROL DE DEUDAS</th>
            </tr>
            <tr>
                <th style="font-weight: bold; border: 1px solid #000000;">PACIENTE</th>
                <th style="font-weight: bold; border: 1px solid #000000;">TIPO</th>
                <th style="font-weight: bold; border: 1px solid #000000;">SESIONES</th>
                <th style="font-weight: bold; border: 1px solid #000000;">TOTAL DEUDA</th>
            </tr>
            @if($debtors->isEmpty())
                <tr><td colspan="4">No hay deudas registradas</td></tr>
            @else
                @foreach($debtors as $dept)
                <tr>
                    <td style="border: 1px solid #000000;">{{ $dept['user']->nombre }}</td>
                    <td style="border: 1px solid #000000;">{{ $dept['is_frequent'] ? 'FRECUENTE' : 'NUEVO' }}</td>
                    <td style="border: 1px solid #000000;">{{ $dept['sessions_count'] }}</td>
                    <td style="border: 1px solid #000000;">{{ $dept['total_debt'] }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
