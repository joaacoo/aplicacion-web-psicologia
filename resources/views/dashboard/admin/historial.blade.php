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
        <div style="border: 3px solid #000; border-radius: 12px; overflow: hidden; background: white; box-shadow: 4px 4px 0px rgba(0,0,0,0.1);">
            @if($activityLogs->count() > 0)
                <div style="max-height: 450px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            @foreach($activityLogs as $log)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td data-label="Fecha" style="padding: 1rem 0.8rem; font-size: 0.85rem; color: #666; width: 140px; font-weight: 600;">
                                        {{ $log->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m H:i') }} hs
                                    </td>
                                    <td data-label="Acción" style="padding: 1rem 0.8rem; font-size: 0.95rem; line-height: 1.4;">
                                        <span style="display: inline-block; text-transform: uppercase; font-size: 0.7rem; background: #f0f0f0; padding: 0.15rem 0.4rem; border-radius: 4px; margin-right: 0.6rem; font-weight: 800; border: 1px solid #ddd; color: #444;">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                        <span style="color: #333; font-weight: 500;">{{ $log->description }}</span>
                                    </td>
                                    <td style="padding: 1rem 0.8rem; text-align: right; width: 100px;">
                                        @php
                                            $revertibleActions = ['pago_verificado', 'pago_rechazado', 'turno_cancelado', 'turno_confirmado'];
                                        @endphp
                                        @if(in_array($log->action, $revertibleActions))
                                            <button class="neobrutalist-btn" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: #fff; border-width: 2px;" onclick="openRevertModal({{ $log->id }}, '{{ str_replace('_', ' ', $log->action) }}')">
                                                Revertir
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 4rem 2rem; text-align: center; background: #fdfdfd;">
                    <div style="background: #f0f0f0; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px dashed #ccc;">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #bbb; font-size: 1.5rem;"></i>
                    </div>
                    <h4 style="margin: 0; font-size: 1.1rem; color: #333; font-family: 'Syne', sans-serif; font-weight: 700;">Sin actividad</h4>
                    <p style="margin: 0.5rem 0 0; color: #888; font-size: 0.9rem; font-family: 'Manrope', sans-serif;">No hay acciones registradas en el historial por el momento.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.revert-modal')
@include('dashboard.admin.partials.scripts')
@endsection
