@extends('layouts.app')

@section('title', 'Mi Portal - Lic. Nazarena De Luca')

@section('content')
<link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
<style>
    /* Ensure all neobrutalist elements have black borders */
    .neobrutalist-card {
        border: 3px solid #000 !important;
        box-shadow: 4px 4px 0px #000 !important;
    }
    .neobrutalist-btn {
        border: 3px solid #000 !important;
        box-shadow: 3px 3px 0px #000 !important;
    }
    .neobrutalist-input {
        border: 3px solid #000 !important;
        box-shadow: 3px 3px 0px #000 !important;
    }
    .day-btn:not(.disabled) {
        border: 3px solid #000 !important;
        box-shadow: 3px 3px 0px #000 !important;
    }
    .time-pill:not(.disabled) {
        border: 3px solid #000 !important;
        box-shadow: 2px 2px 0px #000 !important;
    }
    .modality-btn {
        border: 3px solid #000 !important;
        box-shadow: 3px 3px 0px #000 !important;
    }

    /* Mobile Bottom Navigation */
    @media (max-width: 1024px) {
        .mobile-nav-bar { display: flex !important; }
        
        /* Turnos as Cards on Mobile */
        #mis-turnos tr {
            display: block;
            border: 3px solid #000 !important; /* Stronger border */
            box-shadow: none !important;      /* No shadow */
            border-radius: 15px !important;
            margin-bottom: 1.5rem !important;
            background: #fff;
            position: relative;
        }
        #mis-turnos thead {
            display: none; /* Hide header on mobile */
        }
        #mis-turnos td {
            display: block;
            text-align: left;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #eee;
        }
        #mis-turnos td:last-child {
            border-bottom: none;
        }
        /* Header style for the first cell (Date/Time) */
        #mis-turnos td:nth-of-type(1) {
            background: #f0f0f0;
            border-bottom: 2px solid #000 !important;
            border-radius: 12px 12px 0 0;
            padding: 10px 15px !important;
            font-weight: 800;
        }
    }
</style>
<style>
    /* Mobile & Tablet (iPad) Cards for Turnos */
    @media (max-width: 1024px) {
        .mobile-nav-bar { display: flex !important; }
        
        #mis-turnos tr {
            display: block;
            border: 3px solid #000 !important;
            box-shadow: none !important;
            border-radius: 15px !important;
            margin-bottom: 1.5rem !important;
            background: #fff;
            position: relative;
        }
        #mis-turnos thead { display: none; }
        
        /* Reset cell styles for card view */
        #mis-turnos td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding: 0.8rem 1rem !important;
            border-bottom: 1px solid #eee;
        }
        
        #mis-turnos td:last-child { border-bottom: none; }
        
        /* Header style for the first cell (Date/Time) */
        #mis-turnos td:nth-of-type(1) {
            background: #f0f0f0;
            border-bottom: 2px solid #000 !important;
            border-radius: 12px 12px 0 0;
            padding: 10px 15px !important;
            font-weight: 800;
            font-weight: 800;
            display: block; /* Make date header block */
            text-align: left;
        }

        /* iPad/Tablet specific styles for WhatsApp Widget */
        #whatsapp-chat-window {
            position: absolute; 
            bottom: 95px; 
            right: 0px; 
            width: 320px; 
            max-width: 90vw; 
            background: white; 
            border: 3px solid #000; 
            box-shadow: 6px 6px 0px #000; 
            border-radius: 15px; 
            min-height: 380px; 
            max-height: 90vh;
            overflow: visible;
        }
        
        /* Hide the 'Fecha' label override if needed, or keep it */
        #mis-turnos td:nth-of-type(1):before { content: none !important; }
    }

    /* Mobile Phone Specific Adjustments for WhatsApp Widget */
    @media (max-width: 480px) {
        #whatsapp-chat-window {
            width: 85vw !important;
            max-width: 320px !important;
            right: 5px !important; /* Added space from right edge */
            bottom: 85px !important;
            min-height: auto !important;
            max-height: 80vh !important;
            box-sizing: border-box !important; /* Ensure border is included in width */
            margin-right: 0 !important;
        }

        #whatsapp-chat-window > div:nth-child(2) { /* Body content */
             overflow-y: auto !important;
             max-height: 55vh !important;
        }
    }
</style>

<!-- Top-Down Menu Overlay (Replaces Bottom Nav) -->
<div id="patient-overlay" onclick="togglePatientMenu()" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(168, 226, 250, 0.3); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 9998; transition: all 0.3s ease;"></div>

<div id="patient-top-menu" style="display: flex; flex-direction: column; position: fixed; top: -150%; left: 0; width: 100%; background: white; border-bottom: 4px solid #000; z-index: 9999; padding: 1.5rem 1rem 2rem; transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); gap: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0 0.5rem;">
        <span style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.2rem; text-transform: uppercase;">Menú de Paciente</span>
        <button onclick="togglePatientMenu()" style="background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #000;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <nav style="display: flex; flex-direction: column; gap: 10px;">
        <a href="#" onclick="togglePatientMenu(); window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="menu-chip" style="background: var(--color-celeste);">
            <i class="fa-solid fa-calendar-plus"></i> <span>Reservar Turno</span>
        </a>
        <a href="#mis-turnos-section" onclick="togglePatientMenu()" class="menu-chip" style="background: var(--color-lila);">
            <i class="fa-solid fa-clock-rotate-left"></i> <span>Mis Turnos y Pagos</span>
        </a>
        <a href="#materiales" onclick="togglePatientMenu()" class="menu-chip" style="background: var(--color-amarillo);">
            <i class="fa-solid fa-folder-open"></i> <span>Mi Biblioteca</span>
        </a>
    </nav>
</div>

<style>
    /* Estilo de las píldoras del menú */
    .menu-chip {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.6rem 1rem; /* Even smaller padding */
        border: 3px solid #000;
        border-radius: 50px; 
        text-decoration: none;
        color: #000;
        font-weight: 400;
        font-size: 0.8rem; /* Smaller font size */
        font-family: 'Syne', sans-serif;
        box-shadow: 4px 4px 0px #000;
        transition: all 0.2s;
    }
    .menu-chip:active {
        transform: translate(2px, 2px);
        box-shadow: 0px 0px 0px #000;
    }
    .menu-chip i { font-size: 1.3rem; width: 25px; text-align: center; }

    /* Estado abierto */
    #patient-top-menu.active {
        transform: translateY(150%); /* Lo baja desde el top */
    }
    
    /* Mobile Override: Ensure menu works */
    @media (max-width: 768px) {
        #patient-top-menu.active {
            top: 0 !important; /* Force top 0 when active */
            transform: none !important; /* Use top transition instead */
        }
    }
</style>

<script>
function togglePatientMenu() {
    const menu = document.getElementById('patient-top-menu');
    const overlay = document.getElementById('patient-overlay');
    const logoutBtnHeader = document.querySelector('.header-logout-form'); // Seleccionamos el form del botón salir

    const isOpen = menu.classList.contains('active');

    if (isOpen) {
        menu.classList.remove('active');
        menu.style.top = '-150%';
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        
        // Mostrar botón del header
        if(logoutBtnHeader) logoutBtnHeader.classList.remove('hidden-btn');
    } else {
        menu.classList.add('active');
        menu.style.top = '0'; // Slide down
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Ocultar botón del header con transición
        if(logoutBtnHeader) logoutBtnHeader.classList.add('hidden-btn');
    }
}
</script>

<!-- Block 1: User Provided Patient Dashboard Content -->
<div class="container mt-16" style="padding-top: 1rem;">
    <style>
        @media (min-width: 769px) {
            .container.mt-16 {
                margin-top: 0 !important;
                padding-top: 2rem !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                padding-bottom: 40px !important;
            }
            @media (min-width: 1024px) {
                .flex.gap-4 > div:first-child { flex: 1.6 !important; }
                .flex.gap-4 > div:last-child { flex: 1 !important; }
                #booking { max-width: 920px !important; width: 100% !important; }
            }
        }
    </style>
    <style>
        @media (max-width: 768px) {
            .container.mt-16 {
                padding-top: 1rem !important;
                margin-top: 0 !important;
                padding-bottom: 3rem !important;
            }
        }
        
        /* Tablet and iPad optimizations */
        @media (min-width: 769px) and (max-width: 1024px) {
            .container.mt-16 {
                padding: 2rem 2rem 3rem !important;
            }
            
            .dashboard-flex-container {
                flex-direction: column !important;
                gap: 2rem !important;
            }
            
            #booking, #mis-turnos-section {
                min-width: 100% !important;
                width: 100% !important;
            }
            
            /* Reduce booking section bottom margin */
            .booking-section {
                margin-bottom: 1rem !important;
                padding-bottom: 1.5rem !important;
            }
            
            .days-grid {
                grid-template-columns: repeat(7, 1fr) !important;
            }
            
            /* More spacing for table on iPad */
            #mis-turnos table td,
            #mis-turnos table th {
                padding: 0.8rem !important;
            }
            
            /* Stretch table to full width on iPad */
            #mis-turnos table {
                width: 100% !important;
            }
            
            /* WhatsApp button adjustment for iPad */
            #whatsapp-widget-container {
                bottom: 2rem !important;
                right: 2rem !important;
            }
        }
        
        /* Landscape phone optimization */
        @media (max-height: 600px) and (orientation: landscape) {
            .container.mt-16 {
                padding-top: 0.5rem !important;
                padding-bottom: 1rem !important;
            }
            
            #patient-top-menu {
                padding: 1rem !important;
            }
            
            .menu-chip {
                padding: 0.4rem 0.8rem !important;
                font-size: 0.75rem !important;
            }
            
            #mis-turnos {
                padding: 1.5rem !important;
            }
            
            .booking-section {
                margin-top: 3.5rem !important;
                padding-top: 1.5rem !important;
            }
        }
        
        .days-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)) !important;
            gap: 0.5rem;
        }
        @media (min-width: 1024px) {
             .flex.gap-4 > div:first-child { flex: 1.2 !important; }
             .flex.gap-4 > div:last-child { flex: 1 !important; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/whatsapp_widget.css') }}">
    <style>
        .dashboard-flex-container {
            gap: 3rem;
            flex-wrap: wrap;
            align-items: stretch;
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .dashboard-flex-container {
                margin-top: 10px !important;
                gap: 2rem;
            }
        }
    </style>

@section('content')
<div class="dashboard-flex-container flex">
    
    <!-- Left Column: New Appointment Stepper -->
    <div style="flex: 1.5; min-width: 350px; display: flex; flex-direction: column;">
        <div id="booking" class="neobrutalist-card booking-section" style="margin-bottom: 4rem; padding-bottom: 3rem; flex: 1;">
            <style>
                @media (max-width: 768px) {
                    .booking-section {
                        margin-top: 2rem !important;
                        padding-top: 1.5rem !important;
                    }
                }
            </style>
            <!-- Feedback de Errores -->
            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <h3 class="mb-4" style="font-family: 'Syne', sans-serif; font-weight: 700; white-space: nowrap;"><i class="fa-solid fa-calendar-plus"></i> Reservar Nuevo Turno</h3>
                
                <div class="stepper-container">
                    <!-- Step Indicators -->
                    <div class="step-header no-select">
                        <div class="step-indicator active" id="ind-1">1</div>
                        <div class="step-indicator" id="ind-2">2</div>
                        <div class="step-indicator" id="ind-3">3</div>
                        <div class="step-indicator" id="ind-4">4</div>
                    </div>

                    <form id="reserve-form" action="{{ route('appointments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="appointment_date" id="final_date">
                        
                        <!-- Step 1: Day Selection -->
                        <div class="booking-step active" id="step-1">
                            <label class="block font-bold mb-4">Paso 1: Elegí el día</label>
                            <div class="days-grid" id="days-grid" style="margin-bottom: 2rem;">
                                <!-- Dynamics Days will be inserted by JS -->
                            </div>
                            <div class="flex justify-between mt-8">
                                <span></span>
                                <button type="button" class="neobrutalist-btn bg-celeste" onclick="nextStep(2)" id="next-1">Siguiente</button>
                            </div>
                        </div>

                        <!-- Step 2: Time Selection -->
                        <div class="booking-step" id="step-2">
                            <label class="block font-bold mb-4">Paso 2: Elegí el horario</label>
                            <div class="time-pills" id="times-grid" style="margin-bottom: 2rem;">
                                <!-- JS Generated Times -->
                            </div>
                            <div class="flex justify-between mt-12">
                                <button type="button" class="neobrutalist-btn bg-lila" onclick="prevStep(1)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-celeste" onclick="nextStep(3)" id="next-2">Siguiente</button>
                            </div>
                        </div>

                        <!-- Step 3: Modality Selection -->
                        <div class="booking-step" id="step-3">
                            <label class="block font-bold mb-4">Paso 3: Modalidad</label>
                            <div class="modality-selector" style="margin-bottom: 2rem;">
                                <div class="modality-btn" onclick="selectModality('presencial')" id="mod-presencial">
                                    <i class="fa-solid fa-house-user"></i>
                                    <span>Presencial</span>
                                </div>
                                <div class="modality-btn" onclick="selectModality('virtual')" id="mod-virtual">
                                    <i class="fa-solid fa-video"></i>
                                    <span>Virtual</span>
                                </div>
                            </div>
                            <input type="hidden" name="modalidad" id="selected_modalidad">
                            
                            <div class="flex justify-between mt-12">
                                <button type="button" class="neobrutalist-btn bg-lila" onclick="prevStep(2)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-celeste" onclick="nextStep(4)" id="next-3">Siguiente</button>
                            </div>
                        </div>

                        <!-- Step 4: Payment & Confirm -->
                        <div class="booking-step" id="step-4">
                            <label class="block font-bold mb-4">Paso 4: Pago y Confirmación</label>
                            
                            <div id="payment-instructions" style="background: white; padding: 1.2rem; border: 3px solid #000; margin-bottom: 2rem; box-shadow: 4px 4px 0px #000; border-radius: 12px; max-width: 400px; margin-left: auto; margin-right: auto;">
                                <h4 style="margin-bottom: 0.8rem; font-size: 0.9rem; font-weight: 900; text-align: center; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #eee; padding-bottom: 0.5rem;">Datos de Pago</h4>
                                <div onclick="copyAlias()" style="cursor: pointer; background: #f0f0f0; padding: 12px; border: 2px solid #000; border-radius: 8px; text-align: center; transition: all 0.2s;" onmouseover="this.style.background='#e0e0e0'" onmouseout="this.style.background='#f0f0f0'">
                                    <p style="margin:0; font-family: monospace; font-size: 1.1rem; font-weight: 900;">Alias: <span id="alias-text">nazarena.deluca</span></p>
                                    <span id="copy-badge-status" style="font-size: 0.7rem; font-weight: 700; color: #666;">(Toca para copiar)</span>
                                </div>
                                
                                <p id="payment-warning" style="margin-top: 1rem; font-weight: 800; color: rgb(221, 0, 0); display: block; font-size: 0.8rem; background: rgb(255, 238, 238); padding: 10px; border: 1px solid rgb(255, 204, 204); border-radius: 6px; text-align: center;">
                                    <i class="fa-solid fa-circle-exclamation"></i> Comprobante obligatorio para pacientes nuevos.
                                </p>
                            </div>

                            <div id="proof-upload-section" style="max-width: 400px; margin: 0 auto;">
                                <label class="block font-bold mb-2 text-center" style="font-size: 0.85rem;">Subir Comprobante:</label>
                                <div class="neobrutalist-input" style="padding: 10px; background: white; text-align: center; margin-bottom: 1rem;">
                                    <input type="file" name="proof" id="proof_input" style="font-size: 0.85rem; width: 100%; cursor: pointer;" accept="image/*,application/pdf" onchange="previewFile()" required="">
                                </div>
                                <div id="file-preview" style="display: none; margin-bottom: 1rem; border: 2px solid #000; padding: 5px; background: #eee; border-radius: 8px; text-align: center;">
                                    <!-- Preview content -->
                                </div>
                                <p style="font-size: 0.75rem; color: #555; background: #f9f9f9; padding: 10px; border: 2px dashed #ccc; border-radius: 8px; text-align: center;">
                                    <i class="fa-solid fa-info-circle"></i> Los presupuestos se envían por WhatsApp al contacto.
                                </p>
                            </div>

                            <div class="flex justify-between mt-12">
                                <button type="button" class="neobrutalist-btn bg-lila" onclick="prevStep(3)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-verde" onclick="confirmReserve()">Confirmar Turno</button>
                            </div>
                        </div>
                        <!-- Live Summary -->
                        <div id="booking-summary" style="margin-top: 1.5rem; padding: 1rem; background: #f8f8f8; border: 2px dashed #ccc; border-radius: 10px; display: none; font-weight: 700; color: #333;">
                            <i class="fa-solid fa-calendar-check"></i> <span id="summary-text">Reserva para...</span>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: My Appointments -->
    <div id="mis-turnos-section" style="flex: 2.5; min-width: 300px;">
        
        <style>
            /* Hide mobile labels on desktop */
            .mobile-label {
                display: none;
            }
            
            @media (max-width: 1024px) {
                #mis-turnos { padding: 1.5rem !important; }
                #mis-turnos h3 { font-size: 1.1rem; margin-bottom: 0.5rem !important; } /* Reduced margin */
                
                /* Fix button clipping */
                .neobrutalist-btn { margin: 5px !important; }
                .flex.justify-between { flex-wrap: wrap; gap: 10px; }
                     
                /* Mobile Card View for Table */
                #mis-turnos table, #mis-turnos thead, #mis-turnos tbody, #mis-turnos th, #mis-turnos td, #mis-turnos tr { 
                    display: block; 
                }
                
                #mis-turnos thead tr { 
                    position: absolute;
                    top: -9999px;
                    left: -9999px;
                }
                
                #mis-turnos tr { border-bottom: 3px solid #000 !important; margin-bottom: 1rem; background: #fff; padding: 10px; border: 2px solid #eee; border-radius: 8px; }
                
                #mis-turnos td { 
                    border: none !important;
                    position: relative;
                    padding-left: 50% !important; 
                    text-align: right;
                    padding-top: 5px !important;
                    padding-bottom: 5px !important;
                    min-height: 30px;
                }
                
                #mis-turnos td:before { 
                    position: absolute;
                    top: 6px;
                    left: 10px;
                    width: 45%; 
                    padding-right: 10px; 
                    white-space: nowrap;
                    text-align: left;
                    font-weight: 800;
                    color: #666;
                }
                
                /* Labels */
                /* Labels */
                #mis-turnos td:nth-of-type(1):before { content: "Turno:"; } /* Changed to "Turno:" for clarity */
                #mis-turnos td:nth-of-type(2):before { content: "Estado:"; }
                #mis-turnos td:nth-of-type(3):before { content: "Pago:"; }
                #mis-turnos td:nth-of-type(4):before { content: "Acciones:"; }

                /* Modified Date Style: Black and Smaller */
                #mis-turnos td:nth-of-type(1) { 
                    font-weight: 800; 
                    font-size: 0.95rem !important;
                    color: #111 !important;
                }
                
                /* Align content properly */
                #mis-turnos td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding-left: 10px !important;
                    text-align: right;
                }
                
                /* Show mobile label only on mobile */
                .mobile-label {
                    display: inline !important;
                    position: absolute;
                    left: 10px;
                    top: 12px;
                    font-weight: 800 !important;
                    color: #111 !important;
                }
                
                #mis-turnos td:before {
                    position: static;
                    font-weight: 700;
                    color: #666;
                }

                /* 4. Improve Actions Column for Mobile */
                #mis-turnos td:nth-of-type(4) {
                    flex-direction: column !important;
                    align-items: stretch !important; /* Full width children */
                    gap: 10px !important;
                    text-align: left !important;
                    padding-top: 15px !important;
                    padding-bottom: 15px !important;
                }
                
                #mis-turnos td:nth-of-type(4):before {
                    content: "Acciones:" !important;
                    display: block !important;
                    margin-bottom: 5px;
                    font-size: 0.85rem; /* Smaller font size as requested */
                    color: #000 !important;
                    font-family: 'Inter', sans-serif !important;
                    font-weight: 800 !important;
                    text-decoration: none !important;
                }
                
                #mis-turnos td:nth-of-type(4) .neobrutalist-btn {
                    width: 100% !important;
                    text-align: center !important;
                    margin: 0 !important;
                    display: block;
                }
            }
        </style>
            <div id="mis-turnos" class="neobrutalist-card" style="width: 100% !important; max-width: 100% !important; padding: 2.5rem 1.5rem !important; background: white; border: 3px solid #000; box-shadow: 8px 8px 0px #000;">
            <h3 class="no-select" style="background: var(--color-lila); display: inline-block; padding: 0.5rem 1rem; border: 3px solid #000; font-size: 1.4rem; margin-bottom: 1.5rem; box-shadow: 4px 4px 0px #000; font-family: 'Syne', sans-serif; font-weight: 700; white-space: nowrap;">Mis Turnos y Pagos</h3>
            
            <div style="overflow-x: auto; width: 100%;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-family: 'Inter', sans-serif;">
                    <thead>
                        <tr style="border-bottom: 3px solid #000;">
                            <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Fecha</th>
                            <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Estado Turno</th>
                            <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Pago</th>
                            <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Acciones</th>
                        </tr>
                    </thead>
                        <tbody>
                            @if(isset($appointments) && $appointments->count() > 0)
                                @foreach($appointments as $appt)
                                    <tr style="border-bottom: 1px solid #2D2D2D;">
                                        <td style="padding: 0.5rem; white-space: nowrap;"><span class="mobile-label" style="font-weight: 700; color: #666; margin-right: 8px;">Fecha:</span>{{ $appt->fecha_hora->format('d/m H:i') }}</td>
                                        <td style="padding: 0.5rem;">
                                            <span style="font-weight: bold; color: {{ $appt->estado == 'cancelado' ? 'red' : 'green' }}">
                                                {{ ucfirst($appt->estado) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.5rem;">
                                            @if($appt->payment)
                                                <span style="color: green; font-weight: bold;">{{ ucfirst($appt->payment->estado) }}</span>
                                            @else
                                                <span style="color: red; font-weight: bold;">Pendiente</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.5rem;">
                                            @if($appt->estado != 'cancelado')
                                                <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST">
                                                    @csrf
                                                    <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" 
                                                        onclick="window.showConfirm('¿Seguro querés cancelar este turno?', () => this.closest('form').submit())">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 1rem;">No tenés turnos registrados.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Biblioteca de Materiales (NEW) -->
        <div id="materiales" class="neobrutalist-card" style="margin-top: 2rem; padding: 2.5rem !important; border: 3px solid #000; box-shadow: 8px 8px 0px #000; background: white;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 4px solid #000; padding-bottom: 0.8rem; font-size: 1.4rem; font-family: 'Syne', sans-serif; font-weight: 700;"><i class="fa-solid fa-folder-open"></i> Mi Biblioteca de Materiales</h3>
            
                @if(isset($resources) && count($resources) > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                        @foreach($resources as $res)
                        <div class="neobrutalist-card" style="background: white; border-width: 4px; padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-shadow: 6px 6px 0px #000 !important;">
                            <div>
                                <div style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--color-celeste);">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <h4 style="margin: 0 0 0.8rem 0; font-weight: 900; line-height: 1.2; font-size: 1.2rem;">{{ $res->title }}</h4>
                                @if($res->description)
                                    <p style="font-size: 0.95rem; color: #555; margin-bottom: 1.5rem;">{{ $res->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('resources.download', $res->id) }}" class="neobrutalist-btn bg-amarillo w-full text-center" style="font-size: 1rem; padding: 12px; font-weight: 800;">
                                <i class="fa-solid fa-download"></i> Descargar
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; background: #f9f9f9; border: 2px dashed #ccc; border-radius: 10px; font-family: 'Inter', sans-serif;">
                        <p style="color: #666; font-weight: 700;">No hay materiales compartidos todavía.</p>
                    </div>
                @endif
            </div>

    </div>
        <!-- Custom Alert Modal (Cartel) -->
        <div id="alert-modal-overlay" class="confirm-modal-overlay" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);">
            <div class="confirm-modal" style="max-width: 400px; padding: 0 !important; border: 5px solid #000; box-shadow: 10px 10px 0px #000; border-radius: 20px;">
                <div style="background-color: var(--color-celeste); padding: 1rem; border-bottom: 5px solid #000; text-align: center;">
                    <h3 style="margin: 0; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.5rem;">¡ATENCIÓN!</h3>
                </div>

                <div style="padding: 2rem; text-align: center;">
                    <p id="alert-modal-message" style="font-family: 'Manrope', sans-serif; font-weight: 700; margin-bottom: 1.5rem;">
                        Primero debes seleccionar un día para continuar.
                    </p>

                    <div style="display: flex; justify-content: center;">
                        <button onclick="closeAlert()" class="neobrutalist-btn bg-amarillo" style="min-width: 120px; margin-top: 0;">
                            ENTENDIDO
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
        

@php
// Variables for script
$availabilities = $availabilities ?? [];
$googleAvailableSlots = $googleAvailableSlots ?? collect([]);
$occupiedSlots = $occupiedSlots ?? [];
$user = auth()->user();
$tipoPaciente = $user && $user->paciente ? $user->paciente->tipo_paciente : 'nuevo';
$blockWeekends = $blockWeekends ?? false;
$blockedDays = $blockedDays ?? [];
@endphp

<script>
    const availabilities = @json($availabilities);
    const googleSlots = @json($googleAvailableSlots->map(fn($s) => [
        'date' => is_array($s) ? $s['start_time']->format('Y-m-d') : $s->start_time->format('Y-m-d'), 
        'time' => is_array($s) ? $s['start_time']->format('H:i') : $s->start_time->format('H:i')
    ]));
    const occupiedSlots = @json($occupiedSlots);
    const userType = "{{ $tipoPaciente }}";
    const weekendsBlocked = @json($blockWeekends);
    const specificBlockedDays = @json($blockedDays);
    
    let selectedDay = null; // Format YYYY-MM-DD
    let selectedTime = null; // Format HH:mm
    let selectedModalidad = null;

    function initStepper() {
        generateDays();
        if (userType === 'nuevo') {
            document.getElementById('payment-warning').style.display = 'block';
            document.getElementById('proof_input').required = true;
        }
    }

    function generateDays() {
        const grid = document.getElementById('days-grid');
        grid.innerHTML = '';
        
        const today = new Date();
        // Generate next 28 days (4 weeks)
        for (let i = 1; i <= 28; i++) {
            const date = new Date();
            date.setDate(today.getDate() + i);
            
            const dayOfWeek = date.getDay(); // 0-6
            const dateStr = date.toISOString().split('T')[0];
            // Priority: Google Slots override blocks
            const hasGoogleSlots = googleSlots.some(s => s.date === dateStr);
            const isBaseAvailable = availabilities.some(a => a.dia_semana == dayOfWeek);
            
            const isWeekendBlocked = weekendsBlocked && (dayOfWeek === 0 || dayOfWeek === 6);
            const isSpecificBlocked = specificBlockedDays.includes(dateStr);
            
            // Logic: 
            // 1. If hasGoogleSlots is true, it's AVAILABLE (overrides all blocks).
            // 2. If NO Google Slots, we check standard blocks and base availability.
            let isBlocked = false;

            if (hasGoogleSlots) {
                isBlocked = false; // Explicit availability overrides everything
            } else {
                // Formatting fallback to standard availability
                if (!isBaseAvailable) {
                    isBlocked = true; // No basic slots
                } else if (isWeekendBlocked || isSpecificBlocked) {
                    isBlocked = true; // Blocked by rule
                }
            }

            const dayBtn = document.createElement('div');
            // Quitamos 'disabled' para que el usuario pueda probar el flujo completo
            dayBtn.className = 'day-btn' + (isBlocked ? ' disabled' : ''); 
            
            // Add style for blocked
            if (isBlocked) {
                dayBtn.style.opacity = '0.3';
                dayBtn.style.cursor = 'not-allowed';
                dayBtn.style.background = '#ddd';
                dayBtn.title = isSpecificBlocked ? 'Día no disponible' : 'Fines de semana no disponibles';
            }

            dayBtn.innerHTML = `
                <div style="font-size: 0.6rem; opacity: 0.7;">${date.toLocaleDateString('es-AR', { weekday: 'short' }).toUpperCase()}</div>
                <div style="font-size: 1.1rem;">${date.getDate()}</div>
                <div style="font-size: 0.6rem;">${date.toLocaleDateString('es-AR', { month: 'short' })}</div>
            `;
            
            if (!isBlocked) {
                dayBtn.onclick = () => selectDay(dateStr, dayBtn);
            }

            grid.appendChild(dayBtn);
        }
    }

    function updateSummary() {
        const summary = document.getElementById('booking-summary');
        const text = document.getElementById('summary-text');
        
        if (!selectedDay) {
            summary.style.display = 'none';
            return;
        }

        summary.style.display = 'block';
        let msg = `Reserva para el ${selectedDay.split('-').reverse().join('/')}`;
        
        if (selectedTime) {
            msg += ` a las ${selectedTime}`;
        }
        
        if (selectedModalidad) {
            msg += ` (${selectedModalidad === 'virtual' ? 'Virtual' : 'Presencial'})`;
        }

        text.innerText = msg;
    }

    function selectDay(dateStr, element) {
        if (selectedDay === dateStr) {
            // Deseleccionar si ya estaba marcado
            selectedDay = null;
            element.classList.remove('selected');
            selectedTime = null;
            document.getElementById('times-grid').innerHTML = '';
            document.getElementById('final_date').value = '';
            updateSummary();
            return;
        }

        selectedDay = dateStr;
        document.querySelectorAll('.day-btn').forEach(b => b.classList.remove('selected'));
        element.classList.add('selected');
        
        // Reset steps forward
        selectedTime = null;
        updateSummary();
        generateTimes(dateStr);
    }

    function generateTimes(dateStr) {
        const grid = document.getElementById('times-grid');
        grid.innerHTML = '';
        
        const date = new Date(dateStr + 'T12:00:00'); // Use noon to avoid TZ issues
        const dayOfWeek = date.getDay();
        
        // Priority 1: Google Calendar "LIBRE" slots
        let dayGoogleSlots = googleSlots.filter(s => s.date === dateStr);
        
        let slotsToRender = [];
        
        if (dayGoogleSlots.length > 0) {
            slotsToRender = dayGoogleSlots.map(s => ({ hora_inicio: s.time }));
        } else {
            // Priority 2: Fallback to base hours if no Google Slots for this specific day
            slotsToRender = availabilities.filter(a => a.dia_semana == dayOfWeek);
        }
        
        if (slotsToRender.length === 0) {
            // Show booking form instead of waitlist banner when no times available
            grid.innerHTML = '';
            const bookingForm = document.getElementById('booking');
            if (bookingForm && bookingForm.style.display !== 'none') {
                // Scroll to booking form
                bookingForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            return;
        }

        // Sort slots by time
        slotsToRender.sort((a,b) => a.hora_inicio.localeCompare(b.hora_inicio));

        slotsToRender.forEach(slot => {
            const timeStr = slot.hora_inicio.substring(0, 5); // HH:mm
            const fullDateTime = dateStr + ' ' + timeStr + ':00';
            const isOccupied = occupiedSlots.includes(fullDateTime);
            
            const pillContainer = document.createElement('div');
            pillContainer.style.display = 'flex';
            pillContainer.style.alignItems = 'center';
            pillContainer.style.gap = '0.5rem';

            const pill = document.createElement('div');
            pill.className = 'time-pill' + (isOccupied ? ' disabled' : '');
            pill.innerText = timeStr;
            
            pillContainer.appendChild(pill);

            if (!isOccupied) {
                pill.onclick = () => selectTime(timeStr, pill);
            } else {
                pill.style.opacity = '0.3';
                pill.style.cursor = 'not-allowed';
                pill.style.textDecoration = 'line-through';
                
                const waitBtn = document.createElement('button');
                waitBtn.type = 'button';
                waitBtn.className = 'neobrutalist-btn';
                waitBtn.style.padding = '2px 8px';
                waitBtn.style.fontSize = '0.6rem';
                waitBtn.style.background = 'var(--color-amarillo)';
                waitBtn.innerHTML = '<i class="fa-solid fa-bell"></i> Avisarme';
                waitBtn.title = 'Unirse a la lista de espera para este horario';
                waitBtn.onclick = () => joinWaitlist(dateStr, timeStr);
                pillContainer.appendChild(waitBtn);
            }
            grid.appendChild(pillContainer);
        });
    }

    function selectTime(timeStr, element) {
        if (selectedTime === timeStr) {
            selectedTime = null;
            element.classList.remove('selected');
            updateSummary();
            return;
        }
        selectedTime = timeStr;
        document.querySelectorAll('.time-pill').forEach(p => p.classList.remove('selected'));
        element.classList.add('selected');
        updateSummary();
    }

    function joinWaitlist(date, time) {
        fetch('{{ route("waitlist.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ fecha_especifica: date, hora_inicio: time })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al unirse a la lista de espera.');
        });
    }

    function selectModality(val) {
        if (selectedModalidad === val) {
            selectedModalidad = null;
            document.getElementById('selected_modalidad').value = '';
            document.querySelectorAll('.modality-btn').forEach(b => b.classList.remove('selected'));
            updateSummary();
            return;
        }
        selectedModalidad = val;
        document.getElementById('selected_modalidad').value = val;
        
        document.querySelectorAll('.modality-btn').forEach(b => b.classList.remove('selected'));
        document.getElementById('mod-' + val).classList.add('selected');
        updateSummary();
    }

    function previewFile() {
        const input = document.getElementById('proof_input');
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            
            if (file.type.startsWith('image/')) {
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '200px';
                    img.style.borderRadius = '5px';
                    preview.appendChild(img);
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                const fileURL = URL.createObjectURL(file);
                preview.innerHTML = `
                    <div style="position: relative; height: 350px; overflow: auto; border-radius: 5px; border: 1px solid #ccc;">
                        <embed src="${fileURL}#toolbar=0&navpanes=0" type="application/pdf" width="100%" height="100%" style="border: none;" />
                    </div>
                    <p style="margin-top: 5px; font-weight: bold; font-size: 0.8rem;">${file.name}</p>
                `;
                preview.style.display = 'block';
            }
        } else {
            preview.style.display = 'none';
        }
    }

    function copyAlias() {
        const text = document.getElementById('alias-text').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const badge = document.getElementById('copy-badge-status');
            badge.innerText = '¡Copiado!';
            badge.innerText = '¡Copiado!';
            badge.style.color = '#166534'; // Dark green for visibility
            setTimeout(() => {
                badge.innerText = '(Toca para copiar)';
                badge.style.color = '#666';
            }, 2000);
        });
    }

    window.showAlert = function(msg) {
        document.getElementById('alert-modal-message').innerText = msg;
        document.getElementById('alert-modal-overlay').style.display = 'flex';
    };

    function closeAlert() {
        document.getElementById('alert-modal-overlay').style.display = 'none';
    }

    function nextStep(step) {
        // Validación según el paso actual antes de avanzar
        const currentStep = document.querySelector('.booking-step.active');
        const currentStepId = currentStep ? parseInt(currentStep.id.split('-')[1]) : 1;

        if (step > currentStepId) { // Solo validar si intentamos avanzar
            if (currentStepId === 1 && !selectedDay) {
                window.showAlert('Primero debes seleccionar un día para continuar.');
                return;
            }
            if (currentStepId === 2 && !selectedTime) {
                window.showAlert('Primero debes seleccionar un horario para continuar.');
                return;
            }
            if (currentStepId === 3 && !selectedModalidad) {
                window.showAlert('Primero debes seleccionar la modalidad (Virtual o Presencial).');
                return;
            }
        }

        document.querySelectorAll('.booking-step').forEach(s => s.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');
        
        document.querySelectorAll('.step-indicator').forEach((ind, i) => {
            if (i + 1 < step) ind.classList.add('completed');
            if (i + 1 == step) ind.classList.add('active');
            else ind.classList.remove('active');
        });

        // Navbar scroll removed - neobrutalist navbar is always visible

        if (step === 4) {
            document.getElementById('final_date').value = selectedDay + ' ' + selectedTime;
        }
    }

    function prevStep(step) {
        nextStep(step);
    }

    function confirmReserve() {
        if (userType === 'nuevo') {
            const proof = document.getElementById('proof_input');
            if (!proof.files.length) {
                window.showAlert('Como paciente nuevo, debes subir el comprobante para reservar.');
                return;
            }
        }
        
        let dateParts = selectedDay.split('-'); // YYYY-MM-DD
        let formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`; // DD/MM/YYYY
        
        window.showConfirm('¿Confirmás la reserva para el día ' + formattedDate + ' a las ' + selectedTime + '?', function() {
            document.getElementById('reserve-form').submit();
        });
    }

    // Initialize
    initStepper();

    function confirmDeleteAccount() {
        window.openDeleteModal();
    }
</script>
<!-- Patient Sidebar Navigation (Replaces Bottom Nav) -->
<div id="patient-overlay" onclick="togglePatientSidebar()" style="
    display: none; 
    position: fixed; 
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%; 
    background: rgba(103, 58, 183, 0.4); /* Lila transparente */
    backdrop-filter: blur(8px); 
    -webkit-backdrop-filter: blur(8px); 
    z-index: 10000; 
    transition: all 0.4s ease;
"></div>

<div id="patient-sidebar" style="position: fixed; top: 0; left: -300px; width: 280px; height: 100%; background: white; border-right: 5px solid #000; z-index: 10001; transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; padding: 2rem 1.5rem; box-shadow: 15px 0px 0px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <span style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.2rem; text-transform: uppercase;">Menú</span>
        <button onclick="togglePatientSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <nav style="display: flex; flex-direction: column; gap: 1rem;">
        <a href="#booking" onclick="togglePatientSidebar()" class="sidebar-link" style="background: var(--color-celeste);">
            <i class="fa-solid fa-calendar-plus"></i> Reservar Turno
        </a>
        <a href="#mis-turnos-section" onclick="togglePatientSidebar()" class="sidebar-link" style="background: var(--color-lila);">
            <i class="fa-solid fa-clock-rotate-left"></i> Mis Turnos
        </a>
        <a href="#materiales" onclick="togglePatientSidebar()" class="sidebar-link" style="background: var(--color-amarillo);">
            <i class="fa-solid fa-folder-open"></i> Materiales
        </a>
    </nav>

    <div style="margin-top: auto; padding-top: 2rem; border-top: 2px dashed #000;">
        <button onclick="openLogoutModal()" style="width: 100%; padding: 1rem; background: #fee2e2; border: 3px solid #000; border-radius: 12px; color: #e11d48; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
            <i class="fa-solid fa-right-from-bracket"></i> CERRAR SESIÓN
        </button>
    </div>
</div>

<style>
    .sidebar-link {
        text-decoration: none;
        color: #000;
        font-weight: 800;
        padding: 1.2rem;
        border: 3px solid #000;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1rem;
        transition: transform 0.2s;
    }
    .sidebar-link i { font-size: 1.2rem; width: 25px; text-align: center; color: #000; }
    .sidebar-link:active { transform: scale(0.95); }
    
    #patient-sidebar.open {
        left: 0 !important;
    }
</style>

<script>
    function togglePatientSidebar() {
        const sidebar = document.getElementById('patient-sidebar');
        const overlay = document.getElementById('patient-overlay');
        
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            overlay.style.display = 'none';
            document.body.style.overflow = ''; 
        } else {
            sidebar.classList.add('open');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden'; 
        }
    }
</script>
@endsection
