<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Comprobante</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>¡Nuevo Comprobante Recibido!</h2>
    <p>El paciente <strong>{{ $patientName }}</strong> ha subido un nuevo comprobante de pago.</p>
    <p>Para el turno del: <strong>{{ $appointmentDate }}</strong></p>
    
    <p>
        <a href="{{ route('admin.dashboard') }}" style="background: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ir al Panel</a>
    </p>
</body>
</html>
