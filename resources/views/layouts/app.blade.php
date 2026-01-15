<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lic. Nazarena De Luca')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
</head>
<body style="background-color: var(--color-celeste);"> <!-- Fondo celeste en body general como en Hero del reference para dar más vida -->

    @if(!request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*'))
    <nav class="navbar-unificada no-select">
        <div class="navbar-content">
            <div class="logo-text">
                <span class="brand-title logo no-select">Lic. <span class="hide-on-mobile">Nazarena</span> De Luca</span>
            </div>
            
            <div class="nav-unificada-links">
                <!-- Notification Bell (Always Visible) -->
                @auth
                    <div class="notification-bell-container no-select" id="notif-bell" title="Notificaciones" style="margin-right: 0.5rem;">
                        <i class="fa-solid fa-bell"></i>
                        <span class="notification-badge" id="notif-count" style="display: none;">0</span>
                        
                        <div class="notification-dropdown" id="notif-dropdown">
                            <div class="notification-header" style="padding: 0.8rem 1rem; border-bottom: 2px solid #eee; background: white; border-radius: 12px 12px 0 0;">
                                <span style="font-size: 0.85rem; font-weight: 800; color: #555;">Notificaciones</span>
                                <button onclick="markAllRead()" style="background: none; border: none; font-size: 0.7rem; font-weight: 700; cursor: pointer; color: var(--color-dark); text-decoration: underline;">Limpiar</button>
                            </div>
                            <div id="notif-items" style="background: white;"><div class="notification-empty" style="padding: 2rem; text-align: center; color: #999; font-size: 0.85rem;">No hay avisos nuevos.</div></div>
                        </div>
                    </div>
                @endauth

                @auth
                    <!-- Mobile Menu Trigger -->
                    <button class="neobrutalist-btn mobile-only-btn" onclick="toggleMobileMenu()" style="padding: 0.6rem 1rem; background: var(--color-lila); margin-left: 0.5rem; display: none;">
                        <i class="fa-solid fa-bars" style="font-size: 1.2rem;"></i>
                    </button>

                    <!-- Desktop Nav Links (Hidden on Mobile) -->
                    <div class="admin-nav-links desktop-menu">
                        @if(auth()->user()->rol != 'paciente')
                            <a href="#agenda" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-lila);">Hoy</a>
                            <a href="#pagos" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-amarillo);">Pagos</a>
                            <a href="#turnos" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-verde);">Turnos</a>
                            <a href="#pacientes" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-celeste);">Pacientes</a>
                        @endif
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="javascript:void(0)" class="neobrutalist-btn bg-lila no-select" style="margin-left: 0.5rem;" onclick="window.showConfirm('¿Estás segura de que querés cerrar sesión?', () => document.getElementById('logout-form').submit())">Salir</a>
                    </div>
                @endauth
            </div>
        </div>
        
        <!-- Mobile Dropdown Menu -->
        <div id="mobile-nav-dropdown" class="mobile-nav-dropdown" style="display: none;">
            @auth
                @if(auth()->user()->rol != 'paciente')
                    <a href="#agenda" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-day"></i> Agenda Hoy</a>
                    <a href="#pagos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-money-bill-wave"></i> Pagos</a>
                    <a href="#turnos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-alt"></i> Gestión Turnos</a>
                    <a href="#pacientes" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-users"></i> Pacientes</a>
                    <a href="#documentos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-file-alt"></i> Documentos</a>
                @else
                    <a href="#booking" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-plus"></i> Reservar Turno</a>
                    <a href="#mis-turnos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-check"></i> Mis Turnos</a>
                    <a href="#materiales" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-folder-open"></i> Mi Biblioteca</a>
                @endif
                <div style="border-top: 2px solid #eee; margin: 0.5rem 0;"></div>
                <a href="javascript:void(0)" class="mobile-nav-item logout" onclick="window.showConfirm('¿Estás segura de que querés cerrar sesión?', () => document.getElementById('logout-form').submit())"><i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión</a>
            @endauth
            </div>
        </div>
    </nav>
    @endif

    @yield('subnavigation')

    <!-- Post-Login Session Prompt (DISABLED) -->
    @auth
        {{--
        <script>
            window.addEventListener('load', () => {
                // Solo si el usuario no tiene la cookie de "recordar" activa (Laravel setea esto si se logueó con el checkbox)
                // Pero como lo quitamos del login, chequeamos si ya mostramos el prompt esta sesión
                // Check if we just registered (session flag) or if it's a normal login
                const showPromptFlag = {{ session('show_session_prompt') ? 'true' : 'false' }};
                
                if (showPromptFlag || !localStorage.getItem('session_prompt_shown')) {
                    setTimeout(() => {
                        window.showConfirm('¿Querés que recordemos tu sesión para no tener que ingresar los datos la próxima vez?', function() {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("auth.remember") }}';
                            
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            
                            form.appendChild(csrf);
                            document.body.appendChild(form);
                            form.submit();
                        });
                        localStorage.setItem('session_prompt_shown', 'true');
                    }, 2000);
                }
            });
        </script>
        --}}
    @endauth

    <main class="{{ request()->routeIs('admin.dashboard') ? '' : 'container' }}" style="min-height: 80vh; padding-top: 3rem; padding-bottom: 3rem; {{ request()->routeIs('admin.dashboard') ? 'width: 100%; max-width: none; padding-left: 1rem; padding-right: 1rem;' : '' }}">
        @if(!request()->routeIs('login') && !request()->routeIs('register'))
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container text-center">
            <h2 style="color: white; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">Lic. Nazarena De Luca</h2>
            <p style="font-family: 'Manrope', sans-serif; opacity: 0.8; margin-bottom: 1.5rem;">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
            

            @auth
                {{-- 
                @if(auth()->user()->rol == 'paciente')
                    <div style="margin-top: 1rem;">
                        <button type="button" 
                                onclick="openDeleteModal()" 
                                style="background: none; border: none; color: rgba(255,255,255,0.5); text-decoration: none; cursor: pointer; font-size: 0.75rem; font-family: 'Manrope', sans-serif; transition: color 0.3s;" 
                                onmouseover="this.style.color='rgba(255,255,255,0.8)'" 
                                onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                            Quiero darme de baja del sistema
                        </button>
                    </div>
                @endif
                --}}
            @endauth
        </div>
    </footer>

    <!-- Custom Confirmation Modal -->
    <div id="confirm-modal-overlay" class="confirm-modal-overlay" style="display: none;">
        <div class="confirm-modal">
            <div class="confirm-modal-title">¿Estás seguro?</div>
            <div id="confirm-modal-message" class="confirm-modal-message"></div>
            <div class="confirm-modal-buttons">
                <button id="confirm-cancel" class="neobrutalist-btn bg-rosa">Cancelar</button>
                <button id="confirm-ok" class="neobrutalist-btn bg-verde">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal (DISABLED) -->
    {{-- 
    <div id="delete-account-modal" class="confirm-modal-overlay">
        <div class="confirm-modal" style="border-color: #ff4d4d;">
            <div class="confirm-modal-title" style="color: #d00;">Eliminar Cuenta</div>
            <div class="confirm-modal-message">
                <p style="margin-bottom: 1rem;">¿Estás segura de que querés darte de baja?</p>
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.5rem;">Esta acción es <strong>irreversible</strong>. Se eliminarán todos tus turnos, historial y documentos.</p>
                
                <form action="{{ route('patient.account.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div style="margin-bottom: 1.5rem; text-align: left;">
                        <label style="font-size: 0.8rem; font-weight: 700; display: block; margin-bottom: 0.5rem;">Ingresá tu contraseña para confirmar:</label>
                        <input type="password" name="password" class="neobrutalist-input" required placeholder="Tu contraseña actual" style="border-color: #d00;">
                        @error('password')
                            <span style="color: red; font-size: 0.8rem; font-weight: 700;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="confirm-modal-buttons">
                        <button type="button" class="neobrutalist-btn" style="background: white;" onclick="closeDeleteModal()">Cancelar</button>
                        <button type="submit" class="neobrutalist-btn" style="background: #ff4d4d; color: white; border-color: #aa0000;">Confirmar Baja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    --}}

    @auth
    <script>
        // Delete Account Logic (DISABLED)
        function openDeleteModal() {
            // document.getElementById('delete-account-modal').style.display = 'flex';
        }

        function closeDeleteModal() {
            // document.getElementById('delete-account-modal').style.display = 'none';
        }

        // Custom Confirmation Logic
        let _pendingConfirmAction = null;
        const confirmOverlay = document.getElementById('confirm-modal-overlay');
        const confirmMessage = document.getElementById('confirm-modal-message');
        const confirmOk = document.getElementById('confirm-ok');
        const confirmCancel = document.getElementById('confirm-cancel');
 
        // Scroll Up to Show Navbar Logic
        let lastScrollY = window.scrollY;
        const navbar = document.querySelector('.navbar-unificada');

        window.addEventListener('scroll', () => {
            if (window.scrollY > lastScrollY && window.scrollY > 100) {
                // Scrolling down
                navbar.classList.add('nav-hidden');
            } else {
                // Scrolling up
                navbar.classList.remove('nav-hidden');
            }
            lastScrollY = window.scrollY;
        });

        window.showConfirm = function(message, callback) {
            confirmMessage.innerText = message;
            _pendingConfirmAction = callback;
            confirmOverlay.style.display = 'flex';
        };

        confirmOk.addEventListener('click', () => {
            if (_pendingConfirmAction) _pendingConfirmAction();
            confirmOverlay.style.display = 'none';
        });

        confirmCancel.addEventListener('click', () => {
            confirmOverlay.style.display = 'none';
        });

        const bell = document.getElementById('notif-bell');
        const dropdown = document.getElementById('notif-dropdown');
        const items = document.getElementById('notif-items');
        const count = document.getElementById('notif-count');

        if (bell) {
            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
                fetchNotifications();
            });

            // Auto fetch initial count
            setInterval(fetchNotifications, 30000);
            fetchNotifications();
        }

        document.addEventListener('click', () => {
            if (dropdown) dropdown.classList.remove('active');
        });

        async function fetchNotifications() {
            const res = await fetch('{{ route("notifications.latest") }}');
            const data = await res.json();
            
            if (data.length > 0) {
                count.innerText = data.length;
                count.style.display = 'flex';
                items.innerHTML = data.map(n => `
                    <a href="${n.link || '#'}" class="notification-item" onclick="markAsRead(${n.id})" style="border-bottom: 1px solid #f0f0f0; padding: 12px 15px;">
                        <p style="margin:0; font-size: 0.85rem; font-weight: 500; color: #333;">${n.mensaje}</p>
                        <small style="color: #bbb; font-size: 0.7rem; font-weight: 400;">${new Date(n.created_at).toLocaleDateString()} ${new Date(n.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                    </a>
                `).join('');
            } else {
                count.style.display = 'none';
                items.innerHTML = '<div class="notification-empty" style="padding: 2rem; text-align: center; color: #999; font-size: 0.85rem;">No hay avisos nuevos.</div>';
            }
        }

        async function markAsRead(id) {
            await fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        }

        async function markAllRead() {
            await fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            fetchNotifications();
        }
        
        // Mobile Menu Logic
        window.toggleMobileMenu = function() {
            const drop = document.getElementById('mobile-nav-dropdown');
            if (drop.style.display === 'none' || drop.style.display === '') {
                drop.style.display = 'block';
            } else {
                drop.style.display = 'none';
            }
        };

        // Show modal if there are password errors (validation redirect)
        {{--
        @if($errors->has('password'))
            window.addEventListener('DOMContentLoaded', () => {
                openDeleteModal();
            });
        @endif
        --}}
    </script>
    @endauth

    <!-- Public Access for Guest (Floating Widget also for guests?) -->
    <!-- We will show it for everyone including guests, BUT NOT FOR ADMINS AND NOT ON LOGIN -->
    <!-- Public Access for Guest (Floating Widget also for guests?) -->
    @php
        $user = auth()->user();
        $isPatient = $user && $user->rol == 'paciente';
        $isGuest = !auth()->check();
        $isLoginOrRegister = request()->routeIs('login') || request()->routeIs('register');
        
        $showWidget = ($isGuest || $isPatient) && !$isLoginOrRegister;
    @endphp

    @if($showWidget)
    <div id="whatsapp-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; font-family: 'Outfit', sans-serif;">
        <!-- Chat Box -->
        <div id="whatsapp-chat-box" style="display: none; position: absolute; bottom: 80px; right: 0; flex-direction: column; width: 280px; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px #000; border-radius: 15px; overflow: hidden; opacity: 0; transform: translateY(10px); transition: opacity 0.3s ease, transform 0.3s ease;">
            <!-- Header -->
            <div style="background: #25D366; padding: 0.8rem; display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 40px; height: 40px; background: white; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <img src="{{ asset('img/profile-chat.png') }}" alt="Nazarena" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="margin: 0; font-weight: 800; font-size: 0.75rem; color: #000; line-height: 1; text-transform: uppercase; font-family: 'Manrope', sans-serif; letter-spacing: 1px; opacity: 0.8;">Lic.</p>
                    <h5 style="margin: 0; font-weight: 800; font-size: 0.85rem; color: #000; line-height: 1.1; font-family: 'Manrope', sans-serif; letter-spacing: -0.2px;">Nazarena De Luca</h5>
                </div>
            </div>
            <!-- Body -->
            <div style="padding: 1rem; background: #fff; flex: 1; display: flex; flex-direction: column; gap: 0.8rem;">
                <div style="background: #f0fdf4; border: 2px solid #25D366; padding: 10px; border-radius: 10px 10px 10px 0; max-width: 95%; box-shadow: 2px 2px 0px #25D366;">
                    <p style="margin: 0; font-size: 0.85rem; color: #333; font-weight: 600;">¡Hola! 👋 Soy la Lic. Nazarena De Luca. <br>¿Tenés alguna consulta o necesitás ayuda con los turnos?</p>
                </div>

                <!-- Quick Actions -->
                <div style="display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.5rem;">
                    <a href="https://wa.me/5491139560673?text={{ urlencode('Hola Nazarena, ¿cómo puedo reservar un turno?') }}" target="_blank" class="neobrutalist-btn" style="padding: 0.4rem; font-size: 0.7rem; text-align: left; background: #fff; text-transform: none; border-width: 2px; box-shadow: 2px 2px 0px #000;">
                        <i class="fa-solid fa-calendar-day" style="color: #25D366;"></i> ¿Cómo reservo un turno?
                    </a>
                    <a href="https://wa.me/5491139560673?text={{ urlencode('Hola Nazarena, tengo una consulta sobre los pagos de las sesiones.') }}" target="_blank" class="neobrutalist-btn" style="padding: 0.4rem; font-size: 0.7rem; text-align: left; background: #fff; text-transform: none; border-width: 2px; box-shadow: 2px 2px 0px #000;">
                        <i class="fa-solid fa-money-bill-wave" style="color: #25D366;"></i> Consulta sobre pagos
                    </a>
                    <a href="https://wa.me/5491139560673?text={{ urlencode('Hola Nazarena, te quería consultar sobre la modalidad de las sesiones.') }}" target="_blank" class="neobrutalist-btn" style="padding: 0.4rem; font-size: 0.7rem; text-align: left; background: #fff; text-transform: none; border-width: 2px; box-shadow: 2px 2px 0px #000;">
                        <i class="fa-solid fa-video" style="color: #25D366;"></i> Modalidad de las sesiones
                    </a>
                </div>
            </div>
            <!-- Footer -->
            <div style="padding: 0.7rem; background: white; border-top: 2px solid #eee; text-align: center;">
                <a href="https://wa.me/5491139560673" target="_blank" class="neobrutalist-btn w-full" style="display: block; text-decoration: none; padding: 0.5rem; background-color: #25D366; color: white; border-color: #000; font-size: 0.8rem; box-shadow: 3px 3px 0px #000; font-family: 'Manrope', sans-serif; font-weight: 800;">
                    <i class="fa-brands fa-whatsapp"></i> Chatear
                </a>
            </div>
        </div>

        <!-- Trigger Button -->
        <button onclick="toggleWhatsApp()" class="whatsapp-btn">
            <i class="fa-brands fa-whatsapp"></i>
        </button>
    </div>

    <script>
        function toggleWhatsApp() {
            const box = document.getElementById('whatsapp-chat-box');
            if (!box) return;
            
            if (box.style.display === 'flex') {
                box.style.opacity = '0';
                box.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    box.style.display = 'none';
                }, 300);
            } else {
                box.style.display = 'flex';
                // Trigger reflow
                void box.offsetWidth;
                box.style.opacity = '1';
                box.style.transform = 'translateY(0)';
            }
        }
    </script>
    @endif
</body>
</html>
