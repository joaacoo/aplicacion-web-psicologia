<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - Lic. Nazarena</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
</head>
<body style="background-color: var(--color-celeste);"> <!-- Fondo celeste en body general como en Hero del reference para dar más vida -->
    <script>
        // Critical System Script - Must load early
        window.toggleAdminSidebar = function() {
            try {
                const sidebar = document.getElementById('admin-sidebar');
                const toggleBtn = document.getElementById('admin-sidebar-toggle');
                
                if (!sidebar) {
                    console.error('Error: Sidebar no encontrada');
                    return;
                }
                
                const isMobile = window.innerWidth <= 1024;
                
                if (isMobile) {
                    sidebar.classList.toggle('active');
                    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
                    
                    // Close button logic
                    const closeBtn = document.querySelector('.sidebar-close-btn');
                    if(closeBtn) closeBtn.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
                } else {
                    sidebar.classList.toggle('collapsed');
                    
                    // Force style update to guarantee visibility
                    if (sidebar.classList.contains('collapsed')) {
                        sidebar.style.transform = 'translateX(-100%)';
                    } else {
                        sidebar.style.transform = 'translateX(0)';
                    }

                    document.cookie = "sidebar_collapsed=" + sidebar.classList.contains('collapsed') + "; path=/; max-age=" + (60 * 60 * 24 * 30);
                    
                    // Layout push
                    const layout = document.getElementById('admin-layout-container');
                    if(layout) layout.classList.toggle('sidebar-open', !sidebar.classList.contains('collapsed'));
                }

                // Icon Sync
                if (toggleBtn) {
                     const i = toggleBtn.querySelector('i');
                     if(i) {
                         const isOpen = (isMobile && sidebar.classList.contains('active')) || (!isMobile && !sidebar.classList.contains('collapsed'));
                         i.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
                     }
                }
            } catch(e) {
                console.error('Error crítico sidebar:', e);
            }
        };
    </script>

    @auth
        @php
            $isAdmin = auth()->user()->rol == 'admin';
            $isPatient = auth()->user()->rol == 'paciente';
        @endphp
        {{-- Logout form removed (using footer one) --}}
    @endauth

    @php
        $isAuthPage = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.*');
    @endphp

    @if($isAuthPage)
        {{-- Auth pages: Minimal, no nav, no extra padding --}}
        @yield('content')
    @else
        <!-- STRUCTURE: Unified Application Wrapper -->
        <div class="app-wrapper">
            
            <!-- [SIDEBAR SECTION] Only for Admin/Psychologist -->
            @if(isset($isAdmin) && $isAdmin)
                <aside class="admin-sidebar @if(isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] == 'true') collapsed @endif" id="admin-sidebar" style="z-index: 6001; display: flex; flex-direction: column; height: 100vh; position: sticky; top: 0; left: 0; flex-shrink: 0;">
                    <!-- Close button for mobile -->
                    <button onclick="window.toggleAdminSidebar()" class="sidebar-close-btn" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; z-index: 10; display: none; color: #000; padding: 0.5rem;">
                        <i class="fa-solid fa-times"></i>
                    </button>
                    
                    @if(auth()->user()->email === 'joacooodelucaaa16@gmail.com')
                        <div class="sidebar-logo" style="padding: 1.5rem 0.5rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; width: 100%; height: var(--header-height); flex-shrink: 0;">
                             <span class="sidebar-text sidebar-text-small" style="font-size: 0.95rem !important; font-weight: normal; letter-spacing: -0.2px; white-space: nowrap; flex-shrink: 0; color: white; display: flex; align-items: center; text-align: center; height: 100%;">Panel de Desarrollador</span>
                        </div>
                    @else
                            <div class="sidebar-logo" style="padding: 1.5rem 0.5rem; margin-bottom: 2rem; border-bottom: 3px solid #000; display: flex; align-items: center; justify-content: center; gap: 0.6rem; overflow: hidden; width: 100%; height: var(--header-height); flex-shrink: 0;">
                            <i class="fa-solid fa-brain" style="color: #000; font-size: 1.3rem; flex-shrink: 0; display: flex; align-items: center; height: 100%;"></i>
                            <span class="sidebar-text sidebar-text-small" style="font-size: 0.95rem !important; font-weight: normal; letter-spacing: -0.2px; white-space: nowrap; flex-shrink: 0; color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; text-align: center; height: 100%;">Espacio Terapéutico</span>
                        </div>
                    @endif
                    
                    <nav class="sidebar-nav" style="flex: 1; min-height: 0; padding: 15px; padding-bottom: 2rem; overflow-y: auto; overflow-x: visible; display: flex; flex-direction: column; gap: 12px; overscroll-behavior: contain;">
                        <!-- Sidebar Links (Same as before) -->
                        @if(auth()->user()->email !== 'joacooodelucaaa16@gmail.com')
                            <a href="{{ route('admin.home') }}" class="sidebar-link bg-lila">
                                <i class="fa-solid fa-house"></i>
                                <span class="sidebar-text" style="font-weight: 900;">Inicio</span>
                            </a>
                            <a href="{{ route('admin.agenda') }}" class="sidebar-link bg-celeste">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span class="sidebar-text">Agenda Hoy</span>
                            </a>
                            <a href="{{ route('admin.finanzas') }}" class="sidebar-link bg-verde">
                                <i class="fa-solid fa-chart-line"></i>
                                <span class="sidebar-text">Finanzas</span>
                            </a>
                            <a href="{{ route('admin.pacientes') }}" class="sidebar-link bg-amarillo">
                                <i class="fa-solid fa-users"></i>
                                <span class="sidebar-text">Pacientes</span>
                            </a>
                            <a href="{{ route('admin.pagos') }}" class="sidebar-link bg-verde">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                <span class="sidebar-text">Pagos</span>
                            </a>
                            <a href="{{ route('admin.turnos') }}" class="sidebar-link bg-rosa">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span class="sidebar-text">Gestión Turnos</span>
                            </a>
                            <a href="{{ route('admin.documentos') }}" class="sidebar-link bg-lila">
                                <i class="fa-solid fa-folder-open"></i>
                                <span class="sidebar-text">Biblioteca</span>
                            </a>
                            <a href="{{ route('admin.waitlist') }}" class="sidebar-link bg-celeste">
                                <i class="fa-solid fa-clock"></i>
                                <span class="sidebar-text">Lista Espera</span>
                            </a>
                            <a href="{{ route('admin.configuracion') }}" class="sidebar-link bg-amarillo">
                                <i class="fa-solid fa-gear"></i>
                                <span class="sidebar-text">Disponibilidad</span>
                            </a>
                            <a href="{{ route('admin.historial') }}" class="sidebar-link bg-verde">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span class="sidebar-text">Historial</span>
                            </a>
                        @endif
                        @if(auth()->user()->email === 'joacooodelucaaa16@gmail.com')
                            <a href="{{ route('admin.developer') }}" class="sidebar-link" style="background: #1f2937; color: white;">
                                <i class="fa-solid fa-terminal"></i>
                                <span class="sidebar-text">Panel Dev</span>
                            </a>
                        @endif
                    </nav>

                     <div class="sidebar-footer" style="padding: 1.5rem 0.5rem; border-top: 3px solid #000; display: flex; align-items: center; justify-content: center; gap: 0.5rem; overflow: hidden; flex-shrink: 0; background: white;">
                            <a href="javascript:void(0)" class="sidebar-link" style="background-color: var(--color-rojo); color: white; width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" onclick="window.showConfirm('¿Cerrar sesión?', () => document.getElementById('logout-form').submit())">
                                <i class="fa-solid fa-sign-out-alt" style="line-height: 1;"></i>
                                <span class="sidebar-text" style="font-size: 0.9rem; line-height: 1;">Cerrar Sesión</span>
                            </a>
                    </div>
                </aside>
            @endif

            <!-- [MAIN CONTENT SECTION] Universal for All Roles -->
            <!-- The 'app-main' class ensures it takes remaining width -->
            <div class="app-main">
                
                <!-- UNIVERSAL HEADER (Identical HTML Structure for ALL) -->
                <header class="universal-header">
                    
                    <!-- LEFT SIDE: Toggle (Admin) + Brand -->
                    <div class="header-left">
                        @if(isset($isAdmin) && $isAdmin)
                            <button id="admin-sidebar-toggle" onclick="window.toggleAdminSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; margin-right: 0.5rem; color: #111827; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                        @endif

                        <div class="logo-text">
                            <span class="brand-title logo no-select">Lic. <span class="hide-on-mobile">Nazarena</span> De Luca</span>
                        </div>
                    </div>

                    <!-- RIGHT SIDE: Navbar Content -->
                    <div class="header-right">
                        <!-- Common Tools (Notifications & Logout) - Visible for ALL Auth Users -->
                        @auth
                           <div class="header-navbar">
                                <!-- Notifications Bell -->
                                <div id="universal-notif-bell" class="notification-bell-container" style="cursor: pointer; position: relative; font-size: 1.5rem; display: flex; align-items: center;">
                                    <i class="fa-solid fa-bell" style="color: #000;"></i>
                                    <span class="notification-badge" id="universal-notif-count" style="position: absolute; top: -5px; right: -5px; background: #ff4d4d; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: none; align-items: center; justify-content: center; border: 2px solid #fff; font-weight: 800;">0</span>
                                    
                                    <!-- Dropdown (Universal) -->
                                    <div id="universal-notif-dropdown" class="notification-dropdown" onclick="event.stopPropagation()" style="display: none; position: absolute; top: calc(100% + 15px); right: -10px; width: 320px; max-width: 90vw; background: white; border: 1px solid rgba(0,0,0,0.1); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 9999; overflow: hidden;">
                                        <div class="notification-header" style="padding: 15px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                                            <span style="font-weight: 700; font-size: 0.9rem; color: #333;">Notificaciones</span>
                                            <button onclick="markAllRead()" style="background: transparent; border: none; color: #666; font-size: 0.75rem; cursor: pointer; text-decoration: underline;">
                                                Marcar leídas
                                            </button>
                                        </div>
                                        <div id="universal-notif-items" class="notif-scroll" style="max-height: 350px; overflow-y: auto; background: white;">
                                            <!-- Items injected via JS -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Logout -->
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="button" 
                                        onclick="window.showConfirm('¿Cerrar sesión?', () => this.closest('form').submit())"
                                        style="background: transparent; border: none; cursor: pointer; font-size: 1.2rem; color: #d32f2f; display: flex; align-items: center; transition: all 0.2s; padding: 8px; margin-left: 0.5rem;"
                                        title="Cerrar Sesión">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span class="hide-on-mobile" style="font-weight: 700; font-family: 'Inter', sans-serif; font-size: 1rem; margin-left: 8px;">Salir</span>
                                    </button>
                                </form>
                           </div>
                        @endauth
                    </div>
                </header>

                <!-- Page Content Body -->
                <main class="page-content">
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

                    @yield('content')
                </main>

            </div> <!-- End app-main -->

        </div> <!-- End app-wrapper -->
    @endif

    @yield('subnavigation')

    @if(auth()->check() && auth()->user()->rol == 'admin' && auth()->user()->email !== 'joacooodelucaaa16@gmail.com' && !request()->routeIs('login') && !request()->routeIs('register') && !request()->routeIs('password.*'))
        
        <!-- BOTÓN FLOTANTE (FAB) -->
        <!-- BOTÓN FLOTANTE (FAB) -->
        <!-- BOTÓN FLOTANTE (FAB) -->
        <style>
            .btn-ai {
                position: fixed;
                bottom: 1.5rem;
                right: 1.5rem;
                width: 60px;
                height: 60px;
                /* Option 2: Azul Sereno - Normal & Trustworthy */
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: white;
                border-radius: 50%;
                border: 2px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 8px 20px rgba(29, 78, 216, 0.4);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.6rem;
                z-index: 10000;
                transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                animation: pulse-glow 3s infinite;
            }

            .btn-ai:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px rgba(29, 78, 216, 0.6);
            }

            @keyframes pulse-glow {
                0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
                70% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
                100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
            }
        </style>
        <button onclick="toggleGemini()" id="ai-fab" class="btn-ai">
            <i class="fa-solid fa-user-tie"></i>
        </button>

        <!-- PANEL ASISTENTE (Oculto por defecto) -->
        <div id="ai-panel" style="
            position: fixed;
            bottom: 6rem;
            right: 1.5rem;
            width: 350px;
            height: 500px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 20px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 10001;
            font-family: 'Inter', sans-serif;
        ">
            <!-- Header Panel -->
            <div style="
                background: #ffffff;
                color: #1f2937;
                padding: 1.2rem;
                font-weight: 700;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f3f4f6;
            ">
                <div style="display: flex; align-items: center; gap: 0.8rem;">
                    <div style="background: #e0f2fe; padding: 8px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-user-tie" style="color: #0369a1; font-size: 1.1rem;"></i>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="letter-spacing: -0.3px; line-height: 1.1; font-size: 1rem;">Asistente Clínico</span>
                        <span style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Modelo: Gemini 1.5 Flash</span>
                    </div>
                </div>
                <button onclick="toggleGemini()" style="background: none; border: none; color: #9ca3af; font-size: 1.1rem; cursor: pointer; padding: 4px; transition: color 0.2s;" onmouseover="this.style.color='#1f2937'" onmouseout="this.style.color='#9ca3af'">
                </button>
            </div>

            <!-- Messages Area -->
            <div id="ai-messages" style="flex: 1; padding: 1rem; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 1rem;">
                <!-- Welcome Message (Auto) -->
                <div style="background: #ffffff; padding: 1rem; border-radius: 16px 16px 16px 0; align-self: flex-start; max-width: 90%; line-height: 1.5; font-size: 0.95rem; color: #374151; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                    Hola Lic. Nazarena 👋<br>¿En qué te puedo ayudar hoy?
                </div>
                
                <!-- Quick Actions Chips -->
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: auto; justify-content: flex-start;">
                    <button onclick="sendGeminiMessage('¿Qué turnos tengo hoy?')" style="background: #ffffff; border: 1px solid #e5e7eb; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: #4b5563; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd'; this.style.color='#1d4ed8'" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#4b5563'">¿Qué turnos tengo hoy?</button>
                    <button onclick="sendGeminiMessage('¿Cómo están mis pagos?')" style="background: #ffffff; border: 1px solid #e5e7eb; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: #4b5563; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd'; this.style.color='#1d4ed8'" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#4b5563'">¿Cómo están mis pagos?</button>
                    <button onclick="sendGeminiMessage('Necesito soporte técnico')" style="background: #ffffff; border: 1px solid #e5e7eb; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: #4b5563; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd'; this.style.color='#1d4ed8'" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#4b5563'">¿Necesito ayuda?</button>
                </div>
            </div>

            <!-- Input Area -->
            <div style="padding: 1rem; background: #ffffff; border-top: 1px solid #f3f4f6; display: flex; align-items: center; gap: 0.5rem;">
                <textarea id="ai-input" placeholder="Escribí tu consulta..." rows="1" style="flex: 1; padding: 0.8rem 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 24px; resize: none; font-family: inherit; font-size: 0.95rem; outline: none; transition: border-color 0.2s; overflow-y: hidden;" oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'" onfocus="this.style.borderColor='#1d4ed8'" onblur="this.style.borderColor='#e5e7eb'" onkeydown="if(event.key === 'Enter' && !event.shiftKey){ event.preventDefault(); sendGeminiMessage(); }"></textarea>
                <button onclick="sendGeminiMessage()" style="
                    background: #1d4ed8; 
                    color: #fff; 
                    border: none; 
                    width: 40px; 
                    height: 40px; 
                    border-radius: 50%; 
                    cursor: pointer; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    box-shadow: 0 4px 6px -1px rgba(29, 78, 216, 0.4); 
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px -1px rgba(29, 78, 216, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(29, 78, 216, 0.4)'">
                    <i class="fa-solid fa-paper-plane" style="font-size: 0.9rem;"></i>
                </button>
            </div>
        </div>

        <script>
            // 3. Chat Logic with AbortController
            let currentController = null;

            window.sendGeminiMessage = async function(msg = null) {
                const input = document.getElementById('ai-input');
                const messages = document.getElementById('ai-messages');
                // The icon is inside the button which is after the input
                const btn = document.querySelector('#ai-input + button'); // The button
                const btnIcon = btn ? btn.querySelector('i') : null;
                
                if(!input || !messages || !btnIcon) return;

                // If currently generating, this button acts as Stop
                if (currentController) {
                    currentController.abort();
                    currentController = null;
                    // Reset Icon
                    btnIcon.className = 'fa-solid fa-paper-plane';
                    btnIcon.style.color = '#ffffff'; 
                    // Append "Stopped" message
                    const stopMsg = document.createElement('div');
                    stopMsg.style.textAlign = 'center';
                    stopMsg.style.fontSize = '0.8rem';
                    stopMsg.style.color = '#999';
                    stopMsg.style.margin = '5px 0';
                    stopMsg.innerText = 'Generación detenida.';
                    messages.appendChild(stopMsg);
                    // Remove the last loading bubble if exists
                    const lastBubble = messages.lastElementChild;
                    if(lastBubble && lastBubble.innerHTML.includes('...')) {
                        lastBubble.remove();
                    }
                    return;
                }

                const messageText = msg || input.value.trim();
                if (!messageText) return;

                // --- UI Updates Start ---
                
                // Change Button to Square (Stop)
                btnIcon.className = 'fa-solid fa-square';
                btnIcon.style.fontSize = '0.8rem';
                
                // 1. Show User Message
                const userMsg = document.createElement('div');
                userMsg.style.marginBottom = '0.5rem';
                userMsg.style.background = '#eff6ff'; // Blue 50
                userMsg.style.color = '#1e3a8a';      // Blue 900
                userMsg.style.padding = '0.8rem';
                userMsg.style.border = '1px solid #bfdbfe'; // Blue 200
                userMsg.style.borderRadius = '12px 12px 0 12px';
                userMsg.style.alignSelf = 'flex-end';
                userMsg.style.maxWidth = '85%';
                userMsg.style.lineHeight = '1.4';
                userMsg.style.fontSize = '0.95rem';
                userMsg.innerHTML = `<strong>Vos:</strong> ${messageText}`;
                messages.appendChild(userMsg);

                if (!msg) input.value = '';
                messages.scrollTop = messages.scrollHeight;

                // 2. Show AI Loading Bubble
                const aiMsg = document.createElement('div');
                aiMsg.style.marginBottom = '0.5rem';
                aiMsg.style.background = '#ffffff'; 
                aiMsg.style.color = '#000000';
                aiMsg.style.padding = '0.8rem';
                aiMsg.style.border = '1px solid #e5e7eb';
                aiMsg.style.borderRadius = '12px 12px 12px 0';
                aiMsg.style.alignSelf = 'flex-start';
                aiMsg.style.maxWidth = '85%';
                aiMsg.style.lineHeight = '1.4';
                aiMsg.style.fontSize = '0.95rem';
                aiMsg.innerHTML = '<strong>IA:</strong> ...'; 
                messages.appendChild(aiMsg);
                messages.scrollTop = messages.scrollHeight;

                // --- Network Request ---
                
                currentController = new AbortController();
                const signal = currentController.signal;

                try {
                    // 3. Petición al Backend (JSON)
                    const response = await fetch('{{ route('admin.ai.chat') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: messageText }),
                        signal: signal
                    });

                    if (!response.ok) throw new Error('Error en la conexión con la IA');

                    const data = await response.json();
                    
                    if (data.response) {
                         aiMsg.innerHTML = `<strong>IA:</strong> ${data.response.replace(/\n/g, '<br>')}`;
                    } else {
                         aiMsg.innerHTML = "<strong>IA:</strong> No entendí, ¿podés repetir?";
                    }
                    messages.scrollTop = messages.scrollHeight;

                } catch (error) {
                    if (error.name === 'AbortError') {
                        console.log('Fetch aborted');
                        aiMsg.innerHTML += '<br><span style="color:#999; font-size:0.8rem;">(Detenido)</span>';
                    } else {
                        aiMsg.innerHTML += `<br><span style="color:red; font-size:0.8rem;">(Error: ${error.message})</span>`;
                        console.error(error);
                    }
                } finally {
                    // Reset Button Icon
                    currentController = null;
                    if(btnIcon) {
                        btnIcon.className = 'fa-solid fa-paper-plane';
                        btnIcon.style.fontSize = '0.9rem';
                    }
                }
            };
        </script>
    @endif

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


    <footer class="footer">
        <div class="container text-center">
            <h2 style="color: white; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">Lic. Nazarena De Luca</h2>
            <!-- Dev Report Button (Minimalist) -->
            <!-- Report Button (Visible to Guests & Non-Dev Auth Users) -->
            <!-- Report Button (Visible to ALL guests and Non-Dev Auth Users) -->
            @if(!auth()->check() || (auth()->check() && auth()->user()->email !== 'joacooodelucaaa16@gmail.com'))
                @if(request()->routeIs('login'))
                    <button onclick="openReportModal()" style="
                        background: transparent; 
                        border: none;
                        color: #ffffff; 
                        font-size: 0.9rem; /* Slightly smaller */
                        cursor: pointer; 
                        margin-bottom: 1rem; 
                        transition: transform 0.2s;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        font-family: 'Manrope', sans-serif;
                        opacity: 1; 
                        text-decoration: underline; /* Added underline style for 'linky' feel or keep just text */
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 1rem;"></i> ¿Problemas para logearte? Reportar fallo
                    </button>
                @else
                    <button onclick="openReportModal()" style="
                        background: transparent; 
                        border: none;
                        color: #ffffff; 
                        font-size: 1rem; 
                        cursor: pointer; 
                        margin-bottom: 1rem; 
                        transition: transform 0.2s;
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        font-family: 'Manrope', sans-serif;
                        opacity: 1;
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem;"></i> Reportar un problema
                    </button>
                @endif
            @endif
            
            <!-- Delete Account Button (Underlined, below Report) -->
            @auth
                @if(auth()->user()->rol == 'paciente')
                    <button type="button" 
                            onclick="openDeleteModal()" 
                            style="background: none; border: none; color: rgba(255,255,255,0.5); text-decoration: underline; cursor: pointer; font-size: 0.75rem; font-family: 'Manrope', sans-serif; transition: color 0.3s; margin-bottom: 1rem; display: block; margin-left: auto; margin-right: auto;" 
                            onmouseover="this.style.color='rgba(255,255,255,0.8)'" 
                            onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                        Quiero darme de baja del sistema
                    </button>
                @endif
            @endauth
            <p style="font-family: 'Manrope', sans-serif; opacity: 0.8; margin-bottom: 1.5rem;">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
            


        </div>
    </footer>

    <!-- Invisible Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Custom Confirmation Modal -->
    <div id="confirm-modal-overlay" class="confirm-modal-overlay" style="display: none;">
        <div class="confirm-modal" style="border-radius: 15px !important; border: 2px solid #000; box-shadow: 6px 6px 0px rgba(0,0,0,0.1);">
            <div class="confirm-modal-title" style="font-family: 'Syne', sans-serif;">Confirmar acción</div>
            <div id="confirm-modal-message" class="confirm-modal-message" style="font-family: 'Inter', sans-serif; font-size: 1rem;"></div>
            <div class="confirm-modal-buttons">
                <button id="confirm-cancel" class="neobrutalist-btn bg-rosa" style="border-radius: 8px;">Cancelar</button>
                <button id="confirm-ok" class="neobrutalist-btn bg-verde" style="border-radius: 8px;">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Duplicate Modal Removed -->

    <!-- Html2Canvas Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function openReportModal() {
            document.getElementById('report-modal-overlay').style.display = 'flex';
            document.getElementById('report-form-content').style.display = 'block';
            document.getElementById('report-success-msg').style.display = 'none';
            document.getElementById('report-loading-msg').style.display = 'none';
            document.getElementById('report-desc').value = '';
        }

        function closeReportModal() {
            document.getElementById('report-modal-overlay').style.display = 'none';
        }

        async function submitReport() {
            const desc = document.getElementById('report-desc').value;
            if (!desc.trim()) {
                alert('Por favor describí el problema.');
                return;
            }

            // Show loading
            document.getElementById('report-form-content').style.display = 'none';
            document.getElementById('report-loading-msg').style.display = 'block';

            try {
                // Capture Screenshot
                const canvas = await html2canvas(document.body);
                const screenshotData = canvas.toDataURL('image/png');

                // Send to backend
                const response = await fetch("{{ route('api.tickets.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        description: desc,
                        metadata: {
                            screenshot: screenshotData,
                            url: window.location.href,
                            user_agent: navigator.userAgent
                        }
                    })
                });

                if (response.ok) {
                    document.getElementById('report-loading-msg').style.display = 'none';
                    document.getElementById('report-success-msg').style.display = 'block';
                } else {
                    throw new Error('Error al enviar');
                }
            } catch (error) {
                console.error(error);
                alert('Hubo un error al enviar el reporte. Por favor intentá de nuevo.');
                document.getElementById('report-loading-msg').style.display = 'none';
                document.getElementById('report-form-content').style.display = 'block';
            }
        }
    </script>

    <!-- Delete Account Modal (DISABLED) -->
    <!-- Delete Account Modal -->
    <div id="delete-account-modal" class="confirm-modal-overlay" style="display: none;">
        <div class="confirm-modal" style="border-color: #ff4d4d; border-radius: 15px !important; border: 2px solid #ff4d4d; box-shadow: 6px 6px 0px rgba(0,0,0,0.1);">
            <div class="confirm-modal-title" style="color: #d00; font-family: 'Syne', sans-serif;">Eliminar Cuenta</div>
            <div class="confirm-modal-message">
                <p style="margin-bottom: 1rem; font-family: 'Inter', sans-serif;">¿Estás segura de que querés darte de baja?</p>
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.5rem; font-family: 'Inter', sans-serif;">Esta acción es <strong>irreversible</strong>. Se eliminarán todos tus turnos, historial y documentos.</p>
                
                <form action="{{ route('patient.account.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div style="margin-bottom: 1.5rem; text-align: left;">
                        <label style="font-size: 0.8rem; font-weight: 700; display: block; margin-bottom: 0.5rem; font-family: 'Inter', sans-serif;">Ingresá tu contraseña para confirmar:</label>
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

    @auth
    <script>
        // Delete Account Logic
        function openDeleteModal() {
            document.getElementById('delete-account-modal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('delete-account-modal').style.display = 'none';
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
            if (navbar) {
                if (window.scrollY > lastScrollY && window.scrollY > 100) {
                    navbar.classList.add('nav-hidden');
                } else {
                    navbar.classList.remove('nav-hidden');
                }
            }
            lastScrollY = window.scrollY;
        });

        window.showConfirm = function(message, callback) {
            confirmMessage.innerText = message;
            _pendingConfirmAction = callback;
            confirmOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Lock scroll
        };

        confirmOk.addEventListener('click', () => {
            if (_pendingConfirmAction) _pendingConfirmAction();
            confirmOverlay.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scroll
        });

        confirmCancel.addEventListener('click', () => {
            confirmOverlay.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scroll
        });

        const bell = document.getElementById('notif-bell');
        const dropdown = document.getElementById('notif-dropdown');
        const items = document.getElementById('notif-items');
        const count = document.getElementById('notif-count');

        if (bell && dropdown) {
            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                // Toggle dropdown visibility
                if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                    dropdown.style.display = 'block';
                    fetchNotifications();
                } else {
                    dropdown.style.display = 'none';
                }
            });

            // Only update count automatically, fetch details on click
            setInterval(() => fetchNotifications(false), 60000); // Check every minute
            fetchNotifications(false); // Initial count check
        }

        document.addEventListener('click', () => {
            if (dropdown) dropdown.style.display = 'none';
        });

        let lastNotifCount = 0;
        async function fetchNotifications(showInDropdown = true) {
            try {
                const res = await fetch('{{ route("notifications.latest") }}');
                const data = await res.json();
                
                lastNotifCount = data ? data.length : 0;

                if (data && data.length > 0) {
                    if (count) {
                        count.innerText = data.length;
                        count.style.display = 'flex';
                        // Add animation only if there are new notifications
                        const bellIcon = document.querySelector('#notif-bell i');
                        if(bellIcon) bellIcon.classList.add('fa-shake');
                    }
                    
                    if (items) {
                        items.innerHTML = data.map(n => `
                            <div class="notification-item" data-id="${n.id}" style="border-bottom: 1px solid #f0f0f0; padding: 16px 24px; display: block; text-decoration: none; transition: background 0.2s; cursor: pointer; background: white;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'" onclick="markAsRead(${n.id})">
                                <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                                    <div style="flex: 1;">
                                        <p style="margin:0; font-size: 0.9rem; font-weight: 500; font-family: 'Inter', sans-serif; color: #1f2937; line-height: 1.5;">${n.mensaje}</p>
                                        <small style="color: #6b7280; font-size: 0.75rem; font-weight: 400; display: block; margin-top: 6px; font-family: 'Inter', sans-serif;">
                                            ${new Date(n.created_at).toLocaleDateString()} a las ${new Date(n.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                        </small>
                                    </div>
                                    <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; margin-top: 6px;"></div>
                                </div>
                            </div>
                        `).join('');
                    }
                } else {
                    // Suppress visual indicators cleanly if 0
                    if (count) {
                        count.innerText = '0'; // For accessibility/debug if inspected
                        count.style.display = 'none'; // Completely hide badge
                        
                        // Remove animation
                        const bellIcon = document.querySelector('#notif-bell i');
                        if(bellIcon) bellIcon.classList.remove('fa-shake');
                    }
                    if (showInDropdown && items) {
                        items.innerHTML = '<div class="notification-empty" style="padding: 4rem 2rem; text-align: center; color: #a1a1a1; font-family: \'Inter\', sans-serif;"><p style="margin: 0; font-weight: 500; font-size: 0.95rem;">No tienes nuevas notificaciones</p></div>';
                    }
                }
            } catch (e) { console.error("Error fetching notifications", e); }
        }

        async function markAsRead(id) {
            // Opacar la notificación antes de eliminarla
            const notifElement = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (notifElement) {
                notifElement.style.opacity = '0.3';
                notifElement.style.transform = 'translateX(20px)';
                notifElement.style.pointerEvents = 'none';
            }
            
            await fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            
            // Esperar un momento y luego refrescar
            setTimeout(() => {
                fetchNotifications();
            }, 300);
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
        
        // Admin Sidebar Toggle Logic removed (consolidated below)

        // Forcing direct click attachment for maximum reliability
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.querySelector('.admin-unified-header .neobrutalist-btn');
            if (btn) {
                btn.onclick = () => {
                    window.toggleAdminSidebar();
                };
            }
            
            // Ensure AI button works
            // Ensure AI button works
            const aiBtn = document.querySelector('.ai-btn');
            if (aiBtn) {
                // Inline onclick handles this. preventing double toggle.
            }
            
            // Ensure Gemini input and buttons work
            const geminiInput = document.getElementById('gemini-input');
            if (geminiInput) {
                geminiInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        window.sendGeminiMessage();
                    }
                });
            }
            
            // Setup send button
            const sendBtn = document.querySelector('.gemini-chat-input button');
            if (sendBtn) {
                sendBtn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    window.sendGeminiMessage();
                };
            }
            
            // Setup quick action buttons
            const quickBtns = document.querySelectorAll('.chat-actions button');
            quickBtns.forEach(btn => {
                btn.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    // Extract the message from onclick attribute or data
                    const onclickAttr = btn.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes('sendQuickMessage')) {
                        // Parse the message from the onclick
                        const match = onclickAttr.match(/sendQuickMessage\('([^']*)'\)/);
                        if (match && match[1]) {
                            window.sendGeminiMessage(match[1]);
                        }
                    }
                };
            });
        });

        function obtenerIconoClima(temperatura, condicion) {
            // Basado en la condición del clima
            if (condicion.includes('nube') || condicion.includes('cloud')) return '☁️';
            if (condicion.includes('lluvia') || condicion.includes('rain')) return '🌧️';
            if (condicion.includes('tormenta') || condicion.includes('thunder')) return '⛈️';
            if (condicion.includes('nieve') || condicion.includes('snow')) return '❄️';
            if (condicion.includes('niebla') || condicion.includes('fog')) return '🌫️';
            if (temperatura > 25) return '☀️';
            if (temperatura > 15) return '🌤️';
            return '🌙';
        }

        // Welcome message is now handled server-side in DashboardController to ensure static randomness.

        window.invocarAsistenteIA = function() {
            window.showConfirm("¡Hola Nazarena! ¿Querés que analice los turnos de hoy para darte un resumen?", function() {
                alert("Analizando agenda... Hoy tenés pacientes con temas de ansiedad y gestión del tiempo destacados.");
            });
        };

        // Initialize dynamic elements
        document.addEventListener('DOMContentLoaded', () => {
            // MOVED: mostrarBienvenida(); logic is now server-side
            // Sync initial sidebar icon state
            const sidebar = document.getElementById('admin-sidebar');
            const icon = document.getElementById('sidebar-toggle-icon');
            if (sidebar && icon) {
                const isMobile = window.innerWidth <= 1024;
                const isOpen = (isMobile && sidebar.classList.contains('active')) || 
                                (!isMobile && !sidebar.classList.contains('collapsed'));
                icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
            }

            // Smart Header Scroll Logic
            const adminHeader = document.querySelector('.admin-unified-header');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                if (!adminHeader) return;
                
                const currentScrollY = window.scrollY;
                const scrollDown = currentScrollY > lastScrollY;
                const threshold = 50; // Minimum scroll to trigger

                // Only trigger if scrolled past threshold
                if (Math.abs(currentScrollY - lastScrollY) < 5) return;

                if (scrollDown && currentScrollY > threshold) {
                    // Scrolling Down -> Hide
                    adminHeader.classList.add('header-hidden');
                } else {
                    // Scrolling Up -> Show
                    adminHeader.classList.remove('header-hidden');
                }
                
                lastScrollY = currentScrollY;
            });
            
            // AI Functions
            window.toggleGemini = function() {
                const panel = document.getElementById('ai-panel');
                if(panel) {
                    panel.style.display = panel.style.display === 'flex' ? 'none' : 'flex';
                    if(panel.style.display === 'flex') {
                        setTimeout(() => document.getElementById('ai-input').focus(), 100);
                    }
                }
            }

            /* Duplicate sendGeminiMessage Removed */

            @if(auth()->check() && auth()->user()->rol == 'admin')
            // Auto-Sync Holidays (Background)
            fetch('{{ route("admin.calendar.import-holidays") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => res.json())
              .then(d => console.log('Holidays synced:', d))
              .catch(e => console.error('Sync failed:', e));
            @endif
        });

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
        $isLoginOrRegister = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.*');
        
        $showWidget = true; // Enabled for everyone for testing purposes
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
                        <i class="fa-solid fa-calendar-day" style="color: #25D366;"></i> ¿Cómo reservo un turno? 📅
                    </a>
                    <a href="https://wa.me/5491139560673?text={{ urlencode('Hola Nazarena, tengo una consulta sobre los pagos de las sesiones.') }}" target="_blank" class="neobrutalist-btn" style="padding: 0.4rem; font-size: 0.7rem; text-align: left; background: #fff; text-transform: none; border-width: 2px; box-shadow: 2px 2px 0px #000;">
                        <i class="fa-solid fa-money-bill-wave" style="color: #25D366;"></i> Consulta sobre pagos 💰
                    </a>
                    <a href="https://wa.me/5491139560673?text={{ urlencode('Hola Nazarena, te quería consultar sobre la modalidad de las sesiones.') }}" target="_blank" class="neobrutalist-btn" style="padding: 0.4rem; font-size: 0.7rem; text-align: left; background: #fff; text-transform: none; border-width: 2px; box-shadow: 2px 2px 0px #000;">
                        <i class="fa-solid fa-video" style="color: #25D366;"></i> Modalidad de las sesiones 💻
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

        <!-- REMOVED OLD OLLAMA CHAT WINDOW -->

    @endif

    <script>
        // -----------------------------------------------------
        // 🧠 SMART STICKY HEADER
        // -----------------------------------------------------
        document.addEventListener('DOMContentLoaded', () => {
             const header = document.querySelector('.admin-unified-header');
             if (!header) return;

             let lastScrollY = window.scrollY;
             const threshold = 50;

             // Estilos para la animación
             header.style.transition = 'transform 0.3s ease, opacity 0.3s ease';

             window.addEventListener('scroll', () => {
                 const currentScrollY = window.scrollY;
                 if (currentScrollY < 0) return; // Rebote iOS
                 
                 const diff = Math.abs(currentScrollY - lastScrollY);
                 if (diff < 5) return;

                 if (currentScrollY > lastScrollY && currentScrollY > threshold) {
                     // ⬇️ SCROLL DOWN -> HIDE
                     header.style.transform = 'translateY(-100%)';
                     header.style.opacity = '0';
                     header.style.pointerEvents = 'none';
                 } else {
                     // ⬆️ SCROLL UP -> SHOW
                     header.style.transform = 'translateY(0)';
                     header.style.opacity = '1';
                     header.style.pointerEvents = 'auto';
                 }
                 lastScrollY = currentScrollY;
             }, { passive: true });
        });


    </script>

    <script>
        function toggleMobileMenu() {
            const dropdown = document.getElementById('mobile-nav-dropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            }
        }
        

        
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

    <script>
        // Cleaned up scripts
        // Auto-close sidebar when clicking a link (Global)
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('admin-sidebar');
            
            // Re-apply robust button logic
            const btn = document.getElementById('admin-sidebar-toggle');
            if (btn) {
                // Force High Z-Index ensuring it's clickable
                btn.style.position = 'relative'; 
                btn.style.zIndex = '999999';

                // Remove existing listeners by cloning
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                // Attach clean listener
                newBtn.onclick = (e) => {
                    e.preventDefault(); 
                    e.stopPropagation();
                    console.log('Button Clicked (Re-attached)'); 
                    window.toggleAdminSidebar();
                };
            }

            if (sidebar) {
                sidebar.addEventListener('click', (e) => {
                    const link = e.target.closest('.sidebar-link');
                    if (link) {
                        document.cookie = "sidebar_collapsed=true; path=/";
                    }
                });
            }
        });
    </script>

    <script>
        // --- Notifications Logic ---
        document.addEventListener('DOMContentLoaded', () => {
            
            function initNotifications(config) {
                const bell = document.getElementById(config.bellId);
                const dropdown = document.getElementById(config.dropdownId);
                const notifItems = document.getElementById(config.itemsId);
                const notifCount = document.getElementById(config.countId);
                
                if (!bell || !dropdown) return;
                
                let isOpen = false;

                async function fetchNotifications() {
                    try {
                        const url = "{{ route('notifications.latest') }}";
                        const res = await fetch(url);
                        if(!res.ok) throw new Error('Network error');
                        const data = await res.json();
                        renderNotifications(data);
                        updateCount(data.length);
                    } catch (e) {
                         // Silent error
                    }
                }

                function renderNotifications(notifs) {
                    if(!notifItems) return;
                    notifItems.innerHTML = '';
                    if (notifs.length === 0) {
                        notifItems.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: #666; font-size: 0.9rem;">No tenés notificaciones nuevas 🎉</div>';
                        return;
                    }
                    notifs.forEach(n => {
                        const item = document.createElement('div');
                        item.style.padding = '12px 15px';
                        item.style.borderBottom = '1px solid #eee';
                        item.style.cursor = 'pointer';
                        item.style.transition = 'background 0.2s';
                        item.onmouseover = () => item.style.background = '#f0f0f0';
                        item.onmouseout = () => item.style.background = 'white';
                        
                        let icon = '<i class="fa-solid fa-info-circle" style="color: #3b82f6;"></i>';
                        if(n.type && n.type.includes('Payment')) icon = '<i class="fa-solid fa-money-bill-wave" style="color: #10b981;"></i>';
                        if(n.type && n.type.includes('Appointment')) icon = '<i class="fa-solid fa-calendar" style="color: #a855f7;"></i>';

                        item.innerHTML = `
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <div style="margin-top: 2px;">${icon}</div>
                                <div>
                                    <div style="font-size: 0.85rem; color: #333; line-height: 1.4;">${n.data.message || 'Nueva notificación'}</div>
                                    <div style="font-size: 0.75rem; color: #999; margin-top: 4px;">${new Date(n.created_at).toLocaleString()}</div>
                                </div>
                            </div>
                        `;
                        // Mark as read onclick
                        item.onclick = async () => {
                            const url = "{{ route('notifications.read', ':id') }}".replace(':id', n.id);
                            await fetch(url, { 
                                method: 'POST', 
                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} 
                            });
                            fetchNotifications();
                        };
                        notifItems.appendChild(item);
                    });
                }

                function updateCount(count) {
                    if(!notifCount) return;
                    if (count > 0) {
                        notifCount.style.display = 'flex';
                        notifCount.innerText = count;
                    } else {
                        notifCount.style.display = 'none';
                    }
                }
                
                // Toggle Logic
                bell.addEventListener('click', (e) => {
                    e.stopPropagation();
                    isOpen = !isOpen;
                    dropdown.style.display = isOpen ? 'block' : 'none';
                    if (isOpen) fetchNotifications();
                });

                document.addEventListener('click', () => {
                    isOpen = false;
                    dropdown.style.display = 'none';
                });
                
                dropdown.addEventListener('click', (e) => e.stopPropagation());
                
                // Poll
                setInterval(fetchNotifications, 60000);
                fetchNotifications();
            }

            // Expose markAllRead globally but relying on active config context is tricky.
            // Simplified: Global markAllRead just hits the endpoint and refreshes page or stays silent.
            // Better: We hook it to reload the lists.
            
            // For now, let's just Init based on what exists.
            // Universal Init
            initNotifications({
                bellId: 'universal-notif-bell',
                dropdownId: 'universal-notif-dropdown',
                itemsId: 'universal-notif-items',
                countId: 'universal-notif-count'
            });

            window.markAllRead = async function() {
                await fetch('{{ route('notifications.read-all') }}', { 
                    method: 'POST', 
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} 
                });
                // Since we don't have easy access to the closure instances, and simplest UX is refresh or just wait next poll.
                // We could dispatch event or just reload page if needed, but for now silent is okay.
                // Or better: Re-run both inits? No, they add listeners.
                
                // Hack: Trigger clicks to close and re-open? No.
                // Let's just trust the user will close and open again.
                // Or we can manually hide the badge
                const c1 = document.getElementById('admin-notif-count');
                if(c1) c1.style.display = 'none';
                const c2 = document.getElementById('patient-notif-count');
                if(c2) c2.style.display = 'none';
                
                // And clear lists
                 const l1 = document.getElementById('admin-notif-items');
                 if(l1) l1.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: #666; font-size: 0.9rem;">No tenés notificaciones nuevas 🎉</div>';
                 const l2 = document.getElementById('patient-notif-items');
                 if(l2) l2.innerHTML = '<div style="padding: 1.5rem; text-align: center; color: #666; font-size: 0.9rem;">No tenés notificaciones nuevas 🎉</div>';
            }
        });
    </script>
    @if(auth()->check() && auth()->user()->rol == 'admin' && auth()->user()->email !== 'joacooodelucaaa16@gmail.com' && !request()->routeIs('login') && !request()->routeIs('register'))
        
        <!-- BOTÓN FLOTANTE (FAB) -->
        <style>
            @keyframes pulse-border {
                0% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.7); }
                70% { box-shadow: 0 0 0 15px rgba(168, 85, 247, 0); }
                100% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0); }
            }
        </style>
        <button onclick="toggleGemini()" id="ai-fab" style="
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 60px;
            height: 60px;
            background: #1976d2;
            color: #fff;
            border-radius: 50%;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            z-index: 10000;
            transition: transform 0.2s;
            animation: pulse-border 2s infinite;
        ">
            <i class="fa-solid fa-user-tie"></i>
        </button>

        <!-- PANEL ASISTENTE (Oculto por defecto) -->
        <div id="ai-panel" style="
            position: fixed;
            bottom: 6rem;
            right: 1.5rem;
            width: 350px;
            height: 500px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 20px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 10001;
            font-family: 'Inter', sans-serif;
        ">
            <!-- Header Panel -->
            <div style="
                background: #ffffff;
                color: #1f2937;
                padding: 1.2rem;
                font-weight: 700;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f3f4f6;
            ">
                <div style="display: flex; align-items: center; gap: 0.8rem;">
                    <div style="background: #eff6ff; padding: 8px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-user-tie" style="color: #3b82f6; font-size: 1.1rem;"></i>
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="letter-spacing: -0.3px; line-height: 1.1; font-size: 1rem;">Asistente Clínico</span>
                        <span style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Modelo: Gemini 1.5 Flash</span>
                    </div>
                </div>
                <button onclick="toggleGemini()" style="background: none; border: none; color: #9ca3af; font-size: 1.1rem; cursor: pointer; padding: 4px; transition: color 0.2s;" onmouseover="this.style.color='#1f2937'" onmouseout="this.style.color='#9ca3af'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Messages Area -->
            <div id="ai-messages" style="flex: 1; padding: 1rem; overflow-y: auto; background: #f0f2f5; display: flex; flex-direction: column; gap: 1rem;">
                <!-- Welcome Message (Auto) -->
                <div style="background: #ffffff; padding: 1rem; border-radius: 16px 16px 16px 0; align-self: flex-start; max-width: 90%; line-height: 1.5; font-size: 0.95rem; color: #374151; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    Hola Lic. Nazarena 👋<br>¿En qué te puedo ayudar hoy?
                </div>
                
                <!-- Quick Actions Chips -->
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: auto; justify-content: flex-start;">
                    <button onclick="sendGeminiMessage('¿Qué turnos tengo hoy?')" style="background: #ffffff; border: 1px solid #e5e7eb; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: #4b5563; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'">¿Qué turnos tengo hoy?</button>
                    <button onclick="sendGeminiMessage('¿Cómo están mis pagos?')" style="background: #ffffff; border: 1px solid #e5e7eb; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: #4b5563; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'">¿Cómo están mis pagos?</button>
                </div>
            </div>

            <!-- Input Area -->
            <div style="padding: 1rem; background: #ffffff; border-top: 1px solid #f3f4f6; display: flex; align-items: center; gap: 0.5rem;">
                <textarea id="ai-input" placeholder="Escribí tu consulta..." rows="1" style="flex: 1; padding: 0.8rem 1rem; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 24px; resize: none; font-family: inherit; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'" onkeydown="if(event.key === 'Enter' && !event.shiftKey){ event.preventDefault(); sendGeminiMessage(); }"></textarea>
                <button onclick="sendGeminiMessage()" style="
                    background: #3b82f6; 
                    color: #fff; 
                    border: none; 
                    width: 40px; 
                    height: 40px; 
                    border-radius: 50%; 
                    cursor: pointer; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4); 
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px -1px rgba(59, 130, 246, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(59, 130, 246, 0.4)'">
                    <i class="fa-solid fa-paper-plane" style="font-size: 0.9rem;"></i>
                </button>
            </div>
        </div>

        <script>
            function toggleGemini() {
                const panel = document.getElementById('ai-panel');
                if (panel) {
                    panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'flex' : 'none';
                    if (panel.style.display === 'flex') {
                        // Focus input
                        setTimeout(() => document.getElementById('ai-input').focus(), 100);
                    }
                }
            }

            let currentGeminiController = null;

            async function sendGeminiMessage(msg = null) {
                const input = document.getElementById('ai-input');
                const messages = document.getElementById('ai-messages');
                const btnIcon = document.querySelector('#ai-input + button i'); 
                
                // If currently generating, stop
                if (currentGeminiController) {
                    currentGeminiController.abort();
                    currentGeminiController = null;
                    if(btnIcon) btnIcon.className = 'fa-solid fa-paper-plane';
                    return;
                }

                const messageText = msg || input.value.trim();
                if (!messageText) return;

                if(btnIcon) btnIcon.className = 'fa-solid fa-square';

                // 1. Show User Message
                const userMsg = document.createElement('div');
                userMsg.style.marginBottom = '0.5rem';
                userMsg.style.background = '#e0f2fe'; 
                userMsg.style.color = '#000';
                userMsg.style.padding = '0.8rem';
                userMsg.style.borderRadius = '12px 12px 0 12px';
                userMsg.style.alignSelf = 'flex-end';
                userMsg.style.maxWidth = '85%';
                userMsg.style.fontSize = '0.95rem';
                userMsg.innerHTML = `<strong>Vos:</strong> ${messageText}`;
                messages.appendChild(userMsg);

                if (!msg) input.value = '';
                messages.scrollTop = messages.scrollHeight;

                // 2. Loading
                const aiMsg = document.createElement('div');
                aiMsg.style.marginBottom = '0.5rem';
                aiMsg.style.background = '#f0f7ff'; 
                aiMsg.style.color = '#000';
                aiMsg.style.padding = '0.8rem';
                aiMsg.style.borderRadius = '12px 12px 12px 0';
                aiMsg.style.alignSelf = 'flex-start';
                aiMsg.style.maxWidth = '85%';
                aiMsg.style.fontSize = '0.95rem';
                aiMsg.innerHTML = '<strong>IA:</strong> ...'; 
                messages.appendChild(aiMsg);
                messages.scrollTop = messages.scrollHeight;

                currentGeminiController = new AbortController();
                
                try {
                    const response = await fetch('{{ route('admin.ai.chat') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: messageText }),
                        signal: currentGeminiController.signal
                    });

                    if (!response.ok) throw new Error('Error en la conexión con la IA');

                    const data = await response.json();
                    
                    if (data.response) {
                        // Replace simple newlines with breaks
                        let formattedResp = data.response.replace(/\n/g, '<br>');
                        // Basic markdown bold support
                        formattedResp = formattedResp.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                        
                        aiMsg.innerHTML = `<strong>IA:</strong> ${formattedResp}`;
                    } else {
                        aiMsg.innerHTML = "<strong>IA:</strong> No entendí, intentalo de nuevo.";
                    }
                    
                    messages.scrollTop = messages.scrollHeight;
                    
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        aiMsg.innerHTML = `<span style="color:red;">Error: ${error.message}</span>`;
                    } else {
                        aiMsg.innerHTML += " (Detenido)";
                    }
                } finally {
                    currentGeminiController = null;
                    if(btnIcon) btnIcon.className = 'fa-solid fa-paper-plane';
                    // Re-focus input for fast conversation
                    input.focus();
                }
            }

        </script>
    @endif

</body>
</html>
