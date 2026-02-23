@extends('layouts.app')

@section('title', 'Contraseña Restablecida - Tu Espacio Seguro')

@section('content')
<div class="flex justify-center items-center" style="min-height: auto; margin-top: 3rem; margin-bottom: 3rem; padding: 1.5rem;">
    <div class="neobrutalist-card text-center" style="width: 100%; max-width: 500px; background: white;">
        
        <div style="font-size: 3rem; color: #4ade80; margin-bottom: 1rem;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        
        <h2 style="margin-bottom: 0.5rem; font-size: 2.2rem;">¡Contraseña Actualizada!</h2>
        <p style="margin-bottom: 2rem; color: #555;">Tu clave de acceso ha sido restablecida correctamente. Ya podés volver a ingresar a tu cuenta con tu nueva contraseña.</p>

        <div style="display: flex; justify-content: center;">
            <a href="{{ route('login') }}" class="neobrutalist-btn bg-amarillo text-center no-select" style="padding: 12px 40px; font-weight: 700; font-size: 1rem; text-decoration: none; color: black; display: inline-block;">
                Iniciar Sesión
            </a>
        </div>
        
    </div>
</div>
@endsection
