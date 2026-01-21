@extends('layouts.app')

@section('title', 'Mi Portal - Lic. Nazarena De Luca')

@section('content')
<!-- Block 1: User Provided Patient Dashboard Content -->
<div class="container mt-16" style="min-height: 80vh; padding-top: 1rem; padding-bottom: 3rem;">
    <link rel="stylesheet" href="{{ asset('css/whatsapp_widget.css') }}">
    <div id="top"></div>
    <div class="flex gap-4" style="flex-wrap: wrap;">
        
        <!-- Left Column: New Appointment Stepper -->
        <div style="flex: 1; min-width: 320px;">
            <!-- Next Session Card (Top) -->
        
        <div id="booking" class="neobrutalist-card" style="margin-bottom: 4rem;">
            
            <!-- Feedback de Errores -->
                            
            <h3 class="mb-4"><i class="fa-solid fa-calendar-plus"></i> Reservar Nuevo Turno</h3>
                
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
        <div style="flex: 2; min-width: 300px;">
            

            <style>
                @media (max-width: 768px) {
                    #mis-turnos { padding: 1rem !important; }
                    #mis-turnos h3 { font-size: 1rem; }
                    #mis-turnos table th, #mis-turnos table td { padding: 0.3rem !important; font-size: 0.85rem; }
                }
            </style>
            <div id="mis-turnos" class="neobrutalist-card">
                <h3 class="no-select" style="background: var(--color-lila); display: inline-block; padding: 0.2rem 0.5rem; border: 3px solid #2D2D2D;">Mis Turnos y Pagos</h3>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="border-bottom: 3px solid #2D2D2D;">
                                <th style="text-align: left; padding: 0.5rem;">Fecha</th>
                                <th style="text-align: left; padding: 0.5rem;">Estado Turno</th>
                                <th style="text-align: left; padding: 0.5rem;">Pago</th>
                                <th style="text-align: left; padding: 0.5rem;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($appointments) && $appointments->count() > 0)
                                @foreach($appointments as $appt)
                                    <tr style="border-bottom: 1px solid #2D2D2D;">
                                        <td style="padding: 0.5rem;">{{ $appt->fecha_hora->format('d/m H:i') }}</td>
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
            <div id="materiales" class="neobrutalist-card" style="margin-top: 2rem;">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem;"><i class="fa-solid fa-folder-open"></i> Mi Biblioteca de Materiales</h3>
                
                @if(isset($resources) && $resources->count() > 0)
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                        @foreach($resources as $res)
                        <div class="neobrutalist-card" style="background: white; border-width: 3px; padding: 1.2rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                            <div>
                                <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--color-celeste);">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <h4 style="margin: 0 0 0.5rem 0; font-weight: 900; line-height: 1.2;">{{ $res->title }}</h4>
                                @if($res->description)
                                    <p style="font-size: 0.85rem; color: #555; margin-bottom: 1rem;">{{ $res->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('resources.download', $res->id) }}" class="neobrutalist-btn bg-amarillo w-full text-center" style="font-size: 0.85rem; padding: 10px;">
                                <i class="fa-solid fa-download"></i> Descargar
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; background: #f9f9f9; border: 2px dashed #ccc; border-radius: 10px;">
                        <p style="color: #666; font-weight: 700;">No hay materiales compartidos todavía.</p>
                    </div>
                @endif
            </div>

    </div>
        <!-- Custom Alert Modal (Cartel) -->
        <div id="alert-modal-overlay" class="confirm-modal-overlay" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.7);">
            <div class="confirm-modal" style="max-width: 400px; padding: 2rem; border: 5px solid #000; box-shadow: 10px 10px 0px #000; border-radius: 20px;">
                <div class="confirm-modal-title" style="color: #000; font-size: 1.5rem; margin-bottom: 1rem; font-weight: 900;">¡Atención!</div>
                <div id="alert-modal-message" class="confirm-modal-message" style="font-weight: 700; font-size: 1rem; margin-bottom: 1.5rem; color: #333; letter-spacing: 0;"></div>
                <div style="display: flex; justify-content: center;">
                    <button onclick="closeAlert()" class="neobrutalist-btn bg-amarillo" style="padding: 5px 20px; font-weight: 800; font-size: 0.75rem; letter-spacing: 0; min-width: 100px;">ENTENDIDO</button>
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

        // Ocultar la barra de navegación al cambiar de paso
        const navbar = document.querySelector('.navbar-unificada');
        if (navbar) {
            navbar.classList.add('nav-hidden');
            setTimeout(() => {
                navbar.classList.remove('nav-hidden');
            }, 1500); // Mostrar nuevamente después de 1.5 segundos
        }

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
        window.showConfirm('¿Estás completamente seguro de que quieres darte de baja? Esta acción es irreversible y se borrarán todos tus datos.', function() {
            if (confirm('Confirmación final: ¿Realmente quieres borrar tu cuenta?')) {
                document.getElementById('delete-account-form').submit();
            }
        });
    }
</script>
<!-- WhatsApp Widget -->
<div id="whatsapp-widget-container">
    <div id="whatsapp-chat-window">
        <div class="wa-header">
            <img src="{{ asset('img/profile-chat.png') }}" alt="Nazarena" class="wa-profile-pic">
            <div class="wa-info">
                <h4>Lic. Nazarena De Luca</h4>
                <p>Normalmente responde en 1 hora</p>
            </div>
            <div style="margin-left: auto; cursor: pointer;" onclick="toggleWhatsApp()">
                <i class="fa-solid fa-times"></i>
            </div>
        </div>
        <div class="wa-body">
            <div class="wa-message-bubble">
                ¡Hola! 👋 Soy Nazarena. <br>¿En qué puedo ayudarte hoy?
            </div>
            
            <div class="wa-quick-replies">
                <a href="https://wa.me/5491139560673?text=Hola,%20quisiera%20reservar%20un%20turno." target="_blank" class="wa-chip">
                    📅 Quiero reservar un turno
                </a>
                <a href="https://wa.me/5491139560673?text=Hola,%20tengo%20una%20consulta%20sobre%20los%20pagos." target="_blank" class="wa-chip">
                    💸 Consulta sobre pagos
                </a>
                <a href="https://wa.me/5491139560673?text=Hola,%20quisiera%20saber%20más%20sobre%20la%20modalidad%20virtual." target="_blank" class="wa-chip">
                    💻 Consulta modalidad virtual
                </a>
            </div>
        </div>
        <div class="wa-footer">
            <a href="https://wa.me/5491139560673" target="_blank" class="wa-btn-main">
                <i class="fa-brands fa-whatsapp"></i> Chatear (11 3956-0673)
            </a>
        </div>
    </div>
    
    <div id="whatsapp-float-btn" onclick="toggleWhatsApp()">
        <i class="fa-brands fa-whatsapp"></i>
    </div>
</div>

<style>
    /* Mobile Optimization for WhatsApp Widget to open from left or fit better */
    @media (max-width: 768px) {
        #whatsapp-chat-window {
            bottom: 80px; 
            right: 1.5rem; /* Anchor to right (aligned with button) */
            left: auto;  /* Do not anchor left */
            transform: scale(0.9); /* Just scale */
            transform-origin: bottom right; /* Grow from button */
            width: 90%;
            max-width: 350px;
        }
        #whatsapp-chat-window.active {
            transform: scale(1);
        }
    }
</style>

<script>
    function toggleWhatsApp() {
        const chatWindow = document.getElementById('whatsapp-chat-window');
        if (chatWindow.style.display === 'flex') {
            chatWindow.classList.remove('active');
            setTimeout(() => {
                chatWindow.style.display = 'none';
            }, 300);
        } else {
            chatWindow.style.display = 'flex';
            // Force reflow
            void chatWindow.offsetWidth;
            chatWindow.classList.add('active');
        }
    }
</script>
</div>

<!-- Block 2: User Provided Manage Modal Content (Appended) -->
<!-- Manage Patient Modal -->
<div id="manageModal" class="confirm-modal-overlay" style="display: none;">
    <div class="confirm-modal" style="max-width: 550px; width: 90%;">
        <div class="confirm-modal-title" id="manageTitle">Gestionar Paciente</div>
        <div class="confirm-modal-message" style="text-align: left;">
            
            <!-- Disassociate Section (Moved Up and Renamed) -->
            <div style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 3px dashed #000;">
                <h4 style="margin-bottom: 0.5rem;">Dar de Baja al Paciente</h4>
                <p style="font-size: 0.85rem; margin-bottom: 1rem; color: #555;">Si el tratamiento terminó o el paciente dejó de asistir, podés desasociarlo aquí.</p>
                
                <form id="manage-delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <label style="font-size: 0.8rem; font-weight: 700;">Motivo de la baja (opcional):</label>
                        <textarea name="motivo" placeholder="Ej: Fin del tratamiento..." class="neobrutalist-input" style="min-height: 80px; font-size: 0.9rem; padding: 10px;"></textarea>
                    </div>
                    <button type="button" class="neobrutalist-btn w-full" style="background: #000; color: white; font-size: 0.9rem;" 
                            onclick="confirmDisassociate()">
                        Confirmar Baja
                    </button>
                </form>
            </div>

            <!-- Contact Section -->
            <div style="margin-bottom: 2rem;">
                <h4 style="margin-bottom: 0.5rem; border-bottom: 2px solid #000; display: inline-block;">Datos de Contacto & Enlaces</h4>
                
                <div style="background: #f9f9f9; padding: 1rem; border: 2px solid #000; margin-bottom: 1rem;">
                    <p><strong>Email:</strong> <span id="manageEmail"></span></p>
                    <p><strong>Teléfono:</strong> <span id="managePhone"></span></p>
                    
                    <!-- Meet Link Form -->
                    <form id="manage-link-form" method="POST" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #ccc;">
                        @csrf
                        <label style="font-size: 0.8rem; font-weight: 700;">Link de Google Meet (Único):</label>
                        <div style="display: flex; gap: 0.5rem; margin-top: 5px;">
                            <input type="url" name="meet_link" id="manageMeetLink" class="neobrutalist-input" placeholder="https://meet.google.com/..." style="flex: 1; font-size: 0.85rem; padding: 8px;">
                            <button type="submit" class="neobrutalist-btn bg-celeste" style="padding: 0 15px; font-size: 0.8rem;">
                                <i class="fa-solid fa-save"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                    <a id="manageMailBtn" href="#" class="neobrutalist-btn text-center" style="background: var(--color-amarillo); font-size: 0.85rem;">
                        <i class="fa-solid fa-envelope"></i> Enviar Mail
                    </a>
                    <a id="manageWhatsAppBtn" href="#" target="_blank" class="neobrutalist-btn text-center" style="background: #25D366; color: white; border-color: #000; font-size: 0.85rem;">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

            <!-- Classification Section -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 3px solid #000; background: #fffbe6; box-shadow: 6px 6px 0px #000; border-radius: 15px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Clasificación de Paciente</h3>
                <p style="font-size: 0.9rem; margin-bottom: 1.5rem; color: #333; font-weight: 500;">
                    ¿Este paciente es nuevo o ya es frecuente? (Los frecuentes no necesitan subir comprobante).
                </p>
                <form id="manage-type-form" method="POST">
                    @csrf
                    <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem;">
                        <button type="submit" name="tipo_paciente" value="nuevo" id="btnTypeNuevo" class="neobrutalist-btn flex-1" style="font-size: 1rem; padding: 15px; background: white;">NUEVO</button>
                        <button type="submit" name="tipo_paciente" value="frecuente" id="btnTypeFrecuente" class="neobrutalist-btn flex-1" style="font-size: 1rem; padding: 15px; background: white;">FRECUENTE</button>
                    </div>
                </form>



                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 2px dashed #000;">
                    <form id="manage-reminder-form" method="POST">
                        @csrf
                        <button type="submit" class="neobrutalist-btn w-full" style="background: var(--color-lila); font-size: 0.9rem; padding: 12px;">
                            <i class="fa-solid fa-bell"></i> Enviar Recordatorio por Mail
                        </button>
                    </form>
                </div>
            </div>

        </div>
        <div class="confirm-modal-buttons" style="margin-top: 2rem;">
            <button onclick="closeManageModal()" class="neobrutalist-btn w-full" style="background: white;">Cerrar</button>
        </div>
    </div>
</div>

<script>
    let currentPatientId = null;
    let currentPatientName = '';

    function closeManageModal() {
        document.getElementById('manageModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function openManageModal(id, name, email, phone, type, meetLink) {
        currentPatientId = id;
        currentPatientName = name;
        
        document.getElementById('manageTitle').innerText = 'Gestionar: ' + name;
        document.getElementById('manageEmail').innerText = email;
        document.getElementById('managePhone').innerText = phone;
        document.getElementById('manageMailBtn').href = 'mailto: ' + email;
        
        const btnNuevo = document.getElementById('btnTypeNuevo');
        const btnFrecuente = document.getElementById('btnTypeFrecuente');
        
        btnNuevo.style.background = (type === 'nuevo') ? 'var(--color-amarillo)' : 'white';
        btnNuevo.style.borderWidth = (type === 'nuevo') ? '4px' : '2px';
        
        btnFrecuente.style.background = (type === 'frecuente') ? 'var(--color-verde)' : 'white';
        btnFrecuente.style.borderWidth = (type === 'frecuente') ? '4px' : '2px';

        const wpBtn = document.getElementById('manageWhatsAppBtn');
        if (phone && phone !== 'No registrado') {
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            // Construct message with link
            const meetUrl = meetLink ? meetLink : '[Link Pendiente]';
            const message = `Hola ${name.split(' ')[0]}, te envío el link para nuestra sesión de hoy: ${meetUrl}`;
            
            wpBtn.href = `https://wa.me/${cleanPhone}/?text=${encodeURIComponent(message)}`;
            wpBtn.setAttribute('data-base-href', `https://wa.me/${cleanPhone}`);
            wpBtn.style.display = 'flex';
            wpBtn.style.alignItems = 'center';
            wpBtn.style.justifyContent = 'center';
        } else {
            wpBtn.style.display = 'none';
        }
        
        // Populate and Action for Meet Link
        const linkInput = document.getElementById('manageMeetLink');
        linkInput.value = meetLink || '';
        document.getElementById('manage-link-form').action = '/admin/patients/' + id + '/update-link';

        document.getElementById('manage-delete-form').action = '/admin/patients/' + id;
        document.getElementById('manage-type-form').action = '/admin/patients/' + id + '/update-type';
        document.getElementById('manage-reminder-form').action = '/admin/patients/' + id + '/send-reminder';
        
        document.getElementById('manageModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function confirmDisassociate() {
        window.showConfirm('¿Estás segura de que querés desasociar permanentemente a ' + currentPatientName + '? Se borrarán todos sus turnos y pagos vinculados.', function() {
            const verification = prompt('Para confirmar la baja definitiva de ' + currentPatientName + ', por favor escribí "ELIMINAR" debajo:');
            if (verification === 'ELIMINAR') {
                document.getElementById('manage-delete-form').submit();
            } else {
                alert('Acción cancelada. El texto no coincidía.');
            }
        });
    }
</script>
@endsection
