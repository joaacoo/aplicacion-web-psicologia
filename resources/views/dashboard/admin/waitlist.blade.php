@extends('layouts.app')

@section('title', 'Lista de Espera - Admin')
@section('header_title', 'Lista de Espera')

@section('content')
<div class="flex flex-col gap-8" style="max-width: 100%; margin: 0 auto;">
    <style>
        @media (max-width: 768px) {
            .flex.flex-col.gap-8 {
                padding: 0 0.5rem !important;
                max-width: 100% !important;
            }
        }
    </style>
    <!-- Lista de Espera (Global) -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
        <style>
            @media (max-width: 768px) {
                .waitlist-header h3 {
                    font-size: 1.2rem !important;
                    text-align: center !important;
                }
                .waitlist-card-container {
                    padding: 0 !important;
                    margin: 0 auto !important;
                    max-width: 100% !important;
                }
                .neobrutalist-card {
                    padding: 1rem !important;
                    margin-left: auto !important;
                    margin-right: auto !important;
                    max-width: 100% !important;
                }
            }
            @media (max-width: 480px) {
                .waitlist-header h3 {
                    font-size: 1rem !important;
                }
            }
        </style>
        <div class="waitlist-header" style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clock"></i> Lista de Espera (Global)</h3>
        </div>
        
        <div class="waitlist-card-container" style="overflow-x: auto; padding: 0;">
            <style>
                @media (max-width: 768px) {
                    .waitlist-table {
                        border: none !important;
                        box-shadow: none !important;
                        background: transparent !important;
                        display: block !important;
                    }
                    .waitlist-table thead { 
                        display: none !important; 
                    }
                    .waitlist-table tbody {
                        display: block !important;
                    }
                    .waitlist-table tr {
                        display: block !important;
                        border: 3px solid #000 !important;
                        margin-bottom: 1rem !important;
                        margin-left: auto !important;
                        margin-right: auto !important;
                        border-radius: 15px !important;
                        padding: 1rem !important;
                        background: white !important;
                        box-shadow: 4px 4px 0px #000 !important;
                        width: 100% !important;
                        max-width: 100% !important;
                        box-sizing: border-box !important;
                    }
                    .waitlist-table td {
                        display: block !important;
                        padding: 1rem 0.5rem 0.75rem 0.5rem !important;
                        border: none !important;
                        border-bottom: 2px solid #eee !important;
                        width: 100% !important;
                        text-align: left !important;
                        position: relative;
                        font-size: 0.9rem !important;
                        line-height: 1.5 !important;
                        min-height: 3rem !important;
                        box-sizing: border-box !important;
                    }
                    .waitlist-table tr > td:first-child {
                        padding-top: 0.5rem !important;
                    }
                    .waitlist-table td:last-child { 
                        border-bottom: none !important;
                        text-align: center !important;
                    }
                    .waitlist-table td:before {
                        content: attr(data-label) ": ";
                        font-weight: 900 !important;
                        color: #000 !important;
                        text-transform: uppercase !important;
                        font-size: 0.7rem !important;
                        display: block !important;
                        margin-bottom: 0.5rem !important;
                        margin-top: 0 !important;
                        padding-bottom: 0 !important;
                        padding-top: 0 !important;
                        letter-spacing: 0.5px !important;
                        line-height: 1.4 !important;
                        border-bottom: none !important;
                        position: relative !important;
                        z-index: 1 !important;
                        text-align: left !important;
                    }
                    .waitlist-table td {
                        color: #000 !important;
                    }
                    .waitlist-table td:not([data-label="Acciones"]) {
                        font-size: 0.9rem !important;
                        line-height: 1.6 !important;
                    }
                    .waitlist-table .waitlist-text-content {
                        display: block !important;
                        width: 100% !important;
                        color: #000 !important;
                        margin-top: 0.8rem !important;
                        padding-top: 0 !important;
                        clear: both !important;
                        position: relative !important;
                        z-index: 0 !important;
                    }
                    .waitlist-table td > span:not(.waitlist-link span):not(.waitlist-text-content) {
                        display: inline-block !important;
                        width: auto !important;
                    }
                    .waitlist-table td > *:not(:before) {
                        margin-top: 0.5rem !important;
                        display: block !important;
                    }
                    .waitlist-table td > a:not(.waitlist-link),
                    .waitlist-table td > form {
                        margin-top: 0.5rem !important;
                        display: block !important;
                    }
                    .waitlist-table td > .waitlist-link {
                        margin-top: 0.5rem !important;
                    }
                    .waitlist-table td.waitlist-date-cell,
                    .waitlist-table td.waitlist-name-cell {
                        font-size: 0.95rem !important;
                    }
                    .waitlist-table td a {
                        word-break: break-word !important;
                        padding: 0.3rem 0 !important;
                        display: inline-flex !important;
                        align-items: center !important;
                        width: auto !important;
                        max-width: 100% !important;
                        gap: 5px !important;
                    }
                    .waitlist-table td a i {
                        flex-shrink: 0 !important;
                    }
                    .waitlist-table td a span {
                        display: inline !important;
                        word-break: break-word !important;
                    }
                    .waitlist-table td > div:not(.preference-wrapper) {
                        width: 100% !important;
                        display: block !important;
                    }
                    .waitlist-table td > form {
                        display: inline-block !important;
                        width: auto !important;
                    }
                    .waitlist-table .neobrutalist-btn {
                        min-width: 44px !important;
                        min-height: 44px !important;
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        margin: 0 auto !important;
                    }
                    .waitlist-table td[data-label="Preferencia"] > div,
                    .waitlist-table .preference-wrapper {
                        width: 100% !important;
                        display: flex !important;
                        flex-direction: column !important;
                        gap: 0.5rem !important;
                    }
                    .waitlist-table td[data-label="Preferencia"] .preference-wrapper > div:first-child {
                        display: flex !important;
                        flex-wrap: wrap !important;
                        gap: 5px !important;
                    }
                    .waitlist-table td[data-label="Preferencia"] span {
                        font-size: 0.65rem !important;
                        padding: 4px 8px !important;
                        display: inline-block !important;
                        width: auto !important;
                    }
                    .waitlist-table td.waitlist-preference-cell > div > div:last-child {
                        font-size: 0.75rem !important;
                        padding: 0.4rem !important;
                        line-height: 1.4 !important;
                    }
                    .waitlist-table td[data-label="Email"] a,
                    .waitlist-table td[data-label="Teléfono"] a,
                    .waitlist-table .waitlist-link {
                        flex-wrap: wrap !important;
                        word-break: break-word !important;
                        display: inline-flex !important;
                        align-items: center !important;
                        width: auto !important;
                        gap: 5px !important;
                    }
                    .waitlist-table .waitlist-link span {
                        word-break: break-word !important;
                        display: inline !important;
                    }
                    .waitlist-table td[data-label="Fecha/Hora"],
                    .waitlist-table td[data-label="Paciente"] {
                        font-size: 0.95rem !important;
                        font-weight: 700 !important;
                    }
                    .waitlist-table form {
                        display: inline-block !important;
                        margin: 0 !important;
                    }
                    .waitlist-table td.waitlist-phone-cell,
                    .waitlist-table td.waitlist-email-cell {
                        font-size: 0.9rem !important;
                    }
                    .waitlist-table td[data-label="Preferencia"] .preference-wrapper > div:last-child {
                        display: flex !important;
                        align-items: flex-start !important;
                        gap: 5px !important;
                    }
                    .waitlist-table td[data-label="Preferencia"] .preference-wrapper > div:last-child i {
                        flex-shrink: 0 !important;
                        margin-top: 2px !important;
                    }
                }
                @media (max-width: 480px) {
                    .waitlist-table tr {
                        padding: 0.75rem !important;
                        border-radius: 12px !important;
                        margin-bottom: 0.75rem !important;
                    }
                    .waitlist-table td {
                        padding: 0.6rem 0 !important;
                    }
                    .waitlist-table td:before {
                        font-size: 0.65rem !important;
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
                            <td data-label="Fecha/Hora" style="padding: 0.8rem; font-weight: 700;" class="waitlist-date-cell">
                                <span class="waitlist-text-content">
                                @if($entry->fecha_especifica)
                                    {{ \Carbon\Carbon::parse($entry->fecha_especifica)->format('d/m') }}
                                @else
                                    Global
                                @endif
                                @if($entry->hora_inicio)
                                    - {{ \Carbon\Carbon::parse($entry->hora_inicio)->format('H:i') }} hs
                                @endif
                                </span>
                            </td>
                            <td data-label="Paciente" style="padding: 0.8rem; font-weight: 900;" class="waitlist-name-cell">
                                <span class="waitlist-text-content">{{ $entry->name }}</span>
                            </td>
                            <td data-label="Teléfono" style="padding: 0.8rem; font-family: 'Inter', sans-serif;" class="waitlist-phone-cell">
                                @php
                                    $fechaMsg = $entry->fecha_especifica ? \Carbon\Carbon::parse($entry->fecha_especifica)->format('d/m') : 'próximamente';
                                    $horaMsg = $entry->hora_inicio ? \Carbon\Carbon::parse($entry->hora_inicio)->format('H:i') : '';
                                    // Trim name and attach comma directly
                                    $wpMessage = "Hola " . trim($entry->name) . ", se liberó un lugar para el " . $fechaMsg . ($horaMsg ? " a las " . $horaMsg : "") . ". ¿Te gustaría tomar el turno? Saludos, Lic. Nazarena De Luca.";
                                    $wpUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $entry->phone) . "?text=" . urlencode($wpMessage);
                                @endphp
                                @if($entry->phone)
                                    <a href="{{ $wpUrl }}" target="_blank" style="color: #25D366; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.85rem;" class="waitlist-link">
                                        <i class="fa-brands fa-whatsapp"></i> <span>{{ $entry->phone }}</span>
                                    </a>
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td data-label="Email" style="padding: 0.8rem; font-family: 'Inter', sans-serif;" class="waitlist-email-cell">
                                @php
                                    $displayEmail = $entry->email ?? ($entry->user ? $entry->user->email : null);
                                @endphp
                                @if($displayEmail)
                                    <a href="mailto:{{ $displayEmail }}?subject=Turno disponible - Lic. Nazarena De Luca&body={{ urlencode($wpMessage) }}" target="_blank" style="color: #ff4d4d; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.85rem;" class="waitlist-link">
                                        <i class="fa-solid fa-envelope"></i> <span>{{ $displayEmail }}</span>
                                    </a>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td data-label="Preferencia" style="padding: 0.8rem;" class="waitlist-preference-cell">
                                <div style="display: flex; flex-direction: column; gap: 0.4rem;" class="preference-wrapper">
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <span style="background: var(--color-celeste); padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                                            <i class="fa-solid {{ $entry->modality == 'Virtual' ? 'fa-video' : ($entry->modality == 'Presencial' ? 'fa-house-user' : 'fa-door-open') }}"></i> {{ $entry->modality }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.85rem; font-weight: 700; color: #000; background: #fffadc; padding: 0.5rem; border: 1px dashed #000; border-radius: 5px; word-break: break-word; display: flex; align-items: flex-start; gap: 5px;" class="availability-text">
                                        <i class="fa-solid fa-clock" style="flex-shrink: 0; margin-top: 2px;"></i> 
                                        <span style="flex: 1; word-break: break-word;">
                                            @php
                                                $availabilityText = $entry->availability ?? 'No especificó disponibilidad';
                                                // Eliminar (PRESENCIAL) y (2026-02-20) o cualquier fecha en formato (YYYY-MM-DD)
                                                $availabilityText = preg_replace('/\s*\(PRESENCIAL\)\s*/i', '', $availabilityText);
                                                $availabilityText = preg_replace('/\s*\([0-9]{4}-[0-9]{2}-[0-9]{2}\)\s*/', '', $availabilityText);
                                                $availabilityText = trim($availabilityText);
                                            @endphp
                                            {{ $availabilityText }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Acciones" style="padding: 0.8rem; text-align: center; width: 150px;">
                                    <div style="display: flex; gap: 5px; justify-content: center; align-items: center;">
                                        <button type="button" class="neobrutalist-btn bg-verde" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" 
                                                onclick="openRecoverAssignModal({{ $entry->id }}, '{{ addslashes($entry->name) }}', '{{ addslashes($entry->modality) }}')"
                                                title="Agendar recuperación">
                                            <i class="fa-solid fa-calendar-plus"></i>
                                        </button>
                                        <form id="delete-waitlist-{{ $entry->id }}" action="{{ route('admin.waitlist.destroy', $entry->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="confirmAction('delete-waitlist-{{ $entry->id }}', '¿Remover de la lista de espera?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: #666;">No hay nadie en la lista de espera.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Agendar Recuperación -->
<div id="recoverAssignModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="neobrutalist-card" style="background: #fff; width: 100%; max-width: 500px; padding: 2rem; position: relative;">
        <button onclick="closeRecoverAssignModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h3 style="margin-top: 0; border-bottom: 3px solid #000; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-calendar-plus"></i> Agendar Recuperación
        </h3>
        <form action="{{ route('admin.appointments.storeRecovery') }}" method="POST">
            @csrf
            <input type="hidden" id="recover_waitlist_id" name="waitlist_id">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Paciente</label>
                <input type="text" id="recover_patient_name" readonly style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px; background: #eee;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Fecha</label>
                    <input type="date" name="fecha" required style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Hora</label>
                    <input type="time" name="hora" required style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Modalidad</label>
                <select name="modalidad" id="recover_modalidad" required style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px;">
                    <option value="virtual">Virtual</option>
                    <option value="presencial">Presencial</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeRecoverAssignModal()" class="neobrutalist-btn bg-white" style="padding: 0.5rem 1rem;">Cancelar</button>
                <button type="submit" class="neobrutalist-btn bg-verde" style="padding: 0.5rem 1rem;">Confirmar y Agendar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRecoverAssignModal(id, name, modality) {
        console.log('Opening recovery modal for:', id, name, modality);
        
        const idInput = document.getElementById('recover_waitlist_id');
        const nameInput = document.getElementById('recover_patient_name');
        const modal = document.getElementById('recoverAssignModal');
        const select = document.getElementById('recover_modalidad');

        if (!idInput || !nameInput || !modal || !select) {
            console.error('Modal elements missing');
            return;
        }

        idInput.value = id;
        nameInput.value = name;
        modal.style.display = 'flex';
        
        if (modality && modality.toLowerCase().includes('virtual')) select.value = 'virtual';
        else if (modality && modality.toLowerCase().includes('presencial')) select.value = 'presencial';
    }

    function closeRecoverAssignModal() {
        document.getElementById('recoverAssignModal').style.display = 'none';
    }
</script>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.scripts')
@endsection
