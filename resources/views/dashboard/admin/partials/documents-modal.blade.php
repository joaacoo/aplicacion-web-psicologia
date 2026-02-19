<div id="documentsModal" class="confirm-modal-overlay" style="display: none; z-index: 100000; background: rgba(168, 226, 250, 0.3); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
    
    <style>
        .confirm-modal-minimal {
            max-width: 600px;
            width: 95%;
            border: 3px solid #000;
            box-shadow: 10px 10px 0px #000;
            border-radius: 20px;
            background: white;
            padding: 0 !important;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }
        @media (max-width: 768px) {
            .confirm-modal-minimal {
                width: 100% !important;
                border-radius: 0 !important;
                height: 100% !important;
                max-height: 100vh !important;
                border: none !important;
            }
        }
    </style>
    <div class="confirm-modal confirm-modal-minimal">
        
        <!-- Header -->
        <div style="padding: 1.5rem 2rem; background: #e0f2fe; border-bottom: 3px solid #000; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.4rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
            <span>Documentos de</span>
            <span id="docsPatientName" style="background: transparent; padding: 0; font-size: 1.6rem; text-decoration: underline; text-decoration-thickness: 3px;">Paciente</span>
        </div>

        <div class="confirm-modal-message" style="text-align: left; padding: 2rem; overflow-y: auto; font-family: 'Inter', sans-serif; flex: 1;">
            
            <!-- Upload Form -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 3px solid #000; border-radius: 15px; background: #fff; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-bottom: 1rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #111; font-size: 1.1rem;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Subir Nuevo Documento
                </h4>
                
                <form id="docsUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_id" id="docsUserId">
                    
                        <div style="margin-bottom: 1rem;">
                            <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 5px;">Destinatario <span style="color:red">*</span></label>
                            <div style="display: flex; gap: 1rem;">
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="radio" name="scope" value="single" checked onchange="toggleScope(this)">
                                    Solo a <span id="radioPatientName" style="font-weight: 700;">este paciente</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 0.9rem;">
                                    <input type="radio" name="scope" value="all" onchange="toggleScope(this)">
                                    A todos los pacientes activos
                                </label>
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 5px;">Nombre del Archivo <span style="color:red">*</span></label>
                            <input type="text" name="name" class="neobrutalist-input w-full" placeholder="Ej: Factura Febrero, Informe..." required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 5px;">Archivo (PDF/IMG)</label>
                                
                                <!-- Custom File Input Wrapper -->
                                <div style="position: relative; display: flex; flex-direction: column; gap: 5px;">
                                    <input type="file" name="file" id="docFileInput" class="neobrutalist-input w-full" accept=".pdf,.jpg,.jpeg,.png"
                                        style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;"
                                        onchange="previewDocFile()">
                                    
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <button type="button" class="neobrutalist-btn" style="background: #e5e7eb; border: 2px solid #000; padding: 5px 10px; font-size: 0.8rem;">
                                            Seleccionar Archivo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </form>

                <!-- Queue List -->
                <div id="uploadQueueContainer" style="display: none; margin-top: 1rem;">
                    <h5 style="margin: 0 0 0.5rem 0; font-weight: 800; font-size: 0.9rem;">Archivos a Subir (<span id="queueCount">0</span>/10)</h5>
                    <div id="uploadQueueList" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 250px; overflow-y: auto;">
                        <!-- JS populated -->
                    </div>
                    <button type="button" onclick="uploadAllFiles()" id="btnUploadAll" class="neobrutalist-btn bg-amarillo w-full" style="justify-content: center; margin-top: 1rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Iniciar Carga
                    </button>
                </div>
            </div>

            <script>
                let uploadQueue = [];

                function previewDocFile() {
                    const input = document.getElementById('docFileInput');
                    const nameInput = document.querySelector('input[name="name"]');
                    
                    if (input.files && input.files[0]) {
                        if(uploadQueue.length >= 10) {
                            alert("Máximo 10 archivos por vez.");
                            input.value = '';
                            return;
                        }

                        // Validate Name
                        const displayName = nameInput.value.trim();
                        if (!displayName) {
                             alert("Por favor, ingresá un nombre para el archivo antes de seleccionarlo.");
                             input.value = '';
                             nameInput.focus();
                             return;
                        }

                        const file = input.files[0];
                        
                        // Create object URL for preview
                        let previewUrl = null;
                        if (file.type.startsWith('image/')) {
                            previewUrl = URL.createObjectURL(file);
                        }
                        
                        uploadQueue.push({
                            file: file,
                            name: displayName,
                            type: 'otro', // Default valid type
                            preview: previewUrl,
                            id: Date.now()
                        });

                        renderQueue();
                        
                        // Reset inputs
                        input.value = '';
                        nameInput.value = '';
                        
                        document.getElementById('uploadQueueContainer').style.display = 'block';
                    }
                }

                function renderQueue() {
                    const list = document.getElementById('uploadQueueList');
                    const countSpan = document.getElementById('queueCount');
                    list.innerHTML = '';
                    countSpan.innerText = uploadQueue.length;

                    if(uploadQueue.length === 0) {
                        document.getElementById('uploadQueueContainer').style.display = 'none';
                        return;
                    }

                    uploadQueue.forEach((item, index) => {
                        const div = document.createElement('div');
                        div.style.cssText = "display: flex; align-items: center; padding: 0.5rem; background: #f0f0f0; border: 1px solid #ddd; border-radius: 6px; gap: 10px;";
                        
                        // Preview
                        let previewHtml = '';
                        if (item.preview) {
                            previewHtml = `<div style="width: 40px; height: 40px; border-radius: 4px; overflow: hidden; flex-shrink: 0; border: 1px solid #ccc;">
                                <img src="${item.preview}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>`;
                        } else {
                            // Generic icon for PDFs etc
                            previewHtml = `<div style="width: 40px; height: 40px; border-radius: 4px; background: #ddd; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #ccc;">
                                <i class="fa-solid fa-file" style="color: #666;"></i>
                            </div>`;
                        }

                        div.innerHTML = `
                            ${previewHtml}
                            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; font-size: 0.85rem;">
                                <strong>${item.name}</strong>
                            </div>
                            <button type="button" onclick="removeFromQueue(${item.id})" style="background: none; border: none; color: #ff5252; cursor: pointer; padding: 0 5px;">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        `;
                        list.appendChild(div);
                    });
                }

                function removeFromQueue(id) {
                    uploadQueue = uploadQueue.filter(x => x.id !== id);
                    renderQueue();
                }

                function showFloatingToast(message) {
                    const toast = document.createElement('div');
                    toast.style.cssText = "position: fixed; top: 100px; right: 20px; z-index: 100000; background: #10b981; color: white; padding: 1rem 1.5rem; border-radius: 12px; border: 3px solid #000; box-shadow: 6px 6px 0px #000; font-weight: 700; font-family: 'Manrope', sans-serif; font-size: 0.95rem; max-width: 400px; animation: slideInRight 0.3s ease-out; display: flex; align-items: center; gap: 0.8rem;";
                    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="font-size: 1.3rem;"></i> <span>${message}</span>`;
                    document.body.appendChild(toast);
                    
                    // Add animation keyframes if not exists
                    if (!document.getElementById('toast-style')) {
                        const style = document.createElement('style');
                        style.id = 'toast-style';
                        style.innerHTML = `@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
                        document.head.appendChild(style);
                    }

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(-10px)';
                        toast.style.transition = 'all 0.5s ease';
                        setTimeout(() => toast.remove(), 500);
                    }, 3000);
                }

                async function uploadAllFiles() {
                    const btn = document.getElementById('btnUploadAll');
                    const origText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Subiendo...';

                    const userId = document.getElementById('docsUserId').value;
                    const url = "{{ route('documents.store') }}";
                    const token = document.querySelector('input[name="_token"]').value;

                    console.log('Upload URL:', url);
                    console.log('User ID:', userId);
                    console.log('Queue length:', uploadQueue.length);

                    let successCount = 0;
                    let errors = [];

                    for (const item of uploadQueue) {
                        const formData = new FormData();
                        formData.append('user_id', userId);
                        // Get scope from radio
                        const scope = document.querySelector('input[name="scope"]:checked').value;
                        formData.append('scope', scope);
                        
                        formData.append('name', item.name);
                        formData.append('type', item.type);
                        formData.append('file', item.file);
                        formData.append('_token', token);

                        console.log('Uploading:', item.name, 'Type:', item.type);

                        try {
                            const res = await fetch(url, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            console.log('Response status:', res.status);
                            
                            if(res.ok) {
                                successCount++;
                                console.log('Upload successful for:', item.name);
                            } else {
                                const errorData = await res.json().catch(() => ({ message: 'Error desconocido' }));
                                console.error('Upload failed:', res.status, errorData);
                                errors.push(`${item.name}: ${errorData.message || res.statusText}`);
                            }
                        } catch(e) {
                            console.error('Upload exception:', e);
                            errors.push(`${item.name}: ${e.message}`);
                        }
                    }

                    // Reset
                    uploadQueue = [];
                    renderQueue();
                    btn.disabled = false;
                    btn.innerHTML = origText;
                    
                    if (successCount > 0) {
                        showFloatingToast(`Se subieron ${successCount} archivos exitosamente.`);
                        closeDocumentsModal();
                        setTimeout(() => window.location.reload(), 1500); 
                    } else {
                        console.error('All uploads failed. Errors:', errors);
                        alert("Hubo un error al subir los archivos. Revisa la consola para más detalles.\n\n" + errors.join('\n'));
                    }
                }
            </script>

            <!-- Existing Documents List -->
            <div>
                <h4 style="margin-bottom: 1rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #111; font-size: 1.1rem; border-bottom: 2px solid #eee; padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-folder-open"></i> Archivos Disponibles
                </h4>
                
                <div id="docsList" style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <!-- JS will populate this -->
                    <p style="text-align: center; color: #666; font-style: italic;">No hay documentos cargados.</p>
                </div>
                <div style="margin-top: 2rem; width: 100%; display: flex; justify-content: center; padding-bottom: 2rem;">
                    <button onclick="closeDocumentsModal()" class="neobrutalist-btn" style="background: #eee; min-width: 120px; border: 2px solid #000; font-weight: 800;">Cerrar</button>
                </div>
            </div>
            <!-- End of confirm-modal-message -->

    </div>
</div>

<script>
    // Helper to generate the list items dynamically
    function renderDocumentItem(doc) {
        
        const downloadUrl = `/documents/${doc.id}/download`;
        const deleteUrl = `/admin/documents/${doc.id}`;
        // Safe check for csrf token
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        const isImage = doc.file_path && doc.file_path.match(/\.(jpeg|jpg|png|webp|gif)$/i);
        const isPdf = doc.file_path && doc.file_path.match(/\.pdf$/i);
        const previewUrl = doc.file_path ? `/storage/${doc.file_path}` : '';
        
        let previewHtml = '';
        if (isImage) {
            previewHtml = `
                <div style="width: 50px; height: 50px; border: 2px solid #000; border-radius: 8px; overflow: hidden; flex-shrink: 0; box-shadow: 2px 2px 0px #000; background: #fff; display: flex; align-items: center; justify-content: center;">
                    <img src="${previewUrl}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            `;
        } else {
            previewHtml = `
                <div style="width: 50px; height: 50px; border: 2px solid #000; border-radius: 8px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 2px 2px 0px #000;">
                    <i class="fa-solid ${isPdf ? 'fa-file-pdf' : 'fa-file'}" style="color: ${isPdf ? '#e11d48' : '#666'}; font-size: 1.4rem;"></i>
                </div>
            `;
        }

        return `
            <div style="display: flex; align-items: center; padding: 0.8rem; border: 2px solid #000; border-radius: 10px; background: #f9f9f9; box-shadow: 2px 2px 0px #ddd; gap: 12px;">
                ${previewHtml}
                <div style="flex: 1; min-width: 0; padding-right: 5px;">
                    <div style="font-weight: 800; font-size: 0.9rem; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${doc.name}</div>
                    <div style="font-size: 0.75rem; color: #666;">
                        <span style="background: #eee; padding: 1px 5px; border-radius: 4px; border: 1px solid #ccc;">${doc.type ? doc.type.toUpperCase() : 'DOC'}</span>
                        • ${new Date(doc.created_at).toLocaleDateString('es-AR')}
                    </div>
                </div>
                <div style="display: flex; gap: 5px; flex-shrink: 0;">
                    <a href="${downloadUrl}" class="neobrutalist-btn" style="padding: 5px 10px; font-size: 0.8rem; background: white;" title="Descargar">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    <form action="${deleteUrl}" method="POST" onsubmit="return confirm('¿Borrar este documento?');" style="margin: 0;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="neobrutalist-btn" style="padding: 5px 10px; font-size: 0.8rem; background: #fee2e2; color: #ef4444;" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        `;
    }
</script>
