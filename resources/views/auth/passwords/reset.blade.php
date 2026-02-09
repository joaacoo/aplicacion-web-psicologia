@extends('layouts.app')

@section('title', 'Nueva Contraseña - Tu Espacio Seguro')

@section('content')
<div class="flex justify-center items-center" style="min-height: auto; margin-top: 3rem; margin-bottom: 3rem; padding: 1.5rem;">
    <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: white;">
        
        <h2 class="text-center" style="margin-bottom: 0.5rem; font-size: 2.2rem;">Nueva Contraseña</h2>
        <p class="text-center" style="margin-bottom: 2rem; color: #555;">Establecé tu nueva clave de acceso.</p>

        <form action="{{ route('password.update') }}" method="POST" class="w-full">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label for="email" style="font-weight: 700;">Email</label>
                <input type="email" name="email" id="email" class="neobrutalist-input @error('email') border-red-500 @enderror" required placeholder="nombre@ejemplo.com" value="{{ $email ?? old('email') }}">
                @error('email')
                    <span style="color: red; font-size: 0.8rem; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" style="font-weight: 700;">Nueva Contraseña</label>
                <input type="password" name="password" id="password" class="neobrutalist-input @error('password') border-red-500 @enderror" required placeholder="Al menos 8 caracteres">
                @error('password')
                    <span style="color: red; font-size: 0.8rem; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-8">
                <label for="password_confirmation" style="font-weight: 700;">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="neobrutalist-input" required placeholder="Repetí tu nueva contraseña">
            </div>

            <div style="display: flex; justify-content: center;">
                <button type="submit" class="neobrutalist-btn text-center bg-verde no-select" style="padding: 10px 40px; font-weight: 700; font-size: 0.85rem;">
                    Restablecer Contraseña
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
