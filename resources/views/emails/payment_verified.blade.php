@extends('emails.layout')

@section('header', 'Pago Verificado')

@section('content')
    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    <p>Te informo que tu pago para el turno del día <strong>{{ $fecha }}</strong> ha sido verificado con éxito.</p>
    
    <div style="background: #DCFCE7; padding: 15px; border: 2px solid #000; margin: 20px 0; text-align: center;">
        <p style="margin:0; font-weight: 900; font-size: 1.2rem; color: #166534;">¡TURNO CONFIRMADO!</p>
    </div>

    @if($link_reunion)
        <p>Como tu sesión es <strong>virtual</strong>, podés unirte a la reunión en el horario acordado usando este enlace:</p>
        <a href="{{ $link_reunion }}" class="btn" style="background: #D9F99D;">Unirse a Google Meet</a>
    @else
        <p>Como tu sesión es <strong>presencial</strong>, te espero en el consultorio en el horario acordado.</p>
    @endif

    <p>Ya podés ver los detalles actualizados en tu portal.</p>

    <a href="{{ url('/') }}" class="btn" style="background: #BAE6FD;">Ir a mi Portal</a>

    <p>Saludos,<br><strong>Lic. Nazarena De Luca</strong></p>
@endsection
