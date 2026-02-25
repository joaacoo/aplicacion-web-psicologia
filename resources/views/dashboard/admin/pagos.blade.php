@extends('layouts.app')

@section('title', 'Pagos - Admin')
@section('header_title', 'Pagos y Cobros')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Validaciones de Pago Pendientes -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
        <h3><i class="fa-solid fa-money-bill-transfer"></i> Validaciones de Pago Pendientes</h3>
        
        @php
            $pendingPayments = $appointments->filter(fn($a) => $a->payment && $a->payment->estado == 'pendiente');
        @endphp

        @if($pendingPayments->isEmpty())
            <p>No hay pagos nuevos para revisar.</p>
        @else
            <!-- Responsive Container: Horizontal on desktop, Vertical on mobile -->
            <div id="payment-cards-container" style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 1.5rem; padding-top: 0.5rem; flex-wrap: wrap;">
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
                                            onclick="confirmAction('verify-payment-{{ $appt->payment->id }}', '¿Confirmás que el pago es válido?', 'Verificando pago...')">
                                        <i class="fa-solid fa-check"></i> Validar
                                    </button>
                                </form>
                                <form id="reject-payment-{{ $appt->payment->id }}" action="{{ route('admin.payments.reject', $appt->payment->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <button type="button" class="neobrutalist-btn bg-lila w-full no-select" style="padding: 8px; font-size: 0.75rem;" 
                                            onclick="confirmAction('reject-payment-{{ $appt->payment->id }}', '¿Estás seguro/a que querés rechazar este comprobante?', 'Rechazando pago...')">
                                        <i class="fa-solid fa-times"></i> Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Scroll Hint (only shown if scrollable) -->
            <p id="scroll-hint-pagos" style="font-size: 0.8rem; color: #666; text-align: center; margin-top: 0.5rem; display: none;">
                <i class="fa-solid fa-arrows-left-right"></i> Deslizá para ver más pagos pendientes
            </p>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const container = document.getElementById('payment-cards-container');
                    const hint = document.getElementById('scroll-hint-pagos');
                    if (container && hint) {
                        // Check if container has horizontal scroll
                        if (container.scrollWidth > container.clientWidth) {
                            hint.style.display = 'block';
                        }
                    }
                });
            </script>
        @endif
    </div>
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.scripts')
@endsection
