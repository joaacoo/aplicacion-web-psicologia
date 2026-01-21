@extends('layouts.app')

@section('title', 'Lista de Espera - Admin')
@section('header_title', 'Lista de Espera')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Lista de Espera (Global) -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 4rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-clock"></i> Lista de Espera (Global)</h3>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border: 3px solid #000; box-shadow: 6px 6px 0px #000;">
                <thead>
                    <tr style="background: #000; color: #fff;">
                        <th style="padding: 0.8rem; text-align: left;">Fecha/Hora</th>
                        <th style="padding: 0.8rem; text-align: left;">Paciente</th>
                        <th style="padding: 0.8rem; text-align: left;">Teléfono</th>
                        <th style="padding: 0.8rem; text-align: left;">Preferencia</th>
                        <th style="padding: 0.8rem; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitlist ?? [] as $entry)
                        <tr style="border-bottom: 2px solid #000;">
                            <td style="padding: 0.8rem; font-weight: 700;">
                                @if($entry->fecha_especifica)
                                    {{ \Carbon\Carbon::parse($entry->fecha_especifica)->format('d/m') }}
                                @else
                                    Global
                                @endif
                                @if($entry->hora_inicio)
                                    - {{ \Carbon\Carbon::parse($entry->hora_inicio)->format('H:i') }} hs
                                @endif
                            </td>
                            <td style="padding: 0.8rem; font-weight: 900;">{{ $entry->name }}</td>
                            <td style="padding: 0.8rem;">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $entry->phone) }}" target="_blank" style="color: #25D366; font-weight: 800; text-decoration: none;">
                                    <i class="fa-brands fa-whatsapp"></i> {{ $entry->phone }}
                                </a>
                            </td>
                            <td style="padding: 0.8rem;">
                                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                                    <span style="background: var(--color-celeste); padding: 2px 8px; border: 2px solid #000; border-radius: 6px; font-size: 0.75rem; font-weight: 800; width: fit-content; text-transform: uppercase;">
                                        {{ $entry->modality }}
                                    </span>
                                    <div style="font-size: 0.9rem; font-weight: 700; color: #000; background: #fffadc; padding: 0.5rem; border: 1px dashed #000; border-radius: 5px;">
                                        <i class="fa-solid fa-clock"></i> {{ $entry->availability ?? 'No especificó disponibilidad general' }}
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.8rem; text-align: right;">
                                <form id="delete-waitlist-{{ $entry->id }}" action="{{ route('admin.waitlist.destroy', $entry->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="confirmAction('delete-waitlist-{{ $entry->id }}', '¿Remover de la lista de espera?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: #666;">No hay nadie en la lista de espera.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.scripts')
@endsection
