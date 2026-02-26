@extends('layouts.app')

@section('title', 'Pacientes - Admin')
@section('header_title', 'Gestión de Pacientes')

@section('content')
<link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
<div class="flex flex-col gap-8 admin-content-padding">
    <style>
        @media (max-width: 768px) {
            .admin-content-padding {
                padding-left: 15px !important;
                padding-right: 15px !important;
                box-sizing: border-box;
            }
            /* Hide table and show cards */
            .patients-table {
                display: none;
            }
            .patients-cards {
                display: block;
            }
        }
        @media (min-width: 769px) {
            .patients-table {
                display: table;
                table-layout: fixed;
            }
            .patients-cards {
                display: none;
            }
        }
        .patient-card {
            border: 3px solid #000;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: white;
            box-shadow: 6px 6px 0px #000;
        }
        .patient-card .card-header {
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 0.5rem;
        }
        .patient-card .card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            align-items: center;
        }
        .patient-card .card-label {
            font-weight: bold;
            font-size: 0.8rem;
            color: #666;
            flex: 0 0 30%;
        }
        .patient-card .card-value {
            flex: 1;
            text-align: right;
        }
        .patient-card .actions-wrapper {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .patient-card .fixed-res-info {
            text-align: center;
            margin-bottom: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.75rem;
        }
        .patient-card .fixed-res-info .day {
            font-weight: 800;
            color: #000;
            font-size: 0.8rem;
            margin-bottom: 0.2rem;
        }
        .patient-card .fixed-res-info .time {
            font-size: 0.7rem;
            color: #444;
            margin-bottom: 0.2rem;
        }
        .patient-card .frequency {
            font-size: 0.6rem;
            background: #000;
            color: #fff;
            padding: 1px 6px;
            border-radius: 3px;
            font-weight: 800;
            text-transform: uppercase;
            white-space: nowrap;
            display: inline-block;
        }
    </style>
    <!-- Filtros y Búsqueda -->
    <div class="neobrutalist-card" style="background: #f0f0f0; margin-bottom: 2rem; padding: 1.5rem;">
        <form action="{{ route('admin.pacientes') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem; font-size: 0.9rem;">Buscar por nombre:</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej: Juan Perez..." class="neobrutalist-input" style="width: 100%; margin-bottom: 0;">
            </div>
            <div style="width: 200px;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem; font-size: 0.9rem;">Tipo de paciente:</label>
                <select name="type" class="neobrutalist-input" style="width: 100%; margin-bottom: 0;">
                    <option value="">Todos</option>
                    <option value="nuevo" {{ request('type') == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                    <option value="frecuente" {{ request('type') == 'frecuente' ? 'selected' : '' }}>Frecuente</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="neobrutalist-btn bg-amarillo" style="padding: 0.7rem 1.5rem;">
                    <i class="fa-solid fa-magnifying-glass"></i> <span class="hide-mobile">Filtrar</span>
                </button>
                <a href="{{ route('admin.pacientes') }}" class="neobrutalist-btn bg-white" style="padding: 0.7rem 1.5rem; text-decoration: none; color: #000;">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Listado de Pacientes -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
            <h3 style="margin: 0;"><i class="fa-solid fa-users"></i> Listado de Pacientes</h3>
            <div style="font-weight: 800; background: #000; color: #fff; padding: 0.3rem 0.8rem; border-radius: 5px; font-size: 0.9rem;">
                Total: {{ $patients->total() }}
            </div>
        </div>
        
        <div style="overflow-x: auto; width: 100%; border: 3px solid #000; box-shadow: 6px 6px 0px #000; background: white; margin-top: 1rem;">
            <table class="patients-table" style="width: 100%; border-collapse: collapse; background: white; margin: 0;">
                <thead>
                    <tr style="background: #000; color: #fff; border-bottom: 3px solid #fff;">
                        <th style="padding: 0.8rem; text-align: left; width: 25%;">
                             <a href="{{ route('admin.pacientes', array_merge(request()->all(), ['sort' => 'name', 'order' => request('order') == 'asc' ? 'desc' : 'asc'])) }}" style="color: #fff; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                                Nombre
                                @if(request('sort', 'name') == 'name')
                                    <i class="fa-solid fa-sort-{{ request('order', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fa-solid fa-sort" style="opacity: 0.3;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 0.8rem; text-align: left; width: 20%;">Email</th>
                        <th style="padding: 0.8rem; text-align: left; width: 15%;">Teléfono</th>
                        <th style="padding: 0.8rem; text-align: left; width: 10%;">Tipo</th>
                        <th style="padding: 0.8rem; text-align: center; color: #fff; width: 15%;">
                            Reserva Fija
                        </th>
                        <th style="padding: 0.8rem; text-align: center; width: 15%;">Acciones</th>
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
                            <td data-label="Reserva Fija" style="padding: 0.8rem; text-align: center; font-size: 0.85rem;">
                                @php
                                    $fixed = $patient->turnos->first();
                                @endphp
                                @if($fixed)
                                    <div class="fixed-res-mobile-align" style="font-weight: 800; color: #000; margin-bottom: 4px;">
                                        <span style="text-transform: capitalize;">{{ $fixed->fecha_hora->locale('es')->isoFormat('dddd') }}</span>
                                        <br>
                                        <span style="font-size: 0.8rem; color: #444;">{{ $fixed->fecha_hora->format('H:i') }} - {{ $fixed->fecha_hora->addMinutes(45)->format('H:i') }} hs</span>
                                    </div>
                                    <div class="fixed-res-mobile-align" style="display: flex; justify-content: center;">
                                        <span style="font-size: 0.65rem; background: #000; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800; text-transform: uppercase; white-space: nowrap;">
                                            {{ $fixed->frecuencia ?? 'Semanal' }}
                                        </span>
                                    </div>
                                @else
                                    <span style="color: #999; font-style: italic; font-size: 0.75rem;">Sin reserva fija</span>
                                @endif
                            </td>
                            <td style="padding: 0.8rem; text-align: center;">
                                <div class="action-buttons-container" style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                    <button class="neobrutalist-btn no-select bg-amarillo" style="padding: 0.2rem 0.6rem; font-size: 0.7rem; flex: 1; white-space: nowrap; justify-content: center;"
                                            onclick="openManageModal('{{ $patient->id }}', '{{ $patient->nombre }}', '{{ $patient->email }}', '{{ $patient->telefono ?? 'No registrado' }}', '{{ $patient->tipo_paciente }}', '{{ $patient->meet_link }}', '{{ $patient->paciente->precio_personalizado ?? 0 }}')">
                                        Gestionar
                                    </button>
                                    <button class="neobrutalist-btn bg-celeste" style="padding: 0.2rem 0.6rem; font-size: 0.7rem; flex: 1; white-space: nowrap; justify-content: center;"
                                            onclick='openDocumentsModal({{ $patient->id }}, "{{ $patient->nombre }}", @json($patient->documents))'>
                                        <i class="fa-solid fa-folder"></i> Docs
                                    </button>
                                    <a href="{{ route('admin.clinical-history.initialize', $patient->id) }}" 
                                       class="neobrutalist-btn bg-white" 
                                       style="padding: 0.2rem 0.6rem; font-size: 0.7rem; text-decoration: none; color: black; border: 2px solid #000; flex: 1; white-space: nowrap; justify-content: center;">
                                        <i class="fa-solid fa-file-medical"></i> Historia
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="patients-cards">
            @foreach($patients as $patient)
                <div class="patient-card">
                    <div class="card-header">{{ $patient->nombre }}</div>
                    <div class="card-row">
                        <span class="card-label">Email:</span>
                        <span class="card-value">{{ $patient->email }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Teléfono:</span>
                        <span class="card-value">{{ $patient->telefono ?? '-' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Tipo:</span>
                        <span class="card-value">
                            <span class="no-select" style="font-weight: bold; background: {{ $patient->tipo_paciente == 'frecuente' ? 'var(--color-verde)' : 'var(--color-amarillo)' }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px; text-transform: uppercase; font-size: 0.85rem;">
                                {{ ucfirst($patient->tipo_paciente) }}
                            </span>
                        </span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Reserva Fija:</span>
                        <span class="card-value">
                            @php
                                $fixed = $patient->turnos->first();
                            @endphp
                            @if($fixed)
                                <div class="fixed-res-info">
                                    <div class="day">{{ $fixed->fecha_hora->locale('es')->isoFormat('dddd') }}</div>
                                    <div class="time">{{ $fixed->fecha_hora->format('H:i') }} - {{ $fixed->fecha_hora->addMinutes(45)->format('H:i') }} hs</div>
                                    <div class="frequency">{{ $fixed->frecuencia ?? 'Semanal' }}</div>
                                </div>
                            @else
                                <span style="color: #999; font-style: italic; font-size: 0.75rem;">Sin reserva fija</span>
                            @endif
                        </span>
                    </div>
                    <div class="actions-wrapper">
                        <button class="neobrutalist-btn no-select bg-amarillo" style="padding: 0.5rem 1rem; font-size: 0.8rem; flex: 1; white-space: nowrap; justify-content: center;"
                                onclick="openManageModal('{{ $patient->id }}', '{{ $patient->nombre }}', '{{ $patient->email }}', '{{ $patient->telefono ?? 'No registrado' }}', '{{ $patient->tipo_paciente }}', '{{ $patient->meet_link }}', '{{ $patient->paciente->precio_personalizado ?? 0 }}')">
                            Gestionar
                        </button>
                        <button class="neobrutalist-btn bg-celeste" style="padding: 0.5rem 1rem; font-size: 0.8rem; flex: 1; white-space: nowrap; justify-content: center;"
                                onclick='openDocumentsModal({{ $patient->id }}, "{{ $patient->nombre }}", @json($patient->documents))'>
                            <i class="fa-solid fa-folder"></i> Docs
                        </button>
                        <a href="{{ route('admin.clinical-history.initialize', $patient->id) }}" 
                           class="neobrutalist-btn bg-white" 
                           style="padding: 0.5rem 1rem; font-size: 0.8rem; text-decoration: none; color: black; border: 2px solid #000; flex: 1; white-space: nowrap; justify-content: center;">
                            <i class="fa-solid fa-file-medical"></i> Historia
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div style="margin-top: 2rem;">
            {{ $patients->links('vendor.pagination.neobrutalist') }}
        </div>
    </div>
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.manage-modal')
@include('dashboard.admin.partials.documents-modal')
@include('dashboard.admin.partials.scripts')
@endsection
