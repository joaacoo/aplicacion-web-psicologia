@extends('layouts.app')

@section('title', 'Restablecer Contraseña - Tu Espacio Seguro')

@section('content')
<div style="flex: 1; display: flex; flex-direction: column; justify-content: center; background-color: var(--color-celeste);">
    <div class="flex justify-center items-center" style="min-height: auto; margin-top: 3rem; margin-bottom: 3rem; padding: 1.5rem;">
        <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: white; margin-bottom: 4rem; border: 3px solid #000; box-shadow: 4px 4px 0px #000;">
            
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
                    <button type="submit" class="neobrutalist-btn text-center bg-amarillo no-select reset-btn-mobile" style="padding: 10px 30px; font-weight: 700; font-size: 0.85rem;">
                        Enviar Link de Restablecimiento
                    </button>
                </div>
                <style>
                    @media (max-width: 600px) {
                        .reset-btn-mobile {
                            padding: 8px 15px !important;
                            font-size: 0.75rem !important;
                        }
                    }
                </style>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" style="color: #2D2D2D; font-weight: 700; text-decoration: none;">Volver al ingreso</a>
            </div>
        </div>
    </div>
</div>
@endsection
