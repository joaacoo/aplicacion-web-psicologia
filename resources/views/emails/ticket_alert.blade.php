<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #fee2e2; color: #991b1b; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; font-weight: bold; font-size: 1.2rem; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; display: block; }
        .value { background: #f9fafb; padding: 10px; border-radius: 4px; border: 1px solid #eee; }
        .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; text-align: center; }
        .footer { text-align: center; font-size: 0.8rem; color: #888; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            🚨 Nuevo Reporte de Error
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Ticket ID:</span>
                #{{ $ticket->id }}
            </div>
            
            <div class="field">
                <span class="label">Usuario:</span>
                {{ $ticket->user ? $ticket->user->nombre : 'Anónimo' }} ({{ $ticket->user ? $ticket->user->email : 'N/A' }})
            </div>

            <div class="field">
                <span class="label">Problema Reportado:</span>
                <div class="value">"{{ $ticket->description }}"</div>
            </div>

            @if($ticket->metadata)
                <div class="field">
                    <span class="label">Datos Técnicos:</span>
                    <div class="value" style="font-size: 0.85rem; font-family: monospace;">
                        {{ json_encode($ticket->metadata, JSON_PRETTY_PRINT) }}
                    </div>
                </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('admin.developer') }}" class="btn">Ir al Panel de Desarrollador</a>
            </div>
        </div>
        <div class="footer">
            Reporte generado automáticamente por Sistema Web Psicóloga Naza.
        </div>
    </div>
</body>
</html>
