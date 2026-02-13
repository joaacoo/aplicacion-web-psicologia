@extends('layouts.app')

@section('title', 'Configuración - Lic. Nazarena De Luca')
@section('header_title', 'Configuraciones Generales')

@section('content')
<style>
    /* Tabs Styling (Copied and adapted from Finances) */
    .config-tabs {
        display: flex;
        gap: 1rem;
        border-bottom: 2px solid #000; /* Changed to black to match theme */
        margin-bottom: 2rem; /* Reduced spacing as requested */
        flex-wrap: nowrap; /* Prevent wrapping */
        overflow-x: auto; /* Allow horizontal scroll */
        padding-bottom: 5px; /* Space for scrollbar if needed */
        -webkit-overflow-scrolling: touch;
    }
    .tab-btn {
        padding: 0.8rem 1.5rem;
        background: white;
        border: 2px solid #000;
        margin-right: -2px;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        white-space: nowrap; /* Ensure text stays on one line */
        font-size: 0.9rem;
        color: #000;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
        border-bottom: none; /* Look like tabs attached to content */
        border-radius: 10px 10px 0 0; /* Rounded top corners */
        position: relative;
        top: 2px; /* Overlap border */
    }
    .tab-btn:hover {
        background: #f0f0f0;
    }
    .tab-btn.active {
        background: #000;
        color: white;
        border-bottom: 2px solid #000; /* Hide bottom line of tab? No, fill it */
    }
    .tab-pane {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .tab-pane.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="flex flex-col">
    
    <!-- Tab Navigation -->
    <div class="config-tabs">
        <button class="tab-btn active" onclick="switchTab('general')">
            <i class="fa-solid fa-sliders"></i> General
        </button>
        <button class="tab-btn" onclick="switchTab('horarios')">
            <i class="fa-regular fa-calendar-check"></i> Horarios
        </button>
        <button class="tab-btn" onclick="switchTab('bloqueos')">
            <i class="fa-solid fa-user-lock"></i> Bloqueos
        </button>
    </div>

    <!-- SECCIÓN 1: CONFIGURACIÓN GENERAL (Honorarios y Duración) -->
    <div id="tab-general" class="tab-pane active" style="margin-top: 0 !important; padding-top: 0 !important; margin-bottom: 6rem;">
        <!-- Removed redundant header since tabs now serve as headers -->
        <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem;">
           <i class="fa-solid fa-sliders"></i> Configuración de Honorarios y Sesiones
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            
            <!-- Configuración de Honorarios -->
            <div style="background: var(--color-amarillo); border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800; font-size: 1.2rem;"><i class="fa-solid fa-money-bill-wave"></i> Honorarios</h4>
                <p style="font-size: 0.9rem; margin-bottom: 1.5rem; color: #555;">Precio base por sesión.</p>
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 800; display: block; margin-bottom: 0.5rem;">PRECIO BASE ($)</label>
                        <input type="number" name="precio_base_sesion" value="{{ $basePrice }}" class="neobrutalist-input" style="padding: 10px; font-size: 1rem; width: 100%;" required>
                    </div>
                    <input type="hidden" name="duracion_sesion" value="{{ auth()->user()->profesional->duracion_sesion ?? 45 }}">
                    <input type="hidden" name="intervalo_sesion" value="{{ auth()->user()->profesional->intervalo_sesion ?? 15 }}">
                    
                    <button type="submit" class="neobrutalist-btn bg-white w-full" style="padding: 10px; font-size: 0.9rem;">
                        Guardar Honorarios
                    </button>
                </form>
            </div>

            <!-- Configuración de Sesiones -->
            <div style="background: var(--color-celeste); border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800; font-size: 1.2rem;"><i class="fa-solid fa-clock"></i> Sesiones</h4>
                <p style="font-size: 0.9rem; margin-bottom: 1.5rem; color: #555;">Duración y espacios entre turnos.</p>
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 800; display: block; margin-bottom: 0.5rem;">DURACIÓN (MIN)</label>
                            <input type="number" name="duracion_sesion" value="{{ auth()->user()->profesional->duracion_sesion ?? 45 }}" class="neobrutalist-input" style="padding: 10px; font-size: 1rem;" required>
                        </div>
                         <div>
                            <label style="font-size: 0.8rem; font-weight: 800; display: block; margin-bottom: 0.5rem;">INTERVALO (MIN)</label>
                            <input type="number" name="intervalo_sesion" value="{{ auth()->user()->profesional->intervalo_sesion ?? 15 }}" class="neobrutalist-input" style="padding: 10px; font-size: 1rem;" required>
                        </div>
                    </div>
                    <button type="submit" class="neobrutalist-btn bg-amarillo w-full" style="padding: 10px; font-size: 0.9rem;">
                        Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: HORARIOS DE ATENCIÓN (Semanal) -->
    <!-- SECCIÓN 2: HORARIOS DE ATENCIÓN (Semanal) -->
    <div id="tab-horarios" class="tab-pane" style="margin-top: 0 !important; padding-top: 0 !important; margin-bottom: 6rem;">
        <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem;">
            <i class="fa-regular fa-calendar-check"></i> Horarios de Atención Semanal
        </h3>
        <p style="margin: -1rem 0 1.5rem 0; font-size: 0.9rem; color: #555;">
             Estos son tus horarios base. Se usarán si no hay eventos específicos en tu Google Calendar ("LIBRE").
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            
             <!-- Formulario para agregar -->
             <div style="background: #fdfdfd; border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800; font-size: 1.2rem;">Agregar Nuevo Horario</h4>
                <form action="{{ route('admin.availabilities.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Día de la semana:</label>
                        <select name="dia_semana" class="neobrutalist-input" style="width:100%; margin-bottom:0; padding: 10px;" required>
                            <option value="all">TODOS LOS DÍAS</option>
                            <option value="1">Lunes</option>
                            <option value="2">Martes</option>
                            <option value="3">Miércoles</option>
                            <option value="4">Jueves</option>
                            <option value="5">Viernes</option>
                            <option value="6">Sábado</option>
                            <option value="0">Domingo</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="flex:1;">
                            <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Desde:</label>
                            <input type="time" name="hora_inicio" class="neobrutalist-input" style="width:100%; margin-bottom:0; padding: 8px;" required>
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Hasta:</label>
                            <input type="time" name="hora_fin" class="neobrutalist-input" style="width:100%; margin-bottom:0; padding: 8px;" required>
                        </div>
                    </div>
                    <button type="submit" class="neobrutalist-btn bg-amarillo w-full" style="padding: 10px;">Guardar Horario</button>
                </form>
            </div>

            <!-- Listado de horarios -->
            <div style="max-height: 400px; overflow-y: auto; border: 3px solid #000; border-radius: 15px; background: white; box-shadow: 4px 4px 0px #000;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead style="background: #000; color: white; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 1rem; text-align: left;">Día</th>
                            <th style="padding: 1rem; text-align: left;">Rango</th>
                            <th style="padding: 1rem; text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $diasStrings = [0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
                        @endphp
                        @forelse($availabilities as $dayIndex => $dayAvailabilities)
                            @foreach($dayAvailabilities as $avail)
                            <tr style="border-bottom: 2px solid #eee;">
                                <td style="padding: 1rem; font-weight: 800;">{{ $diasStrings[$avail->dia_semana] }}</td>
                                <td style="padding: 1rem;">{{ \Carbon\Carbon::parse($avail->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($avail->hora_fin)->format('H:i') }} hs</td>
                                <td style="padding: 1rem; text-align: right;">
                                    <form id="delete-avail-{{ $avail->id }}" action="{{ route('admin.availabilities.destroy', $avail->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="neobrutalist-btn" style="background: var(--color-rosa); padding: 0.3rem 0.6rem; font-size: 0.7rem;" onclick="confirmAction('delete-avail-{{ $avail->id }}', '¿Eliminar este horario de atención?')"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="3" style="padding: 2rem; text-align: center; color: #999; border: none !important;">No configuraste horarios todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: GESTIÓN DE BLOQUEOS (Días no laborables) -->
    <!-- SECCIÓN 3: GESTIÓN DE BLOQUEOS (Días no laborables) -->
    <div id="tab-bloqueos" class="tab-pane" style="margin-top: 0 !important; padding-top: 0 !important; margin-bottom: 6rem;">
        <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-user-lock"></i> Gestión de Bloqueos Agendados
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            
            <!-- Formulario Bloquear Día -->
            <div style="background: #e0f2f1; border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800; color: #004d40; font-size: 1.2rem; margin-bottom: 1rem;">
                    Bloquear una Fecha
                </h4>
                
                <form id="block-day-form" action="{{ route('admin.blocked-days.store') }}" method="POST" style="margin-bottom: 0;">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Fecha Desde:</label>
                        <input type="date" name="date" class="neobrutalist-input w-full" style="margin-bottom: 0.5rem; cursor: pointer;" required min="{{ date('Y-m-d') }}" onclick="this.showPicker()">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Fecha Hasta (Opcional):</label>
                        <input type="date" name="end_date" class="neobrutalist-input w-full" style="margin-bottom: 0.5rem; cursor: pointer;" min="{{ date('Y-m-d') }}" onclick="this.showPicker()">
                        <p style="font-size: 0.75rem; color: #666; margin: -0.3rem 0 0.5rem 0;">Si dejás esto vacío, solo se bloqueará un día.</p>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Motivo (Opcional):</label>
                        <input type="text" name="reason" placeholder="Ej: Vacaciones / Licencia" class="neobrutalist-input w-full" style="margin-bottom: 0;">
                    </div>
                    <button type="button" class="neobrutalist-btn bg-verde w-full" style="margin-bottom: 0; padding: 10px;" onclick="confirmAction('block-day-form', '¿Bloquear fecha o período?')">Bloquear</button>
                </form>
            </div>

            <!-- Listados -->
            <div style="background: white; border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000; display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Listado Manuales -->
                <div>
                    <h5 style="margin: 0 0 1rem 0; font-weight: 800; font-size: 1.1rem; border-bottom: 2px solid #000; padding-bottom: 0.5rem;">
                        <i class="fa-solid fa-hand-paper"></i> Bloqueos Manuales
                    </h5>
                    <div class="scroll-panel" style="max-height: 200px; overflow-y: auto; background: #fafafa; border: 2px solid #000; padding: 0.5rem; border-radius: 8px;">
                         @forelse($manualBlocks ?? [] as $bd)
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #ccc; padding: 0.5rem; font-size: 0.85rem;">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($bd->date)->format('d/m/Y') }}</strong>
                                    <span style="display:block; font-size: 0.75rem; color: #666;">{{ $bd->reason ?? 'Sin motivo' }}</span>
                                </div>
                                <form id="delete-blocked-{{ $bd->id }}" action="{{ route('admin.blocked-days.destroy', $bd->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="neobrutalist-btn" style="background: var(--color-rosa); padding: 2px 6px; font-size: 0.7rem;" onclick="confirmAction('delete-blocked-{{ $bd->id }}', '¿Desbloquear este día?')"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        @empty
                            <p style="text-align: center; font-size: 0.8rem; color: #777; margin: 0.5rem 0;">No hay bloqueos manuales activos.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Listado Feriados -->
                <div>
                    <h5 style="margin: 0 0 1rem 0; font-weight: 800; font-size: 1.1rem; border-bottom: 2px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fa-solid fa-flag"></i> Feriados Oficiales</span>
                        <span style="font-size: 0.7rem; background: #e8f5e9; color: #1b5e20; padding: 4px 8px; border-radius: 4px; border: 1px solid #1b5e20; font-weight: 700;">Sincronizado 2026</span>
                    </h5>
                    
                    <div class="scroll-panel" style="max-height: 200px; overflow-y: auto; background: #fafafa; border: 2px solid #000; padding: 0.5rem; border-radius: 8px;">
                        @forelse($holidays ?? [] as $bd)
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #ccc; padding: 0.5rem; font-size: 0.85rem;">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($bd->date)->format('d/m/Y') }}</strong>
                                    <span style="display:block; font-size: 0.75rem; color: #666;">{{ $bd->reason ?? 'Feriado' }}</span>
                                </div>
                                <span style="font-size: 2.5rem; color: var(--color-verde); line-height: 1;"><i class="fa-solid fa-circle-check"></i></span>
                            </div>
                        @empty
                            <p id="holiday-sync-status" style="text-align: center; font-size: 0.8rem; color: #777; margin: 0.5rem 0;">Sincronizando feriados...</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
    // Tab Switching Logic
    function switchTab(tabName) {
        // Hide all panes
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        // Deactivate all buttons
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

        // Show selected pane
        document.getElementById('tab-' + tabName).classList.add('active');
        // Activate button - Find button that triggered this, or find by index/selector. 
        // Simpler: event.target adds active, but since I call this inline, I need to pass 'this' or similar. 
        // Let's use event.currentTarget
    }
    
    // Add event listener to buttons to handle 'active' class more reliably than inline onclick
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                buttons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // 1. Auto-Sync Holidays Logic (Silent)
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Check if route exists before fetching to avoid 404 in tests if route missing
        const holidaysRoute = '{{ route("admin.calendar.import-holidays") }}';
        if(holidaysRoute) {
             fetch(holidaysRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Feriados Sincronizados:', data.message);
                const statusElem = document.getElementById('holiday-sync-status');
                if (statusElem) statusElem.innerText = "Sincronizados.";
            })
            .catch(error => console.error('Error Sync Holidays:', error));
        }

        // 2. Scroll Logic: Smart Handling
        const scrollPanels = document.querySelectorAll('.scroll-panel');
        scrollPanels.forEach(panel => {
            panel.addEventListener('wheel', function(e) {
                const atTop = this.scrollTop === 0;
                const atBottom = Math.abs(this.scrollHeight - this.scrollTop - this.clientHeight) < 1;
                const scrollingUp = e.deltaY < 0;
                const scrollingDown = e.deltaY > 0;

                if ((atTop && scrollingUp) || (atBottom && scrollingDown)) {
                    return;
                }
                e.stopPropagation();
            }, { passive: false });
        });
    });
</script>

<!-- Payment Proof Modal (Kept as requested/preserved) -->
<div id="proofModal" class="modal-overlay no-select">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin:0;">Comprobante de Pago</h3>
            <button class="close-modal" onclick="closeProofModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-image-col" style="background: #222; display: flex; align-items: center; justify-content: center; min-height: 200px; flex-direction: column; position: relative;">
                <div id="modalLoader" style="color: white; font-size: 2rem; display: none;">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </div>
                <img id="modalImage" src="" alt="Comprobante" style="max-height: 50vh; max-width: 100%; object-fit: contain; border: 5px solid #fff; box-shadow: 10px 10px 0px #000; display: none;">
                <iframe id="modalPdf" src="" style="width: 100%; height: 50vh; border: 5px solid #fff; box-shadow: 10px 10px 0px #000; display: none;"></iframe>
                <p id="modalError" style="display: none; color: white; font-weight: bold; font-size: 1.2rem;">Archivo no encontrado / No disponible</p>
            </div>
            <div class="modal-info-col">
                <div>
                    <span class="custom-date-label">Paciente</span>
                    <p id="modalPatient" style="font-weight: 900; font-size: 1.2rem; margin:0;"></p>
                </div>
                <div>
                    <span class="custom-date-label">Subido el</span>
                    <p id="modalDate" style="font-weight: 700; margin:0;"></p>
                </div>
                <div style="margin-top:auto;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Generic Action Confirmation Modal -->
<div id="actionConfirmModal" class="confirm-modal-overlay" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000;">
    <div class="neobrutalist-card" style="background: white; max-width: 400px; width: 90%; border: 4px solid #000; box-shadow: 8px 8px 0px #000;">
        <h3 style="margin-top: 0; border-bottom: 2px solid #000; padding-bottom: 0.5rem; text-align: center;">Confirmar Acción</h3>
        <p id="actionConfirmText" style="font-weight: 700; margin: 1.5rem 0; font-size: 1.1rem; text-align: center;"></p>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <button class="neobrutalist-btn bg-lila" onclick="closeActionModal()">Cancelar</button>
            <button id="actionConfirmBtn" class="neobrutalist-btn bg-verde">Confirmar</button>
        </div>
    </div>
</div>
<script>
    // Confirm Action Logic
    let pendingActionFormId = null;

    window.confirmAction = function(formId, message) {
        const form = document.getElementById(formId);
        if (!form) {
            console.error('Form not found:', formId);
            alert('Error: No se encuentra el formulario. Recarga la página.');
            return;
        }

        pendingActionFormId = formId;
        const modal = document.getElementById('actionConfirmModal');
        const textElement = document.getElementById('actionConfirmText');
        
        if (modal && textElement) {
            textElement.innerText = message;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            const btn = document.getElementById('actionConfirmBtn');
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', function() {
                if (pendingActionFormId) {
                    const f = document.getElementById(pendingActionFormId);
                    if(f) f.submit(); 
                }
                closeActionModal();
            });
        } else {
            if(confirm(message)) form.submit();
        }
    };

    window.closeActionModal = function() {
        const modal = document.getElementById('actionConfirmModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        pendingActionFormId = null;
    };
    
    window.openProofModal = function(fileSrc, patientName, uploadDate, fileExtension) {
         // Same implementation as before
        const modalImage = document.getElementById('modalImage');
        const modalPdf = document.getElementById('modalPdf');
        const modalError = document.getElementById('modalError');
        const modalLoader = document.getElementById('modalLoader');
        
        if(modalImage) modalImage.style.display = 'none';
        if(modalPdf) modalPdf.style.display = 'none';
        if(modalError) modalError.style.display = 'none';
        if(modalLoader) modalLoader.style.display = 'block';

        const isPdf = fileExtension ? (fileExtension.toLowerCase() === 'pdf') : fileSrc.toLowerCase().includes('.pdf');
        
        if (isPdf) {
            if(modalPdf) {
                modalPdf.onload = function() {
                    if(modalLoader) modalLoader.style.display = 'none';
                    this.style.display = 'block';
                };
                modalPdf.src = fileSrc;
            }
        } else {
            if(modalImage) {
                modalImage.onload = function() {
                    if(modalLoader) modalLoader.style.display = 'none';
                    if(modalError) modalError.style.display = 'none';
                    this.style.display = 'block';
                };
                modalImage.onerror = function() {
                    if(modalLoader) modalLoader.style.display = 'none';
                    this.style.display = 'none';
                    if(modalError) modalError.style.display = 'block';
                };
                modalImage.src = fileSrc;
            }
        }
        
        document.getElementById('modalPatient').innerText = patientName || 'Paciente';
        document.getElementById('modalDate').innerText = uploadDate ? (uploadDate + ' hs') : '';
        document.getElementById('proofModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    window.closeProofModal = function() {
        const modalImage = document.getElementById('modalImage');
        const modalPdf = document.getElementById('modalPdf');
        const modalError = document.getElementById('modalError');
        const modalLoader = document.getElementById('modalLoader');
        
        if(modalImage) modalImage.src = '';
        if(modalPdf) modalPdf.src = '';
        if(modalError) modalError.style.display = 'none';
        if(modalLoader) modalLoader.style.display = 'none';
        
        document.getElementById('proofModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }
</script>

<style>
    /* Mobile Optimizations */
    @media (max-width: 768px) {
        /* Remove table borders on mobile */
        table {
            border: none !important;
            box-shadow: none !important;
        }
        
        /* Container with table - remove border */
        div[style*="border: 3px solid #000"][style*="overflow-y: auto"] {
            border: 1px solid #000 !important;
            box-shadow: 2px 2px 0px #000 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        /* Grid containers - add padding */
        div[style*="display: grid"][style*="grid-template-columns"] {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            gap: 1rem !important;
        }
        
        /* Form containers with colored backgrounds */
        div[style*="background: #fdfdfd"],
        div[style*="background: #e0f2f1"],
        div[style*="background: var(--color-amarillo)"],
        div[style*="background: var(--color-celeste)"],
        div[style*="background: white"][style*="border: 3px solid #000"] {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        /* Scroll panels */
        .scroll-panel {
            border: 1px solid #000 !important;
        }
    }
</style>

@endsection
