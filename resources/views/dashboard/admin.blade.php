@extends('layouts.app')

@section('title', 'Admin Dashboard - Lic. Nazarena De Luca')


@section('content')
<div class="admin-content-wrapper" style="padding: 2rem; max-width: 1400px; margin: 0 auto; margin-bottom: 40px;">
    
    <!-- Welcome Text -->
    <div style="margin-bottom: 2rem;">
        <h1 id="bienvenida-text" class="welcome-text" style="font-weight: 700; color: #000; font-family: 'Syne', sans-serif; letter-spacing: -0.5px; text-shadow: 2px 2px 0px rgba(0,0,0,0.1); margin-top: 0;">
            ¡Buen día&nbsp;☀️, {{ auth()->user()->nombre }}! Hoy alguien va a sentirse comprendido gracias a vos.&nbsp;🌟
        </h1>
        <style>
            .welcome-text {
                font-size: 1.8rem;
            }
            @media (max-width: 768px) {
                .welcome-text {
                    font-size: 1.2rem !important; /* Smaller as requested */
                    line-height: 1.3 !important;
                    margin-bottom: 1rem !important;
                    margin-top: 0 !important;
                }
                /* Target the parent container to reduce padding on mobile */
                .admin-content-wrapper {
                    padding: 1rem !important;
                }
            }
        </style>
    </div>
    
    <!-- Finance Stats Grid -->
    <!-- Finance Stats Grid -->
    <div class="stats-grid">
            
        <a href="{{ route('admin.finanzas') }}" class="stat-card card-verde">
            <div class="card-header">
                <div>
                    <p class="card-label">Ingresos Mes</p>
                    <h2 class="card-value">{{ $monthlyIncome ?? 0 }}</h2>
                </div>
                <div class="card-icon-circle">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="card-footer">
                Ver finanzas <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('admin.finanzas') }}#honorarios" class="stat-card card-rosa">
            <div class="card-header">
                <div>
                    <p class="card-label">Por Cobrar</p>
                    <h2 class="card-value">{{ $pendingIncome }}</h2>
                </div>
                <div class="card-icon-circle">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="card-footer">
                Gestionar cobros <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('admin.pacientes') }}" class="stat-card card-amarillo">
            <div class="card-header">
                <div>
                    <p class="card-label">Pacientes Nuevos</p>
                    <h2 class="card-value">{{ $newPatientsCount ?? 0 }}</h2>
                </div>
                <div class="card-icon-circle">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
            </div>
            <div class="card-footer">
                Ver pacientes <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('admin.agenda') }}" class="stat-card card-celeste">
            <div class="card-header">
                <div>
                    <p class="card-label">Sesiones</p>
                    <h2 class="card-value">{{ $sessionsCount ?? 0 }}</h2>
                </div>
                <div class="card-icon-circle">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="card-footer">
                Ver agenda <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <style>
        .stats-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 2rem; 
            margin-bottom: 3rem;
        }
        .stat-card {
            text-decoration: none; 
            color: inherit; 
            display: block; 
            border: 3px solid rgb(0, 0, 0); 
            border-radius: 12px; 
            padding: 2rem; 
            box-shadow: rgba(0, 0, 0, 0.1) 8px 8px 0px; 
            transition: transform 0.2s ease 0s, box-shadow 0.2s ease 0s; 
            transform: none;
        }
        .stat-card:hover {
            transform: translate(-2px,-2px);
            box-shadow: 10px 10px 0px rgba(0,0,0,0.1);
        }
        .card-verde { background: var(--color-verde); }
        .card-rosa { background: var(--color-rosa); }
        .card-amarillo { background: var(--color-amarillo); }
        .card-celeste { background: var(--color-celeste); }

        .card-header {
            display: flex; 
            justify-content: space-between; 
            align-items: start; 
            margin-bottom: 1.5rem;
        }
        .card-label {
            margin: 0; 
            color: #666; 
            font-size: 0.9rem; 
            font-weight: 600; 
            text-transform: uppercase;
        }
        .card-value {
            margin: 0.5rem 0 0 0; 
            font-size: 2rem; 
            font-weight: 900; 
            color: #000; 
            font-family: 'Inter', 'Manrope', monospace; 
            letter-spacing: -1px;
        }
        .card-icon-circle {
            background: white; 
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            border: 2px solid #000; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .card-icon-circle i {
            font-size: 1.2rem; 
            color: #000;
        }
        .card-footer {
            color: #000; 
            font-weight: 700; 
            font-size: 0.9rem; 
            display: inline-flex; 
            align-items: center; 
            gap: 0.5rem;
        }
    </style>

    @if(isset($todayAppointments) && $todayAppointments->count() == 0)
        <!-- Empty State for Today -->
        <div style="background: white; border: 3px solid #000; border-radius: 12px; padding: 3rem 2rem; text-align: center; margin-bottom: 2rem; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div style="background: #e6fffa; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 2px solid #000;">
                <i class="fa-solid fa-check" style="font-size: 2.5rem; color: #000;"></i>
            </div>
            <h3 style="margin: 0 0 0.5rem 0; color: #000; font-size: 1.2rem; font-weight: 700; font-family: 'Syne', sans-serif;">Todo al día</h3>
            <p style="margin: 0; color: #666; font-size: 0.95rem; font-weight: 500;">No hay turnos programados para hoy.</p>
        </div>
    @endif
</div>
<div class="flex flex-col gap-8">
    
    <!-- Admin Next Session Widget -->
    @if(isset($nextAdminAppointment))
        <div class="neobrutalist-card" style="margin-bottom: 2rem; background: var(--color-verde); border-color: #000;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.3rem;"><i class="fa-solid fa-bell"></i> Tu próxima sesión comienza pronto</h3>
                    <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem; font-weight: 800;">
                        {{ $nextAdminAppointment->user->nombre }} {{ $nextAdminAppointment->user->apellido }} - {{ $nextAdminAppointment->fecha_hora->locale('es')->isoFormat('dddd D [de] MMMM') }} a las {{ $nextAdminAppointment->fecha_hora->format('H:i') }} hs
                    </p>
                </div>
                <div>
                    <a href="https://meet.google.com/landing" target="_blank" class="neobrutalist-btn bg-amarillo" style="text-decoration: none; display: inline-block; box-shadow: 4px 4px 0px #000; padding: 0.8rem 1.5rem; font-weight: 900;">
                        <i class="fa-solid fa-video"></i> ABRIR MEET
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Turnos para el Día de Hoy -->
    <div id="hoy" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clipboard-list"></i> Turnos para hoy</h3>
            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                <p style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #555;">({{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM YYYY') }})</p>
                <a href="{{ route('admin.turnos') }}" style="font-size: 0.85rem; font-weight: 800; color: #000; text-decoration: none; margin-top: 5px; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform='translateX(0)'">
                    Ver todos los turnos <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
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

    <!-- Configuración de Horarios de Atención -->
    <div id="configuracion" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 id="disponibilidad" style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clock"></i> Horarios de Atención</h3>
                <a href="{{ route('admin.configuracion') }}" style="font-size: 0.85rem; font-weight: 800; color: #000; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform='translateX(0)'">
                    Configuración avanzada <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #555;">
                <strong>Modo Google Calendar:</strong> Si creás eventos en tu Google Calendar que digan <strong>"LIBRE"</strong> o <strong>"DISPONIBLE"</strong>, esos serán los únicos que verán los pacientes ese día. 
                Sino, se usarán estos horarios base como alternativa.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem;">
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
            
            <!-- SECCIÓN 1: ACCIONES (Bloquear/Desbloquear) -->
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
                <form id="toggle-weekends-form" action="{{ route('admin.calendar.toggle-weekends') }}" method="POST">
                    @csrf
                    <button type="button" class="neobrutalist-btn w-full" style="background: {{ $blockWeekends ? '#d32f2f' : '#388e3c' }}; color: white; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.9rem; padding: 0.8rem;" onclick="confirmAction('toggle-weekends-form', '¿Alternar bloqueo de fines de semana?')">
                        @if($blockWeekends)
                            <i class="fa-solid fa-lock"></i> Sáb/Dom: <strong>BLOQUEADOS</strong>
                        @else
                            <i class="fa-solid fa-unlock"></i> Sáb/Dom: <strong>LIBRES</strong>
                        @endif
                    </button>
                    <p style="font-size: 0.75rem; color: #555; margin-top: 0.5rem; text-align: center;">Controla la disponibilidad automática de los fines de semana.</p>
                </form>
            </div>

            <!-- SECCIÓN 2: VISUALIZACIÓN (Listas) -->
            <div style="background: white; border: 3px solid #000; padding: 1.5rem; border-radius: 15px; box-shadow: 4px 4px 0px #000; display: flex; flex-col; gap: 2rem;">
                
                <!-- Listado Manuales -->
                <div>
                    <h5 style="margin: 0 0 1rem 0; font-weight: 800; font-size: 1.1rem; border-bottom: 2px solid #000; padding-bottom: 0.5rem;">
                        <i class="fa-solid fa-hand-paper"></i> Bloqueos Manuales
                    </h5>
                    <div style="max-height: 200px; overflow-y: auto; background: #fafafa; border: 2px solid #000; padding: 0.5rem; border-radius: 8px;">
                         @forelse($blockedDays->where('reason', '!=', 'Feriado Oficial') as $bd)
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
                        <span style="font-size: 0.7rem; background: #eee; padding: 2px 6px; border-radius: 4px; border: 1px solid #999;">Auto-Sync ON</span>
                    </h5>
                    <!-- Se eliminó el botón manual de Sincronizar -->
                    
                    <div style="max-height: 200px; overflow-y: auto; background: #fafafa; border: 2px solid #000; padding: 0.5rem; border-radius: 8px;">
                        @php
                            // Filtrar feriados de la lista general de blockedDays si es posible, o asumir que todo lo que no es manual es feriado?
                            // En CalendarController se guardan como BlockedDay con reason fija.
                            // Vamos a mostrar los que vengan de la DB que parezcan feriados (o simplemente listarlos todos si no hay distinción clara en el objeto, pero el usuario pidió secciones separadas).
                            // Una heurística simple: si la razón es larga o coincide con fechas patrias. O simplemente mostrar todos.
                            // El usuario pidió "dos secciones", asumo que quiere distinguir los que él puso vs los automáticos.
                            // Como no hay campo 'type', usaremos la lógica inversa: Mostrar todo lo que NO mostramos arriba? O simplemente una lista general?
                            // El user snippet mostraba "Feriados Argentina" en una col y Manuales en otra.
                            // Asumiré que los manuales son los ingresados por form. Los feriados vienen del controller.
                            // Para no complicar con lógica de backend no disponible, mostraré una lista general con iconos distintos si es posible, 
                            // O mejor: Listar los 'futuros' bloqueados.
                        @endphp
                         @forelse($blockedDays as $bd)
                            @if(in_array($bd->reason, ['Año Nuevo', 'Carnaval', 'Viernes Santo', 'Navidad']) || str_contains($bd->reason, 'Día') || str_contains($bd->reason, 'Paso a la Inmortalidad'))
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #ccc; padding: 0.5rem; font-size: 0.85rem;">
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($bd->date)->format('d/m/Y') }}</strong>
                                    <span style="display:block; font-size: 0.75rem; color: #666;">{{ $bd->reason }}</span>
                                </div>
                                <span style="font-size: 1.5rem; color: var(--color-verde);"><i class="fa-solid fa-circle-check"></i></span>
                            </div>
                            @endif
                        @empty
                            <p style="text-align: center; font-size: 0.8rem; color: #777; margin: 0.5rem 0;">No hay feriados sincronizados.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Agenda Mensual -->
    <div id="agenda" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-calendar-days"></i> Agenda Mensual</h3>
            <a href="{{ route('admin.agenda') }}" style="font-size: 0.85rem; font-weight: 800; color: #000; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(5px)'" onmouseout="this.style.transform='translateX(0)'">
                Ver agenda completa <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- Google Calendar Sync (NEW) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <!-- EXPORT: App -> Google -->
            <div style="background: #e3f2fd; border: 3px solid #000; border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 4px 4px 0px #000;">
                <div>
                    <h4 style="margin: 0; font-size: 1.1rem; color: #1565c0; font-weight: 800;"><i class="fa-solid fa-cloud-arrow-up"></i> Exportar a Google Calendar</h4>
                    <p style="margin: 0.5rem 0 1rem 0; font-size: 0.85rem; color: #555;">Visualizá tus turnos confirmados en tu calendario personal.</p>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    @if(auth()->user()->ical_token)
                        <div style="background: white; border: 2px solid #000; padding: 0.6rem; border-radius: 8px; font-family: monospace; font-size: 0.75rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <span id="ical-url" style="color: #333; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 180px;">{{ route('agenda.feed', auth()->user()->ical_token) }}</span>
                            <button onclick="copyIcalUrl(this)" class="neobrutalist-btn bg-amarillo" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; box-shadow: 2px 2px 0px #000;">Copiar</button>
                        </div>
                    @else
                        <form id="generate-token-form" action="{{ route('admin.calendar.generateToken') }}" method="POST">
                            @csrf
                            <button type="button" class="neobrutalist-btn bg-celeste w-full" style="font-size: 0.85rem; padding: 0.6rem;" onclick="confirmAction('generate-token-form', '¿Generar nuevo token de calendario?')">Generar Link</button>
                        </form>
                    @endif
                    <a href="https://support.google.com/calendar/answer/37100?hl=es#zippy=%2Cusar-un-v%C3%ADnculo-para-agregar-el-calendario" target="_blank" style="font-size: 0.75rem; color: #1565c0; text-decoration: underline;">¿Cómo agregar este link a mi Google?</a>
                </div>
            </div>

            <!-- IMPORT: Google -> App -->
            <div style="background: #f0fdf4; border: 3px solid #000; border-radius: 12px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 4px 4px 0px #000;">
                <div>
                    <h4 style="margin: 0; font-size: 1.1rem; color: #166534; font-weight: 800;"><i class="fa-solid fa-cloud-arrow-down"></i> Bloquear desde Google Calendar</h4>
                    <p style="margin: 0.5rem 0 1rem 0; font-size: 0.85rem; color: #555;">Tus eventos de Google bloquearán automáticamente turnos en la web.</p>
                </div>

                <form id="update-google-url-form" action="{{ route('admin.calendar.google-url') }}" method="POST" style="margin-bottom: 0.5rem;">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="password" name="google_calendar_url" class="neobrutalist-input w-full" 
                               value="{{ auth()->user()->profesional->google_calendar_url ?? '' }}"
                               placeholder="Pegá aquí la Dirección secreta (URL iCal)..." 
                               style="font-size: 0.75rem; height: 38px; margin:0;"
                               autocomplete="off"
                               onmouseover="this.type='text'" onmouseout="this.type='password'">
                        <button type="button" class="neobrutalist-btn bg-verde" style="font-size: 0.8rem; padding: 0 1rem; flex-shrink: 0;" onclick="confirmAction('update-google-url-form', '¿Guardar dirección de Google Calendar?')">Guardar</button>
                    </div>
                    @error('google_calendar_url')
                        <p style="color:red; font-size: 0.7rem; margin-top: 0.3rem;">{{ $message }}</p>
                    @enderror
                </form>

                <div style="margin-top: 0.5rem; background: #fffadc; border: 1px dashed #eab308; padding: 0.6rem; border-radius: 8px;">
                    <p style="font-size: 0.7rem; color: #854d0e; margin: 0; line-height: 1.3;">
                        <i class="fa-solid fa-lock"></i> <strong>Privacidad:</strong> Este link es privado y solo se usa para sincronizar tus horarios. 
                        El sistema lo procesa de forma interna y no es visible para nadie más.
                    </p>
                    <p style="font-size: 0.7rem; color: #166534; margin: 0.4rem 0 0 0; font-weight: 700;">
                        <i class="fa-solid fa-check-circle"></i> Sincronización automática activada.
                    </p>
                </div>
            </div>
        </div>

        <script>
            function copyIcalUrl(btn) {
                const url = document.getElementById('ical-url').innerText;
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = btn.innerText;
                    btn.innerText = '¡Copiado!';
                    btn.classList.add('bg-verde');
                    btn.classList.remove('bg-amarillo');
                    
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.classList.add('bg-amarillo');
                        btn.classList.remove('bg-verde');
                    }, 2000);
                }).catch(err => {
                    console.error('Error al copiar: ', err);
                });
            }
        </script>

        <!-- Calendar Navigation & Jumper -->
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem; justify-content: center; background: #f0f0f0; padding: 1rem; border: 3px solid #000; border-radius: 10px;">
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button id="prevMonth" class="neobrutalist-btn bg-amarillo" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-chevron-left"></i></button>
                <h2 id="currentMonthYear" style="margin: 0; min-width: 180px; text-align: center; font-size: 1.5rem; font-weight: 900; font-family: 'Courier New', Courier, monospace; text-transform: uppercase; letter-spacing: -1px;"></h2>
                <button id="nextMonth" class="neobrutalist-btn bg-amarillo" style="padding: 0.5rem 1rem;"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <!-- Custom Upward Dropdown -->
                <div class="custom-dropdown" style="position: relative; min-width: 140px; z-index: 1000;">
                    <button id="jumpMonthTrigger" class="neobrutalist-input" style="width: 100%; height: 40px; margin:0; padding: 0 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: white; position: relative; z-index: 1001;">
                        <span id="jumpMonthText">Enero</span>
                        <i class="fa-solid fa-chevron-up"></i>
                    </button>
                    <!-- bottom: 100% ensures it opens UPWARDS -->
                    <div id="jumpMonthOptions" style="display: none; position: absolute; bottom: 100%; left: 0; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 3px solid #000; border-radius: 10px 10px 0 0; box-shadow: 0 -4px 10px rgba(0,0,0,0.1); z-index: 1002;">
                        @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $key => $mes)
                            <div class="custom-option" data-value="{{ $key }}" style="padding: 0.8rem; border-bottom: 2px solid #eee; cursor: pointer; font-weight: 700; font-size: 0.9rem;">{{ $mes }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" id="jumpMonth" value="0">
                
                <!-- Year Selector Upward Dropdown -->
                <div class="custom-dropdown" style="position: relative; min-width: 100px; z-index: 1000;">
                    <button id="jumpYearTrigger" class="neobrutalist-input" style="width: 100%; height: 40px; margin:0; padding: 0 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: white; position: relative; z-index: 1001;">
                        <span id="jumpYearText">2026</span>
                        <i class="fa-solid fa-chevron-up"></i>
                    </button>
                    <div id="jumpYearOptions" style="display: none; position: absolute; bottom: 100%; left: 0; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 3px solid #000; border-radius: 10px 10px 0 0; box-shadow: 0 -4px 10px rgba(0,0,0,0.1); z-index: 1002;">
                        @for($y = 2026; $y <= 2030; $y++)
                            <div class="custom-option-year" data-value="{{ $y }}" style="padding: 0.8rem; border-bottom: 2px solid #eee; cursor: pointer; font-weight: 700; font-size: 0.9rem;">{{ $y }}</div>
                        @endfor
                    </div>
                </div>
                <!-- 'Ir' button removed as per user request -->
                <input type="hidden" id="jumpYear" value="2026">
                
                <button onclick="resetCalendar()" class="neobrutalist-btn bg-celeste" style="height: 40px; padding: 0 1rem; font-size: 0.9rem;" title="Volver al mes actual">
                    <i class="fa-solid fa-rotate-left"></i> Volver al actual
                </button>
            </div>
        </div>

        <style>
            /* Custom Scrollbar */
            #jumpMonthOptions::-webkit-scrollbar { width: 8px; }
            #jumpMonthOptions::-webkit-scrollbar-track { background: #f1f1f1; }
            #jumpMonthOptions::-webkit-scrollbar-thumb { background: #000; }
            #jumpMonthOptions::-webkit-scrollbar-thumb:hover { background: #333; }
            
            .custom-option:hover { background: var(--color-amarillo); }
            
            /* No Spinners for Year Input */
            #jumpYear::-webkit-outer-spin-button,
            #jumpYear::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
            #jumpYear { -moz-appearance: textfield; }
        </style>
        
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
            'telefono' => $appt->user->telefono ?? null,
            'is_projected' => $appt->is_projected ?? false
        ];
    });
    
    $formattedExternal = $externalEvents->map(function($e) {
        return [
            'id' => $e->id,
            'title' => $e->title,
            'start_time' => $e->start_time->timezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i:s'),
            'end_time' => $e->end_time->timezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i:s'),
        ];
    });

    $formattedRegistrations = $recentRegistrations->map(function($reg) {
        return [
            'id' => $reg->id,
            'nombre' => $reg->nombre,
            'created_at' => $reg->created_at->format('Y-m-d H:i:s')
        ];
    });
@endphp
<script>
    // --- GLOBAL ADMIN FUNCTIONS (Defined safely at top) ---
    
    // 1. Confirm Modal Logic
    let pendingActionFormId = null;

    window.confirmAction = function(formId, message) {
        console.log('Action requested for:', formId); 
        
        const form = document.getElementById(formId);
        if (!form) {
            console.error('CRITICAL: Form not found:', formId);
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
            
            // Re-bind confirm button
            const btn = document.getElementById('actionConfirmBtn');
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', function() {
                if (pendingActionFormId) {
                    const f = document.getElementById(pendingActionFormId);
                    if(f) {
                        try {
                            if(typeof window.saveScrollPosition === 'function') window.saveScrollPosition();
                        } catch(e) { console.error('Scroll save error:', e); }
                        f.submit(); 
                    }
                }
                closeActionModal();
            });
        } else {
            // Fallback
            if(confirm(message)) form.submit();
        }
    };

    window.closeActionModal = function() {
        const modal = document.getElementById('actionConfirmModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        pendingActionFormId = null;
    };

    // Calendar functionality
    let currentDate = new Date();
    const appointments = @json($calendarAppointments);
    const registrations = @json($formattedRegistrations);
    const externalEvents = @json($formattedExternal);

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
        document.getElementById('jumpMonthText').innerText = monthNames[month];
        document.getElementById('jumpYear').value = year;
        document.getElementById('jumpYearText').innerText = year;
        
        // Navigation Logic: Check Jan 2026
        const prevBtn = document.getElementById('prevMonth');
        if (year === 2026 && month === 0) {
            prevBtn.disabled = true;
            prevBtn.style.opacity = '0.5';
            prevBtn.style.cursor = 'not-allowed';
        } else {
            prevBtn.disabled = false;
            prevBtn.style.opacity = '1';
            prevBtn.style.cursor = 'pointer';
        }

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

            const dayExternal = externalEvents.filter(ev => ev.start_time.startsWith(dateStr));

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
            
            dayCell.addEventListener('click', () => showDayAppointments(day, month, year, dayAppointments, dayRegs, dayExternal));
            
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
                        if (appt.is_projected) {
                            dot.style.cssText = 'width: 6px; height: 6px; border-radius: 50%; background-color: transparent; border: 1px dashed var(--color-verde); display: inline-block; margin: 0 1px;';
                        } else {
                            dot.style.backgroundColor = (appt.estado === 'confirmado' ? 'var(--color-verde)' : 'var(--color-amarillo)');
                        }
                        dotsContainer.appendChild(dot);
                    });
                } else {
                    // Text list on desktop
                    dayAppointments.slice(0, 3).forEach(appt => {
                        const indicator = document.createElement('div');
                        if (appt.is_projected) {
                            indicator.style.cssText = 'font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background: transparent; padding: 1px; border-radius: 3px; border: 1px dashed #000; color: #555; margin-bottom: 2px;';
                        } else {
                            indicator.style.cssText = 'font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background: #eee; padding: 2px; border-radius: 3px; border: 1px solid #000; margin-bottom: 2px;';
                        }
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
                regCircle.style.cssText = 'width: 8px; height: 8px; background: #ff85b6; border: 1px solid #000; border-radius: 50%; position: absolute; top: 4px; right: 4px;';
                regCircle.title = 'Nuevo paciente registrado';
                dayCell.style.position = 'relative';
                dayCell.appendChild(regCircle);
            }

            // Google indicators (special blue dots)
            if (dayExternal.length > 0) {
                const googleCircle = document.createElement('div');
                googleCircle.style.cssText = 'width: 8px; height: 8px; background: #4285F4; border: 1px solid #000; border-radius: 50%; position: absolute; top: 4px; left: 4px;';
                googleCircle.title = 'Evento de Google';
                dayCell.style.position = 'relative';
                dayCell.appendChild(googleCircle);
            }
            
            grid.appendChild(dayCell);
        }
    }
    
    function showDayAppointments(day, month, year, dayAppointments, dayRegs = [], dayExternal = []) {
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

        // Show External Events (Google)
        if (dayExternal.length > 0) {
            const extDiv = document.createElement('div');
            extDiv.style.cssText = 'margin-bottom: 2rem; border: 2px solid #000; background: #e8f0fe; padding: 1rem; border-radius: 10px; box-shadow: 3px 3px 0 #000;';
            extDiv.innerHTML = `<h5 style="margin:0 0 0.5rem 0; color: #1967d2;"><i class="fa-brands fa-google"></i> Eventos de Google (Bloqueados):</h5>`;
            dayExternal.forEach(ev => {
                const start = new Date(ev.start_time).toLocaleTimeString('es-AR', {hour:'2-digit', minute:'2-digit'});
                const end = new Date(ev.end_time).toLocaleTimeString('es-AR', {hour:'2-digit', minute:'2-digit'});
                extDiv.innerHTML += `<div style="font-size: 0.9rem; font-weight: 700;">- ${ev.title} (${start} - ${end} hs)</div>`;
            });
            content.appendChild(extDiv);
        }

        if (dayAppointments.length === 0 && dayRegs.length === 0 && dayExternal.length === 0) {
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
                    ${appt.is_projected ? 'opacity: 0.75;' : ''}
                `;
                
                let actionsHtml = '';
                if (!appt.is_projected) {
                    actionsHtml = `
                        <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                            ${appt.estado == 'pendiente' ? `
                                <form id="confirm-cal-${appt.id}" action="/admin/appointments/${appt.id}/confirm" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-verde" style="width:100%; padding: 5px 10px; font-size: 0.75rem;" onclick="confirmAction('confirm-cal-${appt.id}', '¿Aceptar este turno?')">Aceptar</button>
                                </form>
                                <form id="reject-cal-${appt.id}" action="/admin/appointments/${appt.id}/cancel" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-lila" style="width:100%; padding: 5px 10px; font-size: 0.75rem;" onclick="confirmAction('reject-cal-${appt.id}', '¿Rechazar/Cancelar este turno?')">Rechazar</button>
                                </form>
                            ` : ''}
                            ${appt.estado == 'confirmado' ? `
                                <form id="cancel-cal-${appt.id}" action="/admin/appointments/${appt.id}/cancel" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-lila" style="width:100%; padding: 5px 10px; font-size: 0.75rem;" onclick="confirmAction('cancel-cal-${appt.id}', '¿Cancelar este turno?')">Cancelar</button>
                                </form>
                            ` : ''}
                        </div>
                    `;
                } else {
                    actionsHtml = `<div style="text-align: right;"><span style="font-size: 0.8rem; font-weight: bold; color: #888; border: 1px dashed #888; padding: 4px 8px; border-radius: 4px; display: inline-block;">PROYECTADO</span></div>`;
                }

                apptDiv.innerHTML = `
                    <div style="text-align: center;">
                        <div style="${appt.is_projected ? 'background: #f5f5f5; border: 3px dashed #000; box-shadow: none;' : 'background: var(--color-amarillo); border: 3px solid #000; box-shadow: 4px 4px 0px #000;'} padding: 0.8rem; border-radius: 10px;">
                            <div style="font-size: 1.8rem; font-weight: 900; line-height: 1; ${appt.is_projected ? 'color: #555;' : ''}">${hours}:${minutes}</div>
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 0.3rem; ${appt.is_projected ? 'color: #555;' : ''}">hs</div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 1.3rem; font-weight: 900; font-family: 'Courier New', monospace; text-transform: uppercase; letter-spacing: -0.5px; ${appt.is_projected ? 'color: #555;' : ''}">${appt.paciente}</h4>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                            <span class="status-badge" style="background: ${appt.is_projected ? '#f5f5f5' : (appt.estado == 'confirmado' ? 'var(--color-verde)' : (appt.estado == 'pendiente' ? 'var(--color-amarillo)' : '#ff85b6'))}; padding: 0.3rem 0.6rem; border-radius: 5px; font-size: 0.85rem; font-weight: bold; border: ${appt.is_projected ? '2px dashed #888' : '2px solid #000'}; ${appt.is_projected ? 'color: #555;' : ''}">
                                ${appt.is_projected ? 'Reserva Fija' : appt.estado.charAt(0).toUpperCase() + appt.estado.slice(1)}
                            </span>
                            <span style="font-size: 0.9rem; color: #666;">
                                <i class="fa-solid fa-${appt.modalidad == 'virtual' ? 'video' : 'user'}"></i> ${appt.modalidad.charAt(0).toUpperCase() + appt.modalidad.slice(1)}
                            </span>
                        </div>
                    </div>
                    
                    ${actionsHtml}
                `;
                
                content.appendChild(apptDiv);
            });
        }
        
        document.getElementById('selectedDayAppointments').style.display = 'block';
        document.getElementById('selectedDayAppointments').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Calendar Jump Logic
    window.jumpToDate = function() {
        const m = parseInt(document.getElementById('jumpMonth').value);
        const y = parseInt(document.getElementById('jumpYear').value);
        currentDate = new Date(y, m, 1);
        renderCalendar();
        document.getElementById('selectedDayAppointments').style.display = 'none';
        
        // Ensure dropdowns are closed
        document.getElementById('jumpMonthOptions').style.display = 'none';
        document.getElementById('jumpYearOptions').style.display = 'none';
    };

    // Month Dropdown Handlers
    const monthTrigger = document.getElementById('jumpMonthTrigger');
    const monthOptions = document.getElementById('jumpMonthOptions');
    if (monthTrigger) {
        monthTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const yearOpts = document.getElementById('jumpYearOptions');
            if (yearOpts) yearOpts.style.display = 'none';
            monthOptions.style.display = (monthOptions.style.display === 'block') ? 'none' : 'block';
        });
    }

    document.querySelectorAll('.custom-option').forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = opt.getAttribute('data-value');
            document.getElementById('jumpMonth').value = val;
            document.getElementById('jumpMonthText').innerText = opt.innerText;
            monthOptions.style.display = 'none';
            jumpToDate();
        });
    });

    // Year Dropdown Handlers
    const yearTrigger = document.getElementById('jumpYearTrigger');
    const yearOptions = document.getElementById('jumpYearOptions');
    if (yearTrigger) {
        yearTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const monthOpts = document.getElementById('jumpMonthOptions');
            if (monthOpts) monthOpts.style.display = 'none';
            yearOptions.style.display = (yearOptions.style.display === 'block') ? 'none' : 'block';
        });
    }

    document.querySelectorAll('.custom-option-year').forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = opt.getAttribute('data-value');
            document.getElementById('jumpYear').value = val;
            document.getElementById('jumpYearText').innerText = opt.innerText;
            yearOptions.style.display = 'none';
            jumpToDate();
        });
    });

    // Global click listener to close dropdowns correctly
    document.addEventListener('click', (e) => {
        if (monthOptions && !monthTrigger.contains(e.target) && !monthOptions.contains(e.target)) {
            monthOptions.style.display = 'none';
        }
        if (yearOptions && !yearTrigger.contains(e.target) && !yearOptions.contains(e.target)) {
            yearOptions.style.display = 'none';
        }
    });
    
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
                    <div style="min-width: 200px; max-width: 240px; flex: 0 0 220px; background: white; border: 3px solid #000; padding: 0.8rem; box-shadow: 4px 4px 0px #000; border-radius: 12px; margin-bottom: 1rem;">
                        <p style="margin-bottom: 0.4rem; font-weight: 700; font-size: 0.95rem; border-bottom: 2px dashed #ccc; padding-bottom: 0.4rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $appt->user->nombre }}
                        </p>
                        <p style="font-size: 0.8rem; margin-bottom: 0.5rem;">
                            <i class="fa-regular fa-calendar"></i> {{ $appt->fecha_hora->format('d/m H:i') }} hs
                        </p>
                        
                        <!-- Actions -->
                        <div style="display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.8rem;">
                            @php
                                $ext = pathinfo($appt->payment->comprobante_ruta, PATHINFO_EXTENSION);
                            @endphp
                            <button type="button" class="neobrutalist-btn w-full no-select" style="background: var(--color-celeste); font-size: 0.75rem; padding: 6px;" 
                                    onclick="openProofModal('{{ route('payments.showProof', $appt->payment->id) }}', '{{ $appt->user->nombre }}', '{{ $appt->payment->created_at->format('d/m H:i') }}', '{{ $ext }}')">
                                <i class="fa-solid fa-image"></i> Ver Comprobante
                            </button>
                            
                            <div style="display: flex; gap: 0.4rem;">
                                <form id="verify-payment-{{ $appt->payment->id }}" action="{{ route('admin.payments.verify', $appt->payment->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-verde w-full no-select" style="padding: 8px; font-size: 0.75rem;" 
                                            onclick="confirmAction('verify-payment-{{ $appt->payment->id }}', '¿Confirmás que el pago es válido?')">
                                        <i class="fa-solid fa-check"></i> Validar
                                    </button>
                                </form>
                                <form id="reject-payment-{{ $appt->payment->id }}" action="{{ route('admin.payments.reject', $appt->payment->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-lila w-full no-select" style="padding: 8px; font-size: 0.75rem;" 
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
        <div style="background: white; border: 3px solid #000; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 6px 6px 0px #000; border-radius: 12px;">
            <h4 style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 800; text-transform: uppercase; color: #555;">Filtros de Búsqueda</h4>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 280px;">
                    <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">Identificar Paciente</label>
                    <input type="text" id="turnoSearch" placeholder="Buscar por nombre o apellido..." class="neobrutalist-input w-full" style="margin:0; width: 100%;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">Filtrar por Estado</label>
                    <select id="turnoFilter" class="neobrutalist-input w-full" style="margin:0; width: 100%;">
                        <option value="todos">Mostrar Todo</option>
                        <option value="pendiente">Solo Pendientes</option>
                        <option value="confirmado">Solo Confirmados</option>
                        <option value="cancelado">Solo Cancelados</option>
                        <option value="frecuente">Pacientes Frecuentes</option>
                        <option value="nuevo">Pacientes Nuevos</option>
                    </select>
                </div>
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
                        @php $tipo = $appt->user->paciente->tipo_paciente ?? 'nuevo'; @endphp
                        <tr style="border-bottom: 2px solid #eee;" class="turno-row" data-paciente="{{ strtolower($appt->user->nombre) }}" data-estado="{{ $appt->estado }}" data-tipo="{{ $tipo }}">
                            <td data-label="Fecha" style="padding: 1rem; font-weight: 700;">{{ $appt->fecha_hora->format('d/m H:i') }} hs</td>
                            <td data-label="Paciente" style="padding: 1rem;">{{ $appt->user->nombre }}</td>
                            <td data-label="Estado" style="padding: 1rem;">
                               <span class="no-select status-badge" style="font-weight: bold; background: {{ $appt->estado == 'confirmado' ? 'var(--color-verde)' : ($appt->estado == 'cancelado' ? 'var(--color-rosa)' : 'var(--color-amarillo)') }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px;">
                                   {{ ucfirst($appt->estado) }}
                               </span>
                            </td>
                            <td data-label="Tipo" style="padding: 1rem;">
                                <span class="no-select" style="font-weight: bold; background: {{ $tipo == 'frecuente' ? 'var(--color-verde)' : 'var(--color-amarillo)' }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px; text-transform: uppercase; font-size: 0.85rem;">
                                    {{ $tipo }}
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
    <div id="documentos" class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
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
                                    <form id="delete-res-{{ $res->id }}" action="{{ route('admin.resources.destroy', $res->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="confirmAction('delete-res-{{ $res->id }}', '¿Borrar recurso?')"><i class="fa-solid fa-trash"></i></button>
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
            <style>
                @media (max-width: 768px) {
                    .patients-table {
                        border: none !important;
                        box-shadow: none !important;
                    }
                    .patients-table thead { display: none; }
                    .patients-table tr {
                        display: block;
                        border: 3px solid #000 !important;
                        margin-bottom: 1rem;
                        border-radius: 12px;
                        padding: 1rem;
                    }
                    .patients-table td {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 0.5rem 0 !important;
                        border: none !important;
                        border-bottom: 1px solid #eee !important;
                    }
                    .patients-table td:last-child { border-bottom: none !important; }
                    .patients-table td:before {
                        content: attr(data-label);
                        font-weight: 800;
                        color: #666;
                        text-transform: uppercase;
                        font-size: 0.75rem;
                    }
                }
            </style>
            <table class="patients-table" style="width: 100%; border-collapse: collapse; margin-top: 1rem; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px #000;">
                <thead>
                    <tr style="background: #000; color: #fff; border-bottom: 3px solid #fff;">
                        <th style="padding: 0.8rem; text-align: left;">Nombre</th>
                        <th style="padding: 0.8rem; text-align: left;">Email</th>
                        <th style="padding: 0.8rem; text-align: left;">Teléfono</th>
                        <th style="padding: 0.8rem; text-align: left;">Tipo</th>
                        <th style="padding: 0.8rem; text-align: center;">Turnos</th>
                        <th style="padding: 0.8rem; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr style="border-bottom: 2px solid #000; background: white; color: #000;">
                            <td data-label="Nombre" style="padding: 0.8rem; font-weight: 700;">{{ $patient->nombre }}</td>
                            <td data-label="Email" style="padding: 0.8rem;">{{ $patient->email }}</td>
                            <td data-label="Teléfono" style="padding: 0.8rem;">{{ $patient->paciente->telefono ?? ($patient->telefono ?? '-') }}</td>
                            <td data-label="Tipo" style="padding: 0.8rem;">
                                @php $tipo = $patient->paciente->tipo_paciente ?? 'nuevo'; @endphp
                                <span class="no-select" style="font-weight: bold; background: {{ $tipo == 'frecuente' ? 'var(--color-verde)' : 'var(--color-amarillo)' }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px; text-transform: uppercase; font-size: 0.85rem;">
                                    {{ ucfirst($tipo) }}
                                </span>
                            </td>
                            <td data-label="Turnos" style="padding: 0.8rem; text-align: center;">{{ $patient->turnos_count ?? $patient->turnos()->count() }}</td>
                            <td data-label="Acciones" style="padding: 0.8rem; text-align: right;">
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

    <!-- Lista de Espera (NEW) -->
    <div id="waitlist" class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clock"></i> Lista de Espera (Global)</h3>
        </div>
        
        <div style="overflow-x: auto;">
            <style>
                @media (max-width: 768px) {
                    .waitlist-table {
                        border: none !important;
                        box-shadow: none !important;
                    }
                    .waitlist-table thead { display: none; }
                    .waitlist-table tr {
                        display: block;
                        border: 3px solid #000 !important;
                        margin-bottom: 1rem;
                        border-radius: 12px;
                        padding: 1rem;
                    }
                    .waitlist-table td {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 0.5rem 0 !important;
                        border: none !important;
                        border-bottom: 1px solid #eee !important;
                    }
                    .waitlist-table td:last-child { border-bottom: none !important; }
                    .waitlist-table td:before {
                        content: attr(data-label);
                        font-weight: 800;
                        color: #666;
                        text-transform: uppercase;
                        font-size: 0.75rem;
                    }
                }
            </style>
            <table class="waitlist-table" style="width: 100%; border-collapse: collapse; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px #000;">
                <thead>
                    <tr style="background: #000; color: #fff;">
                        <th style="padding: 0.8rem; text-align: left;">Fecha/Hora</th>
                        <th style="padding: 0.8rem; text-align: left;">Paciente</th>
                        <th style="padding: 0.8rem; text-align: left;">Teléfono</th>
                        <th style="padding: 0.8rem; text-align: left;">Email</th>
                        <th style="padding: 0.8rem; text-align: left;">Preferencia</th>
                        <th style="padding: 0.8rem; text-align: center; width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitlist ?? [] as $entry)
                        <tr style="border-bottom: 2px solid #000;">
                            <td data-label="Fecha/Hora" style="padding: 0.8rem; font-weight: 700;">
                                @if($entry->fecha_especifica)
                                    {{ \Carbon\Carbon::parse($entry->fecha_especifica)->format('d/m') }}
                                @else
                                    Global
                                @endif
                                @if($entry->hora_inicio)
                                    - {{ \Carbon\Carbon::parse($entry->hora_inicio)->format('H:i') }} hs
                                @endif
                            </td>
                            <td data-label="Paciente" style="padding: 0.8rem; font-weight: 900;">{{ $entry->name }}</td>
                            <td data-label="Teléfono" style="padding: 0.8rem; font-family: 'Inter', sans-serif;">
                                @if($entry->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $entry->phone) }}?text=Hola+{{ urlencode($entry->name) }}%2C+se+liber%C3%B3+un+lugar+para+el+{{ urlencode($entry->fecha_especifica ? \Carbon\Carbon::parse($entry->fecha_especifica)->format('d/m') : 'próxima fecha') }}.+%C2%BFTe+gustar%C3%ADa+tomar+el+turno%3F+Saludos%2C+Lic.+Nazarena+De+Luca." target="_blank" style="color: #25D366; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 0.85rem;">
                                        <i class="fa-brands fa-whatsapp"></i> {{ $entry->phone }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td data-label="Email" style="padding: 0.8rem; font-family: 'Inter', sans-serif;">
                                @if($entry->email)
                                    <a href="mailto:{{ $entry->email }}?subject=Turno disponible - Lic. Nazarena De Luca&amp;body=Hola+{{ urlencode($entry->name) }}%2C+se+liber%C3%B3+un+lugar+para+el+{{ urlencode($entry->fecha_especifica ? \Carbon\Carbon::parse($entry->fecha_especifica)->format('d/m') : 'próxima fecha') }}.+%C2%BFTe+gustar%C3%ADa+tomar+el+turno%3F+Saludos%2C+Lic.+Nazarena+De+Luca." target="_blank" style="color: #ff4d4d; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 5px; font-size: 0.85rem;">
                                        <i class="fa-solid fa-envelope"></i> {{ $entry->email }}
                                    </a>
                                @else
                                    No registrado
                                @endif
                            </td>
                            <td data-label="Preferencia" style="padding: 0.8rem;">
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <span style="background: var(--color-celeste); padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                                            <i class="fa-solid fa-house-user"></i> {{ $entry->modality }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.85rem; font-weight: 700; color: #000; background: #fffadc; padding: 0.5rem; border: 1px dashed #000; border-radius: 5px;">
                                        <i class="fa-solid fa-clock"></i> {{ $entry->availability ?? 'No especificó disponibilidad general' }}
                                    </div>
                                </div>
                            </td>
                            <td data-label="Acciones" style="padding: 0.8rem; text-align: center; width: 150px;">
                                <form id="delete-waitlist-{{ $entry->id }}" action="{{ route('admin.waitlist.destroy', $entry->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="confirmAction('delete-waitlist-{{ $entry->id }}', '¿Remover de la lista de espera?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: #666;">No hay nadie en la lista de espera.</td>
                        </tr>
                    @endforelse
                </tbody>
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
            <div class="modal-image-col" style="background: #222; display: flex; align-items: center; justify-content: center; min-height: 200px; flex-direction: column; position: relative;">
                
                <!-- Loading Spinner -->
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
                <div style="margin-top:auto;">
                    
                </div>
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

                <!-- Configuración de Sesiones -->
                <div class="neobrutalist-card" style="margin-top: 1.5rem; background: var(--color-celeste);">
                    <h4 style="margin-bottom: 1rem;"><i class="fa-solid fa-clock"></i> Configuración de Sesiones</h4>
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
        const modalLoader = document.getElementById('modalLoader');
        
        // Reset state
        if(modalImage) modalImage.style.display = 'none';
        if(modalPdf) modalPdf.style.display = 'none';
        if(modalError) modalError.style.display = 'none';
        
        // Show Loader
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
        
        // Clear sources to stop PDF loading
        if(modalImage) modalImage.src = '';
        if(modalPdf) modalPdf.src = '';
        if(modalError) modalError.style.display = 'none';
        if(modalLoader) modalLoader.style.display = 'none';
        
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

    // Reset Calendar Logic
    window.resetCalendar = function() {
        currentDate = new Date();
        renderCalendar();
    }
    // Scroll Preservation Logic
    document.addEventListener("DOMContentLoaded", function() {
        if (localStorage.getItem("adminScrollPos")) {
            window.scrollTo(0, localStorage.getItem("adminScrollPos"));
            localStorage.removeItem("adminScrollPos");
        }
    });

    window.saveScrollPosition = function() {
        localStorage.setItem("adminScrollPos", window.scrollY);
    };

    // Generic Modal Logic moved to top of script

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
            r.style.display = '';
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

    // Custom dropdowns are managed in the DOMContentLoaded listener above
    
    // Scroll to section based on route
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname;
        let targetId = null;
        
        if (path.includes('/agenda')) targetId = 'agenda';
        else if (path.includes('/pacientes')) targetId = 'pacientes';
        else if (path.includes('/pagos')) targetId = 'pagos';
        else if (path.includes('/turnos')) targetId = 'turnos';
        else if (path.includes('/documentos')) targetId = 'documentos';
        else if (path.includes('/waitlist')) targetId = 'waitlist';
        else if (path.includes('/configuracion')) targetId = 'configuracion';
        else if (path.includes('/historial')) targetId = 'historial';
        
        if (targetId) {
            setTimeout(() => {
                const element = document.getElementById(targetId);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 300);
        }
    });
</script>

<style>
    /* Mobile Optimizations */
    @media (max-width: 768px) {
        /* Remove table borders on mobile */
        table {
            border: none !important;
            box-shadow: none !important;
        }
        
        /* Container with table - reduce border */
        div[style*="border: 3px solid #000"][style*="overflow-y: auto"],
        div[style*="border: 3px solid #000"][style*="overflow-x: auto"] {
            border: 1px solid #000 !important;
            box-shadow: 2px 2px 0px #000 !important;
        }
        
        /* Grid containers - add padding */
        div[style*="display: grid"][style*="grid-template-columns"] {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            gap: 1rem !important;
        }
        
        /* Neobrutalist cards */
        .neobrutalist-card {
            margin-left: 1rem !important;
            margin-right: 1rem !important;
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
        /* Center table cells on mobile */
        tr {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem;
        }
        td {
            display: block;
            width: 100%;
            text-align: center !important;
            padding: 0.5rem 0 !important;
            border: none;
        }
        td:last-child {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        td[data-label]::before {
            display: none; /* Hide labels if not needed or adjust styling */
        }
         /* Specialized alignment for the turnos table if needed */
         #hoy table tr td {
            text-align: center !important;
            justify-content: center !important;
         }
    }
</style>

@endsection
```
