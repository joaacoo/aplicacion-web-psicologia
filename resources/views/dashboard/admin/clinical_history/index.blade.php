@extends('layouts.app')

@section('content')
<style>
    /* Remove all focus effects for clinical history inputs */
    .clinical-history-input:focus,
    .clinical-history-input:active {
        outline: none !important;
        border-color: #000 !important;
        box-shadow: none !important;
        background-color: white !important;
    }
    
    /* Remove yellow autofill background */
    .clinical-history-input:-webkit-autofill,
    .clinical-history-input:-webkit-autofill:hover,
    .clinical-history-input:-webkit-autofill:focus,
    .clinical-history-input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        box-shadow: 0 0 0 30px white inset !important;
    }
    
    /* Mobile-responsive title */
    @media (max-width: 768px) {
        .clinical-history-title {
            font-size: 1.5rem !important;
        }
    }
</style>
<div class="container-fluid" style="padding: 2rem;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div style="margin-bottom: 1.5rem;">
            <h2 class="clinical-history-title" style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 2rem; margin-bottom: 0.5rem;">Historia Clínica</h2>
            <p style="font-family: 'Inter', sans-serif; font-size: 1.1rem; color: #555;">Paciente: <strong>{{ optional($paciente->user)->name ?? 'N/A' }}</strong> ({{ optional($paciente->user)->email ?? 'N/A' }})</p>
        </div>
        <div class="d-flex gap-4 flex-wrap">
            <a href="{{ route('admin.pacientes') }}" class="neobrutalist-btn bg-white" style="text-decoration: none; color: black; border: 3px solid #000; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('admin.clinical-history.export-pdf', $paciente->id) }}" class="neobrutalist-btn" style="text-decoration: none; color: white; background: #dc2626; border: 3px solid #000; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-file-pdf"></i> Descargar Historial Completo (PDF)
            </a>
        </div>
    </div>



    @if ($errors->any())
        <div class="neobrutalist-card mb-4" style="background: #fca5a5; border: 3px solid #000; padding: 1rem;">
            <strong>⚠️ Error:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Filters Form -->
    <div class="neobrutalist-card mb-5" style="background: #f0fdf4; border: 3px solid #000; box-shadow: 4px 4px 0px #000; padding: 2rem; margin-top: 2rem;">
        <h4 style="font-family: 'Syne', sans-serif; font-weight: 700; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-magnifying-glass"></i> Buscar en Historial
        </h4>
        
        <style>
            .search-input-override:focus {
                background-color: #ffffff !important; /* Remove yellow */
                outline: none !important; /* Remove blue ring */
                border-color: #000 !important;
                box-shadow: 4px 4px 0px #000 !important;
            }
        </style>
        <form action="{{ route('admin.clinical-history.search', $paciente->id) }}" method="GET" class="row g-3">
            
            <!-- Keyword Search -->
            <div class="col-12 col-md-4">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">Palabra Clave</label>
                <input 
                    type="text" 
                    name="search" 
                    class="neobrutalist-input w-100 search-input-override" 
                    placeholder="Ej: ansiedad, terapia..." 
                    value="{{ request('search') }}"
                    style="padding: 0.6rem; font-size: 0.9rem;"
                >
            </div>

            <!-- Date Range: From -->
            <div class="col-12 col-md-3">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">Desde</label>
                <input 
                    type="date" 
                    name="date_from" 
                    class="neobrutalist-input w-100 search-input-override" 
                    value="{{ request('date_from') }}"
                    style="padding: 0.6rem; font-size: 0.9rem;"
                    onclick="this.showPicker()"
                >
            </div>

            <!-- Date Range: To -->
            <div class="col-12 col-md-3">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">Hasta</label>
                <input 
                    type="date" 
                    name="date_to" 
                    class="neobrutalist-input w-100 search-input-override" 
                    value="{{ request('date_to') }}"
                    style="padding: 0.6rem; font-size: 0.9rem;"
                    onclick="this.showPicker()"
                >
            </div>

            <!-- Turno Type Filter -->
            <div class="col-12 col-md-2">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">Tipo</label>
                <select name="tipo" class="neobrutalist-input w-100 search-input-override" style="padding: 0.6rem; font-size: 0.9rem;">
                    <option value="">Todas</option>
                    <option value="presencial" {{ request('tipo') === 'presencial' ? 'selected' : '' }}>Presencial</option>
                    <option value="virtual" {{ request('tipo') === 'virtual' ? 'selected' : '' }}>Virtual</option>
                </select>
            </div>

            <!-- Search Buttons -->
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="neobrutalist-btn" style="background: #000; color: #fff; border: 3px solid #000; padding: 0.6rem 1.2rem; font-weight: 700;">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
                <a href="{{ route('admin.clinical-history.index', $paciente->id) }}" class="neobrutalist-btn bg-white" style="border: 3px solid #000; text-decoration: none; padding: 0.6rem 1.2rem; font-weight: 700;">
                    <i class="fa-solid fa-redo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Turnos List -->
    <div class="row" style="margin-top: 3rem;">
        <div class="col-12">
            <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 2rem; padding-bottom: 0.5rem; border-bottom: 3px solid #000;">📋 Sesiones del Paciente</h3>

            @if($turnos->isEmpty())
                <div class="neobrutalist-card text-center p-5" style="background: #f3f4f6; border: 3px solid #000; box-shadow: 4px 4px 0px #000;">
                    <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #6b7280; margin-bottom: 1rem;"></i>
                    <p style="font-size: 1.1rem; color: #374151; font-weight: 600; margin: 0;">No hay turnos registrados para este paciente.</p>
                </div>
            @else
                <style>
                    /* Session Card Layout */
                    .session-header-container {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        gap: 2rem;
                        border-bottom: 2px solid #eee;
                        padding-bottom: 1.5rem;
                        margin-bottom: 1.5rem;
                    }
                    
                    .session-date-badges {
                        display: flex;
                        flex-direction: column;
                        gap: 1rem;
                        flex: 1;
                    }
                    
                    .session-badges {
                        display: flex;
                        gap: 1rem;
                        flex-wrap: wrap;
                    }
                    
                    .session-actions {
                        display: flex;
                        justify-content: flex-end;
                    }
                    
                    /* Mobile Responsive */
                    @media (max-width: 768px) {
                        .session-header-container {
                            flex-direction: column;
                            gap: 1.5rem;
                        }
                        
                        .session-actions {
                            width: 100%;
                            justify-content: stretch;
                        }
                        
                        .session-actions button {
                            width: 100%;
                            padding: 0.75rem 1rem;
                        }
                    }
                </style>
                <div class="d-flex flex-column">
                    @foreach($turnos as $turno)
                        <div class="neobrutalist-card bg-white" style="border: 3px solid #000; box-shadow: 6px 6px 0px #000; padding: 2rem; position: relative; transition: transform 0.2s; margin-bottom: 3rem;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            
                            <!-- Session Header Container -->
                            <div class="session-header-container">
                                
                                <!-- Left Section: Date/Time and Badges -->
                                <div class="session-date-badges">
                                    <span style="font-weight: 900; font-size: 1.3rem; display: block;">
                                        📅 {{ \Carbon\Carbon::parse($turno->fecha_hora)->format('d/m/Y H:i') }}
                                    </span>
                                    
                                    <div class="session-badges">
                                        @if($turno->modalidad)
                                            <span class="badge" style="background: {{ $turno->modalidad === 'presencial' ? '#dbeafe' : ($turno->modalidad === 'virtual' ? '#fce7f3' : '#e0f2fe') }}; color: {{ $turno->modalidad === 'presencial' ? '#1e40af' : ($turno->modalidad === 'virtual' ? '#be185d' : '#0369a1') }}; padding: 0.4rem 0.8rem; border: 2px solid #000; font-weight: 700;">
                                                {{ ucfirst($turno->modalidad) }}
                                            </span>
                                        @else
                                            <span class="badge" style="background: #f3f4f6; color: #6b7280; padding: 0.4rem 0.8rem; border: 2px solid #000; font-weight: 600;">
                                                Sin especificar
                                            </span>
                                        @endif
                                        
                                        <span class="badge" style="background: #fef3c7; color: #000; padding: 0.4rem 0.8rem; border: 2px solid #000; font-weight: 700;">
                                            {{ ucfirst($turno->estado) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Right Section: Action Button -->
                                <div class="session-actions">
                                    @if($turno->clinicalHistory)
                                        <button 
                                            data-content="{{ $turno->clinicalHistory->content }}"
                                            onclick="openEditModal({{ $turno->id }}, this.getAttribute('data-content'))" 
                                            class="neobrutalist-btn" style="padding: 0.75rem 1.5rem; font-size: 0.9rem; background: #fef9c3; border: 2px solid #000; white-space: nowrap;">
                                            <i class="fa-solid fa-pen"></i> Editar Nota
                                        </button>
                                    @else
                                        <button onclick="openAddModal({{ $turno->id }})" 
                                            class="neobrutalist-btn" style="padding: 0.75rem 1.5rem; font-size: 0.9rem; background: #bbf7d0; border: 2px solid #000; white-space: nowrap;">
                                            <i class="fa-solid fa-plus"></i> Agregar Nota
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Note Content (if exists) -->
                            @if($turno->clinicalHistory)
                                <div style="white-space: pre-wrap; font-family: 'Inter', sans-serif; line-height: 1.6; color: #1f2937; background: #f9fafb; padding: 1rem; border-radius: 8px; border: 2px solid #e5e7eb;">
                                    {{ $turno->clinicalHistory->content }}
                                </div>
                                
                                @if($turno->clinicalHistory->created_at->diffInSeconds($turno->clinicalHistory->updated_at) > 1)
                                    <p style="font-size: 0.85rem; color: #ea580c; margin-top: 0.5rem; font-weight: 600;">
                                        ✏️ Editada: {{ $turno->clinicalHistory->updated_at->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                            @else
                                <p style="color: #999; font-style: italic; margin: 0;">Sin nota clínica registrada para esta sesión.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div id="addNoteModal" class="confirm-modal-overlay" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; overflow-y: auto; padding: 1rem;">
    <div class="confirm-modal bg-white" style="width: 90%; max-width: 600px; border: 3px solid #000; box-shadow: 8px 8px 0px #000; padding: 1.5rem; border-radius: 15px; margin: auto;">
        <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 1rem;">Agregar Nota Clínica</h3>
        
        <form id="addNoteForm" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="addContent" class="form-label" style="font-weight: 700;">Contenido de la Nota</label>
                <textarea 
                    id="addContent"
                    name="content" 
                    class="neobrutalist-input clinical-history-input w-100" 
                    rows="8" 
                    required 
                    style="min-height: 200px; resize: vertical; font-size: 16px; padding: 0.75rem;" 
                    placeholder="Escribí aquí las observaciones de la sesión..."></textarea>
            </div>

            <div class="alert alert-info" style="font-size: 0.75rem; border: 2px solid #000; background: #e0f2fe; color: #000; padding: 0.6rem;">
                <i class="fa-solid fa-shield-halved"></i> <strong>Seguro y Encriptado.</strong><br>
                Solo vos podés leer esto. Si perdés tu clave de acceso, estas notas serán irrecuperables.
            </div>

            <div class="d-flex gap-2 justify-content-end mt-3">
                <button type="button" onclick="closeAddModal()" class="neobrutalist-btn bg-white" style="border: 2px solid #000;">Cancelar</button>
                <button type="submit" class="neobrutalist-btn" style="background: #000; color: white; border: 2px solid #000; padding: 0.75rem 1.5rem; font-weight: 700;">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Nota
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Note Modal -->
<div id="editNoteModal" class="confirm-modal-overlay" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; overflow-y: auto; padding: 1rem;">
    <div class="confirm-modal bg-white" style="width: 90%; max-width: 600px; border: 3px solid #000; box-shadow: 8px 8px 0px #000; padding: 1.5rem; border-radius: 15px; margin: auto;">
        <h3 style="font-family: 'Syne', sans-serif; font-weight: 800; margin-bottom: 1rem;">Editar Nota Clínica</h3>
        
        <form id="editNoteForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="editContent" class="form-label" style="font-weight: 700;">Contenido</label>
                <textarea 
                    id="editContent"
                    name="content" 
                    class="neobrutalist-input clinical-history-input w-100" 
                    rows="8" 
                    required 
                    style="min-height: 200px; resize: vertical; font-size: 16px; padding: 0.75rem;"></textarea>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" onclick="closeEditModal()" class="neobrutalist-btn bg-white" style="border: 2px solid #000;">Cancelar</button>
                <button type="submit" class="neobrutalist-btn" style="background: #000; color: white; border: 2px solid #000; padding: 0.75rem 1.5rem; font-weight: 700;">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const pacienteId = {{ $paciente->id }};
    
    function openAddModal(turnoId) {
        const form = document.getElementById('addNoteForm');
        form.action = `/admin/pacientes/${pacienteId}/historia-clinica/${turnoId}`;
        document.getElementById('addNoteModal').style.display = 'flex';
        document.getElementById('addContent').value = '';
        document.getElementById('addContent').focus();
    }
    
    function closeAddModal() {
        document.getElementById('addNoteModal').style.display = 'none';
    }
    
    function openEditModal(turnoId, content) {
        const form = document.getElementById('editNoteForm');
        form.action = `/admin/pacientes/${pacienteId}/historia-clinica/${turnoId}`;
        document.getElementById('editContent').value = content;
        document.getElementById('editNoteModal').style.display = 'flex';
        document.getElementById('editContent').focus();
    }
    
    function closeEditModal() {
        document.getElementById('editNoteModal').style.display = 'none';
    }
    
    // Close modals on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddModal();
            closeEditModal();
        }
    });
    
    /* 
    // ========== OFFLINE MODE FUNCTIONALITY ==========
    
    // Register Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/js/offline-service-worker.js')
            .then(reg => console.log('[Clinical History] Service Worker registered'))
            .catch(err => console.error('[Clinical History] Service Worker registration failed:', err));
    }
    
    // Create offline status banner (hidden by default)
    const offlineBanner = document.createElement('div');
    offlineBanner.id = 'offlineStatus';
    offlineBanner.style.display = 'none';
    offlineBanner.className = 'neobrutalist-card mb-4';
    offlineBanner.style.cssText = 'background: #fef3c7; border: 3px solid #000; padding: 1rem; margin-bottom: 1rem;';
    offlineBanner.innerHTML = '<i class="fa-solid fa-wifi-slash"></i> <strong>Sin conexión a internet.</strong> Las notas se guardarán localmente y se sincronizarán cuando se restaure la conexión.';
    
    // Insert banner after header
    const container = document.querySelector('.container-fluid');
    const header = container.querySelector('.d-flex.justify-content-between');
    header.after(offlineBanner);
    
    // Online/Offline detection
    window.addEventListener('online', () => {
        document.getElementById('offlineStatus').style.display = 'none';
        console.log('[Clinical History] Connection restored, triggering sync...');
        
        // Trigger sync
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            navigator.serviceWorker.ready.then(registration => {
                return registration.sync.register('sync-clinical-notes');
            }).then(() => {
                console.log('[Clinical History] Sync registered');
                // Reload page after sync to show updated data
                setTimeout(() => location.reload(), 2000);
            });
        } else {
            // Fallback: manual sync
            syncPendingNotesManually();
        }
    });
    
    window.addEventListener('offline', () => {
        document.getElementById('offlineStatus').style.display = 'block';
        console.log('[Clinical History] Connection lost, offline mode activated');
    });
    
    // Check initial state
    if (!navigator.onLine) {
        document.getElementById('offlineStatus').style.display = 'block';
    }
    
    // Intercept form submissions for offline handling
    document.getElementById('addNoteForm')?.addEventListener('submit', function(e) {
        if (!navigator.onLine) {
            e.preventDefault();
            saveNoteOffline(this);
        }
    });
    
    document.getElementById('editNoteForm')?.addEventListener('submit', function(e) {
        if (!navigator.onLine) {
            e.preventDefault();
            saveNoteOffline(this);
        }
    });
    
    function saveNoteOffline(form) {
        const formData = new FormData(form);
        const actionUrl = form.action;
        const urlParts = actionUrl.split('/');
        const turnoId = parseInt(urlParts[urlParts.length - 1]);
        
        const noteData = {
            turno_id: turnoId,
            paciente_id: pacienteId,
            content: formData.get('content'),
            csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || '',
        };
        
        // Get existing pending notes
        const pending = JSON.parse(localStorage.getItem('pending_clinical_notes') || '[]');
        
        // Remove duplicate turno if exists
        const filtered = pending.filter(n => n.turno_id !== turnoId);
        filtered.push(noteData);
        
        localStorage.setItem('pending_clinical_notes', JSON.stringify(filtered));
        
        console.log('[Clinical History] Note saved offline:', noteData);
        
        alert('✅ Nota guardada localmente. Se sincronizará cuando tengas conexión.');
        
        // Close modal and reset form
        closeAddModal();
        closeEditModal();
        form.reset();
    }
    
    // Manual sync fallback (for browsers without Background Sync API)
    async function syncPendingNotesManually() {
        const pending = JSON.parse(localStorage.getItem('pending_clinical_notes') || '[]');
        
        if (pending.length === 0) return;
        
        console.log('[Clinical History] Syncing', pending.length, 'pending notes...');
        
        for (const note of pending) {
            try {
                const response = await fetch(
                    `/admin/pacientes/${note.paciente_id}/historia-clinica/${note.turno_id}`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': note.csrf_token,
                        },
                        body: JSON.stringify({ content: note.content }),
                    }
                );
                
                if (response.ok) {
                    console.log('[Clinical History] Note synced:', note.turno_id);
                    const updated = pending.filter(n => n.turno_id !== note.turno_id);
                    localStorage.setItem('pending_clinical_notes', JSON.stringify(updated));
                }
            } catch (error) {
                console.error('[Clinical History] Sync failed:', error);
            }
        }
        
        // Reload to show synced data
        location.reload();
    }
    */
</script>
@endsection
