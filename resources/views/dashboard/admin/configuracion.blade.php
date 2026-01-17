@extends('layouts.app')

@section('title', 'Configuración - Lic. Nazarena De Luca')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Configuración de Horarios de Atención -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem;">
            <h3 id="disponibilidad" style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clock"></i> Horarios de Atención</h3>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #555;">
                <strong>Modo Google Calendar:</strong> Si creás eventos en tu Google Calendar que digan <strong>"LIBRE"</strong> o <strong>"DISPONIBLE"</strong>, esos serán los únicos que verán los pacientes ese día. 
                Sino, se usarán estos horarios base como alternativa.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem;">
            <!-- Configuración de Sesiones (Moved here) -->
            <div style="background: var(--color-celeste); border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800;"><i class="fa-solid fa-clock"></i> Configuración de Sesiones</h4>
                <p style="font-size: 0.85rem; margin-bottom: 1rem; color: #555;">Definí cuánto duran tus turnos y el espacio entre ellos.</p>
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 0.3rem;">DURACIÓN (MIN)</label>
                            <input type="number" name="duracion_sesion" value="{{ auth()->user()->duracion_sesion ?? 45 }}" class="neobrutalist-input" style="padding: 5px; font-size: 0.9rem;" required>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 0.3rem;">INTERVALO (MIN)</label>
                            <input type="number" name="intervalo_sesion" value="{{ auth()->user()->intervalo_sesion ?? 15 }}" class="neobrutalist-input" style="padding: 5px; font-size: 0.9rem;" required>
                        </div>
                    </div>
                    <button type="submit" class="neobrutalist-btn bg-amarillo w-full" style="padding: 5px; font-size: 0.8rem;">
                        Guardar Configuración
                    </button>
                </form>
            </div>
            <!-- Formulario para agregar -->
            <div style="background: #fdfdfd; border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800;">Agregar Nuevo Horario</h4>
                <form action="{{ route('admin.availabilities.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Día de la semana:</label>
                        <select name="dia_semana" class="neobrutalist-input" style="width:100%; margin-bottom:0;" required>
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
                            <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Desde (Ej: 14:00):</label>
                            <input type="time" name="hora_inicio" class="neobrutalist-input" style="width:100%; margin-bottom:0;" required>
                        </div>
                        <div style="flex:1;">
                            <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Hasta (Ej: 15:00):</label>
                            <input type="time" name="hora_fin" class="neobrutalist-input" style="width:100%; margin-bottom:0;" required>
                        </div>
                    </div>
                    <button type="submit" class="neobrutalist-btn bg-amarillo w-full">Guardar Horario</button>
                </form>
            </div>

            <!-- Listado de horarios -->
            <div style="max-height: 350px; overflow-y: auto; border: 3px solid #000; border-radius: 15px; background: white; box-shadow: 4px 4px 0px #000;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead style="background: #000; color: white;">
                        <tr>
                            <th style="padding: 0.8rem; text-align: left;">Día</th>
                            <th style="padding: 0.8rem; text-align: left;">Rango</th>
                            <th style="padding: 0.8rem; text-align: right;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $diasStrings = [0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];
                        @endphp
                        @forelse($availabilities as $avail)
                            <tr style="border-bottom: 2px solid #eee;">
                                <td style="padding: 0.8rem; font-weight: 800;">{{ $diasStrings[$avail->dia_semana] }}</td>
                                <td style="padding: 0.8rem;">{{ \Carbon\Carbon::parse($avail->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($avail->hora_fin)->format('H:i') }} hs</td>
                                <td style="padding: 0.8rem; text-align: right;">
                                    <form id="delete-avail-{{ $avail->id }}" action="{{ route('admin.availabilities.destroy', $avail->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="neobrutalist-btn" style="background: var(--color-rosa); padding: 0.3rem 0.6rem; font-size: 0.7rem;" onclick="confirmAction('delete-avail-{{ $avail->id }}', '¿Eliminar este horario de atención?')"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="padding: 2rem; text-align: center; color: #999;">No configuraste horarios todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- División de Bloqueos en 2 Secciones -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; margin-top: 2rem;">
            
            <!-- SECCIÓN 1: ACCIONES (Formularios) -->
            <div style="background: #e0f2f1; border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-top: 0; font-weight: 800; color: #004d40; font-size: 1.2rem; margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-user-lock"></i> Gestionar Bloqueos
                </h4>
                
                <!-- Form Bloquear Día -->
                <form id="block-day-form" action="{{ route('admin.blocked-days.store') }}" method="POST" style="margin-bottom: 2rem;">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Fecha a bloquear:</label>
                        <input type="date" name="date" class="neobrutalist-input w-full" style="margin-bottom: 0.5rem;" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight: 800; font-size: 0.85rem; margin-bottom: 0.3rem;">Motivo (Opcional):</label>
                        <input type="text" name="reason" placeholder="Ej: Vacaciones / Licencia" class="neobrutalist-input w-full" style="margin-bottom: 0;">
                    </div>
                    <button type="button" class="neobrutalist-btn bg-verde w-full" style="margin-bottom: 0;" onclick="confirmAction('block-day-form', '¿Bloquear esta fecha?')">Bloquear Fecha</button>
                </form>
                
                <div style="border-top: 2px dashed #004d40; margin: 1.5rem 0; opacity: 0.3;"></div>
                
                <!-- Weekend Toggle -->
                <form id="toggle-weekends-form" onsubmit="return false;">
                    @csrf
                    <button type="button" id="btn-toggle-weekends" class="neobrutalist-btn w-full" 
                            style="background: {{ $blockWeekends ? '#d32f2f' : '#388e3c' }}; color: white; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.9rem; padding: 0.8rem;" 
                            onclick="window.showConfirm('¿Estás segura de cambiar la disponibilidad de los fines de semana?', toggleWeekendsAJAX)">
                        @if($blockWeekends)
                            <i class="fa-solid fa-lock"></i> Sáb/Dom: <strong>BLOQUEADOS</strong>
                        @else
                            <i class="fa-solid fa-unlock"></i> Sáb/Dom: <strong>LIBRES</strong>
                        @endif
                    </button>
                    <p style="font-size: 0.75rem; color: #555; margin-top: 0.5rem; text-align: center;">Controla la disponibilidad automática de los fines de semana.</p>
                </form>
            </div>

            <!-- SECCIÓN 2: LISTADOS (Manuales y Feriados) -->
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
                        <span style="font-size: 0.7rem; background: #e8f5e9; color: #1b5e20; padding: 4px 8px; border-radius: 4px; border: 1px solid #1b5e20; font-weight: 700;">Sincronizado con feriados nacionales 2026</span>
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
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Auto-Sync Holidays Logic (Silent)
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('{{ route("admin.calendar.import-holidays") }}', {
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

        // 2. Scroll Logic: Smart Handling
        // Allow panel to scroll content. If boundary reached, scroll page.
        const scrollPanels = document.querySelectorAll('.scroll-panel');
        scrollPanels.forEach(panel => {
            panel.addEventListener('wheel', function(e) {
                const atTop = this.scrollTop === 0;
                const atBottom = Math.abs(this.scrollHeight - this.scrollTop - this.clientHeight) < 1;
                const scrollingUp = e.deltaY < 0;
                const scrollingDown = e.deltaY > 0;

                if ((atTop && scrollingUp) || (atBottom && scrollingDown)) {
                    // Boundary reached: Let event bubble to window (Page Scroll)
                    return;
                }
                
                // Inside bounds: Scroll the panel, but STOP bubbling to prevent double scroll
                // Note: We do NOT preventDefault, so the panel still scrolls. 
                // We just stop the event from reaching the parent (Page).
                e.stopPropagation();
            }, { passive: false });
        });
    });

    // 3. AJAX Weekend Toggle API
    function toggleWeekendsAJAX() {
        const btn = document.getElementById('btn-toggle-weekends');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Optimistic UI Update
        const originalContent = btn.innerHTML;
        const originalColor = btn.style.background;
        
        // Loader State
        btn.disabled = true;
        btn.style.opacity = '0.8';
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Procesando...';

        fetch('{{ route("admin.calendar.toggle-weekends") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.style.opacity = '1';
            
            if (data.success) {
                // Update Button UI based on REAL server response
                if (data.block_weekends) {
                    // It is now BLOCKED -> Red
                    btn.style.background = '#d32f2f'; // Red
                    btn.innerHTML = '<i class="fa-solid fa-lock"></i> Sáb/Dom: <strong>BLOQUEADOS</strong>';
                } else {
                    // It is now FREE -> Green
                    btn.style.background = '#388e3c'; // Green
                    btn.innerHTML = '<i class="fa-solid fa-unlock"></i> Sáb/Dom: <strong>LIBRES</strong>';
                }
            } else {
                alert('Error al actualizar estado.');
                btn.innerHTML = originalContent;
                btn.style.background = originalColor;
            }
        })
        .catch(error => {
            console.error('Error toggling weekends:', error);
            alert('Error al conectar con el servidor.');
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalContent;
            btn.style.background = originalColor;
        });
    }
</script>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.scripts')
@endsection
