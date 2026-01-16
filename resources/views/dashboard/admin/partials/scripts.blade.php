<script>
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
                    if(f) f.submit(); 
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
</script>
