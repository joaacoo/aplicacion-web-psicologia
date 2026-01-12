@extends('layouts.app')

@section('title', 'Admin Dashboard - Lic. Nazarena De Luca')

@section('content')
<div class="flex flex-col gap-8">
    
    <!-- Turnos para el Día de Hoy -->
    <div id="hoy" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clipboard-list"></i> Turnos para hoy</h3>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #555;">({{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM YYYY') }})</p>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border: 3px solid #000;">
                <thead>
                    <tr style="background: #000; color: #fff;">
                        <th style="padding: 0.8rem; text-align: left;">Hora</th>
                        <th style="padding: 0.8rem; text-align: left;">Paciente</th>
                        <th style="padding: 0.8rem; text-align: left;">Estado</th>
                        <th style="padding: 0.8rem; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAppointments as $appt)
                        <tr style="border-bottom: 2px solid #000;">
                            <td data-label="Hora" style="padding: 0.8rem; font-weight: 900;">{{ $appt->fecha_hora->format('H:i') }} hs</td>
                            <td data-label="Paciente" style="padding: 0.8rem;">
                                <div style="font-family: 'Courier New', Courier, monospace; font-weight: bold;">{{ $appt->user->nombre }}</div>
                                <div style="font-size: 0.8rem; color: #666;">{{ ucfirst($appt->modalidad) }}</div>
                            </td>
                            <td data-label="Estado" style="padding: 0.8rem;">
                                <span style="background: {{ $appt->estado == 'confirmado' ? 'var(--color-verde)' : ($appt->estado == 'pendiente' ? 'var(--color-amarillo)' : '#ff85b6') }}; padding: 0.2rem 0.5rem; border: 2px solid #000; border-radius: 5px; font-weight: bold; font-size: 0.8rem; text-transform: uppercase;">
                                    {{ $appt->estado }}
                                </span>
                            </td>
                            <td style="padding: 0.8rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    @if($appt->estado == 'pendiente' && $appt->payment)
                                        <button class="neobrutalist-btn bg-verde" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="openProofModal('{{ route('payments.showProof', $appt->payment->id) }}', '{{ $appt->user->nombre }}', '{{ $appt->payment->created_at->format('d/m H:i') }}', '{{ pathinfo($appt->payment->comprobante_ruta, PATHINFO_EXTENSION) }}')">Ver Pago</button>
                                    @endif
                                    @if($appt->estado != 'cancelado')
                                        <form id="cancel-hoy-{{ $appt->id }}" action="{{ route('admin.appointments.cancel', $appt->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="confirmAction('cancel-hoy-{{ $appt->id }}', '¿Cancelar turno de hoy?')">Cancelar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; font-weight: bold; color: #666;">No hay turnos para hoy.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agenda Mensual -->
    <div id="agenda" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-calendar-days"></i> Agenda Mensual</h3>
        </div>

        <!-- Calendar Navigation & Jumper -->
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem; justify-content: center; background: #f0f0f0; padding: 1rem; border: 3px solid #000; border-radius: 10px;">
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button id="prevMonth" class="neobrutalist-btn bg-amarillo" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-chevron-left"></i></button>
                <h2 id="currentMonthYear" style="margin: 0; min-width: 180px; text-align: center; font-size: 1.5rem; font-weight: 900; font-family: 'Courier New', Courier, monospace; text-transform: uppercase; letter-spacing: -1px;"></h2>
                <button id="nextMonth" class="neobrutalist-btn bg-amarillo" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <select id="jumpMonth" class="neobrutalist-input" style="min-width: 110px; height: 40px; margin:0; padding: 0 0.5rem; cursor: pointer;">
                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $key => $mes)
                        <option value="{{ $key }}">{{ $mes }}</option>
                    @endforeach
                </select>
                <input type="number" id="jumpYear" class="neobrutalist-input" style="width: 80px; height: 40px; margin:0; padding: 0 0.5rem;" value="{{ date('Y') }}" min="2026">
                <button onclick="jumpToDate()" class="neobrutalist-btn bg-celeste" style="height: 40px; padding: 0 1rem; font-size: 0.9rem;">Ir</button>
            </div>
        </div>
        
        <!-- Calendar Grid -->
        <div style="background: white; border: 4px solid #000; box-shadow: 6px 6px 0px #000; padding: 0.5rem; border-radius: 15px;">
            <div style="width: 100%;">
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 0.5rem;" class="calendar-grid-header">
                    @foreach(['D','L','M','M','J','V','S'] as $dia)
                        <div style="text-align: center; font-weight: 900; font-size: 0.8rem; padding: 0.2rem; border-bottom: 2px solid #000;">{{ $dia }}</div>
                    @endforeach
                </div>
                <div id="calendarGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;" class="calendar-grid"></div>
            </div>
        </div>
        
        <div id="selectedDayAppointments" style="margin-top: 2rem; display: none;">
            <h3 id="selectedDayTitle" style="margin-bottom: 1rem; font-weight: 900; font-family: 'Outfit', 'Inter', sans-serif;"></h3>
            <div id="selectedDayContent" style="background: white; border: 4px solid #000; box-shadow: 6px 6px 0px #000; padding: 1.5rem; border-radius: 15px;"></div>
        </div>
    </div>

@php
    $calendarAppointments = $appointments->map(function($appt) {
        return [
            'id' => $appt->id,
            'fecha_hora' => $appt->fecha_hora->timezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i:s'),
            'paciente' => $appt->user->nombre,
            'estado' => $appt->estado,
            'modalidad' => $appt->modalidad,
            'telefono' => $appt->user->telefono ?? null
        ];
    });
@endphp
<script>
    // Calendar functionality
    let currentDate = new Date();
    const appointments = @json($calendarAppointments);
    const registrations = @json($recentRegistrations ?? []);

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Update header
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        document.getElementById('currentMonthYear').innerHTML = 
            monthNames[month] + ' ' + year;
        
        // Populate jumper fields
        document.getElementById('jumpMonth').value = month;
        document.getElementById('jumpYear').value = year;

        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Get today's date for highlighting
        const today = new Date();
        const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;
        
        // Build calendar grid
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';
        
        // Add empty cells for days before month starts
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.style.cssText = 'min-height: 80px;';
            grid.appendChild(emptyCell);
        }
        
        // Add days of month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayAppointments = appointments.filter(appt => appt.fecha_hora.startsWith(dateStr));
            
            // Check for registrations on this day
            const dayRegs = registrations.filter(reg => {
                const regDate = new Date(reg.created_at);
                return regDate.getFullYear() === year && regDate.getMonth() === month && regDate.getDate() === day;
            });

            const dayCell = document.createElement('div');
            const isToday = isCurrentMonth && today.getDate() === day;
            
            dayCell.className = 'calendar-day';
            dayCell.style.cssText = `
                min-height: 80px;
                border: 3px solid #000;
                border-radius: 10px;
                padding: 0.5rem;
                background: ${isToday ? 'var(--color-amarillo)' : 'white'};
                cursor: pointer;
                transition: all 0.2s;
                box-shadow: 3px 3px 0px #000;
                display: flex;
                flex-direction: column;
                gap: 2px;
                overflow: hidden;
            `;
            
            dayCell.addEventListener('mouseenter', () => {
                dayCell.style.transform = 'translate(-2px, -2px)';
                dayCell.style.boxShadow = '5px 5px 0px #000';
            });
            dayCell.addEventListener('mouseleave', () => {
                dayCell.style.transform = '';
                dayCell.style.boxShadow = '3px 3px 0px #000';
            });
            
            dayCell.addEventListener('click', () => showDayAppointments(day, month, year, dayAppointments, dayRegs));
            
            // Day number
            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.style.cssText = 'font-weight: 900; font-size: 1.1rem;';
            dayNumber.textContent = day;
            dayCell.appendChild(dayNumber);
            
            // Appointment indicators
            if (dayAppointments.length > 0) {
                const dotsContainer = document.createElement('div');
                dotsContainer.className = 'calendar-dots';
                
                if (window.innerWidth <= 768) {
                    // Just dots on mobile
                    dayAppointments.forEach(appt => {
                        const dot = document.createElement('div');
                        dot.className = 'calendar-dot';
                        dot.style.backgroundColor = (appt.estado === 'confirmado' ? 'var(--color-verde)' : 'var(--color-amarillo)');
                        dotsContainer.appendChild(dot);
                    });
                } else {
                    // Text list on desktop
                    dayAppointments.slice(0, 3).forEach(appt => {
                        const indicator = document.createElement('div');
                        indicator.style.cssText = 'font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background: #eee; padding: 2px; border-radius: 3px; border: 1px solid #000;';
                        indicator.textContent = appt.paciente.split(' ')[0];
                        dotsContainer.appendChild(indicator);
                    });
                    if (dayAppointments.length > 3) {
                        const more = document.createElement('div');
                        more.style.cssText = 'font-size: 0.6rem; font-weight: bold; padding-left: 2px;';
                        more.textContent = `+${dayAppointments.length - 3} más`;
                        dotsContainer.appendChild(more);
                    }
                }
                dayCell.appendChild(dotsContainer);
            }

            // Registration indicators (special dots)
            if (dayRegs.length > 0) {
                const regCircle = document.createElement('div');
                regCircle.style.cssText = 'width: 10px; height: 10px; background: #ff85b6; border: 1px solid #000; border-radius: 50%; position: absolute; top: 4px; right: 4px;';
                regCircle.title = 'Nuevo paciente registrado';
                dayCell.style.position = 'relative';
                dayCell.appendChild(regCircle);
            }
            
            grid.appendChild(dayCell);
        }
    }
    
    function showDayAppointments(day, month, year, dayAppointments, dayRegs = []) {
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const dayOfWeek = new Date(year, month, day).getDay();
        
        document.getElementById('selectedDayTitle').innerHTML = 
            `<i class="fa-solid fa-calendar-day"></i> ${dayNames[dayOfWeek]} ${day} de ${monthNames[month]}`;
        
        const content = document.getElementById('selectedDayContent');
        content.innerHTML = '';
        
        // Show registrations first
        if (dayRegs.length > 0) {
            const regsDiv = document.createElement('div');
            regsDiv.style.cssText = 'margin-bottom: 2rem; border: 2px solid #000; background: #e0faff; padding: 1rem; border-radius: 10px; box-shadow: 3px 3px 0 #000;';
            regsDiv.innerHTML = `<h5 style="margin:0 0 0.5rem 0;"><i class="fa-solid fa-user-plus"></i> Nuevos Pacientes (Registros):</h5>`;
            dayRegs.forEach(reg => {
                const time = new Date(reg.created_at).toLocaleTimeString('es-AR', {hour:'2-digit', minute:'2-digit'});
                regsDiv.innerHTML += `<div style="font-size: 0.9rem; font-weight: 700;">- ${reg.nombre} (${time} hs)</div>`;
            });
            content.appendChild(regsDiv);
        }

        if (dayAppointments.length === 0 && dayRegs.length === 0) {
            content.innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #666;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;"></i>
                    <p style="font-weight: 700; font-size: 1.1rem;">No hay actividad registrada para este día.</p>
                </div>
            `;
        } else {
            dayAppointments.sort((a, b) => a.fecha_hora.localeCompare(b.fecha_hora));
            
            dayAppointments.forEach(appt => {
                const apptTime = new Date(appt.fecha_hora);
                const hours = String(apptTime.getHours()).padStart(2, '0');
                const minutes = String(apptTime.getMinutes()).padStart(2, '0');
                
                const apptDiv = document.createElement('div');
                apptDiv.style.cssText = `
                    display: grid;
                    grid-template-columns: 120px 1fr auto;
                    gap: 1.5rem;
                    padding: 1.5rem 0;
                    border-bottom: 2px dashed #ddd;
                    align-items: center;
                `;
                
                apptDiv.innerHTML = `
                    <div style="text-align: center;">
                        <div style="background: var(--color-amarillo); border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 0.8rem; border-radius: 10px;">
                            <div style="font-size: 1.8rem; font-weight: 900; line-height: 1;">${hours}:${minutes}</div>
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 0.3rem;">hs</div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 900; font-family: 'Courier New', monospace; text-transform: uppercase; letter-spacing: -0.5px;">${appt.paciente}</h4>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                            <span class="status-badge" style="background: ${appt.estado == 'confirmado' ? 'var(--color-verde)' : (appt.estado == 'pendiente' ? 'var(--color-amarillo)' : '#ff85b6')}; padding: 0.3rem 0.6rem; border-radius: 5px; font-size: 0.85rem; font-weight: bold; border: 2px solid #000;">
                                ${appt.estado.charAt(0).toUpperCase() + appt.estado.slice(1)}
                            </span>
                            <span style="font-size: 0.9rem; color: #666;">
                                <i class="fa-solid fa-${appt.modalidad == 'virtual' ? 'video' : 'user'}"></i> ${appt.modalidad.charAt(0).toUpperCase() + appt.modalidad.slice(1)}
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                        ${appt.estado == 'pendiente' ? `
                            <form action="/admin/appointments/${appt.id}/confirm" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="neobrutalist-btn bg-verde" style="width:100%; padding: 5px 10px; font-size: 0.75rem;">Aceptar</button>
                            </form>
                            <form action="/admin/appointments/${appt.id}/cancel" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="neobrutalist-btn bg-lila" style="width:100%; padding: 5px 10px; font-size: 0.75rem;" onclick="return confirm('¿Rechazar/Cancelar este turno?')">Rechazar</button>
                            </form>
                        ` : ''}
                        ${appt.estado == 'confirmado' ? `
                            <form action="/admin/appointments/${appt.id}/cancel" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="neobrutalist-btn bg-lila" style="width:100%; padding: 5px 10px; font-size: 0.75rem;" onclick="return confirm('¿Cancelar turno?')">Cancelar</button>
                            </form>
                        ` : ''}
                    </div>
                `;
                
                content.appendChild(apptDiv);
            });
        }
        
        document.getElementById('selectedDayAppointments').style.display = 'block';
        document.getElementById('selectedDayAppointments').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
        document.getElementById('selectedDayAppointments').style.display = 'none';
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
        document.getElementById('selectedDayAppointments').style.display = 'none';
    });
    
    // Initial render
    renderCalendar();
</script>

    <!-- Validaciones de Pago (Moved slightly to organize) -->
    <div id="pagos" class="neobrutalist-card" style="background: white; margin-bottom: 4rem;"> <!-- Changed from rosa/indigo to White -->
        <h3><i class="fa-solid fa-money-bill-transfer"></i> Validaciones de Pago Pendientes</h3>
        
        @php
            $pendingPayments = $appointments->filter(fn($a) => $a->payment && $a->payment->estado == 'pendiente');
        @endphp

        @if($pendingPayments->isEmpty())
            <p>No hay pagos nuevos para revisar.</p>
        @else
            <!-- Responsive Container: Horizontal on desktop, Vertical on mobile -->
            <div style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 1.5rem; padding-top: 0.5rem; flex-wrap: wrap;">
                @foreach($pendingPayments as $appt)
                    <div style="min-width: 280px; flex: 1 1 300px; background: white; border: 3px solid #000; padding: 1.2rem; box-shadow: 4px 4px 0px #000; border-radius: 12px; margin-bottom: 1rem;">
                        <p style="margin-bottom: 0.5rem; font-weight: 700; font-size: 1.1rem; border-bottom: 2px dashed #ccc; padding-bottom: 0.5rem;">
                            {{ $appt->user->nombre }}
                        </p>
                        <p style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                            <i class="fa-regular fa-calendar"></i> {{ $appt->fecha_hora->format('d/m H:i') }} hs
                        </p>
                        
                        <!-- Actions -->
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                            @php
                                $ext = pathinfo($appt->payment->comprobante_ruta, PATHINFO_EXTENSION);
                            @endphp
                            <button type="button" class="neobrutalist-btn w-full no-select" style="background: var(--color-celeste); font-size: 0.8rem; padding: 8px;" 
                                    onclick="openProofModal('{{ route('payments.showProof', $appt->payment->id) }}', '{{ $appt->user->nombre }}', '{{ $appt->payment->created_at->format('d/m H:i') }}', '{{ $ext }}')">
                                <i class="fa-solid fa-image"></i> Ver Comprobante
                            </button>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <form id="verify-payment-{{ $appt->payment->id }}" action="{{ route('admin.payments.verify', $appt->payment->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-verde w-full no-select" style="padding: 10px; font-size: 0.8rem;" 
                                            onclick="confirmAction('verify-payment-{{ $appt->payment->id }}', '¿Confirmás que el pago es válido?')">
                                        <i class="fa-solid fa-check"></i> Validar
                                    </button>
                                </form>
                                <form id="reject-payment-{{ $appt->payment->id }}" action="{{ route('admin.payments.reject', $appt->payment->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-lila w-full no-select" style="padding: 10px; font-size: 0.8rem;" 
                                            onclick="confirmAction('reject-payment-{{ $appt->payment->id }}', '¿Rechazar este comprobante?')">
                                        <i class="fa-solid fa-times"></i> Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Scroll Hint -->
            <p style="font-size: 0.8rem; color: #666; text-align: center; margin-top: 0.5rem;">
                <i class="fa-solid fa-arrows-left-right"></i> Deslizá para ver más pagos pendientes
            </p>
        @endif
    </div>

    <!-- Agenda Completa -->
    <div id="turnos" class="neobrutalist-card" style="margin-bottom: 4rem;">
        <div style="background: white; border: 3px solid #000; padding: 1rem; margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; box-shadow: 4px 4px 0px #000;">
            <div style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" id="turnoSearch" placeholder="Buscar por paciente..." class="neobrutalist-input" style="margin:0;">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <select id="turnoFilter" class="neobrutalist-input" style="margin:0;">
                    <option value="todos">Todos los estados</option>
                    <option value="pendiente">Pendientes</option>
                    <option value="confirmado">Confirmados</option>
                    <option value="cancelado">Cancelados</option>
                    <option value="frecuente">Pacientes Frecuentes</option>
                    <option value="nuevo">Pacientes Nuevos</option>
                </select>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                <thead>
                    <tr style="border-bottom: 4px solid #000; background: #f0f0f0;" class="no-select">
                        <th style="padding: 1rem; text-align: left; cursor: pointer;" onclick="sortTable(0)">Fecha <i class="fa-solid fa-sort"></i></th>
                        <th style="padding: 1rem; text-align: left; cursor: pointer;" onclick="sortTable(1)">Paciente <i class="fa-solid fa-sort"></i></th>
                        <th style="padding: 1rem; text-align: left; cursor: pointer;" onclick="sortTable(2)">Estado <i class="fa-solid fa-sort"></i></th>
                        <th style="padding: 1rem; text-align: left;">Tipo</th>
                        <th style="padding: 1rem; text-align: left;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="turnosTableBody">
                    @foreach($appointments as $appt)
                        <tr style="border-bottom: 2px solid #eee;" class="turno-row" data-paciente="{{ strtolower($appt->user->nombre) }}" data-estado="{{ $appt->estado }}" data-tipo="{{ $appt->user->tipo_paciente }}">
                            <td data-label="Fecha" style="padding: 1rem; font-weight: 700;">{{ $appt->fecha_hora->format('d/m H:i') }} hs</td>
                            <td data-label="Paciente" style="padding: 1rem;">{{ $appt->user->nombre }}</td>
                            <td data-label="Estado" style="padding: 1rem;">
                               <span class="no-select status-badge" style="font-weight: bold; background: {{ $appt->estado == 'confirmado' ? 'var(--color-verde)' : ($appt->estado == 'cancelado' ? 'var(--color-rosa)' : 'var(--color-amarillo)') }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px;">
                                   {{ ucfirst($appt->estado) }}
                               </span>
                            </td>
                            <td data-label="Tipo" style="padding: 1rem;">
                                <span class="no-select" style="font-weight: bold; background: {{ $appt->user->tipo_paciente == 'frecuente' ? 'var(--color-verde)' : 'var(--color-amarillo)' }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px; text-transform: uppercase; font-size: 0.85rem;">
                                    {{ $appt->user->tipo_paciente }}
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    @if($appt->estado == 'pendiente')
                                        <form id="confirm-all-{{ $appt->id }}" action="{{ route('admin.appointments.confirm', $appt->id) }}" method="POST">
                                            @csrf
                                            <button type="button" class="neobrutalist-btn no-select" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: var(--color-verde);" onclick="confirmAction('confirm-all-{{ $appt->id }}', '¿Confirmar este turno?')">Confirmar</button>
                                        </form>
                                    @endif
                                    @if($appt->estado != 'cancelado')
                                        <form id="cancel-all-{{ $appt->id }}" action="{{ route('admin.appointments.cancel', $appt->id) }}" method="POST">
                                            @csrf
                                            <button type="button" class="neobrutalist-btn no-select" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: var(--color-lila);" onclick="confirmAction('cancel-all-{{ $appt->id }}', '¿Cancelar turno?')">Cancelar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination Controls -->
        <div id="paginationControls" style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem; align-items: center;">
            <button onclick="changePage(-1)" class="neobrutalist-btn bg-amarillo" id="prevPageBtn" style="padding: 0.5rem 1rem;">Anterior</button>
            <span id="pageIndicator" style="font-weight: 900; font-family: monospace;">Página 1</span>
            <button onclick="changePage(1)" class="neobrutalist-btn bg-amarillo" id="nextPageBtn" style="padding: 0.5rem 1rem;">Siguiente</button>
        </div>
    </div>

    <!-- Gestión de Materiales (Biblioteca) -->
    <div id="materiales" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-folder-open"></i> Biblioteca de Materiales</h3>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; flex-wrap: wrap;">
            <!-- Form Upload -->
            <div style="background: #f9f9f9; padding: 1.5rem; border: 3px solid #000; border-radius: 10px;">
                <h4 style="margin-top:0;">Cargar nuevo material</h4>
                <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight:bold;">Título:</label>
                        <input type="text" name="title" class="neobrutalist-input" style="width:100%; border-width:2px;" required>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight:bold;">Para el paciente (Opcional):</label>
                        <select name="paciente_id" class="neobrutalist-input" style="width:100%; border-width:2px;">
                            <option value="">-- Todos (Global) --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight:bold;">Archivo:</label>
                        <input type="file" name="file" class="neobrutalist-input" style="width:100%; border-width:2px;" required>
                    </div>
                    <button type="submit" class="neobrutalist-btn bg-celeste" style="width:100%;">Subir Material</button>
                </form>
            </div>

            <!-- List Materials -->
            <div style="overflow-y: auto; max-height: 400px;">
                <h4 style="margin-top:0;">Archivos subidos</h4>
                <table style="width: 100%; border-collapse: collapse; background: white; border: 2px solid #000;">
                    <thead>
                        <tr style="background: #000; color: #fff; font-size: 0.8rem;">
                            <th style="padding: 0.5rem;">Título</th>
                            <th style="padding: 0.5rem;">Tipo</th>
                            <th style="padding: 0.5rem;">Para</th>
                            <th style="padding: 0.5rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="materialesTableBody">
                    @forelse($resources as $res)
                        <tr style="border-bottom: 2px solid #000;">
                            <td data-label="Título" style="padding: 0.8rem; font-weight: 700;">{{ $res->title }}</td>
                            <td data-label="Tipo" style="padding: 0.8rem; font-size: 0.8rem; color: #666; font-family: monospace;">{{ strtoupper($res->file_type) }}</td>
                            <td data-label="Para" style="padding: 0.8rem;">
                                @if($res->patient)
                                    <span style="background: var(--color-lila); padding: 2px 6px; border: 1px solid #000; border-radius: 4px; font-size: 0.75rem;">{{ $res->patient->nombre }}</span>
                                @else
                                    <span style="background: #eee; padding: 2px 6px; border: 1px solid #000; border-radius: 4px; font-size: 0.75rem;">Global</span>
                                @endif
                            </td>
                            <td style="padding: 0.8rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="{{ route('resources.download', $res->id) }}" class="neobrutalist-btn bg-celeste" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;"><i class="fa-solid fa-download"></i></a>
                                    <form action="{{ route('admin.resources.destroy', $res->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="return confirm('¿Borrar recurso?')"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">No hay archivos compartidos.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>



    <!-- Listado de Pacientes (Existing) -->
    <div id="pacientes" class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
        <h3><i class="fa-solid fa-users"></i> Listado de Pacientes Registrados</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px #000;">
                <thead>
                    <tr style="background: #000; color: #fff; border-bottom: 3px solid #fff;">
                        <th style="padding: 0.8rem; text-align: left;">Nombre</th>
                        <th style="padding: 0.8rem; text-align: left;">Email</th>
                        <th style="padding: 0.8rem; text-align: left;">Teléfono</th>
                        <th style="padding: 0.8rem; text-align: left;">Tipo</th>
                        <th style="padding: 0.8rem; text-align: center;">Turnos</th>
                        <th style="padding: 0.8rem; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr style="border-bottom: 2px solid #000; background: white; color: #000;">
                            <td data-label="Nombre" style="padding: 0.8rem; font-weight: 700;">{{ $patient->nombre }}</td>
                            <td data-label="Email" style="padding: 0.8rem;">{{ $patient->email }}</td>
                            <td data-label="Teléfono" style="padding: 0.8rem;">{{ $patient->telefono ?? '-' }}</td>
                            <td data-label="Tipo" style="padding: 0.8rem;">
                                <span class="no-select" style="font-weight: bold; background: {{ $patient->tipo_paciente == 'frecuente' ? 'var(--color-verde)' : 'var(--color-amarillo)' }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px; text-transform: uppercase; font-size: 0.85rem;">
                                    {{ ucfirst($patient->tipo_paciente) }}
                                </span>
                            </td>
                            <td data-label="Turnos" style="padding: 0.8rem; text-align: center;">{{ $patient->turnos_count ?? $patient->turnos()->count() }}</td>
                            <td style="padding: 0.8rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button class="neobrutalist-btn no-select bg-amarillo" style="padding: 0.2rem 0.6rem; font-size: 0.7rem;" 
                                            onclick="openManageModal('{{ $patient->id }}', '{{ $patient->nombre }}', '{{ $patient->email }}', '{{ $patient->telefono ?? 'No registrado' }}', '{{ $patient->tipo_paciente }}')">
                                        Gestionar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historial de Acciones (Activity Log) - Moved to bottom as requested -->
    <div id="historial" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Acciones</h3>
        </div>
        <div style="max-height: 300px; overflow-y: auto; border: 3px solid #000; border-radius: 10px;">
            <table style="width: 100%; border-collapse: collapse; background: white;">
                <tbody>
                    @foreach($activityLogs as $log)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td data-label="Fecha" style="padding: 0.8rem; font-size: 0.8rem; color: #666; width: 140px;">
                                {{ $log->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m H:i') }} hs
                            </td>
                            <td data-label="Acción" style="padding: 0.8rem; font-size: 0.9rem;">
                                <strong style="text-transform: uppercase; font-size: 0.75rem; background: #eee; padding: 0.1rem 0.3rem; border-radius: 3px; margin-right: 0.5rem;">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </strong>
                                {{ $log->description }}
                            </td>
                            <td style="padding: 0.8rem; text-align: right; width: 100px;">
                                @php
                                    $revertibleActions = ['pago_verificado', 'pago_rechazado', 'turno_cancelado', 'turno_confirmado'];
                                @endphp
                                @if(in_array($log->action, $revertibleActions))
                                    <button class="neobrutalist-btn" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; background: #eee;" onclick="openRevertModal({{ $log->id }}, '{{ str_replace('_', ' ', $log->action) }}')">
                                        Revertir
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div id="proofModal" class="modal-overlay no-select">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin:0;">Comprobante de Pago</h3>
            <button class="close-modal" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-image-col" style="background: #222; display: flex; align-items: center; justify-content: center; min-height: 300px; flex-direction: column;">
                <img id="modalImage" src="" alt="Comprobante" style="max-height: 70vh; max-width: 100%; object-fit: contain; border: 5px solid #fff; box-shadow: 10px 10px 0px #000; display: none;">
                <iframe id="modalPdf" src="" style="width: 100%; height: 70vh; border: 5px solid #fff; box-shadow: 10px 10px 0px #000; display: none;"></iframe>
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
                <div style="margin-top:auto;">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Patient Modal -->
<div id="manageModal" class="confirm-modal-overlay">
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
            <div style="margin-bottom: 1rem;">
                <h4 style="margin-bottom: 0.5rem; border-bottom: 2px solid #000; display: inline-block;">Datos de Contacto</h4>
                <div style="background: #f9f9f9; padding: 1rem; border: 2px solid #000; margin-bottom: 1rem;">
                    <p><strong>Email:</strong> <span id="manageEmail"></span></p>
                    <p><strong>Teléfono:</strong> <span id="managePhone"></span></p>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                    <a id="manageMailBtn" href="#" class="neobrutalist-btn text-center" style="background: var(--color-amarillo); font-size: 0.85rem;">
                        <i class="fa-solid fa-envelope"></i> Enviar Mail
                    </a>
                    <a id="manageWhatsAppBtn" href="#" target="_blank" class="neobrutalist-btn text-center" style="background: #25D366; color: white; border-color: #000; font-size: 0.85rem;">
                        <i class="fa-solid fa-phone"></i> Teléfono
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

<!-- Revert Action Modal -->
<div id="revertModal" class="confirm-modal-overlay" style="z-index: 10000; display: none;">
    <div class="confirm-modal" style="max-width: 400px;">
        <div class="confirm-modal-title">Revertir Acción</div>
        <div class="confirm-modal-message">
            <p id="revertDescription" style="font-weight: 700; margin-bottom: 1.5rem;"></p>
            <p style="font-size: 0.85rem; color: #d00; margin-bottom: 1rem; border-left: 3px solid #d00; padding-left: 0.5rem;">
                <strong>Atención:</strong> Esta acción deshará los cambios en los estados. Confirmá con tu contraseña administrativa.
            </p>
            <div class="mb-4">
                <input type="password" id="revertPassword" placeholder="Contraseña de Nazarena" class="neobrutalist-input w-full" style="border-width: 3px;">
            </div>
            <p id="revertError" style="color: red; font-size: 0.8rem; display: none; font-weight: 900;"></p>
        </div>
        <div class="confirm-modal-buttons">
            <button onclick="closeRevertModal()" class="neobrutalist-btn" style="background: white;">Cancelar</button>
            <button id="revertSubmitBtn" onclick="submitRevert()" class="neobrutalist-btn bg-amarillo">Confirmar Reverso</button>
        </div>
    </div>
</div>

<script>
    let currentPatientId = null;
    let currentPatientName = '';

    function closeManageModal() {
        document.getElementById('manageModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Unlock scroll
    }

    function openManageModal(id, name, email, phone, type) {
        currentPatientId = id;
        currentPatientName = name;
        
        document.getElementById('manageTitle').innerText = 'Gestionar: ' + name;
        document.getElementById('manageEmail').innerText = email;
        document.getElementById('managePhone').innerText = phone;
        document.getElementById('manageMailBtn').href = 'mailto: ' + email;
        
        // Update type buttons
        const btnNuevo = document.getElementById('btnTypeNuevo');
        const btnFrecuente = document.getElementById('btnTypeFrecuente');
        
        btnNuevo.style.background = (type === 'nuevo') ? 'var(--color-amarillo)' : 'white';
        btnNuevo.style.borderWidth = (type === 'nuevo') ? '4px' : '2px';
        
        btnFrecuente.style.background = (type === 'frecuente') ? 'var(--color-verde)' : 'white';
        btnFrecuente.style.borderWidth = (type === 'frecuente') ? '4px' : '2px';

        const wpBtn = document.getElementById('manageWhatsAppBtn');
        if (phone && phone !== 'No registrado') {
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            wpBtn.href = 'https://wa.me/' + cleanPhone;
            wpBtn.style.display = 'flex';
            wpBtn.style.alignItems = 'center';
            wpBtn.style.justifyContent = 'center';
        } else {
            wpBtn.style.display = 'none';
        }

        // Update forms actions
        document.getElementById('manage-delete-form').action = '/admin/patients/' + id;
        document.getElementById('manage-type-form').action = '/admin/patients/' + id + '/update-type';
        document.getElementById('manage-reminder-form').action = '/admin/patients/' + id + '/send-reminder';
        
        document.getElementById('manageModal').style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Lock scroll
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

    window.openProofModal = function(fileSrc, patientName, uploadDate, fileExtension) {
        console.log('Opening proof modal for:', fileSrc, 'Ext:', fileExtension);
        const modalImage = document.getElementById('modalImage');
        const modalPdf = document.getElementById('modalPdf');
        const modalError = document.getElementById('modalError');
        
        // Reset state
        if(modalImage) {
            modalImage.style.display = 'none';
            // Do not set src = '' as it can trigger onerror
            modalImage.onerror = function() {
                this.style.display = 'none';
                if(modalError) modalError.style.display = 'block';
            };
            modalImage.onload = function() {
                if(modalError) modalError.style.display = 'none';
            };
        }
        if(modalPdf) {
            modalPdf.style.display = 'none';
            modalPdf.src = '';
        }
        if(modalError) modalError.style.display = 'none';

        // Detect if it's a PDF using the explicitly passed extension
        const isPdf = fileExtension ? (fileExtension.toLowerCase() === 'pdf') : fileSrc.toLowerCase().includes('.pdf');
        
        if (isPdf) {
            if(modalPdf) {
                modalPdf.style.display = 'block';
                modalPdf.src = fileSrc;
            }
        } else {
            if(modalImage) {
                modalImage.style.display = 'block';
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
        
        // Clear sources to stop PDF loading
        if(modalImage) modalImage.src = '';
        if(modalPdf) modalPdf.src = '';
        if(modalError) modalError.style.display = 'none';
        
        document.getElementById('proofModal').classList.remove('active');
        document.body.style.overflow = 'auto'; // Restore scroll
    }

    // New Function for Date Jumper
    window.jumpToDate = function() {
        const month = document.getElementById('jumpMonth').value;
        const year = document.getElementById('jumpYear').value;
        
        currentDate.setMonth(parseInt(month));
        currentDate.setFullYear(parseInt(year));
        
        renderCalendar();
    }
    window.confirmAction = function(formId, message) {
        if (window.showConfirm) {
            window.showConfirm(message, function() {
                const form = document.getElementById(formId);
                if (form) form.submit();
                else console.error('Form not found:', formId);
            });
        } else {
            // Fallback if modal fails
            if (confirm(message)) {
                document.getElementById(formId).submit();
            }
        }
    }

    // Unified Filtering, Sorting and Pagination logic
    let currentPage = 1;
    const rowsPerPage = 10;
    const allRowsArr = Array.from(document.querySelectorAll('.turno-row'));

    function applyTableState() {
        const searchTerm = document.getElementById('turnoSearch').value.toLowerCase();
        const statusFilter = document.getElementById('turnoFilter').value;

        // 1. Filter
        const filteredRows = allRowsArr.filter(row => {
            const paciente = row.dataset.paciente;
            const estado = row.dataset.estado;
            const tipo = row.dataset.tipo;
            const matchesSearch = paciente.includes(searchTerm);
            let matchesStatus = statusFilter === 'todos' || estado === statusFilter;
            if (statusFilter === 'frecuente' || statusFilter === 'nuevo') matchesStatus = tipo === statusFilter;
            
            return matchesSearch && matchesStatus;
        });

        // 2. Pagination Calculation
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // 3. Render
        allRowsArr.forEach(r => r.style.display = 'none');
        
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        filteredRows.slice(start, end).forEach(r => {
            // Check if mobile (stacked) or desktop (table-row)
            r.style.display = (window.innerWidth <= 768) ? 'block' : 'table-row';
        });

        // 4. Update UI
        const pageIndicator = document.getElementById('pageIndicator');
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        if (pageIndicator) pageIndicator.innerText = `Página ${currentPage} de ${totalPages}`;
        if (prevBtn) prevBtn.disabled = (currentPage === 1);
        if (nextBtn) nextBtn.disabled = (currentPage === totalPages);
    }

    function changePage(dir) {
        currentPage += dir;
        applyTableState();
    }

    document.getElementById('turnoSearch').addEventListener('input', () => {
        currentPage = 1;
        applyTableState();
    });
    document.getElementById('turnoFilter').addEventListener('change', () => {
        currentPage = 1;
        applyTableState();
    });

    // Sorting logic integrated with state
    let sortDirections = [true, true, true];
    window.sortTable = function(columnIndex) {
        const tableBody = document.getElementById('turnosTableBody');
        const direction = sortDirections[columnIndex] ? 1 : -1;

        allRowsArr.sort((a, b) => {
            const aText = a.children[columnIndex].innerText.trim();
            const bText = b.children[columnIndex].innerText.trim();
            return aText.localeCompare(bText, undefined, { numeric: true, sensitivity: 'base' }) * direction;
        });

        sortDirections[columnIndex] = !sortDirections[columnIndex];
        tableBody.innerHTML = '';
        allRowsArr.forEach(row => tableBody.appendChild(row));
        
        applyTableState();
    }

    // Initial state
    window.addEventListener('resize', applyTableState);
    document.addEventListener('DOMContentLoaded', applyTableState);

    // Date Jumper
    window.jumpToDate = function() {
        const month = document.getElementById('jumpMonth').value;
        const year = document.getElementById('jumpYear').value;
        currentDate.setMonth(parseInt(month));
        currentDate.setFullYear(parseInt(year));
        renderCalendar();
    }


    // Close on click outside
    document.getElementById('proofModal').addEventListener('click', function(e) {
        if (e.target === this) closeProofModal();
    });
    document.getElementById('manageModal').addEventListener('click', function(e) {
        if (e.target === this) closeManageModal();
    });
    // Revert logic
    let pendingRevertId = null;
    window.openRevertModal = function(id, actionName) {
        pendingRevertId = id;
        document.getElementById('revertDescription').innerText = '¿Estás segura de revertir "' + actionName.toUpperCase() + '"?';
        document.getElementById('revertPassword').value = '';
        document.getElementById('revertError').style.display = 'none';
        document.getElementById('revertModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    window.closeRevertModal = function() {
        document.getElementById('revertModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.submitRevert = async function() {
        const password = document.getElementById('revertPassword').value;
        const btn = document.getElementById('revertSubmitBtn');
        const err = document.getElementById('revertError');

        if (!password) {
            err.innerText = 'Por favor ingresá tu contraseña.';
            err.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Procesando...';
        err.style.display = 'none';

        try {
            const response = await fetch(`/admin/activity-logs/${pendingRevertId}/revert`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password })
            });

            const data = await response.json();

            if (response.ok) {
                location.reload(); // Simplest way to see changes
            } else {
                err.innerText = data.error || 'Error desconocido.';
                err.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Confirmar Reverso';
            }
        } catch (e) {
            err.innerText = 'Error de conexión.';
            err.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Confirmar Reverso';
        }
    }
</script>
@endsection
