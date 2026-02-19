@extends('layouts.app')

@section('content')
<div class="auth-wrapper" style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: var(--color-celeste); padding: 20px;">
    
    <div class="neobrutalist-card animate-on-load" style="background: white; border: 3px solid #000; padding: 2.5rem; border-radius: 20px; box-shadow: 8px 8px 0px #000; width: 100%; max-width: 450px; text-align: center;">
        
        <!-- Success Icon -->
        <div style="background: #a3e635; width: 80px; height: 80px; border-radius: 50%; border: 3px solid #000; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; box-shadow: 4px 4px 0px #000;">
            <i class="fa-solid fa-envelope-open-text" style="font-size: 2rem; color: #000;"></i>
        </div>

        <h2 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.8rem; margin-bottom: 1rem; color: #000;">
            ¡Mail Enviado!
        </h2>

        <p style="font-size: 1.1rem; color: #444; margin-bottom: 2rem; line-height: 1.6;">
            Te enviamos un enlace para restablecer tu contraseña a tu correo electrónico. Revisá tu bandeja de entrada (y la carpeta de Spam por las dudas).
        </p>

        <a href="{{ route('login') }}" class="neobrutalist-btn" style="display: inline-block; text-decoration: none; width: 100%; padding: 12px 0; font-weight: 800; font-size: 1.1rem; background: #000; color: white; border: 3px solid #000; box-shadow: 4px 4px 0px #888;">
            Volver al Inicio
        </a>

    </div>
</div>

<style>
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-on-load {
        animation: slideUp 0.6s ease-out forwards;
    }
    .neobrutalist-btn:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0px #888 !important;
    }
    .neobrutalist-btn:active {
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px #888 !important;
    }
</style>
@endsection
