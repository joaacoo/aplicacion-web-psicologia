@extends('layouts.app')

@section('title', 'Registro - Lic. Nazarena De Luca')

@section('content')
<div class="flex justify-center items-center" style="min-height: auto; margin-top: 0.5rem; margin-bottom: 2rem; padding: 1.5rem;">
    <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: white; border: 3px solid #000; box-shadow: 4px 4px 0px #000;">
        
        <h2 class="text-center" style="margin-bottom: 1.2rem; font-size: 2.2rem;">Crear Cuenta</h2>

        <form action="/register" method="POST" class="w-full">
            @csrf
            
            <div class="mb-4">
                <label for="name" style="font-weight: 700;">Nombre Completo</label>
                <input type="text" name="name" id="name" class="neobrutalist-input" required placeholder="Nazarena De Luca" value="{{ old('name') }}">
                @error('name')
                    <span style="color: #c00; font-size: 0.85rem; margin-top: 0.25rem; display: block; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefono" style="font-weight: 700;">Teléfono</label>
                <div style="display: flex; gap: 0.5rem; align-items: stretch;">
                    <!-- Country Flag Button -->
                    <button type="button" id="country-selector-btn" onclick="openCountryModal()" style="display: flex; align-items: center; justify-content: center; padding: 0.75rem; background: white; border: 3px solid #000; border-radius: 8px; cursor: pointer; min-width: 60px; box-shadow: 2px 2px 0px #000;">
                        <span id="selected-flag" style="font-size: 1.5rem; line-height: 1;">🇦🇷</span>
                    </button>
                    
                    <!-- Phone Input -->
                    <input type="text" name="telefono" id="telefono" class="neobrutalist-input" required placeholder="9 11 1234 5678" value="{{ old('telefono') }}" style="flex: 1; margin-bottom: 0;">
                </div>
                <small style="color: #666; font-size: 0.8rem; display: block; margin-top: 0.25rem;">El código de país se agregará automáticamente</small>
                @error('telefono')
                    <span style="color: #c00; font-size: 0.85rem; margin-top: 0.25rem; display: block; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Country Selection Modal -->
            <div id="country-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                <div style="background: white; border: 3px solid #000; border-radius: 16px; box-shadow: 8px 8px 0px #000; max-width: 400px; width: 90%; max-height: 80vh; overflow: hidden; display: flex; flex-direction: column;">
                    <!-- Modal Header -->
                    <div style="padding: 1.5rem; border-bottom: 2px solid #000; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800;">Seleccioná tu país</h3>
                        <button type="button" onclick="closeCountryModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">✕</button>
                    </div>
                    
                    <!-- Search -->
                    <div style="padding: 1rem; border-bottom: 1px solid #eee;">
                        <input type="text" id="country-search" placeholder="Buscar país..." style="width: 100%; padding: 0.75rem; border: 2px solid #eee; border-radius: 8px; font-size: 0.95rem;" onkeyup="filterCountries()">
                    </div>
                    
                    <!-- Country List -->
                    <div id="country-list" style="overflow-y: auto; flex: 1; padding: 0.5rem;">
                        <!-- Countries will be inserted here -->
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="email" style="font-weight: 700;">Email</label>
                <input type="email" name="email" id="email" class="neobrutalist-input" required placeholder="tu@email.com" value="{{ old('email') }}">
                @error('email')
                    <span style="color: #c00; font-size: 0.85rem; margin-top: 0.25rem; display: block; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" style="font-weight: 700;">Contraseña</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="neobrutalist-input" required placeholder="******" style="width: 100%; padding-right: 3rem;">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 1rem; top: 38%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; z-index: 10;">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div style="margin-top: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <div style="flex: 1; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
                            <div id="password-strength-bar" style="height: 100%; width: 0%; background: #ccc; transition: all 0.3s;"></div>
                        </div>
                        <span id="password-strength-text" style="font-size: 0.75rem; font-weight: 700; color: #999; min-width: 110px;">Sin contraseña</span>
                    </div>
                </div>
                
                @error('password')
                    <span style="color: #c00; font-size: 0.85rem; margin-top: 0.25rem; display: block; font-weight: 700;">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" style="font-weight: 700;">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="neobrutalist-input" required placeholder="******">
            </div>

            <!-- Official Google reCAPTCHA -->
            <div style="display: flex; justify-content: center; margin: 1.5rem 0;">
                <div class="g-recaptcha" 
                     data-sitekey="{{ config('services.recaptcha.site_key') }}" 
                     data-callback="onCaptchaSuccess" 
                     data-expired-callback="onCaptchaExpired"></div>
            </div>

            <button type="submit" id="submit-btn" class="neobrutalist-btn w-full text-center bg-celeste" disabled style="opacity: 0.5; cursor: not-allowed;">
                Registrarse
            </button>
        </form>

        <div class="mt-8 text-center" style="margin-top: 2rem;">
            <span style="font-size: 0.95rem;">¿Ya tenés cuenta?</span>
            <a href="{{ route('login') }}" style="color: #2D2D2D; font-weight: 700; text-decoration: none;">Ingresá acá</a>
        </div>
    </div>
</div>

<script>
// Country data (popular countries)
const countries = [
    { name: 'Argentina', code: '+54', flag: '🇦🇷', format: '9 11 1234 5678' },
    { name: 'Chile', code: '+56', flag: '🇨🇱', format: '9 1234 5678' },
    { name: 'Uruguay', code: '+598', flag: '🇺🇾', format: '91 234 567' },
    { name: 'Brasil', code: '+55', flag: '🇧🇷', format: '11 91234 5678' },
    { name: 'Paraguay', code: '+595', flag: '🇵🇾', format: '961 123456' },
    { name: 'Bolivia', code: '+591', flag: '🇧🇴', format: '71234567' },
    { name: 'Perú', code: '+51', flag: '🇵🇪', format: '912 345 678' },
    { name: 'Colombia', code: '+57', flag: '🇨🇴', format: '321 1234567' },
    { name: 'Venezuela', code: '+58', flag: '🇻🇪', format: '412 1234567' },
    { name: 'Ecuador', code: '+593', flag: '🇪🇨', format: '99 123 4567' },
    { name: 'México', code: '+52', flag: '🇲🇽', format: '55 1234 5678' },
    { name: 'España', code: '+34', flag: '🇪🇸', format: '612 34 56 78' },
    { name: 'Estados Unidos', code: '+1', flag: '🇺🇸', format: '(555) 123-4567' },
    { name: 'Reino Unido', code: '+44', flag: '🇬🇧', format: '7400 123456' },
    { name: 'Francia', code: '+33', flag: '🇫🇷', format: '6 12 34 56 78' },
    { name: 'Italia', code: '+39', flag: '🇮🇹', format: '312 345 6789' },
    { name: 'Alemania', code: '+49', flag: '🇩🇪', format: '151 23456789' },
];

let selectedCountry = countries[0]; // Argentina por defecto

// Populate country list on page load
document.addEventListener('DOMContentLoaded', function() {
    const countryList = document.getElementById('country-list');
    countries.forEach(country => {
        const item = document.createElement('div');
        item.className = 'country-item';
        item.style.cssText = 'padding: 1rem; cursor: pointer; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem; transition: background 0.2s;';
        item.innerHTML = `
            <span style="font-size: 1.5rem;">${country.flag}</span>
            <div style="flex: 1;">
                <div style="font-weight: 600; font-size: 0.95rem;">${country.name}</div>
                <div style="font-size: 0.8rem; color: #666;">${country.code}</div>
            </div>
        `;
        item.onmouseover = () => item.style.background = '#f5f5f5';
        item.onmouseout = () => item.style.background = 'transparent';
        item.onclick = () => selectCountry(country);
        countryList.appendChild(item);
    });
});

function openCountryModal() {
    document.getElementById('country-modal').style.display = 'flex';
    document.getElementById('country-search').value = '';
    document.getElementById('country-search').focus();
    // Lock body scroll
    document.body.style.overflow = 'hidden';
}

function closeCountryModal() {
    document.getElementById('country-modal').style.display = 'none';
    // Unlock body scroll
    document.body.style.overflow = '';
}

function selectCountry(country) {
    selectedCountry = country;
    document.getElementById('selected-flag').textContent = country.flag;
    
    // Update input with country code prefix
    const input = document.getElementById('telefono');
    const currentValue = input.value.replace(/^\+\d+\s*/, ''); // Remove old country code
    input.value = country.code + ' ' + currentValue.trim();
    input.placeholder = country.code + ' ' + country.format;
    
    closeCountryModal();
}

function filterCountries() {
    const search = document.getElementById('country-search').value.toLowerCase();
    const items = document.querySelectorAll('.country-item');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(search) ? 'flex' : 'none';
    });
}

// Close modal when clicking outside
document.getElementById('country-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCountryModal();
});

// Phone Number Auto-Formatting with country code
document.getElementById('telefono').addEventListener('input', function(e) {
    let value = e.target.value;
    const code = selectedCountry.code;
    
    // Ensure country code is always present
    if (!value.startsWith(code)) {
        value = code + ' ' + value.replace(/^\+\d+\s*/, '');
    }
    
    let number = value.substring(code.length).replace(/\D/g, ''); // Remove non-digits from number part
    let pattern = selectedCountry.format; // e.g. "9 11 1234 5678"
    let formatted = '';
    let numIdx = 0;
    
    for (let char of pattern) {
        if (numIdx >= number.length) break;
        if (char === ' ') {
            formatted += ' ';
        } else {
            formatted += number[numIdx];
            numIdx++;
        }
    }
    
    // If there are more digits than in the pattern, just append them
    if (numIdx < number.length) {
        formatted += number.substring(numIdx);
    }
    
    e.target.value = code + ' ' + formatted.trim();
});

// Initialize input with default country code
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('telefono');
    if (!input.value || input.value.trim() === '') {
        input.value = selectedCountry.code + ' ';
    }
    input.placeholder = selectedCountry.code + ' ' + selectedCountry.format;
});

// Password Strength Indicator
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const bar = document.getElementById('password-strength-bar');
    const text = document.getElementById('password-strength-text');
    
    let strength = 0;
    let label = 'Sin contraseña';
    let color = '#ccc';
    
    if (password.length === 0) {
        strength = 0;
        label = 'Sin contraseña';
        color = '#ccc';
    } else if (password.length < 8) {
        strength = 25;
        label = 'Muy débil';
        color = '#ff3b30';
    } else {
        // Base strength for 8+ characters
        strength = 40;
        
        // Check for lowercase
        if (/[a-z]/.test(password)) strength += 15;
        
        // Check for uppercase
        if (/[A-Z]/.test(password)) strength += 15;
        
        // Check for numbers
        if (/[0-9]/.test(password)) strength += 15;
        
        // Check for symbols
        if (/[^a-zA-Z0-9]/.test(password)) strength += 15;
        
        // Determine label and color
        if (strength < 60) {
            label = 'Débil';
            color = '#ff9500';
        } else if (strength < 85) {
            label = 'Aceptable';
            color = '#ffcc00';
        } else {
            label = 'Segura';
            color = '#34c759';
        }
    }
    
    bar.style.width = strength + '%';
    bar.style.background = color;
    text.textContent = label;
    text.style.color = color;
});

function togglePasswordVisibility(fieldId, button) {
    const input = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function onCaptchaSuccess() {
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = false;
    submitBtn.style.opacity = '1';
    submitBtn.style.cursor = 'pointer';
}

function onCaptchaExpired() {
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.5';
    submitBtn.style.cursor = 'not-allowed';
}
</script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
