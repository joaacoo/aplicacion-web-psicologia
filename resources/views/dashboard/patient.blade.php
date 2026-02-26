@if(isset($isAjax) && $isAjax)
    @include('dashboard.partials.appointments_table')
    @php return; @endphp
@endif

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
    .disabled-btn {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
        filter: grayscale(0.5);
        box-shadow: none !important;
        transform: none !important;
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
        min-width: 120px !important;
        white-space: nowrap !important;
        text-align: center !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
    }
    
    /* Days Grid and Buttons */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        width: 100%;
        margin-bottom: 2rem;
    }
    .day-btn {
        transition: all 0.2s;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 12px !important;
    }
    .day-btn.selected {
        background: var(--color-verde) !important;
        transform: translate(2px, 2px);
        box-shadow: 1px 1px 0px #000 !important;
    }
    .day-btn:hover:not(.disabled) {
        transform: translate(-2px, -2px);
        box-shadow: 5px 5px 0px #000 !important;
    }
    
    @media (max-width: 600px) {
        .days-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
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
        .show-mobile { display: inline-block !important; }
    }
    
    .show-mobile { display: none; }
    
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
            <i class="fa-solid fa-calendar-plus"></i> <span>Reserva Fija</span>
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
            flex-direction: row;
            gap: 2rem;
            align-items: flex-start;
            margin-top: 0; /* Reduced from 10px to move content higher */
            width: 100%;
        }
        .booking-column {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        #right-column {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            min-width: 0;
        }
        @media (max-width: 1024px) {
            .dashboard-flex-container {
                flex-direction: column !important;
                gap: 2rem !important;
                margin-top: 10px !important;
            }
            .booking-column, #right-column {
                width: 100% !important;
            }
            /* Header collision fixes */
            .booking-section, #fixed-reservation-status { 
                margin-top: 55px !important; /* Reduced from 70px to move content even higher */
            }
        }
        
        /* Better PC Layout fixes */
        body { background-color: var(--color-celeste) !important; }
        .container.mt-16 {
            max-width: 1400px !important;
            margin: 0 auto !important;
            padding-top: 60px !important; /* Reduced from 70px to move content higher */
        }
    </style>
    
    <div class="dashboard-flex-container">
        <div class="booking-column" style="display: flex; justify-content: center; width: 100%;">


<div class="dashboard-grid-container">
    
    <!-- Left Column: New Appointment Stepper OR Fixed Reservation Status -->
    <div class="booking-column">
        @if(isset($fixedReservation))
            <div id="fixed-reservation-status" class="neobrutalist-card" style="margin-bottom: 2rem; background: white; padding: 2rem; border: 5px solid #000; box-shadow: 10px 10px 0px #000; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 10px; right: 10px; font-size: 2rem; opacity: 0.1; transform: rotate(15deg);">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                
                <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; font-size: 1.3rem;">TU RESERVA FIJA</h3>
                
                <div style="background: #f0fdf4; border: 2px solid #166534; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    <p style="margin: 0; font-weight: 800; color: #166534; font-size: 1rem;">
                        <i class="fa-solid fa-check-circle"></i> Ya tenés tu reserva fija para los días:
                    </p>
                    <p style="margin: 5px 0 0 0; font-size: 1.2rem; font-weight: 900; color: #000; letter-spacing: -0.5px;">
                        {{ ucfirst(\Carbon\Carbon::parse($fixedReservation->fecha_hora)->isoFormat('dddd')) }} de {{ \Carbon\Carbon::parse($fixedReservation->fecha_hora)->format('H:i') }} a {{ \Carbon\Carbon::parse($fixedReservation->fecha_hora)->addMinutes(45)->format('H:i') }} hs
                    </p>
                    <span style="display: inline-block; background: #166534; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; margin-top: 8px; text-transform: uppercase;">
                        Modalidad: {{ ucfirst($fixedReservation->modalidad) }} &nbsp;&bull;&nbsp; {{ ucfirst($fixedReservation->frecuencia) }}
                    </span>
                </div>



                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 1.5rem;">
                        <form action="{{ route('appointments.cancelFixed') }}" method="POST" style="width: 100%;">
                            @csrf
                            <button type="button" 
                                    onclick="window.showConfirm('⚠️ ¿ESTÁS SEGURO? Esto cancelará todas tus sesiones fijas de aquí en adelante y liberarás tu horario de forma permanente.', () => { if(typeof window.showProcessing === 'function') window.showProcessing('Cancelando reserva fija...'); this.closest('form').submit(); })"
                                    class="neobrutalist-btn" 
                                    style="width: 100%; background: #fee2e2; color: #e11d48; border: 2px solid #e11d48; padding: 0.8rem; font-weight: 800; font-size: 0.8rem; box-shadow: 3px 3px 0px #e11d48;">
                                <i class="fa-solid fa-calendar-xmark"></i> CANCELAR MI RESERVA FIJA
                            </button>
                        </form>
                    </div>
                </div>

                <p style="margin-top: 0; font-size: 0.75rem; color: #666; font-weight: 600; text-align: center; line-height: 1.4;">
                    <i class="fa-solid fa-circle-info"></i> Recordá que las cancelaciones deben ser con 24hs de antelación.
                </p>
            </div>
        @else
            <div id="booking" class="neobrutalist-card booking-section" style="margin-bottom: 4rem; padding-bottom: 3rem; margin-left: auto; margin-right: auto; width: 100%; max-width: 920px;">
                <style>
                    @media (max-width: 768px) {
                        .booking-section {
                            /* Margin handled by generic mobile class above */
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
            
            <h3 class="mb-4" style="font-family: 'Syne', sans-serif; font-weight: 700; white-space: nowrap;"><i class="fa-solid fa-calendar-plus"></i> <span class="hide-mobile">Reservar Nuevo Turno</span><span class="show-mobile">Reserva Fija</span></h3>
                
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

                        <!-- Step 2: Frequency & Modality -->
                        <div class="booking-step" id="step-2">
                            <label class="block font-bold mb-4">Paso 2: Frecuencia y Modalidad</label>
                            
                            <div style="margin-bottom: 2rem;">
                                <label class="block font-bold mb-2" style="font-size: 0.85rem; color: #666;">¿Con qué frecuencia nos vemos?</label>
                                <div class="modality-selector" style="gap: 15px;">
                                    <div class="modality-btn" onclick="selectFrequency('semanal')" id="freq-semanal">
                                        <i class="fa-solid fa-calendar-day"></i>
                                        <span>Semanal</span>
                                    </div>
                                    <div class="modality-btn" onclick="selectFrequency('quincenal')" id="freq-quincenal">
                                        <i class="fa-solid fa-calendar-week"></i>
                                        <span>Quincenal</span>
                                    </div>
                                </div>
                                <input type="hidden" name="frecuencia" id="selected_frecuencia">
                            </div>

                            <div>
                                <label class="block font-bold mb-2" style="font-size: 0.85rem; color: #666;">¿Qué modalidad preferís?</label>
                                <div class="modality-selector">
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
                            </div>
                            
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

                        <div class="booking-step" id="step-4">
                            <label class="block font-bold mb-4">Paso 4: Pago de la Primera Sesión</label>
                            
                            <div style="background: #eef2ff; border: 2px solid #6366f1; border-radius: 12px; padding: 1rem; margin-bottom: 2rem; font-size: 0.9rem; font-weight: 700; color: #4338ca;">
                                <i class="fa-solid fa-circle-info"></i> Se abona únicamente la <b>primera sesión</b> para confirmar la reserva fija. Las siguientes sesiones se abonan individualmente.
                                <br><br>
                                <i class="fa-solid fa-triangle-exclamation"></i> <b>Importante:</b> Las cancelaciones deben realizarse con al menos 24 horas de anticipación. De lo contrario, se deberá abonar el total de la sesión.
                            </div>
                            
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

                            <div class="flex justify-center mt-12 booking-footer-btns" style="gap: 1.5rem; display: flex; justify-content: center;">
                                <button type="button" class="neobrutalist-btn bg-lila" style="min-width: 140px;" onclick="prevStep(3)">Atrás</button>
                                <button type="button" class="neobrutalist-btn bg-verde" style="min-width: 140px;" onclick="confirmReserve()">Confirmar Turno</button>
                            </div>
                            <style>
                                @media (max-width: 768px) {
                                    .booking-footer-btns {
                                        gap: 0.8rem !important;
                                    }
                                    .booking-footer-btns .neobrutalist-btn {
                                        min-width: 110px !important;
                                        padding: 0.5rem 0.6rem !important;
                                        font-size: 0.75rem !important;
                                        margin: 0 !important;
                                    }
                                }
                            </style>
                        </div>
<!-- Live Summary (Dynamic height) -->
                        <div id="booking-summary-container" style="min-height: 0; transition: all 0.3s ease-in-out; opacity: 0; max-height: 0; overflow: hidden;">
                            <div id="booking-summary" style="padding: 1rem; background: #f8f8f8; border: 2px dashed #ccc; border-radius: 10px; font-weight: 700; color: #333; margin-top: 1.5rem;">
                                <i class="fa-solid fa-calendar-check"></i> <span id="summary-text">Reserva para...</span>
                            </div>
                        </div>

                    </form>
                </div>
            @endif
        </div>

        <!-- Recovery Modal (Eventual) -->
        

        <!-- Right Column: My Appointments -->
    <div id="mis-turnos-section">

        <style>
            /* Hide mobile labels on desktop */
            .mobile-label {
                display: none;
            }
            
            @media (max-width: 1024px) {
                #mis-turnos { padding: 1.5rem !important; }
                #mis-turnos h3 { font-size: 1.1rem; margin-bottom: 0.5rem !important; } /* Reduced margin */
                
                
                /* Shrink Recovery Modal Buttons for Mobile */
                #recovery-modal-overlay .neobrutalist-btn {
                    padding: 8px 12px !important;
                    font-size: 0.75rem !important;
                }

                #recovery-modal-overlay .neobrutalist-card {
                    padding: 1.5rem !important;
                }

                #recovery-modal-overlay h3 {
                    font-size: 1.1rem !important;
                }

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
                
                #mis-turnos tr { border: 3px solid #000 !important; margin-bottom: 1rem; background: #fff; padding: 10px; border-radius: 8px; box-shadow: 4px 4px 0px #000; }
                
                #mis-turnos td { 
                    border: none !important;
                    position: relative;
                    padding-left: 40% !important; /* Adjusted for better label fit */
                    text-align: right;
                    padding-top: 10px !important;
                    padding-bottom: 10px !important;
                    min-height: 45px;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    font-size: 0.85rem;
                }
                
                #mis-turnos td:before { 
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    left: 12px;
                    width: 35%; 
                    text-align: left;
                    font-weight: 800;
                    color: #000;
                    font-family: 'Syne', sans-serif;
                    text-transform: uppercase;
                    font-size: 0.75rem;
                }
                
                /* Labels */
                #mis-turnos td:nth-of-type(1):before { content: "Fecha:"; } 
                #mis-turnos td:nth-of-type(2):before { content: "Hora:"; } 
                #mis-turnos td:nth-of-type(3):before { content: "Modalidad:"; }
                #mis-turnos td:nth-of-type(4):before { content: "Estado:"; }
                #mis-turnos td:nth-of-type(5):before { content: "Pago:"; }
                #mis-turnos td:nth-of-type(6):before { content: "Acciones:"; }
                #mis-turnos td:nth-of-type(5):before { content: "Pago:"; }
                #mis-turnos td:nth-of-type(6):before { content: "Acciones:"; }
                
                /* Update Actions Column Index for Mobile Styling */
                #mis-turnos td:nth-of-type(6) {
                    flex-direction: column !important;
                    align-items: stretch !important;
                    gap: 10px !important;
                    text-align: left !important;
                    padding-top: 15px !important;
                    padding-bottom: 15px !important;
                }
                #mis-turnos td:nth-of-type(6):before {
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
                
            }
            
            /* Remove horizontal scroll on desktop */
            @media (min-width: 1025px) {
                #mis-turnos div[style*="overflow-x: auto"] {
                    overflow-x: visible !important;
                }
            }
        </style>
        </div> <!-- End booking-column -->

        <div id="right-column">
            
            <!-- Mis Turnos y Pagos -->
            <div id="mis-turnos" class="neobrutalist-card" style="margin-bottom: 2rem; padding: 2rem !important; border: 3px solid #000; box-shadow: 8px 8px 0px #000; background: white;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 4px solid #000; padding-bottom: 0.8rem; font-size: 1.4rem; font-family: 'Syne', sans-serif; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-calendar-check"></i> Mis Turnos y Pagos
                </h3>

                @if(isset($creditBalance) && $creditBalance > 0)
                    <!-- Credit Balance Banner (NEW) -->
                    <div class="neobrutalist-card bg-verde" style="margin-bottom: 1.5rem; padding: 1.2rem; border: 3px solid #000; box-shadow: 4px 4px 0px #000; display: flex; align-items: center; gap: 15px;">
                        <div style="background: white; border: 2px solid #000; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; box-shadow: 2px 2px 0px #000;">
                            <i class="fa-solid fa-piggy-bank" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #000;">Saldo a tu favor</p>
                            <h4 style="margin: 0; font-size: 1.4rem; font-weight: 900; font-family: 'Syne', sans-serif;">${{ number_format($creditBalance, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                @endif

                @if(isset($creditBalance) && $creditBalance > 0)
                    <!-- Credit Balance Banner (NEW) -->
                    <div class="neobrutalist-card bg-verde" style="margin-bottom: 1.5rem; padding: 1.2rem; border: 3px solid #000; box-shadow: 4px 4px 0px #000; display: flex; align-items: center; gap: 15px;">
                        <div style="background: white; border: 2px solid #000; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; box-shadow: 2px 2px 0px #000;">
                            <i class="fa-solid fa-piggy-bank" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #000;">Saldo a tu favor</p>
                            <h4 style="margin: 0; font-size: 1.4rem; font-weight: 900; font-family: 'Syne', sans-serif;">${{ number_format($creditBalance, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                @endif

                <div id="appointments-loading" style="display: none; text-align: center; padding: 2rem;">
                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: #000;"></i>
                </div>

                <div id="appointments-table-container">
                    @include('dashboard.partials.appointments_table')
                </div>

            </div>
        </div> <!-- End right-column -->

        <!-- Mis Documentos (full-width, separate section) -->
        <div id="documentos" class="neobrutalist-card" style="padding: 2.5rem !important; border: 3px solid #000; box-shadow: 8px 8px 0px #000; background: white;">
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
        

@php
// Variables for script
$availabilities = $availabilities ?? [];
$googleAvailableSlots = $googleAvailableSlots ?? collect([]);
$occupiedSlots = $occupiedSlots ?? [];
$user = auth()->user();
$tipoPaciente = $user && $user->paciente ? $user->paciente->tipo_paciente : 'nuevo';
$blockWeekends = $blockWeekends ?? false;
$blockedDays = $blockedDays ?? [];
$fixedBlockedSlots = $fixedBlockedSlots ?? [];
@endphp

<script>
    const availabilities = @json($availabilities);
    const googleSlots = @json($googleAvailableSlots);
    const occupiedSlots = @json($occupiedSlots);
    const userType = "{{ $tipoPaciente }}";
    const weekendsBlocked = @json($blockWeekends);
    const specificBlockedDays = @json($blockedDays);
    const fixedBlockedSlots = @json($fixedBlockedSlots);
    const patientAppointments = @json($patientAppointmentsDates ?? []);
    
    let selectedDayOfWeek = null; // 0-6
    let selectedDayName = null;
    let selectedFrequency = null;
    let selectedModality = null;

    function generateDayOfWeekButtons() {
        const grid = document.getElementById('days-grid');
        if (!grid) return;
        grid.innerHTML = '';
        
        const days = [
            { id: 1, name: 'Lunes' },
            { id: 2, name: 'Martes' },
            { id: 3, name: 'Miércoles' },
            { id: 4, name: 'Jueves' },
            { id: 5, name: 'Viernes' }
        ];

        days.forEach(day => {
            const isBaseAvailable = availabilities.some(a => a.dia_semana == day.id);
            
            const dayBtn = document.createElement('div');
            dayBtn.className = 'day-btn' + (!isBaseAvailable ? ' disabled' : ''); 
            
            if (!isBaseAvailable) {
                dayBtn.style.opacity = '0.3';
                dayBtn.style.cursor = 'not-allowed';
                dayBtn.style.background = '#ddd';
                dayBtn.title = 'No hay horarios configurados para este día';
            }

            dayBtn.innerHTML = `
                <div style="font-size: 0.75rem; font-weight: 800; padding: 1rem 2px; letter-spacing: -0.5px; width: 100%; text-align: center;">${day.name.toUpperCase()}</div>
            `;
            
            if (isBaseAvailable) {
                dayBtn.onclick = () => selectDayOfWeek(day.id, day.name, dayBtn);
            }

            grid.appendChild(dayBtn);
        });

        // Add a note about recurring
        const note = document.createElement('p');
        note.style.gridColumn = '1 / -1';
        note.style.marginTop = '1rem';
        note.style.fontSize = '0.85rem';
        note.style.fontWeight = '700';
        note.style.color = '#555';
        note.style.textAlign = 'center';
        note.innerHTML = '<i class="fa-solid fa-circle-info"></i> Esta es una <b>Reserva Fija</b>. Elegí el día que prefieras para tus sesiones semanales o quincenales.';
        grid.appendChild(note);
    }

    function selectDayOfWeek(dayId, dayName, element) {
        if (selectedDayOfWeek === dayId) {
            selectedDayOfWeek = null;
            selectedDayName = null;
            element.classList.remove('selected');
            selectedTime = null;
            document.getElementById('times-grid').innerHTML = '';
            document.getElementById('final_date').value = '';
            updateSummary();
            return;
        }

        selectedDayOfWeek = dayId;
        selectedDayName = dayName;
        document.querySelectorAll('.day-btn').forEach(b => b.classList.remove('selected'));
        element.classList.add('selected');
        
        // Enable Next Button
        document.getElementById('next-1').classList.remove('disabled-btn');

        // Reset steps forward
        selectedFrequency = null;
        document.getElementById('selected_frecuencia').value = '';
        document.querySelectorAll('#step-2 [id^="freq-"]').forEach(b => b.classList.remove('selected'));
        
        selectedModalidad = null;
        document.getElementById('selected_modalidad').value = '';
        document.querySelectorAll('#step-2 [id^="mod-"]').forEach(b => b.classList.remove('selected'));
        
        document.getElementById('next-2').classList.add('disabled-btn');
        
        selectedTime = null;
        document.getElementById('times-grid').innerHTML = '';
        document.getElementById('next-3').classList.add('disabled-btn');

        updateSummary();
    }

    function selectFrequency(freq) {
        if (selectedFrequency === freq) {
            selectedFrequency = null;
            document.getElementById('selected_frecuencia').value = '';
            document.getElementById('freq-' + freq).classList.remove('selected');
        } else {
            selectedFrequency = freq;
            document.getElementById('selected_frecuencia').value = freq;
            document.querySelectorAll('#step-2 [id^="freq-"]').forEach(b => b.classList.remove('selected'));
            document.getElementById('freq-' + freq).classList.add('selected');
        }
        
        checkStep2Ready();
        updateSummary();
    }

    function selectModality(mod) {
        if (selectedModalidad === mod) {
            selectedModalidad = null;
            document.getElementById('selected_modalidad').value = '';
            document.getElementById('mod-' + mod).classList.remove('selected');
        } else {
            selectedModalidad = mod;
            document.getElementById('selected_modalidad').value = mod;
            document.querySelectorAll('#step-2 [id^="mod-"]').forEach(b => b.classList.remove('selected'));
            document.getElementById('mod-' + mod).classList.add('selected');
        }
        
        checkStep2Ready();
        updateSummary();
    }

    function checkStep2Ready() {
        if (selectedFrequency && selectedModalidad) {
            document.getElementById('next-2').classList.remove('disabled-btn');
        } else {
            document.getElementById('next-2').classList.add('disabled-btn');
        }
    }

    function updateSummary() {
        const container = document.getElementById('booking-summary-container');
        const text = document.getElementById('summary-text');
        
        if (!selectedDayOfWeek) {
            container.style.opacity = '0';
            container.style.maxHeight = '0';
            return;
        }

        container.style.opacity = '1';
        container.style.maxHeight = '200px';

        let msg = `Reserva Fija para los días ${selectedDayName}`;
        
        if (selectedFrequency) {
            msg += ` (${selectedFrequency.charAt(0).toUpperCase() + selectedFrequency.slice(1)})`;
        }

        if (selectedTime) {
            msg += ` a las ${selectedTime}`;
        }
        
        if (selectedModalidad) {
            msg += ` [${selectedModalidad === 'virtual' ? 'Virtual' : 'Presencial'}]`;
        }

        text.innerText = msg;
    }


    function getIsoWeek(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
        var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        var weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
        return weekNo;
    }

    function generateTimes() {
        const grid = document.getElementById('times-grid');
        grid.innerHTML = '';
        
        if (!selectedDayOfWeek || !selectedModalidad) return;

        // For Recurring Booking, we show BASE availability for that day of week
        let slotsToRender = availabilities.filter(a => 
            a.dia_semana == selectedDayOfWeek && 
            (a.modalidad === 'cualquiera' || a.modalidad === selectedModalidad)
        );
        
        if (slotsToRender.length === 0) {
            grid.innerHTML = `
                <div style="grid-column: 1 / -1; padding: 2rem; background: #fff4f4; border: 2px dashed #e11d48; border-radius: 12px; text-align: center;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 2rem; color: #e11d48; margin-bottom: 0.5rem; display: block;"></i>
                    <p style="font-weight: 800; color: #e11d48; margin: 0;">No hay horarios configurados para este día y modalidad.</p>
                </div>
            `;
            return;
        }

        // Sort slots by time
        slotsToRender.sort((a,b) => a.hora_inicio.localeCompare(b.hora_inicio));

        // Calculate Next Date for checking occupancy (even if it's recurring, we check the first one)
        const now = new Date();
        const today = now.getDay();
        let diff = selectedDayOfWeek - today;
        if (diff <= 0) diff += 7;
        const nextDate = new Date();
        nextDate.setDate(now.getDate() + diff);
        const y = nextDate.getFullYear();
        const m = String(nextDate.getMonth() + 1).padStart(2, '0');
        const d = String(nextDate.getDate()).padStart(2, '0');
        const dateStr = `${y}-${m}-${d}`;

        const currentWeekParity = getIsoWeek(nextDate) % 2;

        slotsToRender.forEach(slot => {
            const timeStr = slot.hora_inicio.substring(0, 5); // HH:mm
            const displayLabel = slot.label || timeStr; // Use range label if available
            const fullDateTime = dateStr + ' ' + timeStr + ':00';
            
            // FIX (BLOQUEO REFINADO): 
            // 1. Ocupación puntual por turnos existentes
            const isPunctuallyOccupied = occupiedSlots.includes(fullDateTime);
            
            // 2. Bloqueo por Reservas Fijas (Global)
            const isGloballyBlocked = fixedBlockedSlots.some(f => {
                if (f.dia != selectedDayOfWeek) return false;
                if (!f.hora.startsWith(timeStr)) return false;

                // Si el NUEVO paciente quiere SEMANAL: Cualquier reserva fija bloquea.
                if (selectedFrequency === 'semanal') return true;

                // Si el NUEVO paciente quiere QUINCENAL:
                if (selectedFrequency === 'quincenal') {
                    // Bloquea si la reserva existente es SEMANAL
                    if (f.frecuencia === 'semanal') return true;
                    // Bloquea si la reserva existente es QUINCENAL y es la MISMA paridad de semana
                    if (f.frecuencia === 'quincenal' && f.week_parity === currentWeekParity) return true;
                }
                
                return false;
            });

            const isOccupied = isPunctuallyOccupied || isGloballyBlocked;
            
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
        // Optional: refresh or just stay
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
            badge.style.color = '#166534'; // Dark green for visibility
            setTimeout(() => {
                badge.innerText = '(Toca para copiar)';
                badge.style.color = '#666';
            }, 2000);
        });
    }


    function openEventualRecoveryModal() {
        document.getElementById('recovery-modal-overlay').style.display = 'flex';
        selectRecoveryModality('virtual'); // Default
    }

    function closeEventualRecoveryModal() {
        document.getElementById('recovery-modal-overlay').style.display = 'none';
    }

    function selectRecoveryModality(mod) {
        document.getElementById('selected_recovery_modality').value = mod;
        const btnVirtual = document.getElementById('rec-mod-virtual');
        const btnPresencial = document.getElementById('rec-mod-presencial');
        
        btnVirtual.classList.remove('bg-verde', 'text-white');
        btnVirtual.classList.add('bg-white', 'text-black');
        btnPresencial.classList.remove('bg-verde', 'text-white');
        btnPresencial.classList.add('bg-white', 'text-black');
        
        const selectedBtn = mod === 'virtual' ? btnVirtual : btnPresencial;
        selectedBtn.classList.remove('bg-white', 'text-black');
        selectedBtn.classList.add('bg-verde', 'text-white');
    }

    async function sendEventualRecoveryRequest() {
        const from = document.getElementById('recovery-time-pref-from').value;
        const to = document.getElementById('recovery-time-pref-to').value;
        const btn = document.getElementById('eventual-recovery-confirm-btn');
        const modality = document.getElementById('selected_recovery_modality').value;
        
        if (!from || !to) {
            window.showAlert('Por favor, seleccioná un rango de horario (Desde y Hasta).');
            return;
        }

        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ENVIANDO...';

        try {
            const rangeStr = `de ${from} a ${to}`;
            const response = await fetch('{{ route("waitlist.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    modality: modality.charAt(0).toUpperCase() + modality.slice(1),
                    availability: 'RECUPERACIÓN EVENTUAL - Preferencia: ' + rangeStr,
                    is_recovery: true,
                    type: 'Eventual'
                })
            });

            if (response.ok) {
                window.showAlert('Solicitud de recuperación enviada. Nazarena revisará su agenda y disponibilidad y te avisará por Mail o WhatsApp si puede coordinar el espacio.');
                closeEventualRecoveryModal();
            } else {
                alert('Hubo un error al enviar la solicitud.');
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión.');
        } finally {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = 'SOLICITAR';
        }
    }

    function nextStep(step) {
        // Validación según el paso actual antes de avanzar
        const currentStep = document.querySelector('.booking-step.active');
        const currentStepId = currentStep ? parseInt(currentStep.id.split('-')[1]) : 1;

        if (step > currentStepId) { 
            if (currentStepId === 1 && !selectedDayOfWeek) {
                window.showAlert('Primero debes seleccionar un día para continuar.');
                return;
            }
            if (currentStepId === 2 && (!selectedModalidad || !selectedFrequency)) {
                window.showAlert('Primero debes seleccionar frecuencia (Semanal/Quincenal) y modalidad (Virtual/Presencial).');
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

        if (step === 3) {
            generateTimes();
        }

        if (step === 4) {
            // For recurring booking, we calculate the NEXT occurrence of that day of week 
            // to store as the first appointment date in the system (legacy support)
            // or we might need a separate field for recurring bookings. 
            // For now, let's just set dia_semana and time if the backend supports it.
            // If the backend expects a DATE, we find the next date.
            
            const now = new Date();
            const today = now.getDay();
            let diff = selectedDayOfWeek - today;
            if (diff <= 0) diff += 7;
            
            const nextDate = new Date();
            nextDate.setDate(now.getDate() + diff);
            const y = nextDate.getFullYear();
            const m = String(nextDate.getMonth() + 1).padStart(2, '0');
            const d = String(nextDate.getDate()).padStart(2, '0');
            
            document.getElementById('final_date').value = `${y}-${m}-${d} ${selectedTime}`;
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
        
        window.showConfirm('¿Confirmás tu RESERVA FIJA ' + selectedFrequency.toUpperCase() + ' para los ' + selectedDayName + ' a las ' + selectedTime + '?', function() {
            if(typeof window.showProcessing === 'function') window.showProcessing('Creando reserva fija...');
            document.getElementById('reserve-form').submit();
        });
    }

    // Initialize
    function initStepper() {
        generateDayOfWeekButtons();
        if (userType === 'nuevo') {
            const warning = document.getElementById('payment-warning');
            if (warning) warning.style.display = 'block';
            const proof = document.getElementById('proof_input');
            if (proof) proof.required = true;
        }
    }
    
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
        
        if (!sidebar || !overlay) return; // Fix: check for existence

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
@if(false)
<!-- Redundant Modal Removed -->
@endif

<!-- Recovery Session Modal (Calendar for single session) -->
<div id="recovery-overlay" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(5px); align-items: center; justify-content: center; z-index: 100000; padding: 1rem;">
    <div class="neobrutalist-card" style="max-width: 550px; width: 100%; background: white; padding: 2rem; border: 5px solid #000; box-shadow: 10px 10px 0px #000;">
        <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem;">RECUPERAR SESIÓN</h3>
        <p style="font-size: 0.9rem; font-weight: 600; color: #444; margin-bottom: 1.5rem;">
            Elegí un horario entre los <b>espacios disponibles de Nazarena</b> para retomar la sesión cancelada. Ella recibirá tu solicitud.
        </p>

        <div style="margin-bottom: 1.5rem;">
            <label class="block font-bold mb-2">1. Elegí el día:</label>
            <div id="recovery-days-grid" class="days-grid" style="grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 8px;">
                <!-- Filled by JS -->
            </div>

            <div style="margin-top: 1rem;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 800; cursor: pointer; margin-bottom: 0.5rem; color: #444;">
                    <input type="checkbox" id="recovery-no-day-checkbox" onchange="toggleCustomAvailability()" style="width: 18px; height: 18px; accent-color: #000;">
                    ¿No encontrás ningún día conveniente?
                </label>
                <div id="recovery-custom-text-container" style="display: none; margin-top: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 700; display: block; margin-bottom: 0.5rem; color: #444;">Dejá tu disponibilidad (Lun-Vie):</label>
                    <textarea id="recovery-custom-availability" class="neobrutalist-input w-full" rows="2" style="padding: 0.5rem; border-radius: 8px; font-weight: 600;" placeholder="Ej: Puedo los martes por la mañana o jueves a las 18hs..." oninput="checkRecoveryStepReady()"></textarea>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem; display: none;" id="recovery-time-section">
            <div id="recovery-time-range-wrapper">
                <label class="block font-bold mb-2">2. Rango horario preferido:</label>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label style="font-size: 0.75rem; color: #666; font-weight: 800; display: block; margin-bottom: 5px;">DESDE:</label>
                        <input type="time" id="recovery-time-from" class="neobrutalist-input w-full" style="padding: 0.5rem; border-radius: 8px; font-weight: 700;" onclick="this.showPicker ? this.showPicker() : this.click();">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 0.75rem; color: #666; font-weight: 800; display: block; margin-bottom: 5px;">HASTA:</label>
                        <input type="time" id="recovery-time-to" class="neobrutalist-input w-full" style="padding: 0.5rem; border-radius: 8px; font-weight: 700;" onclick="this.showPicker ? this.showPicker() : this.click();">
                    </div>
                </div>
            </div>

            <label class="block font-bold mb-2">3. ¿Qué modalidad preferís?</label>
            <div class="modality-selector" style="display: flex; gap: 10px; margin-bottom: 1rem;">
                <div class="modality-btn recovery-mod-btn" onclick="selectRecoveryModality('presencial')" id="rec-mod-presencial" style="flex: 1; padding: 10px; border: 2px solid #000; border-radius: 10px; text-align: center; cursor: pointer; font-weight: 800; transition: all 0.2s;">
                    <i class="fa-solid fa-house-user"></i><br>Presencial
                </div>
                <div class="modality-btn recovery-mod-btn" onclick="selectRecoveryModality('virtual')" id="rec-mod-virtual" style="flex: 1; padding: 10px; border: 2px solid #000; border-radius: 10px; text-align: center; cursor: pointer; font-weight: 800; transition: all 0.2s;">
                    <i class="fa-solid fa-video"></i><br>Virtual
                </div>
            </div>
            <input type="hidden" id="selected_recovery_modality">

            <p style="font-size: 0.8rem; color: #666; margin-top: 10px; font-weight: 600;">
                <i class="fa-solid fa-paper-plane"></i> Nazarena recibirá tu solicitud para ver su disponibilidad en la agenda y te enviará un mensaje de confirmación por WhatsApp.
            </p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 2rem;">
            <button type="button" onclick="closeRecoveryModal()" class="neobrutalist-btn bg-lila" style="flex: 1;">CANCELAR</button>
            <button type="button" id="recovery-confirm-btn" onclick="sendRecoveryRequest()" class="neobrutalist-btn bg-verde disabled-btn" style="flex: 1;">SOLICITAR</button>
        </div>
    </div>
</div>

<script>
    function openUploadModal(apptId) {
        document.getElementById('upload_appt_id').value = apptId;
        document.getElementById('upload-proof-overlay').style.display = 'flex';
    }

    function closeUploadModal() {
        document.getElementById('upload-proof-overlay').style.display = 'none';
    }

    let recoverySelectedDay = null;
    let recoverySelectedTime = null;

    function openRecoveryModal(apptId, originalDate) {
        document.getElementById('recovery-overlay').style.display = 'flex';
        // Store apptId for the request and button fade
        window.currentRecoveryApptId = apptId;
        window.currentRecoveryOriginalDate = originalDate;
        generateRecoveryDays(originalDate);
    }

    window.closeRecoveryModal = function() {
        const modal = document.getElementById('recovery-overlay');
        if (modal) modal.style.display = 'none';
        
        recoverySelectedDay = null;
        document.getElementById('recovery-time-from').value = '';
        document.getElementById('recovery-time-to').value = '';
        document.getElementById('recovery-custom-availability').value = '';
        document.getElementById('selected_recovery_modality').value = '';
        
        const checkbox = document.getElementById('recovery-no-day-checkbox');
        if (checkbox) checkbox.checked = false;
        
        const textContainer = document.getElementById('recovery-custom-text-container');
        if (textContainer) textContainer.style.display = 'none';
        
        const timeWrapper = document.getElementById('recovery-time-range-wrapper');
        if (timeWrapper) timeWrapper.style.display = 'block';

        document.querySelectorAll('.day-btn').forEach(b => {
             b.classList.remove('selected'); // Remove selected class
             b.style.background = 'white';
             b.style.color = 'black';
             b.style.boxShadow = 'none';
             b.style.transform = 'none';
        });
        document.querySelectorAll('.recovery-mod-btn').forEach(b => {
             b.style.background = 'white';
             b.style.boxShadow = 'none';
             b.style.transform = 'none';
        });
        document.getElementById('recovery-time-section').style.display = 'none';
        document.getElementById('recovery-confirm-btn').classList.add('disabled-btn');
    }

    window.toggleCustomAvailability = function() {
        const checkbox = document.getElementById('recovery-no-day-checkbox');
        const container = document.getElementById('recovery-custom-text-container');
        const timeSection = document.getElementById('recovery-time-section');
        const timeWrapper = document.getElementById('recovery-time-range-wrapper');

        if (checkbox.checked) {
            container.style.display = 'block';
            timeSection.style.display = 'block';
            if (timeWrapper) timeWrapper.style.display = 'none';

            // Clear selected day
            recoverySelectedDay = null;
            document.querySelectorAll('.day-btn').forEach(b => {
                b.classList.remove('selected'); // Remove selected class
                b.style.background = 'white';
                b.style.color = 'black';
                b.style.boxShadow = 'none';
                b.style.transform = 'none';
            });
            document.getElementById('recovery-time-from').value = '';
            document.getElementById('recovery-time-to').value = '';
        } else {
            container.style.display = 'none';
            document.getElementById('recovery-custom-availability').value = '';
            if (timeWrapper) timeWrapper.style.display = 'block';
            
            if (!recoverySelectedDay) {
                timeSection.style.display = 'none';
            }
        }
        checkRecoveryStepReady();
    }

    window.selectRecoveryModality = function(mod) {
        document.getElementById('selected_recovery_modality').value = mod;
        document.querySelectorAll('.recovery-mod-btn').forEach(b => {
             b.style.background = 'white';
             b.style.boxShadow = 'none';
             b.style.transform = 'none';
        });
        const active = document.getElementById('rec-mod-' + mod);
        if (active) {
            active.style.background = 'var(--color-amarillo)';
            active.style.boxShadow = '4px 4px 0px #000';
            active.style.transform = 'translate(-2px, -2px)';
        }
        checkRecoveryStepReady();
    }

    function checkRecoveryStepReady() {
        const from = document.getElementById('recovery-time-from').value;
        const to = document.getElementById('recovery-time-to').value;
        const customAvail = document.getElementById('recovery-custom-availability').value.trim();
        const mod = document.getElementById('selected_recovery_modality').value;
        const btn = document.getElementById('recovery-confirm-btn');
        const hasSpecificTime = recoverySelectedDay && from && to;
        const hasCustomAvail = customAvail.length > 0;

        if ((hasSpecificTime || hasCustomAvail) && mod) {
            btn.classList.remove('disabled-btn');
        } else {
            btn.classList.add('disabled-btn');
        }
    }

    function generateRecoveryDays(originalDate) {
        const grid = document.getElementById('recovery-days-grid');
        grid.innerHTML = '';
        
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1); 

        // Start from tomorrow or originalDate + 7, whichever is later?
        // Actually, user said "desde 7 dia en adelante".
        // Let's assume they want the range to BE 7 days.
        
        for (let i = 7; i < 14; i++) {
            const date = new Date(today);
            date.setDate(today.getDate() + i);
            const dayOfWeek = date.getDay();
            
            // Skip weekends
            if (dayOfWeek === 0 || dayOfWeek === 6) continue;
            
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const dStr = String(date.getDate()).padStart(2, '0');
            const dateStr = `${y}-${m}-${dStr}`;

            const dayBtn = document.createElement('div');
            dayBtn.className = 'day-btn';
            dayBtn.style.padding = '5px';
            dayBtn.innerHTML = `
                <div style="font-size: 0.5rem; opacity: 0.7;">${date.toLocaleDateString('es-AR', { weekday: 'short' }).toUpperCase()}</div>
                <div style="font-size: 0.9rem; font-weight: 800;">${date.getDate()}</div>
            `;
            dayBtn.onclick = () => {
                document.querySelectorAll('#recovery-days-grid .day-btn').forEach(b => b.classList.remove('selected'));
                dayBtn.classList.add('selected');
                recoverySelectedDay = dateStr;
                // Show range input section instead of times grid
                document.getElementById('recovery-time-section').style.display = 'block';
                checkRecoveryStepReady();
            };
            grid.appendChild(dayBtn);
        }
    }

    // Removed generateRecoveryTimes as we now use a custom range textarea

    async function sendRecoveryRequest() {
        const from = document.getElementById('recovery-time-from').value;
        const to = document.getElementById('recovery-time-to').value;
        const mod = document.getElementById('selected_recovery_modality').value;
        const customAvail = document.getElementById('recovery-custom-availability').value.trim();

        const hasSpecificTime = recoverySelectedDay && from && to;
        const hasCustomAvail = customAvail.length > 0;

        if (!hasSpecificTime && !hasCustomAvail) return;
        if (hasSpecificTime && from >= to) return;
        
        if (!mod) {
            window.showAlert('Por favor seleccioná la modalidad.');
            return;
        }

        if (typeof window.showProcessing === 'function') window.showProcessing('Procesando solicitud...');

        const btn = document.getElementById('recovery-confirm-btn');
        btn.disabled = true;
        btn.innerText = 'ENVIANDO...';

        try {
            let availabilityData = "";
            let fechaEspec = null;
            
            if (hasSpecificTime) {
                availabilityData = JSON.stringify({
                    modalidad: mod,
                    desde: from,
                    hasta: to,
                    texto: customAvail || null
                });
                fechaEspec = recoverySelectedDay;
            } else {
                availabilityData = JSON.stringify({
                    modalidad: mod,
                    texto: customAvail
                });
                fechaEspec = null;
            }

            const response = await fetch('{{ route("waitlist.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    fecha_especifica: fechaEspec, 
                    availability: availabilityData,
                    modality: mod.charAt(0).toUpperCase() + mod.slice(1),
                    is_recovery: true,
                    original_appointment_id: window.currentRecoveryApptId
                })
            });

            if (response.ok) {
                closeRecoveryModal();
                if (typeof window.showSuccess === 'function') {
                    window.showSuccess('¡Solicitud de recuperación enviada con éxito! Nazarena recibirá tu pedido y te contactará pronto por WhatsApp para confirmar la nueva fecha.');
                } else {
                    window.showAlert('Solicitud de recuperación enviada con éxito.');
                }
                
                const apptId = window.currentRecoveryApptId;
                if (apptId) {
                    const tableBtns = document.querySelectorAll(`button[onclick*="openRecoveryModal(${apptId}"]`);
                    tableBtns.forEach(btn => {
                        btn.disabled = true;
                        btn.style.opacity = '0.4';
                        btn.style.pointerEvents = 'none';
                        btn.innerHTML = '<i class="fa-solid fa-clock"></i> Solicitado';
                    });
                }
            } else {
                let errorMsg = 'Error al enviar la solicitud.';
                try {
                    const errorData = await response.json();
                    errorMsg = errorData.error || errorData.message || errorMsg;
                } catch (e) {
                    console.error('Non-JSON error response:', e);
                }
                window.showAlert(errorMsg);
            }
        } catch (error) {
            console.error('Recovery Error:', error);
            window.showAlert('Error de conexión.');
        } finally {
            btn.disabled = false;
            btn.innerText = 'SOLICITAR';
            if (typeof window.hideLoader === 'function') window.hideLoader();
        }
    }
    function applyFilters(e) {
        if (e) e.preventDefault();
        
        const form = document.getElementById('appointments-filter-form');
        const container = document.getElementById('appointments-table-container');
        const loading = document.getElementById('appointments-loading');
        
        if (!form || !container) return;

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = `{{ route('patient.dashboard') }}?${params.toString()}`;
        
        if (loading) loading.style.display = 'block';
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('appointments-table-container');
                
                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
                    window.history.pushState({}, '', url);
                } else {
                    window.location.href = url;
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
                window.location.href = url;
            })
            .finally(() => {
                if (loading) loading.style.display = 'none';
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            });
    }

    // Bind form submit and clear filters to applyFilters instead of full reload
    document.addEventListener('DOMContentLoaded', () => {
        const filterForm = document.getElementById('appointments-filter-form');
        const clearBtn = document.getElementById('clear-filters');
        
        if (filterForm) {
            filterForm.addEventListener('submit', applyFilters);
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (filterForm) {
                    filterForm.reset();
                    filterForm.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                    applyFilters();
                } else {
                    window.location.href = clearBtn.href;
                }
            });
        }
    });

    // Time validation for recovery modal
    const recFrom = document.getElementById('recovery-time-from');
    const recTo = document.getElementById('recovery-time-to');
    if (recFrom) recFrom.addEventListener('change', () => { validateRecoveryTimes(); checkRecoveryStepReady(); });
    if (recTo) recTo.addEventListener('change', () => { validateRecoveryTimes(); checkRecoveryStepReady(); });

    function validateRecoveryTimes() {
        const from = document.getElementById('recovery-time-from');
        const to = document.getElementById('recovery-time-to');
        if (from.value && to.value) {
            if (from.value >= to.value) {
                to.value = '';
                checkRecoveryStepReady();
            }
        }
    }

    // Global AJAX for Pagination and Toggle View (Ver Todos/Menos)
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#appointments-table-container a.pagination-mobile-btn, #appointments-table-container .pagination li a, #appointments-table-container a[href*="page="], #appointments-table-container .toggle-view-btn');
        if (link) {
            const url = link.href;
            if (!url || (url.includes('#') && !url.includes('page=') && !e.target.closest('.toggle-view-btn'))) return;

            e.preventDefault();
            e.stopPropagation();

            const container = document.getElementById('appointments-table-container');
            const loading = document.getElementById('appointments-loading');
            
            if (container) {
                if (loading) loading.style.display = 'block';
                container.style.opacity = '0.5';
                container.style.position = 'relative';
                
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('appointments-table-container');
                    
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                        window.history.pushState({}, '', url);
                        
                        // Scroll to top of table section if it's pagination
                        if (!e.target.closest('.toggle-view-btn')) {
                            document.getElementById('mis-turnos')?.scrollIntoView({ behavior: 'smooth' });
                        }
                    } else {
                        // If parsing fails or container unfound, full reload
                        window.location.href = url;
                    }
                })
                .catch(err => {
                    console.error('AJAX Load Error:', err);
                    window.location.href = url;
                })
                .finally(() => {
                    if (loading) loading.style.display = 'none';
                    container.style.opacity = '1';
                });
            }
        }
    });

    // Handle Payment Modal logic
    let currentPaymentAppointmentId = null;

    function openPaymentModal(appointmentId, amount) {
        currentPaymentAppointmentId = appointmentId;
        document.getElementById('payment-appt-id').value = appointmentId;
        document.getElementById('payment-modal-amount').innerText = amount;
        document.getElementById('payment-modal-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        document.getElementById('payment-modal-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }

    function previewPaymentFile() {
        const input = document.getElementById('payment_proof_input');
        const preview = document.getElementById('payment-file-preview');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            preview.style.display = 'block';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<p style="font-size: 0.8rem; font-weight: 800;"><i class="fa-solid fa-file-pdf"></i> Archivo seleccionado: ${file.name}</p>`;
            }
        }
    }
</script>

<!-- Payment Modal Overlay -->
<div id="payment-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 10001; align-items: center; justify-content: center; padding: 1rem;">
    <div class="neobrutalist-card" style="background: white; width: 100%; max-width: 450px; padding: 2rem; border-radius: 12px; position: relative;">
        <button onclick="closePaymentModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #000; z-index: 10;">
            <i class="fa-solid fa-xmark"></i>
        </button>
        
        <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; text-align: center;">INFORMAR PAGO</h3>
        
        <form action="{{ route('appointments.uploadProof') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="appointment_id" id="payment-appt-id">
            
            <div style="background: #f0fdf4; border: 2px solid #166534; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                <p style="margin: 0; font-weight: 800; color: #166534; font-size: 0.9rem;">Monto a abonar:</p>
                <p style="margin: 5px 0 0 0; font-size: 1.5rem; font-weight: 900; color: #000;">$<span id="payment-modal-amount">0</span></p>
            </div>

            <div style="background: #fafafa; border: 2px solid #000; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 3px 3px 0px #000;">
                <p style="margin: 0 0 0.5rem 0; font-weight: 900; font-size: 0.8rem; text-transform: uppercase;">Alias:</p>
                <p style="margin: 0; font-family: 'Inter', sans-serif; font-weight: 500; font-size: 0.95rem; color: #444; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-weight: 800; font-family: 'Syne', sans-serif; color: #000;">nazarena.deluca</span> 
                    <button type="button" onclick="const btn = this; navigator.clipboard.writeText('nazarena.deluca').then(() => { const original = btn.innerText; btn.innerText = '¡Copiado!'; btn.style.background = '#dcfce7'; setTimeout(() => { btn.innerText = original; btn.style.background = '#fff'; }, 7000); })" style="background: #fff; border: 1px solid #000; border-radius: 4px; padding: 2px 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; color: #000; transition: background 0.3s;">Copiar</button>
                </p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem; font-size: 0.85rem;">Subir Comprobante (Máx 10Mb):</label>
                <div class="neobrutalist-input" style="padding: 10px; background: white; text-align: center;">
                    <input type="file" name="proof" id="payment_proof_input" style="font-size: 0.85rem; width: 100%; cursor: pointer;" accept="image/*,application/pdf" onchange="previewPaymentFile()" required>
                </div>
                <div id="payment-file-preview" style="display: none; margin-top: 1rem; border: 2px solid #000; padding: 5px; background: #eee; border-radius: 4px; text-align: center;"></div>
            </div>

            <button type="submit" onclick="if(this.form.checkValidity()) { if(typeof window.showProcessing === 'function') window.showProcessing('Enviando comprobante...'); }" class="neobrutalist-btn bg-verde" style="width: 100%; padding: 0.8rem; font-size: 1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-paper-plane"></i> Enviar Comprobante
            </button>
        </form>
    </div>
</div>
@endsection
