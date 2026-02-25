<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inicio - Lic. Nazarena</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
    <link rel="stylesheet" href="{{ asset('css/whatsapp_widget.css') }}">
    <style>
        /* Body Fix for Full Width Footer */
        body {
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        /* Prevent Auto-Zoom on Mobile Inputs */
        @media screen and (max-width: 768px) {
            /* Prevent Auto-Zoom on Mobile Inputs */
            input, select, textarea, .neobrutalist-input, .neobrutalist-btn {
                font-size: 16px !important;
                -webkit-text-size-adjust: 100% !important; /* iOS */
                touch-action: manipulation;                /* reduce zoom agresivo */
            }
            
            /* 1. Máximo ancho para que no fuerce zoom horizontal */
            .confirm-modal {
                max-width: 95vw !important;
                width: 95vw !important;
                margin: 10px auto !important;
                transform: translate3d(0,0,0);
                will-change: transform;
            }

            /* 3. Evitar que el modal se mueva/vibre con transform hack */
            #report-modal-overlay {
                will-change: transform;
                transform: translate3d(0,0,0);             /* fuerza GPU + estabilidad */
                backface-visibility: hidden;
            }
            
            /* 4. Prevenir vibración del select en el modal de reporte */
            #report-modal-overlay .confirm-modal {
                contain: layout style;
                transform: translate3d(0,0,0);
                will-change: transform;
            }
            
            #report-modal-overlay select[name="subject"] {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }
        }
        
        /* Universal Header Visibility Fix */
        .universal-header {
            z-index: 10000 !important; /* Always on top of everything except modals */
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            position: fixed !important;
            display: flex !important;   /* Restore FLEX to keep alignment */
            align-items: center !important;
            height: 70px !important;    /* Force height consistency */
        }

        @media (max-width: 768px) {
            .universal-header {
                padding: 0.8rem 1rem !important;
            }
            .app-main-wrapper {
                padding-top: 70px !important;
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Prevent background scroll when modal is open */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 15px; /* Evita que la página vibre al ocultar el scroll */
        }

        /* Ajuste automático del margen del contenido (Global) */
        @media (min-width: 1025px) {
            /* Si la sidebar está abierta */
            .admin-layout.sidebar-open .app-main-wrapper {
                margin-left: 280px !important;
                transition: margin-left 0.3s ease;
            }

            /* Si la sidebar está colapsada */
            .admin-sidebar.collapsed ~ .app-main-wrapper {
                margin-left: 100px !important;
                transition: margin-left 0.3s ease;
            }
        }

        /* Minimalist Modal Redesign */
        .confirm-modal-minimal {
            background: white !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden !important;
        }
        .confirm-modal-title-minimal {
            background: #fdfdfd !important;
            color: #1a1a1a !important;
            border-bottom: 1px solid #f0f0f0 !important;
            padding: 1.25rem 1.5rem !important;
            font-weight: 800 !important;
            font-size: 1.1rem !important;
        }

        /* Prevent shake on select focus */
        select, input, textarea {
            font-size: 16px !important;
            transform: none !important;
        }
        select:focus {
            outline: none !important;
            border-color: var(--color-celeste) !important;
            transform: none !important;
        }
        /* Main Content Wrapper */
        .app-main-wrapper {
            transition: margin-left 0.3s ease;
            padding-top: 80px; /* Header height + spacing */
            padding-bottom: 2rem;
            flex: 1; /* Pushes footer to bottom if content is short */
            display: flex;
            flex-direction: column;
            width: 100%;
            box-sizing: border-box;
        }
        /* Static Sidebar Styles */
        .nav-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 80px;
            background: #f8fafc; /* Softer pastel blue-gray */
            border-right: 3px solid #000;
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0; /* Reduced padding */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10001; /* Above header */
            overflow: hidden;
        }

        /* Only allow hover expansion on desktop */
        @media (min-width: 1025px) {
            .nav-sidebar:hover {
                width: 240px;
            }
            .nav-sidebar:hover .nav-text {
                opacity: 1;
            }
        }

        /* Desktop Only Hover */
        @media (min-width: 1025px) {
            .nav-sidebar:hover {
                width: 240px;
            }
            .nav-sidebar:hover .sidebar-header {
                justify-content: flex-start;
                padding-left: 25px;
            }
        }

        .nav-logo img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #000;
            object-fit: cover;
        }

        .nav-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-grow: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            text-decoration: none;
            color: #000;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            transition: background 0.2s, transform 0.2s;
            white-space: nowrap;
        }

        .nav-link i {
            font-size: 1.5rem;
            min-width: 30px;
            text-align: center;
        }

        .nav-text {
            margin-left: 20px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .nav-sidebar:hover .nav-text {
            opacity: 1;
        }

        @media (max-width: 1024px) {
            .nav-sidebar.open .nav-text, .nav-sidebar.active .nav-text {
                opacity: 1 !important;
            }
        }

        .nav-link:hover {
            background: #f5f5f5;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: #e0f2fe; /* Light pastel blue */
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        .sidebar-footer {
            /* padding-top: 1rem; */
            /* border-top: 1px solid #eee; */
        }

        .logout-btn {
            color: #ff4d4d !important;
        }

        @media (min-width: 769px) {
            .app-main-wrapper {
                /* padding-left: 80px; */
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                box-sizing: border-box;
            }
            .workspace-container {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 2rem;
            }
        }

        /* Confirmation Modals (Logout & Security) */
        .security-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.85);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 20000;
            backdrop-filter: blur(5px);
        }

        /* Notificaciones */
    .notification-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        width: 380px !important;
        background: white;
        border: 3px solid #000;
        box-shadow: 6px 6px 0px #000 !important; /* Static shadow */
        z-index: 1000;
        max-height: 80vh;
        overflow-y: hidden; /* Header fixed, items scroll */
        overflow-x: hidden !important;
        border-radius: 0 !important;
    }
    
    /* Remove any hover effect that changes shadow or scale */
    .notification-dropdown:hover, .notification-item:hover {
        box-shadow: 6px 6px 0px #000 !important;
        transform: none !important;
    }

    .notification-items-container {
        max-height: 400px;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        /* Custom Scrollbar */
        scrollbar-width: thin;
        scrollbar-color: #000 #f1f1f1;
    }
        .security-modal {
            background: white;
            border: 5px solid #000;
            box-shadow: 10px 10px 0px #000;
            padding: 2.5rem;
            max-width: 450px;
            width: 90%;
            text-align: center;
            border-radius: 20px;
        }

        .security-modal h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .security-modal p {
            font-weight: 600;
            color: #444;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        
    </style>
    <style>
        html { 
            height: 100%; 
        }
        /* Mobile Select Fix */
        select {
            background-color: #fff !important;
            color: #000;
            border: 3px solid #000;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        select:focus {
            outline: none;
            background-color: #fff !important;
        }
        
        /* Notification Items Cursor Fix */
        .notification-item {
            cursor: pointer !important;
            overflow-x: hidden !important;
        }
        
        .notification-dropdown {
            cursor: default !important;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Manrope', sans-serif; background-color: var(--color-celeste); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; width: 100%;">
    <script>
        // Simplified Sidebar Logic - Always Open on Desktop
            window.toggleAdminSidebar = function() {
                    const sidebar = document.getElementById('admin-sidebar');
                    const overlay = document.getElementById('sidebar-overlay');
                    
            if (window.innerWidth <= 1024) {
                        sidebar.classList.toggle('active');
                        if (overlay) overlay.classList.toggle('active');
                }
            };

            // Mobile Select Fix - JS Injection
            document.addEventListener('DOMContentLoaded', function() {
                if (window.innerWidth <= 768) {
                    const selectElement = document.getElementById('report-subject');
                    if (selectElement) {
                        // Force styles directly
                        selectElement.style.cssText = `
                            background-color: white !important;
                            color: black !important;
                            -webkit-appearance: none !important;
                            appearance: none !important;
                            border: 3px solid black !important;
                            border-radius: 10px !important;
                            padding: 0 12px !important;
                            width: 100% !important;
                            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") !important;
                            background-repeat: no-repeat !important;
                            background-position: right 12px center !important;
                            background-size: 16px 16px !important;
                        `;
                        
                        // Force options
                        Array.from(selectElement.options).forEach(opt => {
                            opt.style.backgroundColor = 'white';
                            opt.style.color = 'black';
                        });
                        
                        selectElement.addEventListener('touchstart', function() {
                            this.style.backgroundColor = 'white';
                            this.style.color = 'black';
                        });
                    }
                }
            });
    </script>

    @auth
        @php
            $isAdmin = auth()->user()->rol == 'admin';
            $isPatient = auth()->user()->rol == 'paciente';
        @endphp
    @endauth

    @php
        $isAuthPage = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.*');
    @endphp

    @if($isAuthPage)
        <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
            @yield('content')
        </div>
    @else
            @if(isset($isAdmin) && $isAdmin && !View::hasSection('hide_sidebar'))
            <div class="admin-layout" style="display: flex; flex: 1; width: 100%;">
                

                <!-- Static Sidebar -->
                <nav class="nav-sidebar">
                    <!-- Logo at the very top -->
                    <div class="sidebar-logo" style="padding: 1rem; display: flex; justify-content: center; align-items: center; margin-bottom: 0.5rem;">
                        <a href="{{ route('admin.home') }}" style="display: flex; justify-content: center; align-items: center; text-decoration: none;">
                            <img src="{{ asset('img/logo-nuevo.png') }}" alt="Logo" style="height: 80px; width: auto;">
                        </a>
                    </div>
                    
                    <div class="sidebar-header" style="padding: 1rem; border-bottom: 2px solid #eee; margin-bottom: 1rem; display: flex; justify-content: center; flex-direction: column; align-items: center; gap: 10px;">
                        <!-- Branding Image -->
                         <div class="nav-brand-img" style="width: 50px; height: 50px; border-radius: 50%; border: 3px solid #000; overflow: hidden; background: white; box-shadow: 3px 3px 0px #000;">
                            <img src="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}" alt="Nazarena" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <style>
                            @media (max-width: 1024px) {
                                .nav-brand-img {
                                    display: none !important;
                                }
                                .sidebar-header {
                                    border-bottom: none !important;
                                    padding: 0 !important;
                                    margin: 0 !important;
                                }
                                
                                /* Mobile sidebar full screen with overlay */
                                .nav-sidebar {
                                    position: fixed !important;
                                    left: -100% !important;
                                    top: 0 !important;
                                    width: 70% !important;
                                    max-width: 300px !important;
                                    height: 100vh !important;
                                    background: #f8fafc !important;
                                    z-index: 999999 !important;
                                    transition: left 0.3s ease-in-out !important;
                                    overflow-y: auto !important;
                                    box-shadow: 5px 0 15px rgba(0,0,0,0.3) !important;
                                }
                                
                                .nav-sidebar.open {
                                    left: 0 !important;
                                }
                                
                                /* Close button */
                                .sidebar-close-btn {
                                    display: flex !important;
                                }
                                
                                /* Sidebar logo on mobile */
                                .sidebar-logo {
                                    padding: 1.5rem 1rem !important;
                                    border-bottom: 2px solid #000 !important;
                                }
                            }
                            
                            /* Close button - hidden on desktop */
                            .sidebar-close-btn {
                                display: none;
                                position: absolute;
                                top: 1rem;
                                right: 1rem;
                                background: #dc2626;
                                color: white;
                                border: 2px solid #000;
                                width: 40px;
                                height: 40px;
                                border-radius: 8px;
                                cursor: pointer;
                                align-items: center;
                                justify-content: center;
                                font-size: 1.2rem;
                                box-shadow: 3px 3px 0px #000;
                                z-index: 999999;
                            }
                            
                            .sidebar-close-btn:active {
                                transform: translate(2px, 2px);
                                box-shadow: 1px 1px 0px #000;
                            }
                            
                            /* Overlay */
                            .sidebar-overlay {
                                display: none;
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100vw;
                                height: 100vh;
                                background: rgba(0, 0, 0, 0.5);
                                z-index: 999998;
                            }
                            
                            .sidebar-overlay.active {
                                display: block;
                            }
                        </style>
                    </div>
                    
                    <!-- Close button for mobile -->
                    <button class="sidebar-close-btn" onclick="window.toggleAdminSidebar()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <div class="nav-items" style="flex: 1; display: flex; flex-direction: column;">
                        <a href="{{ route('admin.pacientes') }}" class="nav-link {{ request()->routeIs('admin.pacientes') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-group"></i>
                            <span class="nav-text">Pacientes</span>
                        </a>
                        <a href="{{ route('admin.agenda') }}" class="nav-link {{ request()->routeIs('admin.agenda') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span class="nav-text">Agenda</span>
                        </a>
                        <a href="{{ route('admin.waitlist') }}" class="nav-link {{ request()->routeIs('admin.waitlist') ? 'active' : '' }}">
                            <i class="fa-solid fa-users-line"></i>
                            <span class="nav-text">Lista de Espera</span>
                        </a>
                        <a href="{{ route('admin.finanzas') }}" class="nav-link {{ request()->routeIs('admin.finanzas') ? 'active' : '' }}">
                            <i class="fa-solid fa-wallet"></i>
                            <span class="nav-text">Finanzas</span>
                        </a>
                         <a href="{{ route('admin.configuracion') }}" class="nav-link {{ request()->routeIs('admin.configuracion') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i>
                            <span class="nav-text">Configuración</span>
                        </a>
                         <a href="{{ route('admin.historial') }}" class="nav-link {{ request()->routeIs('admin.historial') ? 'active' : '' }}">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span class="nav-text">Acciones</span>
                        </a>
                    </div>

                    <div class="sidebar-footer">
                        <a href="javascript:void(0)" onclick="openLogoutModal()" class="nav-link logout-btn">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="nav-text">Salir</span>
                        </a>
                    </div>
                </nav>

                <main class="app-main-wrapper" style="margin-left: 280px; padding-top: 100px !important; width: calc(100% - 280px); min-height: 100vh; background-color: var(--color-celeste);">
 
                    <!-- Universal Header (Restored) -->
                    <header class="universal-header" style="background: white; border-bottom: var(--border-thick); position: fixed; top: 0; left: 0; right: 0; z-index: 7000; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <div class="header-content" style="padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; width: 100%; box-sizing: border-box; max-width: 100vw;">
                            <!-- LEFT SIDE: Toggle (Admin) + Brand -->
                            <div class="header-left" style="display: flex; align-items: center; gap: 1rem;">
                                <button id="admin-sidebar-toggle" class="admin-sidebar-toggle-btn" onclick="window.toggleAdminSidebar()" style="display: none; background: transparent; border: none; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; color: rgb(51, 51, 51); position: relative; z-index: 999999;">
                                    <i class="fa-solid fa-bars"></i>
                                </button>
                <style>
                                    @media (max-width: 1024px) {
                                        #admin-sidebar-toggle {
                                            display: flex !important;
                                        }
                                        /* Hide brand on mobile sidebar view per request */
                                        /* .nav-brand-img { display: none !important; } */
                                    }
                                </style>

                                <style>
                                    @media (min-width: 1025px) {
                                        .logo-shift {
                                            margin-left: 120px;
                                        }
                                    }
                                </style>
                                <a href="{{ route('admin.home') }}" class="logo-text logo-shift" style="text-decoration: none; display: flex; align-items: center; gap: 0.8rem;">
                                    <span class="brand-title logo no-select" style="font-size: 1.4rem;">Lic. <span class="hide-on-mobile">Nazarena</span> De Luca</span>
                                </a>
                            </div>

                            <!-- RIGHT SIDE: Tools -->
                            <div class="header-right" style="display: flex; align-items: center; gap: 1.5rem;">
                                @auth
                                    <div class="header-navbar" style="display: flex; align-items: center; gap: 1.2rem;">
                                        <!-- Notifications Bell (Increased gap) -->
                                        <div id="notif-bell" class="notification-bell-container" onclick="toggleNotifications(event)" style="cursor: pointer; position: relative; font-size: 1.8rem; display: flex; align-items: center; z-index: 10001; -webkit-tap-highlight-color: transparent; margin-left: 1rem;">
                                            <i class="fa-solid fa-bell" style="color: #000;"></i>
                                            <span class="notification-badge" id="notif-count" style="position: absolute; top: 2px; right: 2px; background: #ff4d4d; color: white; border-radius: 50%; min-width: 17px; height: 17px; font-size: 0.6rem; display: none; align-items: center; justify-content: center; border: 1.5px solid #fff; font-weight: 800; padding: 0 4px;">0</span>
                                            
                                            <!-- Dropdown -->
                                            <div id="notif-dropdown" class="notification-dropdown" onclick="event.stopPropagation()" style="display: none; position: absolute; top: calc(100% + 15px); right: -10px; width: 360px; max-width: 90vw; background: white; border-radius: 16px; box-shadow: 0 16px 40px rgba(0,0,0,0.18), 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; z-index: 10005; border: 3px solid #000; cursor: default;">
                                                <div class="notification-header" style="padding: 1rem 1.2rem; border-bottom: 3px solid #000; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
                                                    <span style="font-weight: 800; font-size: 1.05rem; color: #111; font-family: 'Manrope', sans-serif;">Notificaciones</span>
                                                    <button onclick="markAllRead()" style="background: var(--color-celeste); border: 2px solid #000; color: #000; font-size: 0.75rem; font-weight: 700; cursor: pointer; padding: 0.4rem 0.8rem; border-radius: 8px; box-shadow: 2px 2px 0px #000;">
                                                        Leer todas
                                                    </button>
                                                </div>
                                                <div id="notif-items" class="notification-items-container" style="max-height: 420px; overflow-y: auto;">
                                                    <div class="notification-empty" style="padding: 3rem 1.5rem; text-align: center; color: #888;">
                                                        <div class="icon-placeholder" style="color: #bbb;"><i class="fa-solid fa-bell"></i></div>
                                                        <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem; font-size: 1.1rem;">Todo al día</div>
                                                        <div style="font-size: 0.9rem; line-height: 1.4; color: #666;">No tenés notificaciones nuevas por ahora.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Logout (Restored Desktop Form) -->
                                        <form action="{{ route('logout') }}" method="POST" style="display: inline;" class="header-logout-form">
                                            @csrf
                                            <button type="button" 
                                                onclick="openLogoutModal()"
                                                class="neobrutalist-btn"
                                                style="background: var(--color-rojo); color: #fff; border: 3px solid #000; box-shadow: 3px 3px 0px #000; padding: 0.4rem 1rem; font-weight: 700; display: none; link-style: none; align-items: center; gap: 0.5rem;"
                                                title="Cerrar Sesión">
                                                <i class="fa-solid fa-right-from-bracket"></i>
                                                <span>Salir</span>
                                            </button>
                                        </form>
                                         <style>
                                            @media (max-width: 1024px) {
                                                .header-logout-form button {
                                                    display: flex !important;
                                                    padding: 0.4rem !important;
                                                }
                                                .header-logout-form button span {
                                                    display: none !important;
                                                }
                                                /* Force bell visibility */
                                                #notif-bell, #patient-notif-bell {
                                                    display: flex !important;
                                                    visibility: visible !important;
                                                    opacity: 1 !important;
                                                }
                                            }
                                        </style>
                                   </div>
                                @endauth
                            </div>
                        </div>
                    </header>

                    <!-- Global Flash Messages (Toast Style) -->
                    <div id="global-flash-messages" style="position: fixed; top: 100px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; max-width: 90vw; width: 400px; pointer-events: none;">
                        @if(session('success'))
                            <div class="neobrutalist-flash success" style="pointer-events: auto; background: #bbf7d0; border: 3px solid #000; padding: 1rem; box-shadow: 4px 4px 0px #000; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; animation: slideInRight 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-check-circle" style="font-size: 1.4rem;"></i>
                                    <span style="font-weight: 700; font-size: 0.95rem;">{{ session('success') }}</span>
                                </div>
                                <button onclick="this.parentElement.remove()" style="background: transparent; border: none; font-weight: 900; font-size: 1.5rem; cursor: pointer; padding: 0 0.5rem; line-height: 1;">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="neobrutalist-flash error" style="pointer-events: auto; background: #fca5a5; border: 3px solid #000; padding: 1rem; box-shadow: 4px 4px 0px #000; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; animation: slideInRight 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.4rem;"></i>
                                    <span style="font-weight: 700; font-size: 0.95rem;">{{ session('error') }}</span>
                                </div>
                                <button onclick="this.parentElement.remove()" style="background: transparent; border: none; font-weight: 900; font-size: 1.5rem; cursor: pointer; padding: 0 0.5rem; line-height: 1;">&times;</button>
                            </div>
                        @endif
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const flashes = document.querySelectorAll('.neobrutalist-flash');
                            if (flashes.length > 0) {
                                setTimeout(function() {
                                    flashes.forEach(flash => {
                                        flash.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                        flash.style.opacity = '0';
                                        flash.style.transform = 'translateX(100%)';
                                        setTimeout(() => flash.remove(), 500);
                                    });
                                }, 10000); // 10 segundos
                            }
                        });
                    </script>

                    <style>
                        @keyframes slideInRight {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                    </style>


                    <div class="workspace-container" style="width: 100%; max-width: 1400px; min-height: 80vh; padding-top: 20px; margin: 0 auto; display: flex; flex-direction: column;">
                        @yield('content')
                    </div>
                </main>
            </div>
                            @elseif(View::hasSection('hide_sidebar'))
                                <main style="width: 100%; min-height: 100vh;">
                                    @yield('content')
                                </main>
                            @else
            <!-- Patient Header -->
            @auth
                <header class="universal-header" style="background: white; border-bottom: var(--border-thick); position: fixed; top: 0; left: 0; right: 0; z-index: 7000; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div class="header-content" style="padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; width: 100%; box-sizing: border-box; max-width: 1400px; margin: 0 auto;">
                        <!-- LEFT SIDE: Brand -->
                        <div class="header-left" style="display: flex; flex: 1; align-items: center; gap: 1rem;">
                            <a href="{{ route('patient.dashboard') }}" style="text-decoration: none;">
                                <div class="logo-text" style="margin-left: 0; cursor: pointer;">
                                    <span class="brand-title logo no-select" style="font-size: 1.4rem;">Lic. <span class="hide-on-mobile">Nazarena</span> De Luca</span>
                                </div>
                            </a>
                        </div>

                        <!-- CENTER: PC Toolbar -->
                        <nav class="header-toolbar hide-on-mobile" style="display: flex; align-items: center; gap: 1.5rem; background: #f8f9fa; border: 3px solid #000; border-radius: 50px; padding: 0.4rem 1.5rem; box-shadow: 4px 4px 0px #000;">
                            <a href="#reservar" onclick="event.preventDefault(); document.querySelector('{{ $fixedReservation ? '#fixed-reservation-status' : '.booking-column' }}')?.scrollIntoView({behavior:'smooth'})" style="text-decoration: none; color: #000; font-weight: 800; font-size: 0.9rem; text-transform: uppercase; transition: all 0.2s;" class="toolbar-link">{{ $fixedReservation ? 'Mi Reserva' : 'Reservar' }}</a>
                            <a href="#mis-turnos" onclick="event.preventDefault(); document.getElementById('mis-turnos-section')?.scrollIntoView({behavior:'smooth'})" style="text-decoration: none; color: #000; font-weight: 800; font-size: 0.9rem; text-transform: uppercase; transition: all 0.2s;" class="toolbar-link">Mis Turnos</a>
                            <a href="#documentos" onclick="event.preventDefault(); document.getElementById('documentos')?.scrollIntoView({behavior:'smooth'})" style="text-decoration: none; color: #000; font-weight: 800; font-size: 0.9rem; text-transform: uppercase; transition: all 0.2s;" class="toolbar-link">Documentos</a>
                        </nav>
                        <style>
                            .toolbar-link:hover { color: var(--color-rojo); transform: translateY(-1px); }
                            @media (max-width: 1024px) {
                                .header-toolbar { display: none !important; }
                            }
                        </style>
                            <style>
                                @media (max-width: 768px) {
                                    .header-left .logo-text {
                                        margin-left: -0.5rem !important;
                                    }
                                    .header-left .brand-title.logo {
                                        font-size: 1.4rem !important;
                                        max-width: 220px; /* Increased max-width */
                                        display: inline-block;
                                        white-space: nowrap;
                                        overflow: hidden;
                                        text-overflow: ellipsis; /* Truncate with dots */
                                    }
                                    .header-left .hide-on-mobile {
                                        display: inline; /* Let truncation handle it */
                                    }
                                    .sidebar-trigger-btn {
                                        display: block !important; /* Show trigger on mobile */
                                    }
                                    /* Body padding removed (sidebar is overlay) */
                                    body { padding-bottom: 0 !important; }
                                    #whatsapp-widget-container { bottom: 20px !important; }
                                    
                            </style>
                        
                            <!-- RIGHT SIDE: Notifications, Logout -->
                            <div class="header-right" style="display: flex; flex: 1; align-items: center; gap: 1.5rem; justify-content: flex-end;">
                               <div class="header-navbar" style="display: flex; align-items: center; gap: 1.2rem;">
                                <!-- Mobile Sidebar Trigger (Top-Down Menu) -->
                                <button onclick="togglePatientMenu()" class="sidebar-trigger-btn" style="background: var(--color-amarillo); border: 3px solid #000; padding: 0.5rem; border-radius: 10px; cursor: pointer; box-shadow: 3px 3px 0px #000; display: none;">
                                    <i class="fa-solid fa-bars" style="font-size: 1.2rem; color: #000;"></i>
                                </button>
                                <!-- Notifications Bell -->
                                <div id="patient-notif-bell" class="notification-bell-container" onclick="toggleNotifications(event, 'patient-notif-dropdown')" style="cursor: pointer; position: relative; font-size: 1.8rem; display: flex; align-items: center; z-index: 10001;">
                                        <i class="fa-solid fa-bell" style="color: #000;"></i>
                                    <span class="notification-badge" id="patient-notif-count" style="position: absolute; top: 2px; right: 2px; background: #ff4d4d; color: white; border-radius: 50%; min-width: 17px; height: 17px; font-size: 0.6rem; display: {{ (isset($notifications) && $notifications->count() > 0) ? 'flex' : 'none' }}; align-items: center; justify-content: center; border: 1.5px solid #fff; font-weight: 800; padding: 0 4px;">{{ isset($notifications) ? $notifications->count() : 0 }}</span>
                                        
                                    <!-- Dropdown -->
                                    <div id="patient-notif-dropdown" class="notification-dropdown" onclick="event.stopPropagation()" style="display: none; position: absolute; top: calc(100% + 15px); right: -10px; width: 360px; max-width: 95vw; background: white; border-radius: 16px; box-shadow: 0 16px 40px rgba(0,0,0,0.18), 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; z-index: 10005; border: 3px solid #000;">
                                            <div class="notification-header" style="padding: 1rem 1.2rem; border-bottom: 3px solid #000; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
                                                <span style="font-weight: 800; font-size: 1.05rem; color: #111; font-family: 'Manrope', sans-serif;">Notificaciones</span>
                                            <button onclick="markAllRead()" style="background: var(--color-celeste); border: 2px solid #000; color: #000; font-size: 0.75rem; font-weight: 700; cursor: pointer; padding: 0.4rem 0.8rem; border-radius: 8px; box-shadow: 2px 2px 0px #000;">
                                                    Leer todas
                                                </button>
                                            </div>
                                        <div id="patient-notif-items" class="notification-items-container" style="max-height: 420px; overflow-y: auto;">
                                            @if(isset($notifications) && $notifications->count() > 0)
                                                @foreach($notifications as $notification)
                                                    <div class="notification-item" style="padding: 1rem 1.2rem; border-bottom: 1px solid #eee; display: flex; gap: 1rem; align-items: flex-start; transition: background 0.2s; cursor: pointer;" onclick="window.location.href='{{ $notification->data['link'] ?? '#' }}'">
                                                        <div class="notif-icon" style="background: {{ $notification->data['type'] == 'success' ? '#dcfce7' : '#f3f4f6' }}; color: {{ $notification->data['type'] == 'success' ? '#166534' : '#374151' }}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 2px solid #000; flex-shrink: 0;">
                                                            @if(($notification->data['type'] ?? '') == 'success')
                                                                <i class="fa-solid fa-check"></i>
                                                            @elseif(($notification->data['type'] ?? '') == 'pago')
                                                                <i class="fa-solid fa-wallet"></i>
                                                            @else
                                                                <i class="fa-solid fa-bell"></i>
                                                            @endif
                                                        </div>
                                                        <div class="notif-content" style="flex: 1;">
                                                            <div style="font-weight: 800; color: #111; font-size: 0.95rem; margin-bottom: 0.2rem; font-family: 'Manrope', sans-serif;">{{ $notification->data['title'] ?? 'Nueva Notificación' }}</div>
                                                            <div style="font-size: 0.85rem; color: #555; line-height: 1.4;">{{ $notification->data['mensaje'] ?? '' }}</div>
                                                            <div style="font-size: 0.75rem; color: #999; margin-top: 0.4rem; font-weight: 600;">{{ $notification->created_at->diffForHumans() }}</div>
                                                        </div>
                                                        <div class="notif-indicator" style="width: 8px; height: 8px; background: var(--color-rojo); border-radius: 50%;"></div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="notification-empty" style="padding: 3rem 1.5rem; text-align: center; color: #888;">
                                                    <div class="icon-placeholder" style="color: #bbb; font-size: 2rem; margin-bottom: 1rem;"><i class="fa-solid fa-bell-slash"></i></div>
                                                    <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem; font-size: 1.1rem;">Todo al día</div>
                                                    <div style="font-size: 0.9rem; line-height: 1.4; color: #666;">No tenés notificaciones nuevas por ahora.</div>
                                                </div>
                                            @endif
                                        </div>
                                        </div>
                                    </div>

                                <!-- Logout Button -->
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;" class="header-logout-form">
                                    @csrf
                                        <button type="button" 
                                        onclick="openLogoutModal()"
                                        class="neobrutalist-btn"
                                        style="background: var(--color-rojo); color: #fff; border: 3px solid #000; box-shadow: 3px 3px 0px #000; padding: 0.4rem 1rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;"
                                        title="Cerrar Sesión">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span class="hide-on-mobile logout-text">Salir</span>
                                        </button>
                                </form>
                               </div>
                            <style>
                                @media (max-width: 768px) {
                                    .header-navbar {
                                        flex-direction: row !important;
                                        gap: 1rem !important; /* Visual equality */
                                        align-items: center !important;
                                    }
                                    .header-logout-form {
                                        display: flex !important; /* Ensure form behaves as a flex item */
                                        margin: 0 !important;
                                    }
                                    /* En móvil mostrar solo el icono: ocultar el texto del logout */
                                    .header-logout-form .logout-text {
                                        display: none !important;
                                    }
                                    .header-logout-form .neobrutalist-btn {
                                        padding: 0.4rem !important; /* Square-ish button */
                                        width: 40px !important;
                                        height: 40px !important;
                                        justify-content: center !important;
                                        align-items: center !important;
                                        box-shadow: 2px 2px 0px #000 !important; /* Consistent shadow */
                                    }
                                    /* Reducir gaps en la derecha para evitar que se corte el botón */
                                    .header-right {
                                        gap: 0 !important; /* Control spacing via sidebar-trigger-btn and navbar gap */
                                    }
                                }
                            </style>
                        </div>
                    </div>
                </header>
            @endauth

            <main class="container mt-16" style="flex: 1; min-height: auto; padding-top: @auth 90px; @else 3rem; @endauth padding-bottom: 3rem; width: 100%; max-width: 1400px; margin: 0 auto;">
                    {{-- Flash Messages (Floating Toast Style) --}}
                    @if(session('success'))
                        <div id="flash-message-success" class="flash-toast" style="position: fixed; top: 100px; right: 20px; z-index: 99999; background: #10b981; color: white; padding: 1rem 1.5rem; border-radius: 12px; border: 3px solid #000; box-shadow: 6px 6px 0px #000; font-weight: 700; font-family: 'Manrope', sans-serif; font-size: 0.95rem; max-width: 400px; animation: slideInRight 0.3s ease-out;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <i class="fa-solid fa-circle-check" style="font-size: 1.3rem;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                        <script>
                            setTimeout(() => {
                                const toast = document.getElementById('flash-message-success');
                                if (toast) {
                                    toast.style.animation = 'slideOutRight 0.3s ease-in';
                                    setTimeout(() => toast.remove(), 300);
                                }
                            }, 10000); // Auto-dismiss after 10 seconds
                        </script>
                    @endif
                    
                    @if(session('error'))
                        <div id="flash-message-error" class="flash-toast" style="position: fixed; top: 100px; right: 20px; z-index: 99999; background: #ef4444; color: white; padding: 1rem 1.5rem; border-radius: 12px; border: 3px solid #000; box-shadow: 6px 6px 0px #000; font-weight: 700; font-family: 'Manrope', sans-serif; font-size: 0.95rem; max-width: 400px; animation: slideInRight 0.3s ease-out;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <i class="fa-solid fa-circle-xmark" style="font-size: 1.3rem;"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                        <script>
                            setTimeout(() => {
                                const toast = document.getElementById('flash-message-error');
                                if (toast) {
                                    toast.style.animation = 'slideOutRight 0.3s ease-in';
                                    setTimeout(() => toast.remove(), 300);
                                }
                            }, 10000); // Auto-dismiss after 10 seconds
                        </script>
                    @endif

                    <style>
                        /* Subtle Bell Interaction */
        .notification-bell-container {
            position: relative;
            cursor: pointer;
        }
        
        /* NUCLEAR OPTION: Kill all hover transforms on notification items */
        .notification-item, 
        .notification-item:hover,
        .notification-bell-container:hover i,
        .notification-bell-container i:hover {
            transform: none !important;
            transition: none !important;
            animation: none !important;
            box-shadow: none !important;
            filter: none !important;
        }

        /* Prevent bell icon scale */
        .fa-bell {
            transition: none !important;
        }
        
                        @keyframes slideInRight {
                            from {
                                transform: translateX(400px);
                                opacity: 0;
                            }
                            to {
                                transform: translateX(0);
                                opacity: 1;
                            }
                        }
                        
                        @keyframes slideOutRight {
                            from {
                                transform: translateX(0);
                                opacity: 1;
                            }
                            to {
                                transform: translateX(400px);
                                opacity: 0;
                            }
                        }
                        
                        /* Mobile adjustments */
                        @media (max-width: 768px) {
                            .flash-toast {
                                right: 10px !important;
                                left: 10px !important;
                                max-width: calc(100% - 20px) !important;
                                top: 80px !important;
                            }
                        }
                    </style>

                    @yield('content')
                </main>
        @endif
    @endif

    <!-- Custom Logout Modal -->
    <div id="logout-modal-overlay" class="confirm-modal-overlay" style="display: none; background: rgba(0,0,0,0.7); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
        <div class="security-modal" style="padding: 2rem; background: white; border: 3px solid #000; box-shadow: 10px 10px 0px #000; border-radius: 20px; max-width: 400px; width: 90%; position: relative; z-index: 1000000;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 0.8rem;">Cerrar sesión</h3>
            <p style="font-family: 'Inter', sans-serif; font-weight: 600; color: #555; margin-bottom: 1.5rem; font-size: 0.9rem;">¿Confirmás que querés cerrar tu sesión?</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="closeLogoutModal()" class="neobrutalist-btn bg-white" style="flex: 1; border: 2px solid #000; padding: 0.6rem; font-weight: 700; font-size: 0.85rem; background: white; color: #000;">Cancelar</button>
                <button onclick="document.getElementById('logout-form').submit();" class="neobrutalist-btn" style="flex: 1; background: var(--color-rojo); color: white; border: 2px solid #000; padding: 0.6rem; font-weight: 700; font-size: 0.85rem;">SALIR</button>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            document.getElementById('logout-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeLogoutModal() {
            document.getElementById('logout-modal-overlay').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    </script>

    <!-- GLOBAL FOOTER -->
  



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


    <!-- Invisible Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Custom Confirmation Modal -->
    <div id="confirm-modal-overlay" class="confirm-modal-overlay" style="display: none;">
        <div class="confirm-modal">
            <div class="confirm-modal-title" style="font-family: 'Syne', sans-serif; background: var(--color-rojo); color: white;">Confirmar acción</div>
            <div id="confirm-modal-message" class="confirm-modal-message" style="font-family: 'Inter', sans-serif;"></div>
            <div class="confirm-modal-buttons">
                <button id="confirm-cancel" class="neobrutalist-btn" style="background: #fff; border: 3px solid #000; color: #000; box-shadow: 3px 3px 0px #000;">Cancelar</button>
                <button id="confirm-ok" class="neobrutalist-btn" style="background: var(--color-rojo); color: #fff; border: 3px solid #000; box-shadow: 3px 3px 0px #000;">Confirmar</button>
            </div>
        </div>
    </div>
    
    <script>
        // Restoring robust custom confirm.
        // If message is simple, we use native CONFIRM to ensure it works for user.
        // If he wants it fancy, he can provide a callback to the modal.
        // Fallback robusto:
        window.showConfirm = function(message, callback) {
            const overlay = document.getElementById('confirm-modal-overlay');
            if(overlay) {
                const msgEl = document.getElementById('confirm-modal-message');
                const okBtn = document.getElementById('confirm-ok');
                const cancelBtn = document.getElementById('confirm-cancel');
                
                if (msgEl && okBtn && cancelBtn) {
                     msgEl.innerText = message;
                     overlay.style.display = 'flex';
                     document.body.style.overflow = 'hidden';
                     
                     // Use cloneNode to wipe old listeners
                     const newOk = okBtn.cloneNode(true);
                     const newCancel = cancelBtn.cloneNode(true);
                     okBtn.parentNode.replaceChild(newOk, okBtn);
                     cancelBtn.parentNode.replaceChild(newCancel, cancelBtn);
                     
                     newOk.addEventListener('click', function() {
                         overlay.style.display = 'none';
                         document.body.style.overflow = '';
                         if(callback) callback();
                     });
                     newCancel.addEventListener('click', function() {
                         overlay.style.display = 'none';
                         document.body.style.overflow = '';
                     });
                     return;
                }
            }
            if(confirm(message) && callback) callback();
        };

        // Función para verificar cancelación - siempre permite cancelar, pero avisa si ya pagó
        window.checkCancelAppointment = function(appointmentId, fechaHora, isProjected = false) {
            if (isProjected) {
                // Para sesiones proyectadas, no hay formulario real, solo alerta
                window.showAlert('Esta sesión aún no está confirmada. El turno se confirmará al pagar.');
                return;
            }
            
            // Si es una sesión real, siempre permitir cancelar
            // Si ya pagó, mostrar mensaje de crédito
            const form = document.querySelector('form[action*="/appointments/" + appointmentId + "/cancel"]');
            if (form) {
                const pagoVerificado = form.dataset.pagoVerificado === 'true';
                
                if (pagoVerificado) {
                    window.showConfirm('El pago se guardará como crédito para tu próxima sesión. ¿Continuar?', () => {
                        form.submit();
                    });
                } else {
                    window.showConfirm('¿Seguro querés cancelar este turno?', () => {
                        form.submit();
                    });
                }
            }
        };
    </script>

    <!-- Report Problem Modal (Polished Neobrutalist) -->
    <div id="report-modal-overlay" class="confirm-modal-overlay" style="display: none; z-index: 100000; background: rgba(0,0,0,0.6);">
        <div class="confirm-modal" style="max-width: 480px; width: 92%; border: 3px solid #000; box-shadow: 8px 8px 0px #000; position: relative;"> 
            <div class="confirm-modal-title report-modal-header" style="background: #000; color: white; position: relative; padding: 1.2rem 2rem; display: flex; align-items: center;">
                <span class="report-modal-title-text" style="font-family: 'Inter', sans-serif; font-weight: 700; white-space: nowrap; font-size: 0.8rem;">Reportar un problema</span>
                <button class="report-modal-close-btn" onclick="closeReportModal()" style="position: absolute; right: 8px; top: 8px; background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
            </div>
            <style>
                /* Desktop Override for Report Modal */
                @media (min-width: 1024px) {
                    .report-modal-header {
                        justify-content: center !important;
                    }
                    .report-modal-title-text {
                        font-size: 1.2rem !important; /* "Un poco más grande" */
                    }
                    .report-modal-close-btn {
                        display: none !important; /* Hide close button on PC */
                    }
                }
                /* Mobile: Make title larger */
                @media (max-width: 768px) {
                    .report-modal-title-text {
                        font-size: 1rem !important;
                    }
                }
            </style>
            
            <div class="confirm-modal-message" style="padding: 2rem; text-align: left;">
                <div id="report-form-content">
                    <p style="margin-bottom: 1.5rem; font-size: 0.95rem; color: #444; font-weight: 600; font-family: 'Manrope', sans-serif;">Describí el problema que encontraste y lo revisaremos lo antes posible.</p>
                    <form id="report-issue-form" action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="url_origen" value="{{ request()->fullUrl() }}">
                        <div style="margin-bottom: 1.2rem;">
                            <label style="font-weight: 400; font-size: 0.8rem; text-transform: uppercase; display: block; margin-bottom: 0.5rem; letter-spacing: 0.5px; font-family: 'Syne', sans-serif; color: #111;">Asunto</label>
                            <select name="subject" id="report-subject" class="neobrutalist-input modal-select" style="width: 100%; height: auto; border: 3px solid #000; padding: 12px; font-weight: 700; font-size: 0.95rem; background: #fafafa; border-radius: 10px; box-shadow: 4px 4px 0px #000; outline: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px top 50%; background-size: 12px auto;">
                                <option value="Error visual / Diseño" style="background: #fafafa; color: #000;">Error visual / Diseño</option>
                                <option value="Algo no funciona" style="background: #fafafa; color: #000;">Algo no funciona</option>
                                <option value="Sugerencia" style="background: #fafafa; color: #000;">Sugerencia</option>
                                <option value="Otro" style="background: #fafafa; color: #000;">Otro</option>
                            </select>
                            <style>
                                .modal-select:focus { outline: none !important; border-color: #000 !important; }
                                
                                /* Mobile Fix for Select Background */
                                @media (max-width: 768px) {
                                    /* Mobile Fix for Select Background - ADVANCED NUCLEAR OPTION */
                                    select#report-subject {
                                        /* Remove native styling */
                                        -webkit-appearance: none !important;
                                        -moz-appearance: none !important;
                                        appearance: none !important;
                                        
                                        /* Force white background */
                                        background-color: #ffffff !important;
                                        color: #000000 !important;
                                        border: 3px solid #000 !important;
                                        border-radius: 10px !important;
                                        font-weight: 700 !important;
                                        opacity: 1 !important;
                                        
                                        /* INCREASED HEIGHT FOR MOBILE */
                                        padding: 16px 12px !important;
                                        min-height: 56px !important;
                                        height: auto !important;
                                        
                                        /* Custom Arrow SVG */
                                        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") !important;
                                        background-repeat: no-repeat !important;
                                        background-position: right 12px center !important;
                                        background-size: 16px 16px !important;
                                    }
                                    
                                    /* Force options white */
                                    select#report-subject option {
                                        background-color: #ffffff !important;
                                        color: #000000 !important;
                                        -webkit-appearance: none !important;
                                    }
                                    
                                    /* CRITICAL: Fix cyan/blue selection color on mobile */
                                    select#report-subject option:checked {
                                        background-color: #ffffff !important;
                                        color: #000000 !important;
                                        background-image: none !important;
                                    }
                                    
                                    select#report-subject option:hover {
                                        background-color: #f0f0f0 !important;
                                        color: #000000 !important;
                                        background-image: none !important;
                                    }
                                    
                                    select#report-subject option:focus {
                                        background-color: #ffffff !important;
                                        color: #000000 !important;
                                        background-image: none !important;
                                    }
                                    
                                    select#report-subject option:active {
                                        background-color: #ffffff !important;
                                        color: #000000 !important;
                                        background-image: none !important;
                                    }
                                    
                                    /* Focus state */
                                    select#report-subject:focus {
                                        outline: none !important;
                                        background-color: #ffffff !important;
                                        box-shadow: 4px 4px 0px #000 !important;
                                    }
                                }
                            </style>
                        </div>
                        <div class="mb-4">
                            <label style="font-weight: 400; font-size: 0.8rem; text-transform: uppercase; display: block; margin-bottom: 0.5rem; letter-spacing: 0.5px; font-family: 'Syne', sans-serif; color: #111;">Descripción:</label>
                            <textarea name="description" id="report-description" class="neobrutalist-input" required placeholder="Ej: No puedo visualizar mis turnos..." style="min-height: 100px; border: 3px solid #000; padding: 12px; width: 100%; box-sizing: border-box; font-size: 0.9rem; background: #fafafa; border-radius: 10px; box-shadow: 4px 4px 0px #000;"></textarea>
                        </div>
                        <div class="confirm-modal-buttons" style="margin-top: 1.8rem; display: flex; gap: 1rem; padding: 0;">
                            <button type="button" onclick="closeReportModal()" class="neobrutalist-btn" style="flex: 1; background: white; border: 3px solid #000; padding: 0.5rem 1rem; font-weight: 700; font-size: 0.85rem;">Cancelar</button>
                            <button type="button" onclick="submitReport()" class="neobrutalist-btn" style="flex: 1; background: var(--color-amarillo); border: 3px solid #000; padding: 0.5rem 1rem; font-weight: 700; font-size: 0.85rem;">Enviar</button>
                        </div>
                </div>

                <div id="report-preloader" style="display: none; text-align: center; padding: 3rem 1rem;">
                    <style>
                        .loader-bars {
                            display: flex;
                            justify-content: center;
                            gap: 8px;
                            margin-bottom: 1.5rem;
                        }
                        .loader-bar {
                            width: 15px;
                            height: 40px;
                            background: white;
                            border: 3px solid #000;
                            box-shadow: 3px 3px 0px #000;
                            animation: barPulse 0.8s ease-in-out infinite;
                        }
                        .loader-bar:nth-child(2) { animation-delay: 0.2s; background: var(--color-amarillo); }
                        .loader-bar:nth-child(3) { animation-delay: 0.4s; background: var(--color-celeste); }

                        @keyframes barPulse {
                            0%, 100% { transform: scaleY(1); }
                            50% { transform: scaleY(1.5); }
                        }
                    </style>
                    <div class="loader-bars">
                        <div class="loader-bar"></div>
                        <div class="loader-bar"></div>
                        <div class="loader-bar"></div>
                    </div>
                    <p style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Enviando reporte...</p>
                </div>

                <div id="report-success-msg" style="display: none; text-align: center; padding: 1rem;">
                    <div style="width: 60px; height: 60px; background: var(--color-verde); border: 2px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 3px 3px 0px #000;">
                        <i class="fa-solid fa-check" style="font-size: 1.8rem; color: #000;"></i>
                    </div>
                    <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 0.5rem;">¡Enviado!</h3>
                    <p style="font-size: 0.9rem; color: #444; margin-bottom: 1.5rem;">Gracias por avisar. Lo revisaremos pronto.</p>
                    <button class="neobrutalist-btn" onclick="closeReportModal()" style="background: #000; color: white; width: 100%; border: none;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Duplicate Modal Removed -->

    <!-- Html2Canvas Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>

        async function submitReport() {
            const descEl = document.getElementById('report-description');
            const desc = descEl ? descEl.value : '';
            
            if (!desc.trim()) {
                alert('Por favor describí el problema.');
                return;
            }

            const formContent = document.getElementById('report-form-content');
            const preloader = document.getElementById('report-preloader');
            const successMsg = document.getElementById('report-success-msg');

            formContent.style.display = 'none';
            preloader.style.display = 'block';

            try {
                const response = await fetch("{{ route('tickets.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        subject: document.getElementById('report-subject').value,
                        description: desc,
                        url_origen: window.location.href
                    })
                });

                if (response.ok) {
                    preloader.style.display = 'none';
                    successMsg.style.display = 'block';
                } else {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al enviar');
                }
            } catch (error) {
                console.error(error);
                alert('Hubo un error al enviar el reporte: ' + error.message);
                preloader.style.display = 'none';
                formContent.style.display = 'block';
            }
        }
    </script>

    @auth
    <!-- Delete Account Modal -->
    <div id="delete-account-modal" class="confirm-modal-overlay" style="display: none;">
        <div class="confirm-modal" style="border: var(--border-thick); box-shadow: var(--shadow-hard); max-width: 420px; width: 95%;"> <!-- más chico -->
            <div class="confirm-modal-title" style="background: var(--color-rojo); color: white; font-family: 'Syne', sans-serif;">Eliminar Cuenta</div>
            <div class="confirm-modal-message" style="padding: 2rem;">
                <p style="margin-bottom: 1rem; font-size: 1.1rem; font-weight: 800;">¿Estás completamente segura?</p>
                <p style="font-size: 0.9rem; color: #555; margin-bottom: 1.5rem;">Esta acción es <strong>irreversible</strong>. Se eliminarán todos tus turnos, historial y documentos.</p>

                <form action="{{ route('patient.account.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div style="margin-bottom: 1.5rem; text-align: left;">
                        <label style="font-size: 0.9rem; font-weight: 700; display: block; margin-bottom: 0.5rem;">Contraseña para confirmar:</label>
                        <input type="password" name="password" class="neobrutalist-input" required placeholder="Tu contraseña actual" style="border: var(--border-thick); width: 100%; padding: 12px;">
                        @error('password')
                            <span style="color: red; font-size: 0.85rem; font-weight: 700;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="confirm-modal-buttons" style="display: flex; gap: 1rem; justify-content: center; padding: 0;">
                        <button type="button" class="neobrutalist-btn flex-1" style="background: white;" onclick="closeDeleteModal()">Cancelar</button>
                        <button type="submit" class="neobrutalist-btn flex-1" style="background: var(--color-rojo); color: white; border: var(--border-thick);">Borrar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endauth

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
 
        // Navbar scroll logic removed - new neobrutalist navbar is always visible

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
        
        // Ensure Cancel button has black border
        if (confirmCancel) confirmCancel.style.border = '2.5px solid #000';

        // Confirm overlay logic (unchanged)
        // Duplicates removed

        // NOTIFICATIONS LOGIC
        const bellDesktop = document.getElementById('notif-bell-desktop');
        const bellMobile = document.getElementById('notif-bell-mobile');
        const dropdown = document.getElementById('notif-dropdown');
        const items = document.getElementById('notif-items');
        const countDesktop = document.getElementById('notif-count-desktop');
        const countMobile = document.getElementById('notif-count-mobile');

        function toggleDropdown(e) {
            e.stopPropagation();
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                dropdown.style.display = 'block';
                fetchNotifications();
            } else {
                dropdown.style.display = 'none';
            }
        }

        if (bellDesktop) bellDesktop.addEventListener('click', toggleDropdown);
        if (bellMobile) bellMobile.addEventListener('click', toggleDropdown);

        if (dropdown) {
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
                
                // data now expects { notifications: [], unread_count: X }
                const notifications = data.notifications || [];
                const unreadCount = data.unread_count || 0;

                // Buscar elementos tanto de admin como de paciente
                const countDesktop = document.getElementById('notif-count') || document.getElementById('patient-notif-count');
                const countMobile = document.getElementById('notif-count-mobile');
                const items = document.getElementById('notif-items') || document.getElementById('patient-notif-items');
                
                if (countDesktop) {
                    countDesktop.innerText = unreadCount;
                    countDesktop.style.display = unreadCount > 0 ? 'flex' : 'none';
                }
                if (countMobile) {
                    countMobile.innerText = unreadCount;
                    countMobile.style.display = unreadCount > 0 ? 'flex' : 'none';
                }
                
                if (items && notifications.length > 0) {
                    items.innerHTML = notifications.map(n => `
                        <div class="notification-item" data-id="${n.id}" style="border-bottom: 1px solid #f0f0f0; padding: 16px 24px; display: block; text-decoration: none; cursor: pointer; background: ${n.leido ? 'white' : '#f9fafb'};" onclick="markAsRead('${n.id}', '${n.link}')">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                                <div style="flex: 1;">
                                    <p style="margin:0; font-size: 0.9rem; font-weight: ${n.leido ? '400' : '600'}; font-family: 'Inter', sans-serif; color: #1f2937; line-height: 1.5;">${n.mensaje}</p>
                                    <small style="color: #6b7280; font-size: 0.75rem; font-weight: 400; display: block; margin-top: 6px; font-family: 'Inter', sans-serif;">
                                        ${new Date(n.created_at).toLocaleDateString()} a las ${new Date(n.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                    </small>
                                </div>
                                ${!n.leido ? '<div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; margin-top: 6px;" class="unread-dot"></div>' : ''}
                            </div>
                        </div>
                    `).join('');
                } else if (items) {
                    items.innerHTML = '<div style="padding: 2rem; text-align: center; color: #6b7280;">No hay notificaciones recientes</div>';
                }
            } catch (e) { 
                console.error("Error fetching notifications", e); 
            }
        }

        async function markAsRead(id, link = null) {
            // Sin animaciones ni demoras, acción instantánea y estática
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                
                if (link && link !== '#') {
                    window.location.href = link;
                } else {
                    fetchNotifications();
                }
            } catch (error) {
                console.error("Error marking as read", error);
                if (link && link !== '#') window.location.href = link;
            }
        }

        async function markAllRead() {
            // Optimistic UI update: instantly mark UI as read before wait
            document.querySelectorAll('.notification-item').forEach(item => {
                item.style.background = 'white';
                const text = item.querySelector('p');
                if (text) text.style.fontWeight = '400';
                const dot = item.querySelector('.unread-dot');
                if (dot) dot.remove();
            });
            const counters = [document.getElementById('notif-count'), document.getElementById('notif-count-mobile'), document.getElementById('patient-notif-count')];
            counters.forEach(c => { if(c) c.style.display = 'none'; });

            await fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            // We do not re-fetch here because the optimistic UI update already made them look read,
            // and re-fetching might cause the dropdown to re-render or blink.
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
            // Initialize sidebar state from cookie
            const sidebar = document.getElementById('admin-sidebar');
            if (sidebar) {
                const isMobile = window.innerWidth <= 1024;
                const cookieValue = document.cookie.split('; ').find(row => row.startsWith('sidebar_collapsed='));
                const isCollapsed = cookieValue ? cookieValue.split('=')[1] === 'true' : false;
                
                if (!isMobile) {
                    // Desktop: apply collapsed state from cookie
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        sidebar.style.transform = 'translateX(-100%)';
                    } else {
                        sidebar.classList.remove('collapsed');
                        sidebar.style.transform = 'translateX(0)';
                        sidebar.style.display = 'flex';
                    }
                } else {
                    // Mobile: always start hidden
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(-100%)';
                }
            }
            
            // Sync initial sidebar icon state
            const icon = document.getElementById('sidebar-toggle-icon');
            if (sidebar && icon) {
                const isMobile = window.innerWidth <= 1024;
                const isOpen = (isMobile && sidebar.classList.contains('active')) || 
                                (!isMobile && !sidebar.classList.contains('collapsed'));
                icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
            }

            // Smart Header Scroll Logic
            const adminHeader = document.querySelector('.universal-header');
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
        $isAdmin = $user && $user->rol == 'admin';
        $isPatient = $user && $user->rol == 'paciente';
        $isLoginOrRegister = request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.*');
        
        // Show widget for patients and guests, not on login/register pages, and not for admins
        $showWidget = ($isPatient || !$user) && !$isLoginOrRegister && !$isAdmin;
    @endphp

    @if($showWidget)
    <div id="whatsapp-widget-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10000; font-family: 'Manrope', sans-serif;">
        <!-- Minimalist Chat Window -->
        <div id="whatsapp-chat-window" style="display: none; position: absolute; bottom: 95px; right: 5px; width: 320px; max-width: 90vw; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px rgba(0,0,0,1); border-radius: 15px; overflow: visible; transform-origin: bottom right; animation: fadeUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); min-height: 380px; max-height: 90vh; box-sizing: border-box; z-index: 10001;">
            
            <!-- Elegant Header -->
            <div style="background: #fff; padding: 1rem 1.2rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #000; position: relative; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 45px; height: 45px; border-radius: 50%; border: 2px solid #25D366; overflow: hidden; flex-shrink: 0;">
                        <img src="{{ asset('img/profile-chat.png') }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                </div>
                    <div>
                        <h4 style="margin: 8px 0 0 0; font-weight: 800; font-size: 0.9rem; color: #000; font-family: 'Manrope', sans-serif;">Lic. Nazarena De Luca</h4>
                        <span style="font-size: 0.7rem; color: #25D366; font-weight: 700;">En línea</span>
                </div>
            </div>
                <button onclick="toggleWhatsApp()" style="position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #666;"><i class="fa-solid fa-xmark"></i></button>
                </div>

            <!-- Body -->
            <div style="padding: 1.2rem; background: #fff; display: flex; flex-direction: column; justify-content: center;">
                <p style="margin: 0 0 1rem 0; font-size: 0.95rem; color: #444; line-height: 1.4; font-weight: 700; font-family: 'Syne', sans-serif;">
                    Hola 👋 ¿Cómo puedo ayudarte hoy?
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="https://wa.me/5491139560673?text=Hola,%20quiero%20pedir%20un%20turno." target="_blank" class="neobrutalist-btn bg-white w-full" style="text-align: center; text-decoration: none; font-size: 0.7rem; padding: 0.4rem 0.6rem; border-width: 2px; box-shadow: 3px 3px 0px #000; border-radius: 8px;">
                        🗓️ Quiero pedir un turno
                    </a>
                    <a href="https://wa.me/5491139560673?text=Hola,%20quisiera%20saber%20como%20son%20las%20sesiones." target="_blank" class="neobrutalist-btn bg-white w-full" style="text-align: center; text-decoration: none; font-size: 0.7rem; padding: 0.4rem 0.6rem; border-width: 2px; box-shadow: 3px 3px 0px #000; border-radius: 8px;">
                        📍 ¿Cómo son las sesiones?
                    </a>
                     <a href="https://wa.me/5491139560673?text=Hola,%20tengo%20una%20duda/consulta." target="_blank" class="neobrutalist-btn bg-white w-full" style="text-align: center; text-decoration: none; font-size: 0.7rem; padding: 0.4rem 0.6rem; border-width: 2px; box-shadow: 3px 3px 0px #000; border-radius: 8px;">
                        💬 Tengo una duda/consulta
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="padding: 0.8rem; background: #fafafa; border-top: 2px solid #000; margin-top: auto; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                 <a href="https://wa.me/5491139560673" target="_blank" class="neobrutalist-btn" style="background: #25D366; color: white; border: 2px solid #000; width: 100%; text-align: center; justify-content: center; align-items: center; display: flex; gap: 6px; font-weight: 600; padding: 0.6rem; font-size: 0.8rem; box-shadow: 3px 3px 0px #000; font-family: 'Manrope', sans-serif; border-radius: 8px;">
                    <i class="fa-brands fa-whatsapp" style="font-size: 1rem; margin-top: 1px;"></i> INICIAR CHAT
                </a>
            </div>
        </div>

        <!-- FAB -->
        <button onclick="toggleWhatsApp()" style="width: 55px; height: 55px; background: #25D366; color: white; border-radius: 50%; border: 3px solid #000; box-shadow: 4px 4px 0px #000; font-size: 2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; z-index: 10002;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <i class="fa-brands fa-whatsapp"></i>
        </button>
    </div>
    @endif
    
    <script>
        function toggleNotifications(e) {
            e.stopPropagation();
            const dropdown = e.currentTarget.querySelector('.notification-dropdown');
            if (dropdown) {
                const isHidden = dropdown.style.display === 'none';
                // Close all others
                document.querySelectorAll('.notification-dropdown').forEach(d => d.style.display = 'none');
                dropdown.style.display = isHidden ? 'block' : 'none';
            }
        }
        
        function toggleWhatsApp() {
            const win = document.getElementById('whatsapp-chat-window');
            if(win) {
                win.style.display = (win.style.display === 'none') ? 'block' : 'none';
            }
        }

        // Close dropdowns on click outside
        document.addEventListener('click', () => {
             document.querySelectorAll('.notification-dropdown').forEach(d => d.style.display = 'none');
        });
    </script>

    <script>
        // -----------------------------------------------------
        // 🧠 SMART STICKY HEADER
        // -----------------------------------------------------
        document.addEventListener('DOMContentLoaded', () => {
             const header = document.querySelector('.universal-header');
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



        window.openReportModal = function(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Save current scroll position
            const scrollY = window.scrollY;
            
            // Prevent body scroll
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflow = 'hidden';
            document.body.classList.add('modal-open');
            
            // Store scroll position for restoration
            document.body.dataset.scrollY = scrollY;
            
            const overlay = document.getElementById('report-modal-overlay');
            const content = document.getElementById('report-form-content');
            const success = document.getElementById('report-success-msg');
            
            if (overlay) overlay.style.display = 'flex';
            if (content) content.style.display = 'block';
            if (success) success.style.display = 'none';
            
            // Clear previous description
            const descEl = document.getElementById('report-description');
            if (descEl) descEl.value = '';
        }

        window.closeReportModal = function() {
            const overlay = document.getElementById('report-modal-overlay');
            if (overlay) overlay.style.display = 'none';
            
            // Restore scroll position
            const scrollY = document.body.dataset.scrollY || 0;
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflow = '';
            document.body.classList.remove('modal-open');
            
            // Restore scroll position
            window.scrollTo(0, parseInt(scrollY) || 0);
        }

        // Notification Bell Toggle Improved for Mobile
        const bellBtn = document.getElementById('universal-notif-bell');
        if (bellBtn) {
            bellBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdown = document.getElementById('universal-notif-dropdown');
                if (dropdown) {
                    const isVisible = dropdown.style.display === 'block';
                    dropdown.style.display = isVisible ? 'none' : 'block';
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            const dropdown = document.getElementById('universal-notif-dropdown');
            if (dropdown) dropdown.style.display = 'none';
            
            // Also close patient dropdown if exists
            const pDropdown = document.getElementById('notif-dropdown');
            if (pDropdown) pDropdown.style.display = 'none';
        });

        // Patient Notification Toggle
        window.toggleNotifications = function(e, specificId = null) {
            e.preventDefault();
            e.stopPropagation();
            
            // Default to 'notif-dropdown' if no ID provided (for backward compatibility if needed)
            // But prefer specificID if passed
            let targetId = specificId || 'notif-dropdown';
            
            // If specificId is 'patient-notif-dropdown', we toggle that one.
            // If it's not provided, we try 'notif-dropdown' (Admin).
            
            const dropdown = document.getElementById(targetId);
            
            if (dropdown) {
                const isVisible = dropdown.style.display === 'block'; // Or check current computed style
                
                // Close ANY other open dropdowns
                const allDropdowns = ['notif-dropdown', 'patient-notif-dropdown', 'universal-notif-dropdown'];
                allDropdowns.forEach(id => {
                    const el = document.getElementById(id);
                    if(el) el.style.display = 'none';
                });

                // Toggle target
                dropdown.style.display = isVisible ? 'none' : 'block';
            }
        };

        // Mark All Notifications as Read (Admin and Patient)
        window.markAllRead = function() {
            fetch('{{ route('notifications.read-all') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update Patient UI
                    const pBadge = document.getElementById('patient-notif-count');
                    if(pBadge) pBadge.style.display = 'none';
                    
                    const pContainer = document.getElementById('patient-notif-items');
                    if(pContainer) {
                        pContainer.innerHTML = `
                            <div class="notification-empty" style="padding: 3rem 1.5rem; text-align: center; color: #888;">
                                <div class="icon-placeholder" style="color: #bbb; font-size: 2rem; margin-bottom: 1rem;"><i class="fa-solid fa-bell-slash"></i></div>
                                <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem; font-size: 1.1rem;">Todo al día</div>
                                <div style="font-size: 0.9rem; line-height: 1.4; color: #666;">No tenés notificaciones nuevas por ahora.</div>
                            </div>
                        `;
                    }
                    
                    // Update Admin UI
                    const aBadge = document.getElementById('notif-count');
                    if(aBadge) aBadge.style.display = 'none';
                    
                    const aContainer = document.getElementById('notif-items');
                    if(aContainer) {
                        aContainer.innerHTML = `
                            <div class="notification-empty" style="padding: 3rem 1.5rem; text-align: center; color: #888;">
                                <div class="icon-placeholder" style="color: #bbb; font-size: 2rem; margin-bottom: 1rem;"><i class="fa-solid fa-bell-slash"></i></div>
                                <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem; font-size: 1.1rem;">Todo al día</div>
                                <div style="font-size: 0.9rem; line-height: 1.4; color: #666;">No tenés notificaciones nuevas por ahora.</div>
                            </div>
                        `;
                    }
                }
            })
            .catch(error => console.error('Error marking notifications as read:', error));
        };
    </script>


      <footer class="footer" style="background: var(--color-dark); color: white; padding: 2rem 1rem 3rem 1rem; border-top: 3px solid #000; position: relative; width: 100%; box-sizing: border-box; display: flex; align-items: center; justify-content: center;">
        <div style="max-width: 1200px; width: 100%; text-align: center;">
            <h2 style="color: white; font-family: 'Syne', sans-serif; margin-bottom: 0.5rem;">Lic. Nazarena De Luca</h2>
            
            @if(!auth()->check() || (auth()->check()))
                @if(request()->routeIs('login'))
                    <button type="button" onclick="openReportModal(event)" style="background: transparent; border: none; color: rgb(255, 255, 255); font-size: 0.9rem; cursor: pointer; margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 8px; font-family: Manrope, sans-serif; opacity: 1; text-decoration: underline;">
                        <i class="fa-solid fa-circle-exclamation"></i> ¿Problemas para logearte? Reportar fallo
                    </button>
                @else
                    <button type="button" onclick="openReportModal(event)" style="background: transparent; border: none; color: rgb(255, 255, 255); font-size: 0.85rem; cursor: pointer; margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 8px; font-family: 'Manrope', sans-serif; opacity: 0.9; text-decoration: none; font-weight: 700;">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 0.9rem;"></i> Reportar un problema
                    </button>
                @endif
            @endif
            
            @auth
                @if(auth()->user()->rol == 'paciente' && !request()->routeIs('login') && !request()->routeIs('register'))
                    <button type="button" onclick="openDeleteModal()" style="background: none; border: none; color: white; text-decoration: underline; cursor: pointer; font-size: 0.85rem; font-family: 'Manrope', sans-serif; margin-bottom: 1rem; display: block; margin-left: auto; margin-right: auto; opacity: 0.8; font-weight: 600; transition: opacity 0.3s ease;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                        Quiero darme de baja del sistema
                    </button>
                    <!-- Delete Form -->
                    <form id="delete-account-form" action="{{ route('patient.account.destroy') }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            @endauth
            
            <p style="font-family: 'Manrope', sans-serif; opacity: 0.8; margin-bottom: 0; font-size: 0.85rem;">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </footer>
    <!-- Mobile Menu Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const dropdown = document.getElementById('mobile-menu-dropdown');

            if (toggleBtn && dropdown) {
                toggleBtn.addEventListener('click', () => {
                    if (dropdown.style.display === 'none') {
                        dropdown.style.display = 'block';
                        toggleBtn.querySelector('i').className = 'fa-solid fa-xmark';
                    } else {
                        dropdown.style.display = 'none';
                        toggleBtn.querySelector('i').className = 'fa-solid fa-bars';
                    }
                });

                // Cerrar al clickear fuera
                document.addEventListener('click', (e) => {
                    if (!toggleBtn.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = 'none';
                        const icon = toggleBtn.querySelector('i');
                        if (icon) icon.className = 'fa-solid fa-bars';
                    }
                });
            }
        });
        
        // Admin sidebar toggle for mobile
        window.toggleAdminSidebar = function() {
            const sidebar = document.querySelector('.nav-sidebar');
            let overlay = document.querySelector('.sidebar-overlay');
            
            // Create overlay if it doesn't exist
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                overlay.onclick = () => window.toggleAdminSidebar();
                document.body.appendChild(overlay);
            }
            
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            } else {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            }
        };
    </script>

    <!-- GLOBAL SECURITY MODALS -->
    <!-- Logout Confirmation Modal -->
    <div id="logout-modal-overlay" class="security-modal-overlay">
        <div class="security-modal" style="padding: 2rem;">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 0.8rem;">¿Cerrar sesión?</h3>
            <p style="font-family: 'Inter', sans-serif; font-weight: 600; color: #555; margin-bottom: 1.5rem; font-size: 0.9rem;">Estás por salir del sistema. ¿Confirmás que querés cerrar tu sesión?</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button onclick="closeLogoutModal()" class="neobrutalist-btn bg-white" style="flex: 1; border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 0.6rem; font-weight: 700; font-size: 0.85rem;">Cancelar</button>
                <button onclick="document.getElementById('logout-form').submit();" class="neobrutalist-btn" style="flex: 1; background: var(--color-rojo); color: white; border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 0.6rem; font-weight: 700; font-size: 0.85rem;">SALIR</button>
            </div>
        </div>
    </div>

    <!-- Account Deletion Modal (Password Required) -->
    <div id="delete-modal-overlay" class="security-modal-overlay">
        <div class="security-modal" style="padding: 2rem;">
            <h3 style="color: var(--color-rojo); font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.5rem; margin-bottom: 0.8rem;">¡Atención!</h3>
            <p style="font-family: 'Inter', sans-serif; font-weight: 600; color: #555; margin-bottom: 1.2rem; font-size: 0.85rem;">Esta acción borrará permanentemente tu cuenta. Ingresá tu contraseña para confirmar:</p>
            
            <form id="delete-account-form-final" action="{{ auth()->user() && auth()->user()->rol == 'paciente' ? route('patient.account.destroy') : '#' }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="neobrutalist-input" style="padding: 0.8rem; margin-bottom: 1.5rem; border: 2px solid #000; box-shadow: 3px 3px 0px #000; border-radius: 10px; background: #fff;">
                    <input type="password" name="password" placeholder="Contraseña actual" style="width: 100%; border: none; outline: none; font-size: 0.9rem; font-weight: 700;" required>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" onclick="closeDeleteModal()" class="neobrutalist-btn bg-white" style="flex: 1; border: 2px solid #000; padding: 0.6rem; font-weight: 700; font-size: 0.85rem;">Cancelar</button>
                    <button type="submit" class="neobrutalist-btn" style="flex: 1; background: var(--color-rojo); color: white; border: 2px solid #000; padding: 0.6rem; font-weight: 700; font-size: 0.85rem;">BORRAR</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // GLOBAL SECURITY SCRIPTS
        window.openLogoutModal = function() {
            document.getElementById('logout-modal-overlay').style.display = 'flex';
            document.body.classList.add('modal-open');
        }
        window.closeLogoutModal = function() {
            document.getElementById('logout-modal-overlay').style.display = 'none';
            document.body.classList.remove('modal-open');
        }
        window.openDeleteModal = function() {
            document.getElementById('delete-modal-overlay').style.display = 'flex';
            document.body.classList.add('modal-open');
        }
        window.closeDeleteModal = function() {
            document.getElementById('delete-modal-overlay').style.display = 'none';
            document.body.classList.remove('modal-open');
        }



        // Refine Sidebar on Mobile
        window.addEventListener('resize', () => {
            const sidebar = document.querySelector('.nav-sidebar');
            if (window.innerWidth > 1024) {
                if(sidebar) sidebar.style.left = '0';
            } else {
                if(sidebar && sidebar.style.left === '0px') {
                    // keep it open if it was open
                } else {
                    if(sidebar) sidebar.style.left = '-100%';
                }
            }
        });
        
        // Initial mobile setup
        if (window.innerWidth <= 1024) {
            const s = document.querySelector('.nav-sidebar');
            if(s) s.style.left = '-100%';
        }
    </script>
    <script>
        // SOLUTION 1: NUCLEAR JAVASCRIPT FIX FOR MOBILE SELECT BACKGROUND
        (function() {
            // Detect Mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;
            
            if (!isMobile) return; 
            
            // Function to force styles aggressively
            function forceDropdownStyle() {
                const selects = document.querySelectorAll('select');
                
                selects.forEach(select => {
                    // Force white background and text
                    select.style.cssText += `
                        background-color: white !important;
                        color: black !important;
                        -webkit-appearance: none !important;
                        -moz-appearance: none !important;
                        appearance: none !important;
                        opacity: 1 !important;
                    `;
                    
                    // Force options
                    Array.from(select.options).forEach(option => {
                        option.style.cssText += `
                            background-color: white !important;
                            color: black !important;
                            padding: 8px !important;
                        `;
                    });
                });
            }
            
            // Run on Load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', forceDropdownStyle);
            } else {
                forceDropdownStyle();
            }
            
            // Run repeatedly to catch dynamic updates or browser overrides
            setInterval(forceDropdownStyle, 500);
            
            // Run on interactions
            document.addEventListener('touchstart', forceDropdownStyle);
            document.addEventListener('click', forceDropdownStyle);
            document.addEventListener('focusin', forceDropdownStyle);
        })();
    </script>
    <!-- Global Loading Overlay -->
    <div id="global-loader-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); backdrop-filter: blur(4px); z-index: 100000; align-items: center; justify-content: center; flex-direction: column; font-family: 'Syne', sans-serif;">
        <div class="neobrutalist-card" style="background: white; padding: 2rem; border: 4px solid #000; box-shadow: 10px 10px 0px #000; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 3rem; color: #000;"></i>
            <h2 style="margin: 0; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Cargando...</h2>
            <p style="margin: 0; font-weight: 600; font-size: 0.9rem; color: #444;">Por favor, esperá un momento.</p>
        </div>
    </div>

    <script>
        window.showLoader = function() {
            const loader = document.getElementById('global-loader-overlay');
            if (loader) loader.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };
        window.hideLoader = function() {
            const loader = document.getElementById('global-loader-overlay');
            if (loader) loader.style.display = 'none';
            document.body.style.overflow = '';
        };
    </script>

</body>
</html>
