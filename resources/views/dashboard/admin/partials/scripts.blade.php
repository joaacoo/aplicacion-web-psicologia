<script>
    window.showLoader = function() {
        console.log('Loader shown');
        // If there's a global loader element, show it here
        // document.getElementById('global-loader')?.style.display = 'flex';
    };
    window.hideLoader = function() {
        console.log('Loader hidden');
        // document.getElementById('global-loader')?.style.display = 'none';
    };
    // Confirm Action Logic
    let pendingActionFormId = null;

    window.confirmAction = function(formId, message) {
        const form = document.getElementById(formId);
        if (!form) {
            console.error('Form not found:', formId);
            alert('Error: No se encuentra el formulario. Recarga la página.');
            return;
        }

        pendingActionFormId = formId;
        const modal = document.getElementById('actionConfirmModal');
        const textElement = document.getElementById('actionConfirmText');
        
        if (modal && textElement) {
            textElement.innerText = message;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            const btn = document.getElementById('actionConfirmBtn');
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', function() {
                if (pendingActionFormId) {
                    const f = document.getElementById(pendingActionFormId);
                    if(f) {
                        window.showLoader();
                        f.submit(); 
                    }
                }
                closeActionModal();
            });
        } else {
            if(confirm(message)) form.submit();
        }
    };

    window.closeActionModal = function() {
        const modal = document.getElementById('actionConfirmModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        pendingActionFormId = null;
    };

    window.openProofModal = function(fileSrc, patientName, uploadDate, fileExtension) {
        const modalImage = document.getElementById('modalImage');
        const modalPdf = document.getElementById('modalPdf');
        const modalError = document.getElementById('modalError');
        const modalLoader = document.getElementById('modalLoader');
        
        if(modalImage) modalImage.style.display = 'none';
        if(modalPdf) modalPdf.style.display = 'none';
        if(modalError) modalError.style.display = 'none';
        if(modalLoader) modalLoader.style.display = 'block';

        const isPdf = fileExtension ? (fileExtension.toLowerCase() === 'pdf') : fileSrc.toLowerCase().includes('.pdf');
        
        if (isPdf) {
            if(modalPdf) {
                modalPdf.onload = function() {
                    if(modalLoader) modalLoader.style.display = 'none';
                    this.style.display = 'block';
                };
                modalPdf.src = fileSrc;
            }
        } else {
            if(modalImage) {
                modalImage.onload = function() {
                    if(modalLoader) modalLoader.style.display = 'none';
                    if(modalError) modalError.style.display = 'none';
                    this.style.display = 'block';
                };
                modalImage.onerror = function() {
                    if(modalLoader) modalLoader.style.display = 'none';
                    this.style.display = 'none';
                    if(modalError) modalError.style.display = 'block';
                };
                modalImage.src = fileSrc;
            }
        }
        
        document.getElementById('modalPatient').innerText = patientName || 'Paciente';
        document.getElementById('modalDate').innerText = uploadDate ? (uploadDate + ' hs') : '';
        document.getElementById('proofModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    window.closeProofModal = function() {
        const modalImage = document.getElementById('modalImage');
        const modalPdf = document.getElementById('modalPdf');
        const modalError = document.getElementById('modalError');
        const modalLoader = document.getElementById('modalLoader');
        
        if(modalImage) modalImage.src = '';
        if(modalPdf) modalPdf.src = '';
        if(modalError) modalError.style.display = 'none';
        if(modalLoader) modalLoader.style.display = 'none';
        
        document.getElementById('proofModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Manage Patient Modal Logic
    window.openManageModal = function(id, name, email, phone, type, meetLink, customPrice) {
        // Set Basic Info
        document.getElementById('manageNameHighlight').innerText = name;
        document.getElementById('manageEmail').innerText = email;
        
        // Meet Link
        const meetInput = document.getElementById('manageMeetLink');
        if(meetInput) meetInput.value = meetLink || '';

        // Contact Buttons
        const mailBtn = document.getElementById('manageMailBtn');
        if(mailBtn) mailBtn.href = 'mailto:' + email;

        const waBtn = document.getElementById('manageWhatsAppBtn');
        if(waBtn) {
            if (phone && phone.trim() !== '' && phone !== 'No registrado') {
                let cleanPhone = phone.replace(/[^0-9]/g, '');
                // Ensure it has country code if missing (optional, but good practice for wa.me)
                if (cleanPhone.length === 10) cleanPhone = '549' + cleanPhone; 
                waBtn.href = 'https://wa.me/' + cleanPhone;
                waBtn.style.display = 'block';
            } else {
                waBtn.style.display = 'none';
            }
        }

        // Honorarios / Custom Price
        const chkPrice = document.getElementById('chkCustomPrice');
        const inputPrice = document.getElementById('inputCustomPrice');
        const containerPrice = document.getElementById('customPriceContainer');
        const btnUpdate = document.getElementById('btnUpdateHonorario');

        if(customPrice && customPrice > 0) {
            if(chkPrice) chkPrice.checked = true;
            if(inputPrice) inputPrice.value = customPrice;
            if(containerPrice) containerPrice.style.display = 'block';
            if(btnUpdate) btnUpdate.style.display = 'block';
        } else {
            if(chkPrice) chkPrice.checked = false;
            if(inputPrice) inputPrice.value = '';
            if(containerPrice) containerPrice.style.display = 'none';
            if(btnUpdate) btnUpdate.style.display = 'none';
        }

        // Set Form Actions
        // Assuming your routes are set up like /admin/patients/{id}/...
        const baseUrl = '/admin/patients/' + id; // Fixed to match actual Laravel routes
        
        const linkForm = document.getElementById('manage-link-form');
        if(linkForm) linkForm.action = baseUrl + '/update-meet';
        
        const typeForm = document.getElementById('manage-type-form');
        if(typeForm) typeForm.action = baseUrl + '/update-type';

        const honorarioForm = document.getElementById('manage-honorario-form');
        if(honorarioForm) honorarioForm.action = baseUrl + '/update-honorario';

        const disassociateForm = document.getElementById('manage-disassociate-form');
        if(disassociateForm) disassociateForm.action = baseUrl;

        // Show Modal
        const modal = document.getElementById('manageModal');
        if(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Scroll modal content to top
            const modalMessage = modal.querySelector('.confirm-modal-message');
            if(modalMessage) {
                modalMessage.scrollTop = 0;
            }
        }
    };

    window.closeManageModal = function() {
        const modal = document.getElementById('manageModal');
        if(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    window.toggleCustomPrice = function() {
        const chk = document.getElementById('chkCustomPrice');
        const container = document.getElementById('customPriceContainer');
        const btnUpdate = document.getElementById('btnUpdateHonorario');
        
        if(chk && container) {
            const isChecked = chk.checked;
            container.style.display = isChecked ? 'block' : 'none';
            if(btnUpdate) {
                btnUpdate.style.display = isChecked ? 'block' : 'none';
            }
        }
    };

    window.confirmDisassociate = function() {
        if(confirm('¿Estás seguro que querés dar de baja a este paciente de tu lista activa?')) {
            window.showLoader();
            document.getElementById('manage-disassociate-form').submit();
        }
    };

    // Documents Modal Logic
    window.openDocumentsModal = function(userId, patientName, documents) {
        document.getElementById('docsPatientName').innerText = patientName;
        document.getElementById('docsUserId').value = userId;
        
        const listContainer = document.getElementById('docsList');
        listContainer.innerHTML = '';

        if (documents && documents.length > 0) {
            documents.forEach(doc => {
                listContainer.innerHTML += renderDocumentItem(doc);
            });
        } else {
            listContainer.innerHTML = '<p style="text-align: center; color: #666; font-style: italic;">No hay documentos cargados.</p>';
        }

        const modal = document.getElementById('documentsModal');
        if(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeDocumentsModal = function() {
        const modal = document.getElementById('documentsModal');
        if(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };
    // AJAX save for Meet Link 
    document.addEventListener('DOMContentLoaded', () => {
        const linkForm = document.getElementById('manage-link-form');
        if (linkForm) {
            linkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = this.action;
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalContent = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.innerHTML = '<i class="fa-solid fa-check"></i>';
                    submitBtn.style.background = '#86efac';
                    
                    // Update the local appointments data to reflect the change if the user re-opens
                    // This is complex as appointments is a global const, but for the current session:
                    const meetInput = document.getElementById('manageMeetLink');
                    if(meetInput) meetInput.value = formData.get('meet_link');
                    
                    setTimeout(() => {
                        submitBtn.innerHTML = originalContent;
                        submitBtn.style.background = '#bae6fd';
                        submitBtn.disabled = false;
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback to normal submit if AJAX fails
                    this.submit();
                });
            });
        }

        const honorarioFormSubmit = document.getElementById('manage-honorario-form');
        if (honorarioFormSubmit) {
            honorarioFormSubmit.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = this.action;
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalContent = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Editado';
                    submitBtn.style.background = '#86efac';
                    
                    // Show success state
                    setTimeout(() => {
                        submitBtn.innerHTML = originalContent;
                        submitBtn.style.background = '#86efac';
                        submitBtn.disabled = false;
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback to normal submit if AJAX fails
                    this.submit();
                });
            });
        }
    });
</script>
