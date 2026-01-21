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

    @auth
        @php
            $isAdmin = auth()->user()->rol == 'admin';
            $isPatient = auth()->user()->rol == 'paciente';
        @endphp
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @endauth

    @php
        $isAuthPage = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.*');
    @endphp

    @if($isAuthPage)
        {{-- Auth pages: Minimal, no nav, no extra padding --}}
        @yield('content')
    @else
        @if(isset($isAdmin) && $isAdmin)
            <!-- Admin Layout Container -->
            <div class="admin-layout @if(isset($isAdmin) && $isAdmin && isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] == 'true') @else sidebar-open @endif" id="admin-layout-container">
                <aside class="admin-sidebar @if(isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] == 'true') collapsed @endif" id="admin-sidebar" style="z-index: 6001; overscroll-behavior: contain;">
                    <!-- Close button for mobile -->
                    <button onclick="window.toggleAdminSidebar()" class="sidebar-close-btn" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; z-index: 10; display: none; color: #000; padding: 0.5rem;">
                    </button>
                    
                    @if(auth()->user()->email === 'joacooodelucaaa16@gmail.com')
                        <div class="sidebar-logo" style="padding: 1.5rem 0.5rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; width: 100%; height: 2.5rem;">
                             <span class="sidebar-text sidebar-text-small" style="font-size: 0.95rem !important; font-weight: normal; letter-spacing: -0.2px; white-space: nowrap; flex-shrink: 0; color: white; display: flex; align-items: center; text-align: center; height: 100%;">Panel de Desarrollador</span>
                        </div>
                    @else
                        <div class="sidebar-logo" style="padding: 1.5rem 0.5rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; gap: 0.6rem; overflow: hidden; width: 100%; height: 2.5rem;">
                            <i class="fa-solid fa-brain" style="color: #000; font-size: 1.3rem; flex-shrink: 0; display: flex; align-items: center; height: 100%;"></i>
                            <span class="sidebar-text sidebar-text-small" style="font-size: 0.95rem !important; font-weight: normal; letter-spacing: -0.2px; white-space: nowrap; flex-shrink: 0; color: rgba(0, 0, 0, 0.5); display: flex; align-items: center; text-align: center; height: 100%;">Espacio Terapéutico</span>
                        </div>
                    @endif
                    
                    <nav class="sidebar-nav">
                        @if(auth()->user()->email !== 'joacooodelucaaa16@gmail.com')
                            <a href="{{ route('admin.home') }}" class="sidebar-link bg-lila" style="border: 3px solid #000; box-shadow: 4px 4px 0px #000; transform: scale(1.02); z-index: 10;">
                                <i class="fa-solid fa-house"></i>
                                <span class="sidebar-text" style="font-weight: 900;">Inicio</span>
                            </a>
                            <a href="{{ route('admin.agenda') }}" class="sidebar-link bg-celeste">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span class="sidebar-text">Agenda Hoy</span>
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

                    <div class="sidebar-footer" style="padding: 1.5rem 0.5rem; margin-top: 2rem; border-top: 1px solid rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; gap: 0.5rem; overflow: hidden;">
                            <a href="javascript:void(0)" class="sidebar-link" style="background-color: var(--color-rojo); color: white; width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" onclick="window.showConfirm('¿Cerrar sesión?', () => document.getElementById('logout-form').submit())">
                                <i class="fa-solid fa-sign-out-alt" style="line-height: 1;"></i>
                                <span class="sidebar-text" style="font-size: 0.9rem; line-height: 1;">Cerrar Sesión</span>
                            </a>
                    </div>
                </aside>

                <main class="main-content" style="padding-top: 0 !important;"> 
                    <!-- Unified Admin Header -->
                    <div class="admin-unified-header" style="padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; background: white; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 40;">
                        <div style="display: flex; align-items: center;">
                             <button onclick="window.toggleAdminSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; margin-right: 1rem; color: #111827;">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                            <span style="font-size: 1.3rem; font-weight: 700; font-family: 'Fraunces', serif; color: #111827; display: flex; align-items: center; gap: 10px;">
                                Lic. Nazarena De Luca
                                <span style="color: #e5e7eb; font-weight: 300; font-size: 1.5rem;">|</span>
                                <span style="color: #6b7280; font-weight: 500;">
                                    @hasSection('header_title')
                                        @yield('header_title')
                                    @else
                                        @if(request()->routeIs('admin.historial'))
                                            Historial
                                        @elseif(request()->routeIs('admin.developer'))
                                            Panel Developer
                                        @else
                                            Panel de Control
                                        @endif
                                    @endif
                                </span>
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1.2rem;">
                            <!-- Notifications Bell -->
                            <div id="notif-bell" class="notification-bell-container" style="cursor: pointer; position: relative; font-size: 1.5rem; display: flex; align-items: center;">
                                <i class="fa-solid fa-bell" style="color: #000;"></i>
                                <span class="notification-badge" id="notif-count" style="position: absolute; top: -5px; right: -5px; background: #ff4d4d; color: white; border-radius: 50%; width: 24px; height: 24px; font-size: 0.7rem; display: none; align-items: center; justify-content: center; border: 2px solid #fff; font-weight: 800; font-weight: 900;">0</span>
                                
                                <!-- Dropdown -->
                                <div id="notif-dropdown" class="notification-dropdown" onclick="event.stopPropagation()" style="display: none; position: absolute; top: calc(100% + 20px); right: -10px; width: 380px; max-width: 90vw; background: white; border: 1px solid rgba(0,0,0,0.1); border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); z-index: 9999; overflow: hidden;">
                                    <style>
                                        .notif-scroll::-webkit-scrollbar { width: 6px; }
                                        .notif-scroll::-webkit-scrollbar-track { background: transparent; }
                                        .notif-scroll::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 3px; }
                                    </style>
                                    <style>
                                        /* Smart Header Styles */
                                        .admin-unified-header {
                                            transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), opacity 0.3s ease;
                                        }
                                        .admin-unified-header.header-hidden {
                                            transform: translateY(-100%);
                                            opacity: 0;
                                            pointer-events: none;
                                        }
                                    </style>
                                    <div class="notification-header" style="padding: 18px 24px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background: white;">
                                        <span style="font-weight: 700; font-size: 1rem; font-family: 'Inter', sans-serif; letter-spacing: -0.5px; color: #1a1a1a;">Notificaciones</span>
                                        <button onclick="markAllRead()" style="background: transparent; border: none; color: #666; font-size: 0.8rem; cursor: pointer; font-weight: 500; font-family: 'Inter', sans-serif;" onmouseover="this.style.color='#000'" onmouseout="this.style.color='#666'">
                                            Marcar leídas
                                        </button>
                                    </div>
                                    <div id="notif-items" class="notif-scroll" style="max-height: 450px; overflow-y: auto; background: #fafafa;">
                                        <!-- Notifications here -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logout Button in Header -->
                            <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="button" 
                                    onclick="window.showConfirm('¿Cerrar sesión?', () => document.getElementById('logout-form-header').submit())"
                                    style="background: transparent; border: none; cursor: pointer; font-size: 1.2rem; color: #d32f2f; display: flex; align-items: center;"
                                    title="Cerrar Sesión">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </button>
                            </form>
                        </div>
                    </div>

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

                    <div class="workspace-container" style="width: 100%; max-width: 1400px; min-height: 80vh;">
                        @yield('content')
                    </div>
                </main>
            </div>
        @else
            <!-- Standard Top Nav Layout (Patient / Guest) -->
            <nav class="navbar-unificada no-select">
                <div class="navbar-content">
                    <div class="logo-text">
                        <span class="brand-title logo no-select">Lic. <span class="hide-on-mobile">Nazarena</span> De Luca</span>
                    </div>
                    
                    <div class="nav-unificada-links">
                        @auth
                            <!-- Mobile Menu Trigger -->
                            <button class="neobrutalist-btn mobile-only-btn" onclick="toggleMobileMenu()" style="padding: 0.6rem 1rem; background: var(--color-lila); margin-left: 0.5rem; display: none;">
                                <i class="fa-solid fa-bars" style="font-size: 1.2rem;"></i>
                            </button>

                            <!-- Desktop Nav Links -->
                            <div class="admin-nav-links desktop-menu">
                                <!-- Notifications Bell for Patients -->
                                <div id="notif-bell" class="notification-bell-container" style="cursor: pointer; position: relative; font-size: 1.2rem; margin-right: 1rem;">
                                    <i class="fa-solid fa-bell"></i>
                                    <span class="notification-badge" id="notif-count" style="display: none; position: absolute; top: -5px; right: -5px; background: #ff4d4d; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; border: 2px solid #000; font-weight: 800;">0</span>
                                    
                                    <!-- Dropdown -->
                                    <div id="notif-dropdown" class="notification-dropdown" style="display: none;">
                                        <div class="notification-header" style="padding: 10px 15px; border-bottom: 2px solid #000; display: flex; justify-content: space-between; align-items: center; background: #f8f8f8;">
                                            <span style="font-weight: 800; font-size: 0.85rem;">Notificaciones</span>
                                            <button onclick="markAllRead()" style="background: none; border: none; color: #555; font-size: 0.7rem; cursor: pointer; text-decoration: underline;">Limpiar todo</button>
                                        </div>
                                        <div id="notif-items" style="max-height: 300px; overflow-y: auto;">
                                            <!-- Notifications here -->
                                        </div>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="window.showConfirm('¿Querés cerrar sesión?', () => document.getElementById('logout-form').submit())" class="neobrutalist-btn" style="margin-left: 1rem; padding: 0.3rem 0.8rem; font-size: 0.85rem; background-color: #ff4d4d; color: white;">
                                    <i class="fa-solid fa-sign-out-alt" style="margin-right: 0.5rem;"></i> Salir
                                </a>



                            </div>
                        @endauth
                </div>
                
                <!-- Mobile Dropdown Menu -->
                <div id="mobile-nav-dropdown" class="mobile-nav-dropdown" style="display: none;">
                    @auth
                        @if(!$isPatient)
                            <a href="#agenda" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-day"></i> Agenda Hoy</a>
                            <a href="#pacientes" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-users"></i> Pacientes</a>
                            <a href="#pagos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-money-bill-wave"></i> Pagos</a>
                            <a href="#turnos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-alt"></i> Gestión Turnos</a>
                            <a href="#documentos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-file-alt"></i> Biblioteca</a>
                        @else
                            <a href="#booking" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-plus"></i> Reservar Turno</a>
                            <a href="#mis-turnos" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-calendar-check"></i> Mis Turnos</a>
                            <a href="#materiales" class="mobile-nav-item" onclick="toggleMobileMenu()"><i class="fa-solid fa-folder-open"></i> Mi Biblioteca</a>
                        @endif
                        <div style="border-top: 2px solid #eee; margin: 0.5rem 0;"></div>
                        <a href="javascript:void(0)" class="mobile-nav-item logout" onclick="window.showConfirm('¿Querés cerrar sesión?', () => document.getElementById('logout-form').submit())"><i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión</a>
                    @endauth
                </div>
            </nav>

            <main class="container mt-16" style="min-height: 80vh; padding-top: 3rem; padding-bottom: 3rem;">
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
                <div class="footer-links">
                    <a href="#">Privacidad</a>
                    <a href="#">Términos</a>
                    <span style="font-size: 0.7rem; color: #aaa;">v2.1 (Updated)</span>
                </div>

                @yield('content')
            </main>
        @endif
    @endif

    @yield('subnavigation')

    @if(auth()->check() && auth()->user()->rol == 'admin' && auth()->user()->email !== 'joacooodelucaaa16@gmail.com' && !request()->routeIs('login') && !request()->routeIs('register'))
        
        <!-- BOTÓN FLOTANTE (FAB) -->
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
                    <button onclick="sendGeminiMessage('Necesito soporte técnico')" style="background: #ffffff; border: 1px solid #e5e7eb; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: #4b5563; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'">¿Necesito ayuda?</button>
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
            // ... (keep existing scripts) ...
            
            window.sendGeminiMessage = async function(msg = null) {
                // 3. Chat Logic with AbortController
        let currentController = null;

        async function sendGeminiMessage(msg = null) {
            const input = document.getElementById('ai-input');
            const messages = document.getElementById('ai-messages');
            const btnIcon = document.querySelector('#ai-input + button i'); // The icon inside the button
            const btn = document.querySelector('#ai-input + button');
            
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
            userMsg.style.background = 'var(--color-celeste)'; 
            userMsg.style.color = '#000000';
            userMsg.style.padding = '0.8rem';
            userMsg.style.border = '2px solid #000';
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
            aiMsg.style.background = '#f0f7ff'; 
            aiMsg.style.color = '#000000';
            aiMsg.style.padding = '0.8rem';
            aiMsg.style.border = '2px solid #000';
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
                    // Already handled in the button click logic mainly, but ensuring UI consistency
                } else {
                    aiMsg.innerHTML = `<span style="color:red; font-size:0.85rem;">Error: ${error.message}</span>`;
                }
            } finally {
                // Reset Button Icon
                currentController = null;
                btnIcon.className = 'fa-solid fa-paper-plane';
                btnIcon.style.fontSize = '0.9rem';
            }
        }
        SendGeminiMessage = sendGeminiMessage; // Global alias
        }
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
                    opacity: 1; /* Force opacity */
                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem;"></i> Reportar un problema
                </button>
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

    <!-- Report Problem Modal -->
    <div id="report-modal-overlay" class="confirm-modal-overlay" style="display: none; z-index: 10002;">
        <div class="confirm-modal" style="width: 500px; max-width: 90%; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 2px solid #000; background: white;">
            <div class="confirm-modal-title" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 15px;">
                <span style="font-family: 'Syne', sans-serif; font-weight: 700;"><i class="fa-solid fa-bug" style="color: #ef4444; margin-right: 8px;"></i> Reportar Problema</span>
                <button onclick="closeReportModal()" style="background:none; border:none; cursor:pointer; font-size: 1.2rem;"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <div id="report-form-content">
                <div style="margin: 1rem 0;">
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem; font-family: 'Inter', sans-serif;">Describí brevemente qué pasó.</p>
                    <textarea id="report-desc" class="neobrutalist-input" rows="4" placeholder="Ej: No puedo guardar el turno cuando..." style="width: 100%; border: 2px solid #000; border-radius: 8px; padding: 10px; font-family: 'Inter', sans-serif;"></textarea>
                </div>
                <div class="confirm-modal-buttons" style="display: flex; gap: 10px; justify-content: center;">
                    <button class="neobrutalist-btn bg-rosa" onclick="closeReportModal()" style="border-radius: 8px;">Cancelar</button>
                    <button id="btn-send-report" class="neobrutalist-btn bg-celeste" onclick="submitReport()" style="border-radius: 8px;">Enviar Reporte</button>
                </div>
            </div>

            <!-- Success Message State -->
            <div id="report-success-msg" style="display: none; text-align: center; padding: 20px;">
                <i class="fa-solid fa-check-circle" style="font-size: 3rem; color: #388e3c; margin-bottom: 15px;"></i>
                <h3 style="font-family: 'Syne', sans-serif; font-size: 1.2rem; margin-bottom: 10px;">¡Gracias por avisar!</h3>
                <p style="font-family: 'Inter', sans-serif; color: #555; line-height: 1.5;">Ya se está encargando el técnico de arreglarlo. Muchas gracias y perdón las molestias.</p>
                <button class="neobrutalist-btn" onclick="closeReportModal()" style="margin-top: 20px; background: #000; color: white; border-radius: 8px;">Cerrar</button>
            </div>
            
            <!-- Loading State -->
            <div id="report-loading-msg" style="display: none; text-align: center; padding: 20px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: #000; margin-bottom: 15px;"></i>
                <p style="font-family: 'Inter', sans-serif; color: #555;">Enviando reporte...</p>
            </div>
        </div>
    </div>

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
            document.body.style.overflow = 'hidden'; // Bloquear scroll
        };

        confirmOk.addEventListener('click', () => {
            if (_pendingConfirmAction) _pendingConfirmAction();
            confirmOverlay.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restaurar scroll
        });

        confirmCancel.addEventListener('click', () => {
            confirmOverlay.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restaurar scroll
        });

        const bell = document.getElementById('notif-bell');
        const dropdown = document.getElementById('notif-dropdown');
        const notifItems = document.getElementById('notif-items');
        const notifCount = document.getElementById('notif-count');
        
        let isOpen = false;

        async function fetchNotifications() {
            try {
                const res = await fetch('{{ route('api.notifications.unread') }}');
                const data = await res.json();
                renderNotifications(data);
                updateCount(data.length);
            } catch (e) {
                console.error(e);
            }
        }

        function renderNotifications(notifs) {
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
                
                // Icon based on type
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
                item.onclick = () => markRead(n.id);
                notifItems.appendChild(item);
            });
        }

        function updateCount(count) {
            if (count > 0) {
                notifCount.style.display = 'flex';
                notifCount.innerText = count;
                bell.classList.add('shake');
            } else {
                notifCount.style.display = 'none';
                bell.classList.remove('shake');
            }
        }

        async function markRead(id) {
            await fetch('{{ route('api.notifications.markRead', '') }}/' + id, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
            fetchNotifications();
        }

        window.markAllRead = async function() {
            await fetch('{{ route('api.notifications.markAllRead') }}', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
            fetchNotifications();
        }

        if(bell) {
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
        }
        
        // Polling every 60s
        if(bell) setInterval(fetchNotifications, 60000);
        if(bell) fetchNotifications();

    </script>
    @endauth
</body>
</html>
