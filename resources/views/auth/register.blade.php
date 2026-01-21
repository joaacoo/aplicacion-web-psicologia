@extends('layouts.app')

@section('title', 'Registro - Lic. Nazarena De Luca')

@section('content')
<div class="flex justify-center items-center" style="min-height: auto; margin-top: 0.5rem; margin-bottom: 2rem; padding: 0.5rem;">
    <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: white;">
        
        <h2 class="text-center" style="margin-bottom: 1.2rem; font-size: 2.2rem;">Crear Cuenta</h2>

        <form action="/register" method="POST" class="w-full">
            @csrf
            
            <div class="mb-4">
                <label for="name" style="font-weight: 700;">Nombre Completo</label>
                <input type="text" name="name" id="name" class="neobrutalist-input" required placeholder="Nazarena De Luca" value="{{ old('name') }}">
            </div>

            <div class="mb-4">
                <label for="telefono" style="font-weight: 700;">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="neobrutalist-input" required placeholder="Ej: +54 9 11 1234 5678" value="{{ old('telefono') }}">
            </div>

            <div class="mb-4">
                <label for="email" style="font-weight: 700;">Email</label>
                <input type="email" name="email" id="email" class="neobrutalist-input" required placeholder="tu@email.com">
            </div>

            <div class="mb-4">
                <label for="password" style="font-weight: 700;">Contraseña</label>
                <input type="password" name="password" id="password" class="neobrutalist-input" required placeholder="******">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" style="font-weight: 700;">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="neobrutalist-input" required placeholder="******">
            </div>

            <button type="submit" class="neobrutalist-btn w-full text-center bg-celeste">
                Registrarse
            </button>
        </form>

        <div class="mt-8 text-center" style="margin-top: 2rem;">
            <span style="font-size: 0.95rem;">¿Ya tenés cuenta?</span>
            <a href="{{ route('login') }}" style="color: #2D2D2D; font-weight: 700; text-decoration: none;">Ingresá acá</a>
        </div>
    </div>
</div>
@endsection
