@extends('layouts.app')

@section('title', 'Turnos - Admin')
@section('header_title', 'Gestión de Turnos')

@section('content')
<div class="flex flex-col gap-8">
    <!-- Agenda Completa -->
    <div class="neobrutalist-card" style="margin-bottom: 4rem;">
        <div style="background: white; border: 3px solid #000; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 6px 6px 0px #000; border-radius: 12px;">
            <h4 style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 800; text-transform: uppercase; color: #555;">Filtros de Búsqueda</h4>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 280px;">
                    <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">Identificar Paciente</label>
                    <input type="text" id="turnoSearch" placeholder="Buscar por nombre o apellido..." class="neobrutalist-input w-full" style="margin:0; width: 100%;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 700; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">Filtrar por Estado</label>
                    <select id="turnoFilter" class="neobrutalist-input w-full" style="margin:0; width: 100%;">
                        <option value="todos">Mostrar Todo</option>
                        <option value="pendiente">Solo Pendientes</option>
                        <option value="confirmado">Solo Confirmados</option>
                        <option value="cancelado">Solo Cancelados</option>
                        <option value="frecuente">Pacientes Frecuentes</option>
                        <option value="nuevo">Pacientes Nuevos</option>
                    </select>
                </div>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                <thead>
                    <tr style="border-bottom: 4px solid #000; background: #f0f0f0;" class="no-select">
                        <th style="padding: 1rem; text-align: left; cursor: pointer;" onclick="sortTable(0)">Fecha <i class="fa-solid fa-sort"></i></th>
                        <th style="padding: 1rem; text-align: left; cursor: pointer;" onclick="sortTable(1)">Paciente <i class="fa-solid fa-sort"></i></th>
                        <th style="padding: 1rem; text-align: left; cursor: pointer;" onclick="sortTable(2)">Estado <i class="fa-solid fa-sort"></i></th>
                        <th style="padding: 1rem; text-align: left;">Tipo</th>
                        <th style="padding: 1rem; text-align: left;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="turnosTableBody">
                    @foreach($appointments as $appt)
                        @php $tipo = $appt->user->paciente->tipo_paciente ?? 'nuevo'; @endphp
                        <tr style="border-bottom: 2px solid #eee;" class="turno-row" data-paciente="{{ strtolower($appt->user->nombre) }}" data-estado="{{ $appt->estado }}" data-tipo="{{ $tipo }}">
                            <td data-label="Fecha" style="padding: 1rem; font-weight: 700;">{{ $appt->fecha_hora->format('d/m H:i') }} hs</td>
                            <td data-label="Paciente" style="padding: 1rem;">{{ $appt->user->nombre }}</td>
                            <td data-label="Estado" style="padding: 1rem;">
                               <span class="no-select status-badge" style="font-weight: bold; background: {{ $appt->estado == 'confirmado' ? 'var(--color-verde)' : ($appt->estado == 'cancelado' ? 'var(--color-rosa)' : 'var(--color-amarillo)') }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px;">
                                   {{ ucfirst($appt->estado) }}
                               </span>
                            </td>
                            <td data-label="Tipo" style="padding: 1rem;">
                                @php $tipo = $appt->user->paciente->tipo_paciente ?? 'nuevo'; @endphp
                                <span class="no-select" style="font-weight: bold; background: {{ $tipo == 'frecuente' ? 'var(--color-verde)' : 'var(--color-amarillo)' }}; padding: 0.3rem 0.6rem; border: 2px solid #000; border-radius: 5px; text-transform: uppercase; font-size: 0.85rem;">
                                    {{ $tipo }}
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.5rem;">
                                    @if($appt->estado == 'pendiente')
                                        <form id="confirm-all-{{ $appt->id }}" action="{{ route('admin.appointments.confirm', $appt->id) }}" method="POST">
                                            @csrf
                                            <button type="button" class="neobrutalist-btn no-select" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: var(--color-verde);" onclick="confirmAction('confirm-all-{{ $appt->id }}', '¿Confirmar este turno?')">Confirmar</button>
                                        </form>
                                    @endif
                                    @if($appt->estado != 'cancelado')
                                        <form id="cancel-all-{{ $appt->id }}" action="{{ route('admin.appointments.cancel', $appt->id) }}" method="POST">
                                            @csrf
                                            <button type="button" class="neobrutalist-btn no-select" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: var(--color-lila);" onclick="confirmAction('cancel-all-{{ $appt->id }}', '¿Cancelar turno?')">Cancelar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination Controls -->
        <div id="paginationControls" style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem; align-items: center;">
            <button onclick="changePage(-1)" class="neobrutalist-btn bg-amarillo" id="prevPageBtn" style="padding: 0.5rem 1rem;">Anterior</button>
            <span id="pageIndicator" style="font-weight: 900; font-family: monospace;">Página 1</span>
            <button onclick="changePage(1)" class="neobrutalist-btn bg-amarillo" id="nextPageBtn" style="padding: 0.5rem 1rem;">Siguiente</button>
        </div>
    </div>
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.scripts')
<script>
    // Unified Filtering, Sorting and Pagination logic
    let currentPage = 1;
    const rowsPerPage = 10;
    const allRowsArr = Array.from(document.querySelectorAll('.turno-row'));

    function applyTableState() {
        const searchTerm = document.getElementById('turnoSearch').value.toLowerCase();
        const statusFilter = document.getElementById('turnoFilter').value;

        const filteredRows = allRowsArr.filter(row => {
            const paciente = row.dataset.paciente;
            const estado = row.dataset.estado;
            const tipo = row.dataset.tipo;
            const matchesSearch = paciente.includes(searchTerm);
            let matchesStatus = statusFilter === 'todos' || estado === statusFilter;
            if (statusFilter === 'frecuente' || statusFilter === 'nuevo') matchesStatus = tipo === statusFilter;
            
            return matchesSearch && matchesStatus;
        });

        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        allRowsArr.forEach(r => r.style.display = 'none');
        
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        filteredRows.slice(start, end).forEach(r => {
            r.style.display = '';
        });

        const pageIndicator = document.getElementById('pageIndicator');
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        if (pageIndicator) pageIndicator.innerText = `Página ${currentPage} de ${totalPages}`;
        
        if (prevBtn) {
            // [MODIFIED] Hide button if on page 1
            if (currentPage === 1) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'inline-block';
                prevBtn.disabled = false;
            }
        }
        
        if (nextBtn) {
             // [MODIFIED] Hide next button if on last page or if there's only 1 page
             if (currentPage >= totalPages || totalPages === 1) {
                 nextBtn.style.display = 'none'; // Using display none to completely hide it
             } else {
                 nextBtn.style.display = 'inline-block';
                 nextBtn.disabled = false;
             }
        }
    }

    function changePage(dir) {
        currentPage += dir;
        applyTableState();
    }

    document.getElementById('turnoSearch').addEventListener('input', () => {
        currentPage = 1;
        applyTableState();
    });
    document.getElementById('turnoFilter').addEventListener('change', () => {
        currentPage = 1;
        applyTableState();
    });

    let sortDirections = [true, true, true];
    window.sortTable = function(columnIndex) {
        const tableBody = document.getElementById('turnosTableBody');
        const direction = sortDirections[columnIndex] ? 1 : -1;

        allRowsArr.sort((a, b) => {
            const aText = a.children[columnIndex].innerText.trim();
            const bText = b.children[columnIndex].innerText.trim();
            return aText.localeCompare(bText, undefined, { numeric: true, sensitivity: 'base' }) * direction;
        });

        sortDirections[columnIndex] = !sortDirections[columnIndex];
        tableBody.innerHTML = '';
        allRowsArr.forEach(row => tableBody.appendChild(row));
        
        applyTableState();
    }

    window.addEventListener('resize', applyTableState);
    document.addEventListener('DOMContentLoaded', applyTableState);
</script>
@endsection
