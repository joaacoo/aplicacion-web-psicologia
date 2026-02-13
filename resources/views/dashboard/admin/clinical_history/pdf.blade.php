<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica - {{ $paciente->nombre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 2cm;
        }
        
        h1 {
            font-size: 24px;
            color: #000;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        h2 {
            font-size: 16px;
            color: #000;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .header {
            background: #f9fafb;
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        
        .header p {
            margin: 8px 0;
            font-size: 13px;
        }
        
        .header strong {
            color: #000;
        }
        
        .turno-block {
            margin-bottom: 25px;
            border-left: 5px solid #000;
            padding-left: 15px;
            page-break-inside: avoid;
        }
        
        .turno-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .turno-date {
            font-weight: bold;
            color: #000;
            font-size: 14px;
        }
        
        .turno-type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .turno-type.presencial {
            background: #dbeafe;
            color: #0369a1;
        }
        
        .turno-type.virtual {
            background: #fce7f3;
            color: #be185d;
        }
        
        .turno-type.default {
            background: #e5e7eb;
            color: #374151;
        }
        
        .note-content {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 4px;
            color: #555;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 13px;
            margin-top: 10px;
        }
        
        .no-note {
            color: #999;
            font-style: italic;
            font-size: 13px;
            background: #f9fafb;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #d1d5db;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #000;
            font-size: 12px;
            color: #666;
        }
        
        .footer strong {
            color: #000;
        }
        
        .hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>Historia Clinica</h1>
        
        <p><strong>Paciente:</strong> {{ $paciente->nombre }}</p>
        
        @if($paciente->email)
            <p><strong>Email:</strong> {{ $paciente->email }}</p>
        @endif
        
        <div class="hr"></div>
        
        <p><strong>Total de Sesiones Registradas:</strong> {{ count($turnos) }}</p>
        <p><strong>Sesiones con Notas Clinicas:</strong> {{ count($turnos->filter(fn($t) => $t->clinicalHistory)) }}</p>
        
        <div class="hr"></div>
        
        <p style="font-size: 12px; color: #6b7280;"><strong>Generado:</strong> {{ $generated_at->format('d/m/Y H:i') }}</p>
    </div>

    <!-- CONTENIDO -->
    @if($turnos->isEmpty())
        <div class="empty-state">
            <p>No hay sesiones registradas para este paciente.</p>
        </div>
    @else
        @foreach($turnos as $turno)
            <div class="turno-block">
                <!-- HEADER DEL TURNO -->
                <div class="turno-header">
                    <div class="turno-date">
                        {{ $turno->fecha_hora->format('d/m/Y H:i') }}
                    </div>
                    
                    @if($turno->tipo)
                        <span class="turno-type {{ $turno->tipo }}">
                            {{ ucfirst($turno->tipo) }}
                        </span>
                    @else
                        <span class="turno-type default">
                            Sin especificar
                        </span>
                    @endif
                </div>
                
                <!-- CONTENIDO DEL TURNO -->
                @if($turno->clinicalHistory)
                    <div class="note-content">
{{ $turno->clinicalHistory->content }}
                    </div>
                    
                    @if($turno->clinicalHistory->created_at->diffInDays($turno->clinicalHistory->updated_at) > 0)
                        <p style="font-size: 11px; color: #f59e0b; margin-top: 8px;">
                            Editada: {{ $turno->clinicalHistory->updated_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                @else
                    <div class="no-note">
                        Sin nota clinica registrada
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>CONFIDENCIAL:</strong> Este documento contiene informacion clinica sensible. Debe ser almacenado de forma segura y accesible solo por Nazarena De Luca.</p>
        <p style="margin-top: 10px;">Archivo generado automaticamente por el sistema de Historia Clinica.</p>
    </div>
</body>
</html>
