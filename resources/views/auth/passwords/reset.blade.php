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
                <input type="email" name="email" id="email" class="neobrutalist-input @error('email') border-red-500 @enderror" required placeholder="nombre@ejemplo.com" value="{{ $email ?? old('email') }}" readonly style="background-color: #e9ecef; cursor: not-allowed; opacity: 0.7;">
                @error('email')
                    <span style="color: red; font-size: 0.8rem; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" style="font-weight: 700;">Nueva Contraseña</label>
                <input type="password" name="password" id="password" class="neobrutalist-input @error('password') border-red-500 @enderror" required placeholder="Al menos 8 caracteres">
                
                <!-- Modern Visual Feedback Requirements -->
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; margin-bottom: 0.25rem;">
                    <div style="flex: 1; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                        <div id="password-strength-bar" style="height: 100%; width: 0%; background: #ccc; transition: all 0.3s;"></div>
                    </div>
                    <span id="password-strength-text" style="font-size: 0.75rem; font-weight: 700; color: #999; min-width: 110px;">Sin contraseña</span>
                </div>
                
                @error('password')
                    <span style="color: red; font-size: 0.8rem; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-8">
                <label for="password_confirmation" style="font-weight: 700;">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="neobrutalist-input" required placeholder="Repetí tu nueva contraseña">
            </div>

            <!-- reCAPTCHA v2 Integration -->
            <div class="mb-8" style="display: flex; justify-content: center; margin-bottom: 2rem;">
                <!-- Usamos la key proporcionada por el usuario o la del archivo env -->
                <div class="g-recaptcha" 
                     data-sitekey="{{ env('RECAPTCHA_SITE_KEY', '6LefJ28sAAAAAL7cwThQ90-gdUBE1XuEdVUgVSxe') }}"
                     data-callback="onCaptchaSuccess"
                     data-expired-callback="onCaptchaExpired"></div>
            </div>
            @error('g-recaptcha-response')
                <div class="mb-4 text-center">
                    <span style="color: red; font-size: 0.8rem; font-weight: 700;">Por favor, verificá que no sos un robot.</span>
                </div>
            @enderror

            <div style="display: flex; justify-content: center;">
                <button type="submit" id="submit-btn" class="neobrutalist-btn text-center bg-verde no-select" style="padding: 10px 40px; font-weight: 700; font-size: 0.85rem; opacity: 0.5; cursor: not-allowed;" disabled>
                    Restablecer Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    let isCaptchaVerified = false;

    function onCaptchaSuccess() {
        isCaptchaVerified = true;
        document.getElementById('password').dispatchEvent(new Event('input')); // Trigger check
    }

    function onCaptchaExpired() {
        isCaptchaVerified = false;
        document.getElementById('password').dispatchEvent(new Event('input')); // Trigger check
    }

    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submit-btn');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');

        function updateFeedback() {
            const pwd = passwordInput.value;
            const confirmPwd = confirmPasswordInput.value;
            
            let strength = 0;
            let label = 'Sin contraseña';
            let color = '#ccc';
            
            if (pwd.length === 0) {
                strength = 0;
                label = 'Sin contraseña';
                color = '#ccc';
            } else if (pwd.length < 8) {
                strength = 25;
                label = 'Muy débil';
                color = '#ff3b30'; // red
            } else {
                // Base strength for 8+ characters
                strength = 40;
                
                // Check for lowercase
                if (/[a-z]/.test(pwd)) strength += 15;
                
                // Check for uppercase
                if (/[A-Z]/.test(pwd)) strength += 15;
                
                // Check for numbers
                if (/[0-9]/.test(pwd)) strength += 15;
                
                // Check for symbols
                if (/[^a-zA-Z0-9]/.test(pwd)) strength += 15;
                
                // Determine label and color
                if (strength < 60) {
                    label = 'Débil';
                    color = '#ff9500'; // orange
                } else if (strength < 85) {
                    label = 'Buena';
                    color = '#ffcc00'; // yellow
                } else {
                    label = 'Segura';
                    color = '#34c759'; // green
                }
            }

            strengthBar.style.width = strength + '%';
            strengthBar.style.background = color;
            strengthText.innerText = label;
            strengthText.style.color = color;

            // Habilitar el botón si la contraseña tiene largo aceptable, coinciden Y reCAPTCHA está validado
            if (strength >= 40 && pwd === confirmPwd && pwd !== '' && isCaptchaVerified) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
        }

        passwordInput.addEventListener('input', updateFeedback);
        confirmPasswordInput.addEventListener('input', updateFeedback);
    });
</script>
@endsection
