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

    .event-dot.internal { background: var(--color-celeste); }
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
                         return \Carbon\Carbon::parse($app->fecha_hora)->format('Y-m-d') == $dayStr;
                    });
                    
                    $dayExternal = $externalEvents->filter(function($evt) use ($day) {
                        return $evt->start_time->startOfDay()->lte($day) && $evt->end_time->endOfDay()->gte($day);
                    });
                    
                    $isBlocked = $blockedDays->firstWhere('date', $dayStr);
                @endphp

                <div class="calendar-day {{ $isCurrentMonth ? '' : 'other-month' }} {{ $isToday ? 'today' : '' }}" onclick="showDayDetails('{{ $dayStr }}', '{{ $dayDisplay }}')">
                    <div class="day-number">{{ $day->day }}</div>
                    <div class="event-dots">
                        @if($isBlocked)<span class="event-dot blocked"></span>@endif
                        @if($dayAppointments->count() > 0)<span class="event-dot internal"></span>@endif
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
        document.getElementById('day-details-section').style.display = 'block';
        document.getElementById('details-title').innerText = dateDisplay;
        
        // Filter appointments: must match date AND NOT be cancelled
        // Include both regular appointments AND projected fixed appointments (is_projected)
        const dayAppsRaw = appointments.filter(app => {
            const matchesDate = app.fecha_hora.startsWith(dateStr);
            const isNotCancelled = app.estado !== 'cancelado' && app.estado !== 'cancelled';
            const isProjected = app.is_projected === true || app.is_projected === 'true';
            // Show if: matches date, not cancelled, AND (not projected OR is projected and confirmed)
            return matchesDate && isNotCancelled && (app.estado === 'confirmado' || isProjected);
        });
        
        // Use a Set to track seen appointments to avoid duplicates
        const seen = new Set();
        const dayApps = dayAppsRaw.filter(app => {
            const time = app.fecha_hora.substring(11, 16);
            // Deduplicate by time and userId to be safe
            const key = `${time}-${app.user_id}`;
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
        });

        // Sort by time
        dayApps.sort((a, b) => a.fecha_hora.localeCompare(b.fecha_hora));

        const internalContainer = document.getElementById('internal-appointments');
        const noInternal = document.getElementById('no-internal');
        
        internalContainer.innerHTML = '';
        if (dayApps.length > 0) {
            noInternal.style.display = 'none';
            dayApps.forEach(app => {
                const time = app.fecha_hora.substring(11, 16);
                const div = document.createElement('div');
                div.className = 'event-pill internal';
                div.style.padding = '10px 15px';
                div.style.fontSize = '1.05rem';
                div.style.fontWeight = '700';
                div.style.border = '2px solid #000';
                div.style.boxShadow = '3px 3px 0px #000';
                div.style.background = 'white';
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                div.style.gap = '10px';
                div.innerHTML = `
                    <span style="background: var(--color-celeste); padding: 3px 10px; border: 2px solid #000; border-radius: 6px; font-size: 0.95rem; white-space: nowrap;">${time} hs</span> 
                    <a href="/admin/pacientes?search=${encodeURIComponent(app.user.nombre)}" 
                       style="color: #000; text-decoration: underline; font-weight: 800; font-size: 1rem; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                       title="Gestionar paciente">
                        ${app.user.nombre}
                    </a>
                `;
                internalContainer.appendChild(div);
            });
        } else {
            noInternal.style.display = 'block';
        }

        if (shouldScroll) {
            // Scroll to details with offset for header
            const element = document.getElementById('day-details-section');
            const headerOffset = 100;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }
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
        
        showDayDetails(dateStr, dateDisplay, false); // No scroll on init
    });
</script>
@endsection
