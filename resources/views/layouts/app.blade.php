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
                <aside class="admin-sidebar @if(isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] == 'true') collapsed @endif" id="admin-sidebar" style="z-index: 6001;">
                    <!-- Close button for mobile -->
                    <button onclick="window.toggleAdminSidebar()" class="sidebar-close-btn" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; z-index: 10; display: none; color: #000; padding: 0.5rem;">
                        <i class="fa-solid fa-xmark"></i>
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
                        @if(auth()->user()->email !== 'joacooodelucaaa16@gmail.com')
                            <a href="javascript:void(0)" class="sidebar-link" style="background-color: var(--color-rojo); color: white; width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" onclick="window.showConfirm('¿Estás segura de que querés cerrar sesión, Nazarena?', () => document.getElementById('logout-form').submit())">
                                <i class="fa-solid fa-sign-out-alt" style="line-height: 1;"></i>
                                <span class="sidebar-text" style="font-size: 0.9rem; line-height: 1;">Cerrar Sesión</span>
                            </a>
                        @endif
                    </div>
                </aside>

                <main class="main-content" style="padding-top: 0 !important;"> 
                    <!-- Unified Admin Header -->
                    <div class="admin-unified-header" style="display: flex; justify-content: space-between; align-items: center; margin-top: 0 !important; margin-bottom: 2rem; background: white; padding: 1rem 1.5rem; border-bottom: 3px solid #000; box-shadow: 0 2px 0px rgba(0,0,0,0.05); position: sticky; top: 0; left: 0; width: 100%; z-index: 5999; transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); opacity: 1; transform: translateY(0); box-sizing: border-box;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <button class="neobrutalist-btn" onclick="toggleAdminSidebar()" style="padding: 0.5rem 0.8rem; background: var(--color-lila); position: relative; z-index: 6002;">
                                <i class="fa-solid fa-bars" id="sidebar-toggle-icon"></i>
                            </button>
                            <span class="logo no-select" style="font-size: 1.6rem; font-weight: 500;">
                                @if(request()->routeIs('admin.home')) Inicio
                                @elseif(request()->routeIs('admin.agenda')) Agenda del Día
                                @elseif(request()->routeIs('admin.pacientes')) Pacientes
                                @elseif(request()->routeIs('admin.pagos')) Pagos y Cobros
                                @elseif(request()->routeIs('admin.turnos')) Gestión de Turnos
                                @elseif(request()->routeIs('admin.documentos')) Biblioteca
                                @elseif(request()->routeIs('admin.waitlist')) Lista de Espera
                                @elseif(request()->routeIs('admin.configuracion')) Disponibilidad
                                @elseif(request()->routeIs('admin.historial')) Historial
                                @elseif(request()->routeIs('admin.developer')) Panel Developer
                                @else Lic. Nazarena @endif
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1.2rem;">
                            <!-- Notifications Bell -->
                            <div id="notif-bell" class="notification-bell-container" style="cursor: pointer; position: relative; font-size: 3rem; display: flex; align-items: center;">
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
                        <span style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">Modelo: Gemma 3</span>
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
                const input = document.getElementById('ai-input');
                const messages = document.getElementById('ai-messages');
                
                const messageText = msg || input.value.trim();
                
                if (!messageText) return;

                // 1. Mostrar mensaje del usuario (Minimalist)
                const userMsg = document.createElement('div');
                userMsg.style.marginBottom = '0.8rem';
                userMsg.style.background = '#3b82f6'; // Azul vibrante
                userMsg.style.color = '#ffffff';
                userMsg.style.padding = '0.75rem 1rem';
                userMsg.style.borderRadius = '18px 18px 0 18px';
                userMsg.style.alignSelf = 'flex-end';
                userMsg.style.maxWidth = '85%';
                userMsg.style.lineHeight = '1.5';
                userMsg.style.fontSize = '0.95rem';
                userMsg.style.boxShadow = '0 2px 4px rgba(59, 130, 246, 0.1)';
                userMsg.innerHTML = messageText; // No "Vos:", directo texto
                messages.appendChild(userMsg);

                if (!msg) input.value = '';
                messages.scrollTop = messages.scrollHeight;

                // 2. Crear burbuja de respuesta vacía (Minimalist)
                const aiMsg = document.createElement('div');
                aiMsg.style.marginBottom = '0.8rem';
                aiMsg.style.background = '#f3f4f6'; // Gris muy suave
                aiMsg.style.color = '#1f2937';
                aiMsg.style.padding = '0.75rem 1rem';
                aiMsg.style.borderRadius = '18px 18px 18px 0';
                aiMsg.style.alignSelf = 'flex-start';
                aiMsg.style.maxWidth = '85%';
                aiMsg.style.lineHeight = '1.5';
                aiMsg.style.fontSize = '0.95rem';
                aiMsg.innerHTML = '...'; 
                messages.appendChild(aiMsg);
                messages.scrollTop = messages.scrollHeight;

                // ... (Streaming logic same as before, just updating message content) ...
                let fullResponse = "";
                try {
                     const response = await fetch('{{ route('admin.ai.chat') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: messageText })
                    });

                    if (!response.ok) throw new Error('Error en la conexión con la IA');

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();

                    aiMsg.innerHTML = ''; // Limpiar "..."

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;
                        const chunk = decoder.decode(value, { stream: true });
                        fullResponse += chunk;
                        aiMsg.innerHTML = fullResponse.replace(/\n/g, '<br>');
                        messages.scrollTop = messages.scrollHeight;
                    }
                } catch (error) {
                    aiMsg.innerHTML = `<span style="color:red; font-size:0.85rem;">Error: ${error.message}</span>`;
                }
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
                
                lastNotifCount = data.length;

                if (data && data.length > 0) {
                    if (count) {
                        count.innerText = data.length;
                        count.style.display = 'flex';
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
                    if (count) count.style.display = 'none';
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
        
        // Admin Sidebar Toggle Logic
        window.toggleAdminSidebar = function() {
            const sidebar = document.getElementById('admin-sidebar');
            const container = document.getElementById('admin-layout-container');
            const icon = document.getElementById('sidebar-toggle-icon');
            
            if (!sidebar) {
                console.error("Sidebar element not found!");
                return;
            }
            
            const isMobile = window.innerWidth <= 1024;
            
            if (isMobile) {
                sidebar.classList.toggle('active');
                if (container) {
                    container.classList.toggle('sidebar-open', sidebar.classList.contains('active'));
                    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
                }
            } else {
                sidebar.classList.toggle('collapsed');
                if (container) {
                    container.classList.toggle('sidebar-open', !sidebar.classList.contains('collapsed'));
                }
                const isCollapsed = sidebar.classList.contains('collapsed');
                document.cookie = `sidebar_collapsed=${isCollapsed}; path=/; max-age=${60 * 60 * 24 * 30}`;
            }

            // Sync icon state
            if (icon) {
                const isOpen = (isMobile && sidebar.classList.contains('active')) || 
                                (!isMobile && !sidebar.classList.contains('collapsed'));
                icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
            }
        };

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

            window.sendGeminiMessage = async function(msg = null) {
                const input = document.getElementById('ai-input');
                const messages = document.getElementById('ai-messages');
                
                const messageText = msg || input.value.trim();
                
                if (!messageText) return;

                // 1. Mostrar mensaje del usuario
                const userMsg = document.createElement('div');
                userMsg.style.marginBottom = '0.5rem';
                userMsg.style.backgroundColor = 'var(--color-celeste)';
                userMsg.style.padding = '0.8rem';
                userMsg.style.border = '2px solid #000';
                userMsg.style.borderRadius = '12px 12px 0 12px';
                userMsg.style.alignSelf = 'flex-end';
                userMsg.style.maxWidth = '85%';
                userMsg.style.lineHeight = '1.4';
                userMsg.innerHTML = `<strong>Vos:</strong> ${messageText}`;
                messages.appendChild(userMsg);

                if (!msg) input.value = '';
                messages.scrollTop = messages.scrollHeight;

                // 2. Crear burbuja de respuesta vacía (para streaming)
                const aiMsg = document.createElement('div');
                aiMsg.style.marginBottom = '0.5rem';
                aiMsg.style.backgroundColor = '#f0f7ff';
                aiMsg.style.padding = '0.8rem';
                aiMsg.style.border = '2px solid #000';
                aiMsg.style.borderRadius = '12px 12px 12px 0';
                aiMsg.style.alignSelf = 'flex-start';
                aiMsg.style.maxWidth = '85%';
                aiMsg.style.lineHeight = '1.4';
                aiMsg.innerHTML = `<strong>IA:</strong> `; // Inicio
                messages.appendChild(aiMsg);
                messages.scrollTop = messages.scrollHeight;

                let fullResponse = "";

                try {
                    // 3. Petición al Backend (Stream)
                    const response = await fetch('{{ route('admin.ai.chat') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: messageText })
                    });

                    if (!response.ok) throw new Error('Error en la conexión con la IA');

                    // 4. Leer el stream
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;

                        const chunk = decoder.decode(value, { stream: true });
                        // Ollama/PHP might send plain text or multiple JSON objects.
                        // Assuming the PHP controller echoes raw text chunks for simplicity based on the reviewed code.
                        // If it receives JSON chunks, we might need to parse.
                        // The reviewed AIController echoes raw data: echo $data;
                        
                        fullResponse += chunk;
                        aiMsg.innerHTML = `<strong>IA:</strong> ${fullResponse.replace(/\n/g, '<br>')}`;
                        messages.scrollTop = messages.scrollHeight;
                    }

                } catch (error) {
                    aiMsg.innerHTML += `<br><span style="color:red; font-size:0.8rem;">(Error: ${error.message})</span>`;
                    console.error(error);
                }
            }

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

    <!-- Report Problem Modal -->
    <div id="report-modal-overlay" class="confirm-modal-overlay" style="display: none; z-index: 12000;">
        <div class="confirm-modal" style="width: 500px; max-width: 90%; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div class="confirm-modal-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fa-solid fa-bug" style="color: #ef4444; margin-right: 8px;"></i> Reportar Problema</span>
                <button onclick="document.getElementById('report-modal-overlay').style.display = 'none'" style="background:none; border:none; cursor:pointer;"><i class="fa-solid fa-times"></i></button>
            </div>
            <div style="margin: 1rem 0;">
                <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">Describí brevemente qué pasó. Adjuntaremos datos técnicos automáticamente.</p>
                <textarea id="report-desc" class="neobrutalist-input" rows="4" placeholder="Ej: No puedo guardar el turno cuando..." style="width: 100%;"></textarea>
                <div id="report-status" style="margin-top: 5px; font-size: 0.8rem; color: #666;"></div>
            </div>
            <div class="confirm-modal-buttons">
                <button class="neobrutalist-btn bg-rosa" onclick="document.getElementById('report-modal-overlay').style.display = 'none'">Cancelar</button>
                <button class="neobrutalist-btn bg-celeste" onclick="submitReport()">Enviar Reporte</button>
            </div>
        </div>
    </div>

    <script>
        // 1. Error Capture
        window.onerror = function(message, source, lineno, colno, error) {
            fetch('{{ route('api.logs.store') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message,
                    stack: error ? error.stack : null,
                    url: window.location.href,
                    component: 'Global'
                })
            });
        };

        // 2. Report Modal Logic
        function openReportModal() {
            document.getElementById('report-modal-overlay').style.display = 'flex';
            document.getElementById('report-desc').focus();
        }

        async function submitReport() {
            const desc = document.getElementById('report-desc').value;
            const statusBtn = document.getElementById('report-status');
            
            if(!desc.trim()) {
                alert('Por favor describí el problema.');
                return;
            }

            statusBtn.innerText = 'Enviando...';

            try {
                const res = await fetch('{{ route('api.tickets.store') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        description: desc,
                        metadata: {
                            url: window.location.href,
                            userAgent: navigator.userAgent,
                            screen: `${window.screen.width}x${window.screen.height}`
                        }
                    })
                });

                if(res.ok) {
                    alert('¡Reporte enviado! Gracias por avisarnos.');
                    document.getElementById('report-modal-overlay').style.display = 'none';
                    document.getElementById('report-desc').value = '';
                    statusBtn.innerText = '';
                } else {
                    throw new Error('Error al enviar');
                }
            } catch (e) {
                alert('Hubo un error enviando el reporte. Intentalo de nuevo.');
                statusBtn.innerText = 'Error.';
                console.error(e);
            }
        }
    </script>
</body>
</html>
