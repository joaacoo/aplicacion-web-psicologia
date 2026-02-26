@extends('layouts.app')

@section('title', 'Agenda Mensual - Admin')
@section('header_title', 'Agenda Mensual')

@section('content')
<style>
    /* Estilo inspirado en Apple Calendar iOS + Clean Design */
    .calendar-container {
        background: white;
        /* Removed border and heavy shadow as requested */
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); /* Soft shadow instead */
        margin-bottom: 2rem;
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
        background: white; /* Changed to white for cleaner look */
        color: #000;
        border-bottom: 1px solid #eee;
    }

    .calendar-header h2 {
        font-size: 1.8rem;
        margin: 0;
        text-transform: capitalize;
        font-family: 'Inter', sans-serif;
        font-weight: 700;
    }

    .calendar-header .nav-btn {
        background: transparent;
        border: 1px solid #eee;
        border-radius: 50%;
        color: #000;
        font-size: 1.2rem;
        cursor: pointer;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    
    .calendar-header .nav-btn:hover {
        background: #f5f5f5;
    }
    
    .calendar-header a.nav-btn {
        text-decoration: none;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0; /* Seamless grid */
        background: #fff;
    }

    .calendar-day-header {
        background: #fff;
        color: #666;
        padding: 1rem 0.5rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 1px solid #eee;
    }

    .calendar-day {
        background: white;
        min-height: 140px; /* Taller cells */
        padding: 0.8rem;
        text-align: left; /* Less centered, more standard */
        position: relative;
        cursor: pointer;
        transition: background 0.2s;
        border-right: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .calendar-day:hover {
        background: #f9f9f9;
        z-index: 1;
    }

    /* Remove right border for last column */
    .calendar-day:nth-child(7n) {
        border-right: none;
    }

    .calendar-day.other-month {
        background: #fafafa;
        color: #ccc;
    }

    .calendar-day.today {
        background: #fff;
    }
    
    .calendar-day.today .day-number {
        background: #ff3b30;
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
    }

    .day-number {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: inline-block;
        width: 32px;
        height: 32px;
        line-height: 32px;
        text-align: center;
    }

    .event-dots {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .event-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .event-dot.confirmed { background: var(--color-verde); }
    .event-dot.cancelled { background: var(--color-rojo); }
    .event-dot.fixed { background: var(--color-celeste); }
    .event-dot.recovered { background: var(--color-lila); }
    .event-dot.external { background: #ff9500; }
    .event-dot.blocked { background: #ff3b30; }



    @media (max-width: 768px) {
        #details-title {
            font-size: 0.95rem !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .calendar-container {
            border-radius: 0; /* Full bleed on mobile maybe? or small radius */
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-left: 0; 
            margin-right: 0;
            width: 100%;
            border: 1px solid #eee;
        }

        .calendar-header h2 {
            font-size: 1.3rem;
        }
        
        /* Enable horizontal scroll on mobile if needed, or stick to stacked? 
           User liked "que se vean todos los dias bien". 
           Let's try a responsive grid that doesn't squash too much.
        */
        .calendar-grid {
            /* On mobile, maybe show less days? No, standard is 7 cols or list view. 
               User liked the grid but "todos los dias bien".
               Let's ensure minimum width.
            */
            grid-template-columns: repeat(7, minmax(40px, 1fr)); 
        }

        .calendar-day {
            min-height: 80px;
            padding: 0.3rem;
        }
        
        .day-number {
            font-size: 0.9rem;
            width: 24px;
            height: 24px;
            line-height: 24px;
        }
        
        .event-dot {
            width: 6px;
            height: 6px;
        }

        .details-time-pill {
            flex-shrink: 0; 
            min-width: 130px; 
            text-align: center;
        }

        @media (max-width: 480px) {
            .details-time-pill {
                min-width: 110px !important;
            }
            .details-time-pill span {
                font-size: 0.75rem !important;
                padding: 2px 5px !important;
            }
            #day-details-section {
                padding: 1.2rem 1rem !important;
            }
            .appointment-row {
                padding: 8px 10px !important;
                gap: 8px !important;
            }
            .appointment-name {
                font-size: 0.9rem !important;
            }
            .appointment-badge {
                font-size: 0.65rem !important;
                padding: 2px 5px !important;
            }
        }
    }
</style>

<div style="padding: 2rem; width: 98%; margin: 0 auto; margin-bottom: 40px;">

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            {{ session('status') }}
        </div>
    @endif

    <div class="calendar-container">
        <!-- Header estilo Apple -->
        <div class="calendar-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.8rem;">
                @if($prev->lt($minAllowed))
                    <button class="nav-btn" disabled aria-disabled="true" title="No se puede retroceder más">&lt;</button>
                @else
                    <a href="{{ route('admin.agenda', ['month' => $prev->month, 'year' => $prev->year]) }}" class="nav-btn">&lt;</a>
                @endif

                <h2 style="white-space: nowrap;">{{ $currentDate->locale('es')->isoFormat('MMMM YYYY') }}</h2>

                @php $next = $currentDate->copy()->addMonth(); @endphp
                <a href="{{ route('admin.agenda', ['month' => $next->month, 'year' => $next->year]) }}" class="nav-btn">&gt;</a>
            </div>

            <!-- Filtros de Mes y Año -->
            <form action="{{ route('admin.agenda') }}" method="GET" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-top: 5px;">
                <div style="display: flex; gap: 0.5rem; flex-wrap: nowrap;">
                    <select name="month" class="neobrutalist-input" style="padding: 0.3rem 0.5rem; font-size: 0.9rem; border-radius: 8px; cursor: pointer; height: 38px; margin-bottom: 0; min-width: 100px; box-shadow: 2px 2px 0px #000;" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentDate->month == $m ? 'selected' : '' }}>
                                {{ ucfirst(\Carbon\Carbon::create(null, $m, 1)->locale('es')->monthName) }}
                            </option>
                        @endfor
                    </select>
                    
                    <select name="year" class="neobrutalist-input" style="padding: 0.3rem 0.5rem; font-size: 0.9rem; border-radius: 8px; cursor: pointer; height: 38px; margin-bottom: 0; min-width: 80px; box-shadow: 2px 2px 0px #000;" onchange="this.form.submit()">
                        @for($y = 2026; $y <= 2028; $y++)
                            <option value="{{ $y }}" {{ $currentDate->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <a href="{{ route('admin.agenda') }}" class="neobrutalist-btn bg-celeste" style="padding: 0 1rem; font-size: 0.8rem; height: 38px; display: flex; align-items: center; text-decoration: none; white-space: nowrap; margin: 0; border-radius: 8px; box-shadow: 2px 2px 0px #000; border: 3px solid #000;">
                    Mes Actual
                </a>
            </form>
        </div>

        <div class="calendar-grid">
            <!-- Headers días -->
            <div class="calendar-day-header">L</div>
            <div class="calendar-day-header">M</div>
            <div class="calendar-day-header">X</div>
            <div class="calendar-day-header">J</div>
            <div class="calendar-day-header">V</div>
            <div class="calendar-day-header">S</div>
            <div class="calendar-day-header">D</div>

            <!-- Días del mes -->
            @php
                $startOfMonth = $currentDate->copy()->startOfMonth();
                $endOfMonth = $currentDate->copy()->endOfMonth();
                $startOfWeek = $startOfMonth->copy()->startOfWeek();
                $endOfWeek = $endOfMonth->copy()->endOfWeek();
                $day = $startOfWeek->copy();
            @endphp

            @while($day <= $endOfWeek)
                @php
                    $isCurrentMonth = $day->month == $currentDate->month;
                    $isToday = $day->isToday();
                    $dayStr = $day->format('Y-m-d');
                    $dayDisplay = $day->locale('es')->isoFormat('dddd D [de] MMMM');
                    
                    // Re-implement filtering logic
                    $dayAppointments = $appointments->filter(function($app) use ($dayStr) {
                         $appDate = is_string($app->fecha_hora) ? substr($app->fecha_hora, 0, 10) : $app->fecha_hora->format('Y-m-d');
                         return $appDate == $dayStr;
                    });
                    
                    $dayExternal = $externalEvents->filter(function($evt) use ($day) {
                        return $evt->start_time->startOfDay()->lte($day) && $evt->end_time->endOfDay()->gte($day);
                    });
                    
                    $isBlocked = $blockedDays->firstWhere('date', $dayStr);
                @endphp

                <div class="calendar-day {{ $isCurrentMonth ? '' : 'other-month' }} {{ $isToday ? 'today' : '' }}" onclick="showDayDetails('{{ $dayStr }}', '{{ $dayDisplay }}', true)">
                    <div class="day-number">{{ $day->day }}</div>
                    <div class="event-dots">
                        @if($isBlocked)<span class="event-dot blocked"></span>@endif
                        @php
                            $hasConfirmed = $dayAppointments->contains('estado', 'confirmado');
                            $hasFixed = $dayAppointments->filter(fn($a) => $a->estado !== 'cancelado')->contains('es_recurrente', true);
                            $hasRecovered = $dayAppointments->contains('es_recuperacion', true);
                        @endphp
                        @if($hasConfirmed)<span class="event-dot confirmed"></span>@endif
                        @if($hasFixed)<span class="event-dot fixed"></span>@endif
                        @if($hasRecovered)<span class="event-dot recovered"></span>@endif
                        @if($dayExternal->count() > 0)<span class="event-dot external"></span>@endif
                    </div>
                </div>

                @php $day->addDay(); @endphp
            @endwhile
        </div>
    </div>

    <!-- Day Details Section (Moved up) -->
    <div id="day-details-section" style="display: none; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 1.5rem 2.5rem; margin-bottom: 2.5rem; border: 1px solid #eee;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: nowrap;">
            <h2 id="details-title" style="font-size: 1.1rem; font-weight: 800; font-family: 'Syne', sans-serif; margin: 0; color: #000; text-transform: capitalize; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;"></h2>
            <div style="width: 10px;"></div> <!-- Spacer -->
        </div>
        
        <div id="details-content" style="display: block; width: 100%;">
            <!-- Turnos internos -->
            <div style="width: 100%;">
                <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.2rem; color: #000; font-family: 'Syne', sans-serif; border-bottom: 3px solid #000; padding-bottom: 0.3rem; display: inline-block;">Turnos</h3>
                <div id="internal-appointments" style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <!-- Turnos will be inserted here -->
                </div>
                <div id="no-internal" style="color: #999; font-style: italic; font-size: 1rem; font-weight: 700; padding: 1.5rem; border: 2px dashed #ccc; border-radius: 10px; text-align: center;">No hay turnos hoy</div>
            </div>
        </div>
    </div>


</div>

<script>
    // JS Data objects for appointments and events to populate details without AJAX for speed
    const appointments = @json($appointments);
    const externalEvents = @json($externalEvents);

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

    function showDayDetails(dateStr, dateDisplay, shouldScroll = true) {
        const detailsSection = document.getElementById('day-details-section');
        const detailsContent = document.getElementById('details-content');
        const internalContainer = document.getElementById('internal-appointments');
        const noInternal = document.getElementById('no-internal');
        const titleLabel = document.getElementById('details-title');

        detailsSection.style.display = 'block';
        titleLabel.innerText = dateDisplay;
        
        // Show loading state
        internalContainer.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fa-solid fa-circle-notch fa-spin fa-2xl"></i><p style="margin-top: 1rem; font-weight: 700;">Cargando turnos...</p></div>';
        noInternal.style.display = 'none';

        if (shouldScroll) {
            const element = detailsSection;
            const headerOffset = 100;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
            window.scrollTo({ top: offsetPosition, behavior: "smooth" });
        }

        // AJAX Fetch for fresh state
        fetch(`${window.location.origin}/admin/agenda/day-details?date=${dateStr}`)
            .then(response => response.json())
            .then(data => {
                internalContainer.innerHTML = '';
                
                if (data.appointments && data.appointments.length > 0) {
                    noInternal.style.display = 'none';
                    data.appointments.forEach(app => {
                        // REGLA: Los turnos NUNCA se eliminan, todo queda visible
                        // Los cancelados se muestran con opacidad reducida
                        
                        const div = document.createElement('div');
                        
                        // Determinar si es cancelado para aplicar opacidad
                        const isCancelled = app.status_type === 'cancelado' || app.status_type === 'cancelado_fijo';
                        const opacity = isCancelled ? '0.6' : '1';
                        
                        // Badge Selection
                        let badgeHtml = '';
                        switch (app.status_type) {
                            case 'confirmado': badgeHtml = '<span style="background: var(--color-verde); color: #fff; padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">CONFIRMADO</span>'; break;
                            case 'cancelado': badgeHtml = '<span style="background: var(--color-rojo); color: #fff; padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">CANCELADO</span>'; break;
                            case 'cancelado_fijo': badgeHtml = '<span style="background: var(--color-rojo); color: #fff; padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">CANCELADO - FIJO</span>'; break;
                            case 'fijo': badgeHtml = '<span style="background: var(--color-celeste); padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">FIJO</span>'; break;
                            case 'recuperado': badgeHtml = '<span style="background: var(--color-lila); padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">RECUPERADO</span>'; break;
                            case 'ausente': badgeHtml = '<span style="background: #000; color: #fff; padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">AUSENTE</span>'; break;
                            case 'finalizado': badgeHtml = '<span style="background: #fff; border: 2px solid #000; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">FINALIZADO</span>'; break;
                            default: badgeHtml = '<span style="background: #eee; border: 2px solid #000; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; display: inline-block; white-space: nowrap;">' + app.status_type.toUpperCase() + '</span>';
                        }

                        // Calculate end time using session duration
                        let [h, m] = app.time.split(':').map(Number);
                        let durationMinutes = app.duration || 45;
                        
                        let totalMinutes = h * 60 + m + durationMinutes;
                        let eh = Math.floor(totalMinutes / 60) % 24;
                        let em = totalMinutes % 60;
                        let endTime = `${eh.toString().padStart(2, '0')}:${em.toString().padStart(2, '0')}`;

                        div.className = 'appointment-row';
                        div.style.cssText = `
                            padding: 10px 15px;
                            font-size: 1.05rem;
                            font-weight: 700;
                            border: 2px solid #000;
                            box-shadow: 3px 3px 0px #000;
                            background: white;
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            opacity: ${opacity};
                            margin-bottom: 0.8rem;
                        `;
                        
                        div.innerHTML = `
                            <div class="details-time-pill">
                                <span style="background: #eee; padding: 3px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.85rem; white-space: nowrap;">${app.time} - ${endTime} hs</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <a href="/admin/pacientes?search=${encodeURIComponent(app.user_name)}" 
                                   class="appointment-name"
                                   style="color: #000; text-decoration: underline; font-weight: 800; font-size: 1rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    ${app.user_name}
                                </a>
                            </div>
                            <div style="flex-shrink: 0;">
                                <span class="appointment-badge">${badgeHtml}</span>
                            </div>
                        `;
                        internalContainer.appendChild(div);
                    });
                } else {
                    noInternal.style.display = 'block';
                    internalContainer.innerHTML = '';
                }

                // Add External Events if any
                if (data.external_events && data.external_events.length > 0) {
                    const extHeader = document.createElement('h3');
                    extHeader.innerHTML = '<i class="fa-brands fa-google"></i> Eventos Google';
                    extHeader.style.cssText = "font-size: 1.1rem; font-weight: 800; margin-top: 1.5rem; margin-bottom: 1rem; color: #666; font-family: 'Syne', sans-serif; border-bottom: 2px solid #eee; padding-bottom: 0.3rem;";
                    internalContainer.appendChild(extHeader);
                    
                    data.external_events.forEach(evt => {
                        const div = document.createElement('div');
                        div.style.cssText = "padding: 8px 15px; font-size: 0.95rem; border: 2px solid #eee; border-radius: 10px; background: #fafafa; display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem; color: #666;";
                        div.innerHTML = `
                            <span style="font-weight: 800; color: #ff9500;">${evt.start} - ${evt.end}</span>
                            <span style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${evt.title}</span>
                        `;
                        internalContainer.appendChild(div);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching day details:', error);
                internalContainer.innerHTML = '<div class="alert alert-danger" style="margin: 1rem;">Error al cargar los detalles. Por favor, reintentá.</div>';
            });
    }

    function closeDayDetails() {
        document.getElementById('day-details-section').style.display = 'none';
        // Scroll back to absolute top as requested
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    // Initialize with today's details
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date();
        const dateStr = today.toISOString().split('T')[0];
        
        const options = { weekday: 'long', day: 'numeric', month: 'long' };
        let dateDisplay = today.toLocaleDateString('es-ES', options);
        // Ensure no redundant year or extra text that forces wrap
        dateDisplay = dateDisplay.split(',')[0] + ' ' + dateDisplay.split(',').slice(1).join(',').trim();
        
        showDayDetails(dateStr, dateDisplay, true); // Auto scroll to details on init
    });
</script>
@endsection
