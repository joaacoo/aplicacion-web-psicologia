@extends('layouts.app')

@section('content')
<div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(168, 226, 250, 0.3); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); padding: 1.5rem; z-index: 99999;">
    <div class="neobrutalist-card" style="max-width: 500px; width: 100%; text-align: center; padding: 3rem 2rem; background: white; box-shadow: 20px 20px 0px rgba(0,0,0,0.1);">
        <!-- Success Icon -->
        <div style="width: 100px; height: 100px; background: #34c759; border: 4px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: 6px 6px 0px #000;">
            <i class="fa-solid fa-check" style="color: white; font-size: 3.5rem;"></i>
        </div>

        <h1 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 2.2rem; margin-bottom: 1rem; letter-spacing: -1px;">¡Registro Confirmado!</h1>
        
        <p style="font-size: 1.1rem; color: #444; margin-bottom: 2.5rem; line-height: 1.5; font-weight: 600;">
            Tu cuenta ha sido creada exitosamente. Ya podés ingresar al sistema para gestionar tus turnos.
        </p>

        <a href="{{ route('login') }}" class="neobrutalist-btn bg-amarillo w-full" style="display: block; font-size: 1.1rem; padding: 1rem;">
            IR AL INICIO DE SESIÓN
        </a>
    </div>
</div>

<style>
    /* Ensure page content area is clean but keep header/footer if they exist */
    .app-main-wrapper { padding-top: 0 !important; margin-left: 0 !important; }
    main { padding: 0 !important; margin: 0 !important; }
</style>
@endsection
