@extends('layouts.app')

@section('title', 'Agenda Mensual - Admin')
@section('header_title', 'Agenda Mensual')

@section('content')
<div style="padding: 2rem; max-width: 1400px; margin: 0 auto; margin-bottom: 40px;"> <!-- Added margin-bottom to prevent cut-off -->
    <!-- Header: Compact -->


    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            {{ session('status') }}
        </div>
    @endif

    <!-- Calendar Controls with Filters Inside -->
    <div style="background: white; border: 3px solid #000; border-radius: 12px; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; overflow: hidden;"> 
    <!-- Calendar Controls with Filters Inside -->
    <div style="background: white; border: 3px solid #000; border-radius: 12px; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; overflow: hidden;"> 
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
            
            <!-- Left Side: Title & Navigation -->
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h2 style="font-size: 1.8rem; font-weight: 700; font-family: 'Inter', sans-serif; text-transform: capitalize; margin: 0;">
                    {{ $currentDate->locale('es')->isoFormat('MMMM YYYY') }}
                </h2>
                
                <div style="display: flex; gap: 5px;">
                    @php
                        $isJan2026 = $currentDate->year == 2026 && $currentDate->month == 1;
                        $prevRoute = $isJan2026 ? '#' : route('admin.agenda', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]);
                        $prevStyle = 'padding: 0.5rem; background: white; border: 2px solid #000; box-shadow: 2px 2px 0px #000; cursor: pointer; text-decoration: none; color: #000; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px;';
                        if ($isJan2026) {
                            $prevStyle .= ' opacity: 0.5; cursor: not-allowed; pointer-events: none;';
                        }
                    @endphp
                    <a href="{{ $prevRoute }}" class="neobrutalist-btn" style="{{ $prevStyle }}" title="Mes Anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <a href="{{ route('admin.agenda', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" class="neobrutalist-btn" style="padding: 0.5rem; background: white; border: 2px solid #000; box-shadow: 2px 2px 0px #000; cursor: pointer; text-decoration: none; color: #000; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px;" title="Mes Siguiente">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            
            <!-- Right Side: Actions & Filters -->
            <div style="display: flex; gap: 1rem; align-items: center; justify-content: flex-end; white-space: nowrap; flex-wrap: wrap;">
                 <a href="{{ route('admin.agenda') }}" class="neobrutalist-btn" style="background: #f0f0f0; border: 2px solid #000; color: #000; font-size: 0.9rem; padding: 0 1.2rem; height: 42px; display: flex; align-items: center; text-decoration: none; font-weight: 700;">
                    Mes Actual
                 </a>

                 <form action="{{ route('admin.calendar.sync') }}" method="POST" style="margin: 0; display: flex; align-items: center; height: 42px;">
                    @csrf
                    <button type="submit" class="neobrutalist-btn" style="background: white; border: 2px solid #ea4335; color: #ea4335; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem 1rem; height: 42px; box-shadow: 2px 2px 0px rgba(0,0,0,0.1);">
                        <i class="fa-brands fa-google"></i> <span class="d-none d-md-inline">Sincronizar</span>
                    </button>
                </form>

                 <form action="{{ route('admin.agenda') }}" method="GET" style="display: flex; gap: 0.5rem; margin: 0; align-items: center; height: 42px;">
                    <select name="month" class="neobrutalist-input" style="padding: 0.5rem 0.8rem; border: 2px solid #000; height: 42px; min-width: 140px; margin-bottom: 0;" onchange="this.form.submit()">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', $currentDate->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->locale('es')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="neobrutalist-input" style="padding: 0.5rem 0.8rem; border: 2px solid #000; height: 42px; margin-bottom: 0;" onchange="this.form.submit()">
                        @for($y = 2025; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ request('year', $currentDate->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                 </form>
            </div>
        </div>

        <!-- Google Private Link Input (Below header) -->
        <div style="background: #fff; border: 2px solid #000; border-radius: 12px; padding: 1rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
             <i class="fa-solid fa-link" style="color: #666;"></i>
             <span style="font-weight: 700; font-size: 0.9rem;">Link Privado (ICS):</span>
             
             <form action="{{ route('admin.calendar.google-url') }}" method="POST" style="margin: 0; display: flex; align-items: center; flex: 1; gap: 0.5rem;">
                @csrf
                <div style="position: relative; display: flex; align-items: center; flex: 1;">
                    <input type="password" name="google_calendar_url" class="neobrutalist-input" 
                           value="{{ auth()->user()->google_calendar_url }}" 
                           placeholder="Pegar link privado (ICS)..." 
                           style="padding: 0.5rem 0.8rem; width: 100%; font-size: 0.9rem; height: 42px; margin: 0; border: 2px solid #000; border-right: none;"
                           autocomplete="off">
                    <button type="submit" class="neobrutalist-btn bg-amarillo" style="height: 38px; padding: 0 0.5rem; font-size: 0.8rem; border-left: none; display: flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-save"></i> Guardar
                    </button>
                </div>
            </form>
            <a href="https://support.google.com/calendar/answer/37648?hl=es#zippy=%2Cobtener-la-direcci%C3%B3n-secreta-en-formato-ical" target="_blank" style="font-size: 0.8rem; color: #555; text-decoration: underline; display: flex; align-items: center; gap: 4px;" title="Ver cómo obtener el link secreto">
                <i class="fa-regular fa-circle-question"></i> ¿Cómo obtener mi link?
            </a>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
            <!-- Days Header -->
            <div class="calendar-day-header">Lun</div>
            <div class="calendar-day-header">Mar</div>
            <div class="calendar-day-header">Mié</div>
            <div class="calendar-day-header">Jue</div>
            <div class="calendar-day-header">Vie</div>
            <div class="calendar-day-header">Sáb</div>
            <div class="calendar-day-header">Dom</div>

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
                    // Format date for display in details
                    $dayDisplay = $day->locale('es')->isoFormat('dddd D [de] MMMM');
                    
                    // Filter events for this day
                    $dayAppointments = $appointments->filter(function($app) use ($dayStr) {
                         return \Carbon\Carbon::parse($app->fecha_hora)->format('Y-m-d') == $dayStr;
                    });
                    
                    $dayExternalEvents = $externalEvents->filter(function($evt) use ($day) {
                        return $evt->start_time->startOfDay()->lte($day) && $evt->end_time->endOfDay()->gte($day);
                    });
                    $dayBlocked = $blockedDays->firstWhere('date', $dayStr);
                    $esFeriado = $dayBlocked !== null;
                    $nombreFeriado = $dayBlocked ? $dayBlocked->reason : '';
                @endphp

                <div class="calendar-day {{ $isCurrentMonth ? '' : 'other-month' }} {{ $isToday ? 'today' : '' }}" onclick="showDayDetails('{{ $dayStr }}', '{{ $dayDisplay }}')" style="{{ $esFeriado ? 'background: #fff5f5;' : '' }}">
                    <div class="day-number">{{ $day->day }}</div>
                    
                    @if($esFeriado)
                        <div style="font-size: 0.65rem; background: #ff4d4d; color: white; padding: 2px 4px; border-radius: 4px; margin-bottom: 4px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <i class="fa-solid fa-umbrella-beach"></i> {{ $nombreFeriado }}
                        </div>
                    @endif
                    
                    <div class="events-container">
                        <!-- Internal Appointments -->
                        @foreach($dayAppointments as $app)
                            <div class="event-pill internal" title="{{ $app->user->nombre }} - {{ \Carbon\Carbon::parse($app->fecha_hora)->format('H:i') }}">
                                <span class="time">{{ \Carbon\Carbon::parse($app->fecha_hora)->format('H:i') }}</span>
                                <span class="title">{{ Str::limit($app->user->nombre, 10) }}</span>
                            </div>
                        @endforeach

                        <!-- Google Calendar Events -->
                        @foreach($dayExternalEvents as $evt)
                            <div class="event-pill external" title="{{ $evt->summary }}">
                                <span class="time"><i class="fa-brands fa-google" style="font-size: 0.6rem;"></i></span>
                                <span class="title">{{ Str::limit($evt->summary ?? 'Evento', 12) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php
                    $day->addDay();
                @endphp
            @endwhile
        </div>
    </div>

    <!-- Day Details Section (Hidden by default, shown via JS) -->
    <div id="day-details-section" style="display: none; background: white; border: 3px solid #000; border-radius: 12px; box-shadow: 8px 8px 0px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 id="details-title" style="font-size: 1.5rem; font-weight: 700; font-family: 'Inter', sans-serif; margin: 0; color: #000;"></h2>
            <button onclick="closeDayDetails()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div id="details-content" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Turnos internos -->
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #000; font-family: 'Manrope', sans-serif;">Turnos Internos</h3>
                <div id="internal-appointments" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Turnos will be inserted here -->
                </div>
                <div id="no-internal" style="color: #999; font-style: italic;">No hay turnos internos</div>
            </div>

            <!-- Google Calendar Events -->
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: #000; font-family: 'Manrope', sans-serif;">Google Calendar</h3>
                <div id="external-events" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Events will be inserted here -->
                </div>
                <div id="no-external" style="color: #999; font-style: italic;">No hay eventos en Google Calendar</div>
            </div>
        </div>
    </div>

    <!-- Google Calendar Sync Removed -->
</div>

<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px; /* Borders via gap */
        background: #e0e0e0;
        border: 2px solid #000;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .calendar-day-header {
        background: #f8f8f8;
        padding: 10px;
        text-align: center;
        font-weight: 700;
        font-family: 'Manrope', sans-serif;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
    }

    .calendar-day {
        background: white;
        min-height: 120px;
        padding: 8px;
        position: relative;
        transition: background 0.2s;
        cursor: pointer;
    }

    .calendar-day:hover {
        background: #fafafa;
    }

    .calendar-day.other-month {
        background: #f4f4f4;
        color: #aaa;
    }

    .calendar-day.today {
        background: #fffbeb; /* Light yellow for today */
    }
    
    .calendar-day.today .day-number {
        background: var(--color-amarillo);
        color: #000;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .day-number {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 5px;
        color: #333;
    }

    .events-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .event-pill {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        overflow: hidden;
    }

    .event-pill.internal {
        background: var(--color-celeste);
        border: 1px solid rgba(0,0,0,0.1);
        color: #000;
        font-weight: 600;
    }

    .event-pill.external {
        background: #fff0f0;
        border: 1px solid #ea4335;
        color: #ea4335;
    }
    
    .event-pill .time {
        opacity: 0.7;
        font-size: 0.65rem;
    }
</style>

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

    function showDayDetails(dateStr, dateDisplay) {
        document.getElementById('day-details-section').style.display = 'block';
        document.getElementById('details-title').innerText = dateDisplay;
        
        // Filter appointments
        const dayApps = appointments.filter(app => app.fecha_hora.startsWith(dateStr));
        const internalContainer = document.getElementById('internal-appointments');
        const noInternal = document.getElementById('no-internal');
        
        internalContainer.innerHTML = '';
        if (dayApps.length > 0) {
            noInternal.style.display = 'none';
            dayApps.forEach(app => {
                const time = app.fecha_hora.substring(11, 16);
                const div = document.createElement('div');
                div.className = 'event-pill internal';
                div.style.padding = '8px 12px';
                div.style.fontSize = '0.9rem';
                div.innerHTML = `<span style="font-weight:700; margin-right:8px;">${time}</span> <span>${app.user.nombre}</span>`;
                internalContainer.appendChild(div);
            });
        } else {
            noInternal.style.display = 'block';
        }

        // Filter external events
        // Note: Simple logic, assumes start_time string comparison or strict day match which might need moment/dayjs if complex
        // For now, rely on Blade filtering passed to JS? No, simple string match on date part
        const dayEvts = externalEvents.filter(evt => {
            // Simplified check: if event starts on this day
            return evt.start_time.startsWith(dateStr) || (evt.start_time <= dateStr && evt.end_time >= dateStr);
        });
        
        const externalContainer = document.getElementById('external-events');
        const noExternal = document.getElementById('no-external');
        
        externalContainer.innerHTML = '';
        if (dayEvts.length > 0) {
            noExternal.style.display = 'none';
            dayEvts.forEach(evt => {
                const div = document.createElement('div');
                div.className = 'event-pill external';
                div.style.padding = '8px 12px';
                div.style.fontSize = '0.9rem';
                div.innerHTML = `<i class="fa-brands fa-google" style="margin-right:8px;"></i> <span>${evt.summary || 'Evento'}</span>`;
                externalContainer.appendChild(div);
            });
        } else {
            noExternal.style.display = 'block';
        }
        
        // Scroll to details
        document.getElementById('day-details-section').scrollIntoView({ behavior: 'smooth' });
    }

    function closeDayDetails() {
        document.getElementById('day-details-section').style.display = 'none';
    }
</script>
@endsection
