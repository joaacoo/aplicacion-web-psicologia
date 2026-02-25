@extends('errors.layout')

@section('title', 'Sesión Expirada')

@section('code', '419')

@section('message')
<div style="font-family: 'Syne', sans-serif; text-align: center; padding: 2rem;">
    <div style="font-size: 1.5rem; font-weight: 800; color: #000; margin-bottom: 1rem; line-height: 1.2;">
        ¡UPS! TU SESIÓN HA EXPIRADO
    </div>
    
    <div style="background: #fffbeb; border: 3px solid #92400e; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 6px 6px 0px #92400e; text-align: left;">
        <p style="font-weight: 700; color: #92400e; margin-bottom: 0.5rem;">
            <i class="fa-solid fa-circle-info"></i> ¿Por qué pasó esto?
        </p>
        <p style="font-size: 0.95rem; color: #78350f; margin: 0; line-height: 1.4;">
            Es probable que hayas dejado la página abierta por mucho tiempo sin actividad. Por seguridad y para proteger tus datos, el sistema cierra la sesión automáticamente.
        </p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1rem;">
        <p style="font-weight: 800; color: #000; margin: 0;">¿Cómo lo soluciono?</p>
        
        <a href="javascript:location.reload()" 
           style="display: block; padding: 1rem; background: #34c759; border: 3px solid #000; border-radius: 12px; color: #000; font-weight: 900; text-decoration: none; box-shadow: 4px 4px 0px #000; transition: transform 0.2s;"
           onmouseover="this.style.transform='translate(-2px, -2px)'"
           onmouseout="this.style.transform='none'">
            <i class="fa-solid fa-rotate"></i> REFRESCAR LA PÁGINA
        </a>

        <a href="{{ route('dashboard') }}" 
           style="display: block; padding: 1rem; background: #fff; border: 3px solid #000; border-radius: 12px; color: #000; font-weight: 900; text-decoration: none; box-shadow: 4px 4px 0px #000; transition: transform 0.2s;"
           onmouseover="this.style.transform='translate(-2px, -2px)'"
           onmouseout="this.style.transform='none'">
            <i class="fa-solid fa-house"></i> IR AL DASHBOARD
        </a>
    </div>

    <p style="margin-top: 2rem; font-size: 0.85rem; color: #666; font-weight: 600;">
        Si el problema persiste, intentá cerrar sesión y volver a ingresar.
    </p>
</div>
@endsection
