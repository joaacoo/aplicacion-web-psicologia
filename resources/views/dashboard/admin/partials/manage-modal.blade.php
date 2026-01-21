<!-- Manage Patient Modal -->
<div id="manageModal" class="confirm-modal-overlay" style="display: none;">
    <div class="confirm-modal" style="max-width: 550px; width: 90%;">
        <div class="confirm-modal-title" id="manageTitle">Gestionar Paciente</div>
        <div class="confirm-modal-message" style="text-align: left;">
            
            <!-- Disassociate Section (Moved Up and Renamed) -->
            <div style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 3px dashed #000;">
                <h4 style="margin-bottom: 0.5rem;">Dar de Baja al Paciente</h4>
                <p style="font-size: 0.85rem; margin-bottom: 1rem; color: #555;">Si el tratamiento terminó o el paciente dejó de asistir, podés desasociarlo aquí.</p>
                
                <form id="manage-delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <label style="font-size: 0.8rem; font-weight: 700;">Motivo de la baja (opcional):</label>
                        <textarea name="motivo" placeholder="Ej: Fin del tratamiento..." class="neobrutalist-input" style="min-height: 80px; font-size: 0.9rem; padding: 10px;"></textarea>
                    </div>
                    <button type="button" class="neobrutalist-btn w-full" style="background: #000; color: white; font-size: 0.9rem;" 
                            onclick="confirmDisassociate()">
                        Confirmar Baja
                    </button>
                </form>
            </div>

            <!-- Contact Section -->
            <div style="margin-bottom: 2rem;">
                <h4 style="margin-bottom: 0.5rem; border-bottom: 2px solid #000; display: inline-block;">Datos de Contacto & Enlaces</h4>
                
                <div style="background: #f9f9f9; padding: 1rem; border: 2px solid #000; margin-bottom: 1rem;">
                    <p><strong>Email:</strong> <span id="manageEmail"></span></p>
                    <p><strong>Teléfono:</strong> <span id="managePhone"></span></p>
                    
                    <!-- Meet Link Form -->
                    <form id="manage-link-form" method="POST" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #ccc;">
                        @csrf
                        <label style="font-size: 0.8rem; font-weight: 700;">Link de Google Meet (Único):</label>
                        <div style="display: flex; gap: 0.5rem; margin-top: 5px;">
                            <input type="url" name="meet_link" id="manageMeetLink" class="neobrutalist-input" placeholder="https://meet.google.com/..." style="flex: 1; font-size: 0.85rem; padding: 8px;">
                            <button type="submit" class="neobrutalist-btn bg-celeste" style="padding: 0 15px; font-size: 0.8rem;">
                                <i class="fa-solid fa-save"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;">
                    <a id="manageMailBtn" href="#" class="neobrutalist-btn text-center" style="background: var(--color-amarillo); font-size: 0.85rem;">
                        <i class="fa-solid fa-envelope"></i> Enviar Mail
                    </a>
                    <a id="manageWhatsAppBtn" href="#" target="_blank" class="neobrutalist-btn text-center" style="background: #25D366; color: white; border-color: #000; font-size: 0.85rem;">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

            <!-- Classification Section -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 3px solid #000; background: #fffbe6; box-shadow: 6px 6px 0px #000; border-radius: 15px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Clasificación de Paciente</h3>
                <p style="font-size: 0.9rem; margin-bottom: 1.5rem; color: #333; font-weight: 500;">
                    ¿Este paciente es nuevo o ya es frecuente? (Los frecuentes no necesitan subir comprobante).
                </p>
                <form id="manage-type-form" method="POST">
                    @csrf
                    <div style="display: flex; gap: 1rem; margin-bottom: 0.5rem;">
                        <button type="submit" name="tipo_paciente" value="nuevo" id="btnTypeNuevo" class="neobrutalist-btn flex-1" style="font-size: 1rem; padding: 15px; background: white;">NUEVO</button>
                        <button type="submit" name="tipo_paciente" value="frecuente" id="btnTypeFrecuente" class="neobrutalist-btn flex-1" style="font-size: 1rem; padding: 15px; background: white;">FRECUENTE</button>
                    </div>
                </form>

            </div>

             <!-- Honorarios Section (NEW) -->
             <div style="margin-bottom: 2rem; padding: 1.5rem; border: 3px solid #000; background: #fff; box-shadow: 6px 6px 0px #000; border-radius: 15px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 1.2rem;"><i class="fa-solid fa-money-bill-wave"></i> Honorarios del Paciente</h3>
                
                <form id="manage-honorario-form" method="POST">
                    @csrf
                    
                    <div style="background: #f0f0f0; padding: 1rem; border-radius: 8px; border: 1px dashed #999; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <label style="font-weight: 700; font-size: 0.9rem;">Precio Base (Estándar):</label>
                            <span style="font-weight: 900; font-size: 1.1rem;">${{ number_format($basePrice ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="use_custom_price" id="chkCustomPrice" onchange="toggleCustomPrice()" value="1" style="width: 1.2rem; height: 1.2rem; accent-color: #000;">
                            <span style="font-weight: 700; font-size: 0.95rem;">Usar precio personalizado</span>
                        </label>
                    </div>

                    <div id="customPriceContainer" style="display: none; margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700;">Precio por sesión ($):</label>
                        <input type="number" name="honorario_pactado" id="inputCustomPrice" class="neobrutalist-input w-full" placeholder="Ej: 18000">
                    </div>

                    <button type="submit" class="neobrutalist-btn w-full" style="background: var(--color-verde); font-size: 0.9rem; padding: 12px;">
                        <i class="fa-solid fa-save"></i> Guardar Honorarios
                    </button>
                </form>
            </div>

                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 2px dashed #000;">
                    <form id="manage-reminder-form" method="POST">
                        @csrf
                        <button type="submit" class="neobrutalist-btn w-full" style="background: var(--color-lila); font-size: 0.9rem; padding: 12px;">
                            <i class="fa-solid fa-bell"></i> Enviar Recordatorio por Mail
                        </button>
                    </form>
                </div>
            </div>
            <div class="confirm-modal-buttons" style="margin-top: 2rem;">
                <button onclick="closeManageModal()" class="neobrutalist-btn w-full" style="background: white;">Cerrar</button>
            </div>
        </div>
    </div>

<script>
    let currentPatientId = null;
    let currentPatientName = '';

    function closeManageModal() {
        document.getElementById('manageModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function openManageModal(id, name, email, phone, type, meetLink, customPrice) {
        currentPatientId = id;
        currentPatientName = name;
        
        document.getElementById('manageTitle').innerText = 'Gestionar: ' + name;
        document.getElementById('manageEmail').innerText = email;
        document.getElementById('managePhone').innerText = phone;
        document.getElementById('manageMailBtn').href = 'mailto: ' + email;
        
        const btnNuevo = document.getElementById('btnTypeNuevo');
        const btnFrecuente = document.getElementById('btnTypeFrecuente');
        
        btnNuevo.style.background = (type === 'nuevo') ? 'var(--color-amarillo)' : 'white';
        btnNuevo.style.borderWidth = (type === 'nuevo') ? '4px' : '2px';
        
        btnFrecuente.style.background = (type === 'frecuente') ? 'var(--color-verde)' : 'white';
        btnFrecuente.style.borderWidth = (type === 'frecuente') ? '4px' : '2px';

        const wpBtn = document.getElementById('manageWhatsAppBtn');
        if (phone && phone !== 'No registrado') {
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            // Construct message with link
            const meetUrl = meetLink ? meetLink : '[Link Pendiente]';
            const message = `Hola ${name.split(' ')[0]}, te envío el link para nuestra sesión de hoy: ${meetUrl}`;
            
            wpBtn.href = `https://wa.me/${cleanPhone}/?text=${encodeURIComponent(message)}`;
            wpBtn.setAttribute('data-base-href', `https://wa.me/${cleanPhone}`);
            wpBtn.style.display = 'flex';
            wpBtn.style.alignItems = 'center';
            wpBtn.style.justifyContent = 'center';
        } else {
            wpBtn.style.display = 'none';
        }
        
        // Populate and Action for Meet Link
        const linkInput = document.getElementById('manageMeetLink');
        linkInput.value = meetLink || '';
        document.getElementById('manage-link-form').action = '/admin/patients/' + id + '/update-link';

        // Pricing Logic
        const chk = document.getElementById('chkCustomPrice');
        const inputPrice = document.getElementById('inputCustomPrice');
        const formPrice = document.getElementById('manage-honorario-form');
        
        formPrice.action = '/admin/patients/' + id + '/update-honorario';
        
        // customPrice comes as string or decimal. 0 or null means default?
        // Wait, if 0 was default in old system, but new system uses null.
        // I need to check what backend returns. 
        // If it's a number > 0, we assume custom. If null or 0 (and we migrated logic), it's default.
        // Assuming user passes raw value.
        
        // Coerce to number
        const priceVal = parseFloat(customPrice);
        
        if (customPrice && priceVal > 0) {
            chk.checked = true;
            inputPrice.value = priceVal;
            document.getElementById('customPriceContainer').style.display = 'block';
        } else {
            chk.checked = false;
            inputPrice.value = '';
            document.getElementById('customPriceContainer').style.display = 'none';
        }

        document.getElementById('manage-delete-form').action = '/admin/patients/' + id;
        document.getElementById('manage-type-form').action = '/admin/patients/' + id + '/update-type';
        document.getElementById('manage-reminder-form').action = '/admin/patients/' + id + '/send-reminder';
        
        document.getElementById('manageModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function toggleCustomPrice() {
        const chk = document.getElementById('chkCustomPrice');
        const container = document.getElementById('customPriceContainer');
        const input = document.getElementById('inputCustomPrice');
        
        if (chk.checked) {
            container.style.display = 'block';
            input.required = true;
        } else {
            container.style.display = 'none';
            input.required = false;
            input.value = ''; // Reset
        }
    }

    function confirmDisassociate() {
        window.showConfirm('¿Estás segura de que querés desasociar permanentemente a ' + currentPatientName + '? Se borrarán todos sus turnos y pagos vinculados.', function() {
            const verification = prompt('Para confirmar la baja definitiva de ' + currentPatientName + ', por favor escribí "ELIMINAR" debajo:');
            if (verification === 'ELIMINAR') {
                document.getElementById('manage-delete-form').submit();
            } else {
                alert('Acción cancelada. El texto no coincidía.');
            }
        });
    }
</script>
