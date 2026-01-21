@extends('layouts.app')

@section('title', 'Historial - Lic. Nazarena De Luca')
@section('header_title', 'Historial de Acciones')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Historial de Acciones (Activity Log) -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
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

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.revert-modal')
@include('dashboard.admin.partials.scripts')
@endsection
