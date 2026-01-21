@extends('layouts.app')

@section('title', 'Restablecer Contraseña - Tu Espacio Seguro')

@section('content')
<div class="flex justify-center items-center" style="min-height: 70vh;">
    <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: white; margin-bottom: 4rem;">
        
        <h2 class="text-center" style="margin-bottom: 0.5rem; font-size: 2rem;">¿Olvidaste tu contraseña?</h2>
        <p class="text-center" style="margin-bottom: 2rem; color: #555;">Ingresá tu email y te enviaremos un link para crear una nueva.</p>

        @if (session('status'))
            <div class="alert alert-success" style="margin-bottom: 2rem;">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="w-full">
            @csrf
            
            <div class="mb-8">
                <label for="email" style="font-weight: 700;">Email</label>
                <input type="email" name="email" id="email" class="neobrutalist-input @error('email') border-red-500 @enderror" required placeholder="nombre@ejemplo.com" value="{{ old('email') }}">
                @error('email')
                    <span style="color: red; font-size: 0.8rem; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 2rem; display: flex; justify-content: center;">
                <button type="submit" class="neobrutalist-btn text-center bg-amarillo no-select" style="padding: 10px 30px; font-weight: 700; font-size: 0.85rem;">
                    Enviar Link de Restablecimiento
                </button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" style="color: #2D2D2D; font-weight: 700; text-decoration: none;">Volver al ingreso</a>
        </div>
    </div>
</div>
@endsection
