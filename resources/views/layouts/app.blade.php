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
            <a href="{{ url('/') }}" class="logo-text">
                <span class="brand-title logo no-select">Lic. <span class="hide-on-mobile">Nazarena</span> De Luca</span>
            </a>
            <div class="nav-unificada-links">
                @auth
                    <!-- Notification Bell (Only on Dashboard) -->
                    @if(request()->routeIs('admin.dashboard') || request()->routeIs('patient.dashboard'))
                        <div class="notification-bell-container no-select" id="notif-bell" title="Notificaciones">
                            <i class="fa-solid fa-bell"></i>
                            <span class="notification-badge" id="notif-count">0</span>
                            
                            <div class="notification-dropdown" id="notif-dropdown">
                                <div class="notification-header" style="padding: 0.8rem 1rem; border-bottom: 2px solid #eee; background: white; border-radius: 12px 12px 0 0;">
                                    <span style="font-size: 0.85rem; font-weight: 800; color: #555;">Notificaciones</span>
                                    <button onclick="markAllRead()" style="background: none; border: none; font-size: 0.7rem; font-weight: 700; cursor: pointer; color: var(--color-dark); text-decoration: underline;">Limpiar</button>
                                </div>
                                <div id="notif-items" style="background: white;">
                                    <!-- Ajax content -->
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!request()->routeIs('login') && !request()->routeIs('register'))
                        @if(auth()->user()->rol === 'admin')
                            @if(request()->routeIs('admin.dashboard'))
                                <!-- Admin Quick Links -->
                                <div class="admin-nav-links" style="display: flex; gap: 0.5rem; margin-right: 1rem;">
                                    <a href="#agenda" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-lila);">Hoy</a>
                                    <a href="#pagos" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-amarillo);">Pagos</a>
                                    <a href="#turnos" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-verde);">Turnos</a>
                                    <a href="#pacientes" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: var(--color-celeste);">Pacientes</a>
                                </div>
                            @else
                                <a href="{{ route('admin.dashboard') }}" class="neobrutalist-btn bg-amarillo">Admin</a>
                            @endif
                        @elseif(!request()->routeIs('patient.dashboard'))
                            <a href="{{ route('patient.dashboard') }}" class="neobrutalist-btn bg-verde">Mi Portal</a>
                        @endif
                        <a href="{{ route('logout') }}" class="neobrutalist-btn bg-lila no-select">Salir</a>
                    @endif
                @else
                    @if(!request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="neobrutalist-btn no-select" style="background: white;">Ingresar</a>
                    @endif
                    
                    @if(!request()->routeIs('register'))
                        <a href="{{ route('register') }}" class="neobrutalist-btn no-select" style="background: white;">Registro</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>
    @endif

    <!-- Post-Login Session Prompt -->
    @auth
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
    @endauth

    <main class="container" style="min-height: 80vh; padding-top: 3rem; padding-bottom: 3rem;">
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
            

        </div>
    </footer>

    <!-- Custom Confirmation Modal -->
    <div id="confirm-modal-overlay" class="confirm-modal-overlay">
        <div class="confirm-modal">
            <div class="confirm-modal-title">¿Estás seguro?</div>
            <div id="confirm-modal-message" class="confirm-modal-message"></div>
            <div class="confirm-modal-buttons">
                <button id="confirm-cancel" class="neobrutalist-btn bg-rosa">Cancelar</button>
                <button id="confirm-ok" class="neobrutalist-btn bg-verde">Confirmar</button>
            </div>
        </div>
    </div>

    @auth
    <script>
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


    </script>
    @endauth
</body>
</html>
