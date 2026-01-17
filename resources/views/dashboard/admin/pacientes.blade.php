@extends('layouts.app')

@section('title', 'Pacientes - Lic. Nazarena De Luca')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Listado de Pacientes -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
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
                                    onclick="openManageModal('{{ $patient->id }}', '{{ $patient->nombre }}', '{{ $patient->email }}', '{{ $patient->telefono ?? 'No registrado' }}', '{{ $patient->tipo_paciente }}', '{{ $patient->meet_link }}')">
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
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.manage-modal')
@include('dashboard.admin.partials.scripts')
@endsection
