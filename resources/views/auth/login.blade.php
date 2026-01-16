@extends('layouts.app')

@section('title', 'Ingreso - Tu Espacio Seguro')

@section('content')
<div class="flex justify-center items-center" style="min-height: auto; margin-top: 0.5rem; margin-bottom: 2rem; padding: 0.5rem;">
    <!-- Card matching Register: White background, same width constraints -->
    <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: white;">
        
        <!-- Removed Icon entirely as requested ("son pacientes") -->

        <!-- Title matching Register style: mb-4, text-center -->
        <h2 class="text-center" style="margin-bottom: 0.2rem; font-size: 2.2rem;">Iniciar Sesión</h2>
        <p class="text-center" style="margin-bottom: 1.2rem; color: #555; font-size: 0.95rem;">Bienvenida/o a tu espacio seguro</p>

        <form action="/login" method="POST" class="w-full">
            @csrf

            <div class="text-center mb-6">
                <img src="{{ asset('img/logo-nuevo.png') }}" alt="Logo Lic. Nazarena De Luca" style="width: 100%; max-width: 200px; height: auto; margin: 0 auto;">
            </div>
            
            <div class="mb-4">
                <label for="email" style="font-weight: 700;">Email</label>
                <input type="email" name="email" id="email" class="neobrutalist-input" required placeholder="nombre@ejemplo.com" value="{{ old('email') }}">
                @error('email')
                    <span style="color: red; font-size: 0.8rem; font-weight: bold; display: block; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" style="font-weight: 700;">Contraseña</label>
                <input type="password" name="password" id="password" class="neobrutalist-input" required placeholder="Tu contraseña">
                @error('password')
                    <span style="color: red; font-size: 1rem; font-weight: bold; display: block; margin-top: 0.5rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="px-6 flex items-center" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                <input type="checkbox" name="remember" id="remember" style="width: 20px; height: 20px; cursor: pointer; border: 2px solid #000;">
                <label for="remember" class="no-select" style="margin-left: 10px; font-weight: 700; cursor: pointer; font-size: 1rem;">Recordar mi sesión</label>
            </div>

            <div class="mt-4">
                <button type="submit" class="neobrutalist-btn w-full text-center bg-celeste no-select">
                    Ingresar
                </button>
            </div>
        </form>

        <div class="mt-8 text-center flex flex-col gap-4">
            <a href="{{ route('password.request') }}" style="font-size: 0.95rem; font-weight: 700; color: #2D2D2D; text-decoration: underline;">Olvidé mi contraseña</a>
            <div>
                <span style="font-size: 0.95rem;">¿No tenés cuenta?</span>
                <a href="{{ route('register') }}" style="color: #2D2D2D; font-weight: 700; text-decoration: none;">Crear una cuenta</a>
            </div>
        </div>
    </div>
</div>
@endsection
