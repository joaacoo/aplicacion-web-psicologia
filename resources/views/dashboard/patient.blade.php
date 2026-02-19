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
    .time-pills {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
        justify-content: center !important; /* Center the grid items horizontally */
        justify-items: center;
        align-items: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .time-pill:not(.disabled) {
        border: 3px solid #000 !important;
        box-shadow: 2px 2px 0px #000 !important;
        width: 100% !important;
        min-width: 140px !important;
        white-space: nowrap !important;
        text-align: center !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
    }
    .time-pill.disabled {
        width: 100% !important;
        min-width: 140px !important;
        white-space: nowrap !important;
        text-align: center !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
    }
    .modality-btn {
        border: 3px solid #000 !important;
        box-shadow: 3px 3px 0px #000 !important;
    }
    
    .hide-mobile {
        display: inline-block;
    }
    
    @media (max-width: 768px) {
        .hide-mobile { display: none !important; }
    }
    
    .disabled-btn {
        opacity: 0.5 !important;
        pointer-events: none !important;
        cursor: not-allowed !important;
        background: #e5e7eb !important;
        box-shadow: none !important;
        border-color: #9ca3af !important;
        color: #9ca3af !important;
    }

    /* Mobile & Tablet (iPad) Layout Enhancements */
    @media (max-width: 1024px) {
        .mobile-nav-bar { display: flex !important; }
        
        /* Turnos as Cards on Mobile */
        #mis-turnos tr {
            display: block;
            border: 3px solid #000 !important;
            box-shadow: none !important;
            border-radius: 15px !important;
            margin-bottom: 1.5rem !important;
            background: #fff;
            position: relative;
            overflow: hidden;
        }
        #mis-turnos thead { display: none; }
        
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
            border-radius: 0;
            padding: 10px 15px !important;
            font-weight: 800;
            display: block; 
            text-align: left;
        }

        #mis-turnos td:nth-of-type(1):before { content: none !important; }

        /* WhatsApp Widget for Tablets/Mobile */
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
        <a href="#documentos" onclick="togglePatientMenu()" class="menu-chip" style="background: #fdba74;">
            <i class="fa-solid fa-file-contract"></i> <span>Mis Documentos</span>
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
        /* Mobile & Base Container */
        .container.mt-16 {
            padding-left: 1.2rem !important;
            padding-right: 1.2rem !important;
            padding-top: 1rem !important;
            margin-top: 0 !important;
            padding-bottom: 3rem !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        @media (min-width: 769px) {
            .container.mt-16 {
                padding-top: 2rem !important;
                padding-left: 2rem !important;
                padding-right: 2rem !important;
                padding-bottom: 40px !important;
            }
        }
        
        @media (min-width: 1024px) {
            #booking { max-width: 920px !important; width: 100% !important; }
        }
    </style>
        
    <link rel="stylesheet" href="{{ asset('css/whatsapp_widget.css') }}">
    <style>
        .dashboard-flex-container {
            display: flex;
            gap: 3rem;
            flex-wrap: wrap;
            align-items: stretch;
            margin-top: 30px;
            width: 100%;
        }
        .booking-column {
            flex: 0 0 350px;
            width: 350px;
            min-width: 0; /* Critical for flex items to not overflow */
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 1024px) {
            .dashboard-flex-container {
                flex-direction: column !important;
                gap: 2rem !important;
                margin-top: 10px !important;
            }
            .booking-column {
                flex: 1;
                width: 100% !important;
                min-width: 100% !important;
            }
            #booking, #mis-turnos-section {
                width: 100% !important;
                min-width: 0 !important;
            }
        }
    </style>

@section('content')

    <!-- Top Banner: Next Session -->
    @if(isset($nextAppointment))
        @php
            $isToday = $nextAppointment->fecha_hora->isToday();
            $isTomorrow = $nextAppointment->fecha_hora->isTomorrow();
        @endphp
        @if(($isToday || $isTomorrow) && $nextAppointment->estado == 'confirmado')
            <div class="neobrutalist-card next-session-banner" style="margin-bottom: 2rem; margin-top: 1.5rem; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px #000; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; width: 100%; box-sizing: border-box;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="font-size: 1.8rem;">
                        <!-- Changed emoji to something related to time/session as requested -->
                        ⏰
                    </div>
                    <div>
                        <h4 style="margin: 0; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.1rem; text-transform: uppercase;">
                            @if($isToday) Hoy tenés sesión @else Mañana tenés sesión @endif
                        </h4>
                        <p style="margin: 5px 0 0; font-weight: 600; font-size: 0.9rem; color: #444;">
                            {{ $nextAppointment->fecha_hora->format('H:i') }} hs - Modalidad: {{ ucfirst($nextAppointment->modalidad ?? 'Virtual') }}
                        </p>
                    </div>
                </div>
                
                @if(($nextAppointment->modalidad ?? 'virtual') == 'virtual')
                    <a href="{{ $nextAppointment->meet_link ?? '#' }}" target="_blank" class="neobrutalist-btn" style="background: white; color: #000; border: 2px solid #000; padding: 0.5rem 1rem; text-decoration: none; font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; box-shadow: 3px 3px 0px #000;">
                        <img src="https://www.gstatic.com/meet/icons/logo_meet_2020q4_48dp_c6394966c6f534de2833.png" alt="Meet" style="width: 24px; height: 24px;">
                        Unirse al Meet
                    </a>
                @elseif(($nextAppointment->modalidad ?? 'presencial') == 'presencial')
                    <a href="https://www.google.com/maps/search/?api=1&query=626+Somellera,+Adrogue" target="_blank" class="neobrutalist-btn location-btn-mobile" style="background: white; color: #000; border: 2px solid #000; padding: 0.5rem 1rem; text-decoration: none; font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; box-shadow: 3px 3px 0px #000;">
                        <i class="fa-solid fa-map-location-dot" style="font-size: 1.2rem; color: #e11d48;"></i>
                        Ver Ubicación
                    </a>
                @endif
            </div>
        @endif
        <style>
            @media (max-width: 768px) {
                .next-session-banner { margin-top: 6rem !important; }
                .location-btn-mobile {
                    padding: 0.4rem 0.8rem !important;
                    font-size: 0.8rem !important;
                    gap: 5px !important;
                }
                .location-btn-mobile i {
                    font-size: 1rem !important;
                }
            }
        </style>
    @endif

<div class="dashboard-flex-container flex">
    
    <!-- Left Column: New Appointment Stepper -->
    <div class="booking-column">
        <div id="booking" class="neobrutalist-card booking-section" style="margin-bottom: 4rem; padding-bottom: 3rem;">
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
                                <button type="button" class="neobrutalist-btn bg-celeste disabled-btn" onclick="nextStep(2)" id="next-1">Siguiente</button>
                            </div>
                        </div>

                        <!-- Step 2: Modality Selection -->
                        <div class="booking-step" id="step-2">
                            <label class="block font-bold mb-4">Paso 2: Modalidad</label>
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
                            
                            <div class="flex justify-between mt-12" style="gap: 1rem;">
                                <button type="button" class="neobrutalist-btn bg-lila" onclick="prevStep(1)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-celeste disabled-btn" onclick="nextStep(3)" id="next-2">Siguiente</button>
                            </div>
                        </div>

                        <!-- Step 3: Time Selection -->
                        <div class="booking-step" id="step-3">
                            <label class="block font-bold mb-4">Paso 3: Elegí el horario</label>
                            <div class="time-pills" id="times-grid" style="margin-bottom: 2rem;">
                                <!-- JS Generated Times -->
                            </div>
                            <div class="flex justify-between mt-12" style="gap: 1rem;">
                                <button type="button" class="neobrutalist-btn bg-lila" onclick="prevStep(2)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-celeste disabled-btn" onclick="nextStep(4)" id="next-3">Siguiente</button>
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

                            <div class="flex justify-between mt-12" style="gap: 1rem;">
                                <button type="button" class="neobrutalist-btn bg-lila" onclick="prevStep(3)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-verde" onclick="confirmReserve()">Confirmar Turno</button>
                            </div>
                        </div>
<!-- Live Summary (Dynamic height) -->
                        <div id="booking-summary-container" style="min-height: 0; transition: all 0.3s ease-in-out; opacity: 0; max-height: 0; overflow: hidden;">
                            <div id="booking-summary" style="padding: 1rem; background: #f8f8f8; border: 2px dashed #ccc; border-radius: 10px; font-weight: 700; color: #333; margin-top: 1.5rem;">
                                <i class="fa-solid fa-calendar-check"></i> <span id="summary-text">Reserva para...</span>
                            </div>
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
                #mis-turnos td:nth-of-type(1):before { content: "Turno:"; } 
                #mis-turnos td:nth-of-type(2):before { content: "Modalidad:"; }
                #mis-turnos td:nth-of-type(3):before { content: "Estado:"; }
                #mis-turnos td:nth-of-type(4):before { content: "Pago:"; }
                #mis-turnos td:nth-of-type(5):before { content: "Acciones:"; }
                
                /* Update Actions Column Index for Mobile Styling */
                #mis-turnos td:nth-of-type(5) {
                    flex-direction: column !important;
                    align-items: stretch !important;
                    gap: 10px !important;
                    text-align: left !important;
                    padding-top: 15px !important;
                    padding-bottom: 15px !important;
                }
                #mis-turnos td:nth-of-type(5):before {
                    content: "Acciones:" !important;
                    display: block !important;
                    margin-bottom: 5px;
                    font-size: 0.85rem;
                    color: #000 !important;
                    font-family: 'Inter', sans-serif !important;
                    font-weight: 800 !important;
                    text-decoration: none !important;
                }
                #mis-turnos td:nth-of-type(5) .neobrutalist-btn {
                    width: 100% !important;
                    text-align: center !important;
                    margin: 0 !important;
                    display: block;
                }
                
                .actions-wrapper {
                    flex-direction: column !important;
                    width: 100% !important;
                }
                .actions-wrapper a, .actions-wrapper form {
                    width: 100% !important;
                    flex: 1 1 auto;
                }

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


            }
        </style>
            <!-- Fixed Height Container for 3 Items Pagination -->
            <div id="mis-turnos" class="neobrutalist-card" style="width: 100% !important; max-width: 100% !important; padding: 2.5rem 1.5rem !important; background: white; border: 3px solid #000; box-shadow: 8px 8px 0px #000; min-height: 500px; display: flex; flex-direction: column;">
            <h3 class="no-select" style="background: var(--color-lila); display: inline-block; padding: 0.5rem 1rem; border: 3px solid #000; font-size: 1.4rem; margin-bottom: 1.5rem; box-shadow: 4px 4px 0px #000; font-family: 'Syne', sans-serif; font-weight: 700; white-space: nowrap;">Mis Turnos y Pagos</h3>
            
            @if(isset($appointments) && $appointments->count() > 0)
                <div style="overflow-x: auto; width: 100%; flex-grow: 1;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-family: 'Inter', sans-serif;">
                        <thead>
                            <tr style="border-bottom: 3px solid #000;">
                                <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Fecha</th>
                                <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Modalidad</th>
                                <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Estado Turno</th>
                                <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Pago</th>
                                <th style="text-align: left; padding: 0.5rem; font-weight: 800;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appt)
                                <tr style="border-bottom: 1px solid #2D2D2D;">
                                    <td style="padding: 0.5rem; white-space: nowrap;"><span class="mobile-label" style="font-weight: 700; color: #666; margin-right: 8px;">Fecha:</span>{{ $appt->fecha_hora->format('d/m H:i') }}</td>
                                    <td style="padding: 0.5rem;">
                                        {{ ucfirst($appt->modalidad ?? 'Virtual') }}
                                    </td>
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
                                        @if($appt->fecha_hora->isPast())
                                            <span class="neobrutalist-badge" style="background: #e5e7eb; color: #374151; padding: 0.2rem 0.6rem; border-radius: 4px; font-weight: 700; border: 1px solid #9ca3af;">Finalizado</span>
                                        @elseif($appt->estado == 'cancelado')
                                            <span style="color: red; font-weight: 800;">Cancelado</span>
                                        @else
                                            <div class="actions-wrapper" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                @if(($appt->modalidad ?? 'virtual') == 'virtual')
                                                    @if($appt->fecha_hora->isToday() || $appt->fecha_hora->isTomorrow()) 
                                                        <a href="{{ $appt->meet_link ?? '#' }}" target="_blank" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background: #fff; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center;">
                                                            <i class="fa-solid fa-video"></i> Unirse
                                                        </a>
                                                    @endif
                                                @elseif(($appt->modalidad ?? 'presencial') == 'presencial')
                                                    <a href="https://www.google.com/maps/search/?api=1&query=626+Somellera,+Adrogue" target="_blank" class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background: #fff; border: 2px solid #000; text-decoration: none; color: #000; display: inline-flex; align-items: center; gap: 4px; justify-content: center;">
                                                        <i class="fa-solid fa-map-pin"></i> Ubicación
                                                    </a>
                                                @endif
                                                
                                                @if(!$appt->fecha_hora->isPast())
                                                    <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" style="display:inline; width: 100%;">
                                                        @csrf
                                                        <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; width: 100%;" 
                                                            onclick="window.showConfirm('¿Seguro querés cancelar este turno?', () => this.closest('form').submit())">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <style>
                                                @media (min-width: 1025px) {
                                                    .actions-wrapper a, .actions-wrapper form {
                                                        flex: 1;
                                                        width: auto !important;
                                                    }
                                                }
                                            </style>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; padding: 2rem 0;">
                    <div style="background: var(--color-amarillo); border: 3px solid #000; padding: 2.5rem; box-shadow: 6px 6px 0px #000; border-radius: 12px; text-align: center; max-width: 400px; width: 100%;">
                        <i class="fa-solid fa-calendar-xmark" style="font-size: 2.5rem; margin-bottom: 1rem; display: block;"></i>
                        <p style="font-weight: 800; font-size: 1.1rem; margin: 0; font-family: 'Syne', sans-serif;">No tenés turnos registrados.</p>
                    </div>
                </div>
            @endif

                @if ($appointments->hasPages())
                    <style>
                        @media (max-width: 600px) {
                            .pagination-mobile-btn {
                                padding: 0.5rem 1rem !important;
                                font-size: 0.8rem !important;
                                min-width: 0; 
                                width: auto !important; /* Auto width */
                                text-align: center;
                                transition: none !important; 
                                transform: none !important; 
                                display: inline-flex !important;
                                justify-content: center;
                                align-items: center;
                            }
                            .pagination-mobile-btn:active, .pagination-mobile-btn:hover {
                                transform: none !important;
                                box-shadow: 3px 3px 0px #000 !important; 
                            }
                            .pagination-mobile-indicator {
                                font-size: 0.8rem !important;
                            }
                        }
                    </style>
                    <div style="display: flex; justify-content: center; gap: 0.8rem; margin-top: auto; padding-top: 1.5rem; align-items: center; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 0.5rem; width: 100%;">
                        @if ($appointments->onFirstPage())
                            <span class="neobrutalist-btn pagination-mobile-btn" style="background: #eee; cursor: not-allowed; padding: 0.5rem 1rem; font-size: 0.8rem; opacity: 0.6; border: 2px solid #000; box-shadow: none; white-space: nowrap;">Anterior</span>
                        @else
                            <a href="{{ $appointments->previousPageUrl() }}#mis-turnos" class="neobrutalist-btn bg-amarillo pagination-mobile-btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; text-decoration: none; color: black; border: 2px solid #000; font-weight: 800; display: inline-block; white-space: nowrap;">Anterior</a>
                        @endif

                        <span class="pagination-mobile-indicator" style="font-weight: 800; font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #000; padding: 0.4rem 0.2rem; white-space: nowrap;">{{ $appointments->currentPage() }} / {{ $appointments->lastPage() }}</span>

                        @if ($appointments->hasMorePages())
                            <a href="{{ $appointments->nextPageUrl() }}#mis-turnos" class="neobrutalist-btn bg-amarillo pagination-mobile-btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; text-decoration: none; color: black; border: 2px solid #000; font-weight: 800; display: inline-block; white-space: nowrap;">Siguiente</a>
                        @else
                            <span class="neobrutalist-btn pagination-mobile-btn" style="background: #eee; cursor: not-allowed; padding: 0.5rem 1rem; font-size: 0.8rem; opacity: 0.6; border: 2px solid #000; box-shadow: none; white-space: nowrap;">Siguiente</span>
                        @endif
                    </div>
                @endif
            </div>

            <script>
                // Use capture phase to ensure we intercept the event first
                document.addEventListener('click', function(e) {
                    // Check if the clicked element or its parent is a pagination link inside #mis-turnos
                    const link = e.target.closest('#mis-turnos a.pagination-mobile-btn, #mis-turnos .pagination li a');
                    
                    if (link) {
                        e.preventDefault(); // Stop default navigation
                        e.stopPropagation(); // Stop bubbling
                        e.stopImmediatePropagation(); // Stop other listeners on the same element

                        const url = link.href;
                        const container = document.getElementById('mis-turnos');
                        
                        if (container && url && url !== '#') {
                            // Add heavy opacity to indicate loading is happening
                            container.style.opacity = '0.5';
                            container.style.pointerEvents = 'none';
                            
                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.text();
                            })
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContent = doc.getElementById('mis-turnos');
                                
                                if(newContent) {
                                    // Replace content
                                    container.innerHTML = newContent.innerHTML;
                                    
                                    // Scroll into view gently as requested for mobile ("centrado en la seccion")
                                    // Using scrollIntoView with block: 'center' to keep it in focus
                                    if (window.innerWidth <= 768) {
                                        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    }
                                } else {
                                    console.error('Could not find #mis-turnos in response');
                                    // Fallback only if we really failed to find content
                                    window.location.href = url;
                                }
                            })
                            .catch(err => {
                                console.error('Error fetching pagination:', err);
                                window.location.href = url;
                            })
                            .finally(() => {
                                container.style.opacity = '1';
                                container.style.pointerEvents = 'auto';
                            });
                        }
                    }
                }, true); // Use capture: true
            </script>

            <!-- Biblioteca de Materiales (NEW) -->
        <!-- Mis Documentos (Inline) -->
        <!-- Mis Documentos (Inline) -->
        <div id="documentos" class="neobrutalist-card" style="margin-top: 2rem; padding: 2.5rem !important; border: 3px solid #000; box-shadow: 8px 8px 0px #000; background: white;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 4px solid #000; padding-bottom: 0.8rem; font-size: 1.4rem; font-family: 'Syne', sans-serif; font-weight: 700;">
                <i class="fa-solid fa-file-contract"></i> Mis Documentos
            </h3>
            
            @if(isset($documents) && count($documents) > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem;">
                    @foreach($documents as $doc)
                    @php
                        $isImage = preg_match('/\.(jpeg|jpg|png|webp|gif)$/i', $doc->file_path);
                        $isPdf = preg_match('/\.pdf$/i', $doc->file_path);
                        $previewUrl = asset('storage/' . $doc->file_path);
                    @endphp
                    <div style="border: 2px solid #000; border-radius: 10px; overflow: hidden; background: #fff; display: flex; flex-direction: column; box-shadow: 4px 4px 0px #000; transition: transform 0.2s;">
                        <!-- Preview Header -->
                        <div onclick="openDocumentPreview('{{ $previewUrl }}', '{{ $isPdf ? 'pdf' : ($isImage ? 'image' : 'other') }}')" style="cursor: pointer; height: 100px; background: #f0f0f0; border-bottom: 2px solid #000; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;" class="doc-preview-hover">
                            @if($isImage)
                                <img src="{{ $previewUrl }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="fa-solid {{ $isPdf ? 'fa-file-pdf' : 'fa-file' }}" style="font-size: 2.5rem; color: {{ $isPdf ? '#e11d48' : '#666' }};"></i>
                            @endif
                            <div class="preview-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                <i class="fa-solid fa-eye" style="color: white; font-size: 1.5rem;"></i>
                            </div>
                        </div>

                        <div style="padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div style="margin-bottom: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                                    <span style="font-size: 0.65rem; font-weight: 800; background: #eee; padding: 1px 4px; border-radius: 4px; border: 1px solid #999;">
                                        {{ $doc->created_at->format('d/m/y') }}
                                    </span>
                                    <span style="font-size: 0.65rem; text-transform: uppercase; background: #e0f2fe; padding: 1px 4px; border-radius: 4px; border: 1px solid #000; font-weight: 700;">{{ $doc->type }}</span>
                                </div>
                                <h4 style="margin: 0; font-size: 0.85rem; font-weight: 800; word-break: break-all; line-height: 1.1; height: 2.2em; overflow: hidden;">{{ $doc->name }}</h4>
                            </div>
                            <div style="display: flex; gap: 5px;">
                                <button onclick="openDocumentPreview('{{ $previewUrl }}', '{{ $isPdf ? 'pdf' : ($isImage ? 'image' : 'other') }}')" class="neobrutalist-btn bg-amarillo" style="flex: 1; font-size: 0.75rem; padding: 4px; box-shadow: 2px 2px 0px #000;">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </button>
                                <a href="{{ route('documents.download', $doc->id) }}" class="neobrutalist-btn bg-celeste" style="flex: 1; font-size: 0.75rem; padding: 4px; box-shadow: 2px 2px 0px #000; text-align: center; text-decoration: none; color: #000;">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <style>
                    .doc-preview-hover:hover .preview-overlay { opacity: 1 !important; }
                </style>
            @else
                <div style="text-align: center; padding: 3rem; background: #f9f9f9; border: 2px dashed #999; border-radius: 10px;">
                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <p style="color: #666; font-weight: 700; margin: 0;">No tenés documentos disponibles por ahora.</p>
                </div>
            @endif
        </div>

        <!-- Biblioteca de Materiales -->
        @if(isset($resources) && count($resources) > 0)
        <!-- Biblioteca de Materiales -->
        <div id="materiales" class="neobrutalist-card" style="margin-top: 2rem; padding: 2.5rem !important; border: 3px solid #000; box-shadow: 8px 8px 0px #000; background: white;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid #000; padding-bottom: 0.8rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.4rem; font-family: 'Syne', sans-serif; font-weight: 700; margin: 0;">
                    <i class="fa-solid fa-folder-open"></i> Mi Biblioteca de Materiales
                </h3>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
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
        </div>
        @endif



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
    const googleSlots = @json($googleAvailableSlots);
    const occupiedSlots = @json($occupiedSlots);
    const userType = "{{ $tipoPaciente }}";
    const weekendsBlocked = @json($blockWeekends);
    const specificBlockedDays = @json($blockedDays);
    const patientAppointments = @json($patientAppointmentsDates ?? []);
    
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
        let addedDays = 0;
        let offset = 1;

        while (addedDays < 15) {
            const date = new Date();
            date.setDate(today.getDate() + offset);
            
            const dayOfWeek = date.getDay(); // 0-6
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            
            // Skip weekends entirely as requested
            if (isWeekend) {
                offset++;
                continue;
            }

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;

            // Priority: Google Slots override blocks
            const hasGoogleSlots = googleSlots.some(s => s.date === dateStr);
            const isBaseAvailable = availabilities.some(a => a.dia_semana == dayOfWeek);
            
            const isSpecificBlocked = specificBlockedDays.includes(dateStr);
            const isAlreadyBooked = patientAppointments.includes(dateStr);
            
            // Logic: 
            // 1. If hasGoogleSlots is true, it's AVAILABLE (overrides all blocks).
            // 2. If NO Google Slots, we check standard blocks and base availability.
            let isBlocked = false;
            let blockReason = '';

            if (isAlreadyBooked) {
                isBlocked = true;
                blockReason = 'Ya tenés un turno este día';
            } else if (hasGoogleSlots) {
                isBlocked = false; // Explicit availability overrides everything
            } else {
                // Formatting fallback to standard availability
                if (!isBaseAvailable) {
                    isBlocked = true; // No basic slots
                } else if (isSpecificBlocked) {
                    isBlocked = true; // Blocked by rule
                    blockReason = 'Día no disponible';
                }
            }

            const dayBtn = document.createElement('div');
            dayBtn.className = 'day-btn' + (isBlocked ? ' disabled' : ''); 
            
            // Add style for blocked
            if (isBlocked) {
                dayBtn.style.opacity = '0.3';
                dayBtn.style.cursor = 'not-allowed';
                dayBtn.style.background = '#ddd';
                dayBtn.title = blockReason || (isSpecificBlocked ? 'Día no disponible' : 'Fuera de horario');
                
                if (isAlreadyBooked) {
                     dayBtn.style.border = '2px solid #e11d48'; // Red border for already booked
                     dayBtn.style.color = '#e11d48';
                }
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
            addedDays++;
            offset++;
        }
    }

    function updateSummary() {
        const container = document.getElementById('booking-summary-container');
        const text = document.getElementById('summary-text');
        
        if (!selectedDay) {
            container.style.opacity = '0';
            container.style.maxHeight = '0';
            return;
        }

        container.style.opacity = '1';
        container.style.maxHeight = '200px'; // Allow enough room

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
        
        // Enable Next Button
        document.getElementById('next-1').classList.remove('disabled-btn');

        // Reset steps forward
        selectedModalidad = null;
        document.getElementById('selected_modalidad').value = '';
        document.querySelectorAll('.modality-btn').forEach(b => b.classList.remove('selected'));
        document.getElementById('next-2').classList.add('disabled-btn');
        
        selectedTime = null;
        document.getElementById('times-grid').innerHTML = '';
        document.getElementById('next-3').classList.add('disabled-btn');

        updateSummary();
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
            slotsToRender = dayGoogleSlots.map(s => ({ hora_inicio: s.time, modalidad: 'cualquiera' }));
        } else {
            // Priority 2: Fallback to base hours if no Google Slots
            // FILTER BY SELECTED MODALITY
            slotsToRender = availabilities.filter(a => 
                a.dia_semana == dayOfWeek && 
                (a.modalidad === 'cualquiera' || a.modalidad === selectedModalidad)
            );
        }
        
        if (slotsToRender.length === 0) {
            grid.innerHTML = `
                <div style="grid-column: 1 / -1; padding: 2rem; background: #fff4f4; border: 2px dashed #e11d48; border-radius: 12px; text-align: center;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 2rem; color: #e11d48; margin-bottom: 0.5rem; display: block;"></i>
                    <p style="font-weight: 800; color: #e11d48; margin: 0;">En esta modalidad no hay horarios disponibles para el día seleccionado.</p>
                </div>
            `;
            return;
        }

        // Sort slots by time
        slotsToRender.sort((a,b) => a.hora_inicio.localeCompare(b.hora_inicio));

        slotsToRender.forEach(slot => {
            const timeStr = slot.hora_inicio.substring(0, 5); // HH:mm
            const displayLabel = slot.label || timeStr; // Use range label if available
            const fullDateTime = dateStr + ' ' + timeStr + ':00';
            const isOccupied = occupiedSlots.includes(fullDateTime);
            
            const pillContainer = document.createElement('div');
            pillContainer.style.display = 'flex';
            pillContainer.style.flexDirection = 'column';
            pillContainer.style.alignItems = 'center';
            pillContainer.style.gap = '0.5rem';
            pillContainer.style.width = '100%';

            const pill = document.createElement('div');
            pill.className = 'time-pill' + (isOccupied ? ' disabled' : '');
            pill.innerText = displayLabel;
            
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
                waitBtn.onclick = (e) => joinWaitlist(dateStr, timeStr, e.currentTarget);
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
        
        // Enable Next Button
        document.getElementById('next-3').classList.remove('disabled-btn');
        
        updateSummary();
    }

    async function joinWaitlist(date, time, btn) {
        if (btn && btn.disabled) return;
        
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';
        }

        try {
            const response = await fetch('{{ route("waitlist.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    fecha_especifica: date, 
                    hora_inicio: time,
                    availability: 'Horario Específico: ' + date + ' ' + time,
                    modality: selectedModalidad.charAt(0).toUpperCase() + selectedModalidad.slice(1)
                })
            });

            const data = await response.json();

            if (response.ok) {
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Anotado';
                    btn.style.background = '#ccc';
                    btn.style.cursor = 'not-allowed';
                }
                // Show custom waitlist success modal
                document.getElementById('waitlist-success-overlay').style.display = 'flex';
            } else {
                alert('Error: ' + (data.message || 'No se pudo unir a la lista.'));
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.innerHTML = '<i class="fa-solid fa-bell"></i> Avisarme';
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un error de conexión al unirse a la lista de espera.');
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = '<i class="fa-solid fa-bell"></i> Avisarme';
            }
        }
    }

    function closeWaitlistSuccess() {
        document.getElementById('waitlist-success-overlay').style.display = 'none';
    }

    function closeWaitlistSuccess() {
        document.getElementById('waitlist-success-overlay').style.display = 'none';
        // Optional: refresh or just stay
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
        
        // Enable Next Button
        document.getElementById('next-2').classList.remove('disabled-btn');

        // Regenerate times based on new modality
        if (selectedDay) {
            generateTimes(selectedDay);
        }
        
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
            if (currentStepId === 2 && !selectedModalidad) {
                window.showAlert('Primero debes seleccionar la modalidad (Virtual o Presencial).');
                return;
            }
            if (currentStepId === 3 && !selectedTime) {
                window.showAlert('Primero debes seleccionar un horario para continuar.');
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

    /* Booking Form Time Slots: 2 Columns */
    #times-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        width: 100%;
    }
    
    .time-pill {
        width: 100%;
        text-align: center;
        background: white;
        border: 2px solid #000;
        padding: 0.8rem;
        border-radius: 8px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 4px 4px 0px #000;
    }
    
    .time-pill:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0px #000;
        background: var(--color-celeste);
    }
    
    .time-pill.selected {
        background: var(--color-verde);
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px #000;
    }
    
    .time-pill.disabled {
        background: #f3f4f6;
        color: #9ca3af;
        box-shadow: none;
        border-color: #d1d5db;
        cursor: not-allowed;
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

<!-- Waitlist Success Overlay (Integrated Modal) -->
<div id="waitlist-success-overlay" style="display: none; position: fixed; inset: 0; background-color: rgba(103, 58, 183, 0.2); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; z-index: 100000; padding: 1.5rem;">
    <div class="neobrutalist-card" style="max-width: 500px; width: 100%; text-align: center; padding: 3rem 2rem; background: white; box-shadow: 20px 20px 0px rgba(103, 58, 183, 0.3);">
        <div style="width: 80px; height: 80px; background: #34c759; border: 4px solid #000; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; box-shadow: 6px 6px 0px #000;">
            <i class="fa-solid fa-bell" style="color: white; font-size: 2.5rem;"></i>
        </div>

        <h2 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 2rem; margin-bottom: 1rem; letter-spacing: -1px;">¡Anotado con éxito!</h2>
        
        <p style="font-size: 1.1rem; color: #444; margin-bottom: 2.5rem; line-height: 1.5; font-weight: 600;">
            Te uniste a la lista de espera para este horario. Te avisaremos por WhatsApp si se libera el lugar.
        </p>

        <button onclick="closeWaitlistSuccess()" class="neobrutalist-btn bg-amarillo w-full" style="display: block; font-size: 1.1rem; padding: 1rem;">
            LISTO, ¡GRACIAS!
        </button>
    </div>
</div>
<!-- Document Preview Modal -->
<div id="doc-preview-overlay" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); align-items: center; justify-content: center; z-index: 1000000; padding: 1.5rem;" onclick="closeDocumentPreview()">
    <div class="neobrutalist-card" style="max-width: 900px; width: 100%; height: 85vh; background: white; border: 5px solid #000; position: relative; display: flex; flex-direction: column; overflow: visible;" onclick="event.stopPropagation()">
        <button onclick="closeDocumentPreview()" style="position: absolute; top: -15px; right: -15px; background: #ff5f57; border: 3px solid #000; width: 40px; height: 40px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 4px 4px 0px #000; z-index: 101;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div id="doc-preview-content" style="flex-grow: 1; padding: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
            <!-- Iframe or Img injected here -->
        </div>
        <div style="padding: 1rem; border-top: 3px solid #000; background: #fff; display: flex; justify-content: space-between; align-items: center;">
            <span id="doc-preview-title" style="font-weight: 800; font-family: 'Syne', sans-serif;">Vista Previa</span>
            <div style="display: flex; gap: 10px;">
                <a id="doc-preview-download" href="#" class="neobrutalist-btn bg-celeste" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none; color: #000; font-weight: 800; box-shadow: 3px 3px 0px #000;">
                    <i class="fa-solid fa-download"></i> <span class="hide-mobile">Descargar</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function openDocumentPreview(url, type) {
        const content = document.getElementById('doc-preview-content');
        const overlay = document.getElementById('doc-preview-overlay');
        const downloadBtn = document.getElementById('doc-preview-download');
        
        content.innerHTML = '<div style="font-weight: 800;">Cargando vista previa...</div>';
        downloadBtn.href = url;
        
        setTimeout(() => {
            if (type === 'image') {
                content.innerHTML = `<img src="${url}" style="max-width: 100%; max-height: 100%; object-fit: contain; border: 2px solid #000; box-shadow: 10px 10px 0px rgba(0,0,0,0.1);">`;
            } else if (type === 'pdf') {
                content.innerHTML = `<iframe src="${url}#toolbar=0" style="width: 100%; height: 100%; border: none;"></iframe>`;
            } else {
                content.innerHTML = `
                    <div style="text-align: center;">
                        <i class="fa-solid fa-file" style="font-size: 5rem; margin-bottom: 1rem; color: #666;"></i>
                        <p style="font-weight: 800;">Este tipo de archivo no tiene vista previa disponible.</p>
                        <p style="font-size: 0.9rem;">Podés descargarlo para verlo en tu dispositivo.</p>
                    </div>
                `;
            }
        }, 100);
        
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeDocumentPreview() {
        document.getElementById('doc-preview-overlay').style.display = 'none';
        document.body.style.overflow = '';
        document.getElementById('doc-preview-content').innerHTML = '';
    }
</script>
@endsection
