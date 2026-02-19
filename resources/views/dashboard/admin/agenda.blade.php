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

    /* ICS Section Mobile Styling */
    .ics-sync-section {
        background: #fff;
        border: 2px solid #000; /* Kept border here as it's separate */
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 4px 4px 0 #000;
    }

    @media (max-width: 768px) {
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

        /* Refined Mobile ICS */
        .ics-sync-section {
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
        }
        
        .ics-row-desktop {
            flex-direction: column !important;
            gap: 1.2rem !important;
            align-items: stretch !important;
        }
        
        .ics-input-group-mobile {
            flex-direction: column !important;
            gap: 0.8rem !important;
        }

        .ics-input-group-mobile input {
            width: 100% !important;
            height: 52px !important;
        }

        .ics-input-group-mobile button {
            width: 100% !important;
            height: 52px !important;
            justify-content: center !important;
        }

        .ics-row-desktop > div {
            width: 100% !important;
        }

        .ics-row-desktop form {
            min-width: 0 !important;
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
        <div class="calendar-header">
            @php
                $prev = $currentDate->copy()->subMonth();
                $minAllowed = \Carbon\Carbon::create(2026, 1, 1);
            @endphp

            @if($prev->lt($minAllowed))
                <button class="nav-btn" disabled aria-disabled="true" title="No se puede retroceder más">&lt;</button>
            @else
                <a href="{{ route('admin.agenda', ['month' => $prev->month, 'year' => $prev->year]) }}" class="nav-btn">&lt;</a>
            @endif

            <h2>{{ $currentDate->locale('es')->isoFormat('MMMM YYYY') }}</h2>

            @php $next = $currentDate->copy()->addMonth(); @endphp
            <a href="{{ route('admin.agenda', ['month' => $next->month, 'year' => $next->year]) }}" class="nav-btn">&gt;</a>
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
    <div id="day-details-section" style="display: none; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2.5rem; margin-bottom: 2.5rem; border: 1px solid #eee;">
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

    <!-- ICS Sync Section (Refined) -->
    <div class="ics-sync-section" style="margin-top: 1rem; background: #fff; border-radius: 16px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 3rem; width: 100%; box-sizing: border-box;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; justify-content: center;">
            <div style="background: #f8f9fa; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1.5px solid #eee;">
                <i class="fa-solid fa-link" style="color: #6366f1; font-size: 1.4rem;"></i>
            </div>
            <div style="text-align: left;">
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; font-family: 'Syne', sans-serif; color: #1a1a1a;">Sincronización Externa</h3>
                <p style="margin: 0; font-size: 0.9rem; color: #666; font-family: 'Manrope', sans-serif;">Conectá tu Google Calendar mediante el link privado ICS.</p>
            </div>
        </div>

        <div class="ics-row-desktop" style="display: flex; gap: 2rem; align-items: flex-end; justify-content: center; max-width: 1000px; margin: 0 auto; width: 100%;">
            <!-- Form Group -->
            <form action="{{ route('admin.calendar.google-url') }}" method="POST" style="flex: 2; display: flex; flex-direction: column; gap: 0.8rem; width: 100%;">
                @csrf
                <label style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: #888; margin-left: 4px; letter-spacing: 0.5px;">Link Privado (ICS)</label>
                <div class="ics-input-group-mobile" style="display: flex; gap: 0.75rem; width: 100%;">
                    <input type="password" name="google_calendar_url" class="neobrutalist-input" value="{{ auth()->user()->google_calendar_url ?? '' }}" 
                           placeholder="Pegar link privado (ICS)..." 
                           style="flex: 1; margin-bottom: 0; height: 56px; border-radius: 14px; font-size: 1rem; box-shadow: none; border: 2px solid #eee; background: #fcfcfc; width: 100%;"
                           autocomplete="off">
                    <button type="submit" class="neobrutalist-btn bg-amarillo" style="height: 56px; padding: 0 2rem; border-radius: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 4px 4px 0px #000; flex-shrink: 0;">
                        <i class="fa-solid fa-save"></i>
                        <span class="btn-text">Guardar</span>
                    </button>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 0.5rem; padding: 0.75rem 1rem; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px;">
                    <i class="fa-solid fa-shield-halved" style="color: #16a34a; font-size: 1rem;"></i>
                    <span style="font-size: 0.85rem; color: #15803d; font-weight: 600;">Tu link está totalmente seguro. Se guarda encriptado y solo vos podés verlo.</span>
                </div>
            </form>
        </div>

        <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid #f0f0f0; text-align: center;">
            <a href="https://support.google.com/calendar/answer/37648?hl=es#zippy=%2Cobtener-la-direcci%C3%B3n-secreta-en-formato-ical" target="_blank" 
               class="google-help-link"
               style="font-size: 0.9rem; color: #666; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 0.8rem 1.2rem; border-radius: 12px; transition: all 0.2s; background: #f8f9fa; border: 1.5px solid #eee;"
               onmouseover="this.style.background='#eee'; this.style.color='#000'; this.style.borderColor='#ccc'"
               onmouseout="this.style.background='#f8f9fa'; this.style.color='#666'; this.style.borderColor='#eee'">
                <i class="fa-regular fa-circle-question" style="font-size: 1.1rem;"></i>
                <span>¿Cómo obtener mi link privado de Google Calendar?</span>
            </a>
        </div>
        <style>
            @media (max-width: 600px) {
                .google-help-link {
                    flex-direction: column;
                    text-align: center;
                    padding: 1rem !important;
                    width: 100%;
                    box-sizing: border-box;
                }
                .google-help-link span {
                    white-space: normal;
                }
            }
        </style>
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

    function closeDayDetails() {
        document.getElementById('day-details-section').style.display = 'none';
        // Scroll back to absolute top as requested
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection
