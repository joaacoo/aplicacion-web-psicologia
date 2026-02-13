<div id="documentsModal" class="confirm-modal-overlay" style="display: none; z-index: 100000; background: rgba(168, 226, 250, 0.3); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
    
    <div class="confirm-modal confirm-modal-minimal" style="max-width: 600px; width: 95%; border: 3px solid #000; box-shadow: 10px 10px 0px #000; border-radius: 20px; background: white; padding: 0 !important; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;">
        
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
                
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" id="docsUserId">
                    
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 5px;">Nombre del Archivo</label>
                            <input type="text" name="name" class="neobrutalist-input w-full" placeholder="Ej: Factura Febrero, Informe..." required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 5px;">Tipo</label>
                                <select name="type" class="neobrutalist-input w-full" required>
                                    <option value="recibo">Recibo</option>
                                    <option value="certificado">Certificado</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 800; display: block; margin-bottom: 5px;">Archivo (PDF/IMG)</label>
                                
                                <!-- Custom File Input Wrapper -->
                                <div style="position: relative; display: flex; flex-direction: column; gap: 5px;">
                                    <input type="file" name="file" id="docFileInput" class="neobrutalist-input w-full" accept=".pdf,.jpg,.jpeg,.png" required 
                                        style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;"
                                        onchange="previewDocFile()">
                                    
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <button type="button" class="neobrutalist-btn" style="background: #e5e7eb; border: 2px solid #000; padding: 5px 10px; font-size: 0.8rem;">
                                            Seleccionar Archivo
                                        </button>
                                        <span id="fileNameDisplay" style="font-size: 0.8rem; font-weight: 600; color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                                            Ningún archivo seleccionado
                                        </span>
                                    </div>
                                    <!-- Full filename display removed -->
                                </div>
                            </div>
                        </div>

                        <!-- Preview Container -->
                        <div id="docPreviewContainer" style="display: none; margin-top: 1rem; border: 2px dashed #000; padding: 10px; border-radius: 10px; text-align: center; background: #f9f9f9;">
                            <p style="font-size: 0.8rem; font-weight: 700; margin-bottom: 5px;">Vista Previa:</p>
                            <img id="docPreviewImg" style="max-width: 100%; max-height: 200px; display: none; margin: 0 auto; border: 1px solid #ddd; border-radius: 5px;">
                            <iframe id="docPreviewPdf" style="width: 100%; height: 200px; display: none; border: 1px solid #ddd; border-radius: 5px;"></iframe>
                            <p id="docPreviewName" style="font-size: 0.75rem; color: #666; margin-top: 5px;"></p>
                        </div>

                        <button type="submit" class="neobrutalist-btn bg-amarillo w-full" style="justify-content: center; margin-top: 0.5rem;">
                            <i class="fa-solid fa-upload"></i> Subir Documento
                        </button>
                    </div>
                </form>
            </div>

            <script>
                function previewDocFile() {
                    const input = document.getElementById('docFileInput');
                    const container = document.getElementById('docPreviewContainer');
                    const img = document.getElementById('docPreviewImg');
                    const pdf = document.getElementById('docPreviewPdf');
                    const name = document.getElementById('docPreviewName');
                    
                    const fileNameDisplay = document.getElementById('fileNameDisplay');

                    if (input.files && input.files[0]) {
                        const file = input.files[0];
                        const reader = new FileReader();
                        
                        // Update Custom Input Displays
                        fileNameDisplay.textContent = file.name;

                        name.textContent = file.name; // Preview container name
                        container.style.display = 'block';

                        if (file.type.match('image.*')) {
                            reader.onload = function(e) {
                                img.src = e.target.result;
                                img.style.display = 'block';
                                pdf.style.display = 'none';
                            }
                            reader.readAsDataURL(file);
                        } else if (file.type === 'application/pdf') {
                            const blobUrl = URL.createObjectURL(file);
                            pdf.src = blobUrl;
                            pdf.style.display = 'block';
                            img.style.display = 'none';
                        } else {
                            // Generic file icon or message
                            img.style.display = 'none';
                            pdf.style.display = 'none';
                            container.style.display = 'block';
                        }
                    } else {
                        container.style.display = 'none';
                        fileNameDisplay.textContent = 'Ningún archivo seleccionado';
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
            </div>

        </div>
        
        <!-- Close Button -->
        <div style="display: flex; justify-content: center; padding: 1rem; background: #fff; border-top: 3px solid #000;">
            <button onclick="closeDocumentsModal()" class="neobrutalist-btn" style="background: #eee;">Cerrar</button>
        </div>

    </div>
</div>

<script>
    // Helper to generate the list items dynamically
    function renderDocumentItem(doc) {
        // Safe implementation of route generation in JS is tricky without hardcoding or passing base URL.
        // We will use a data attribute approach or simple string concat if we know the route structure.
        // Route is: /documents/{id}/download and DELETE /admin/documents/{id}
        
        const downloadUrl = `/documents/${doc.id}/download`;
        const deleteUrl = `/admin/documents/${doc.id}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        return `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.8rem; border: 2px solid #000; border-radius: 10px; background: #f9f9f9; box-shadow: 2px 2px 0px #ddd;">
                <div style="flex: 1; padding-right: 10px;">
                    <div style="font-weight: 800; font-size: 0.9rem; margin-bottom: 2px;">${doc.name}</div>
                    <div style="font-size: 0.75rem; color: #666;">
                        <span style="background: #eee; padding: 1px 5px; border-radius: 4px; border: 1px solid #ccc;">${doc.type.toUpperCase()}</span>
                        • ${new Date(doc.created_at).toLocaleDateString('es-AR')}
                    </div>
                </div>
                <div style="display: flex; gap: 5px;">
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
