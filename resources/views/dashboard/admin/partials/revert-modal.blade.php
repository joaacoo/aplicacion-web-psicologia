<!-- Revert Action Modal -->
<div id="revertModal" class="confirm-modal-overlay" style="z-index: 10000; display: none;">
    <div class="confirm-modal" style="max-width: 400px;">
        <div class="confirm-modal-title">Revertir Acción</div>
        <div class="confirm-modal-message">
            <p id="revertDescription" style="font-weight: 700; margin-bottom: 1.5rem;"></p>
            <p style="font-size: 0.85rem; color: #d00; margin-bottom: 1rem; border-left: 3px solid #d00; padding-left: 0.5rem;">
                <strong>Atención:</strong> Esta acción deshará los cambios en los estados. Confirmá con tu contraseña administrativa.
            </p>
            <div class="mb-4">
                <input type="password" id="revertPassword" placeholder="Contraseña de Nazarena" class="neobrutalist-input w-full" style="border-width: 3px;">
            </div>
            <p id="revertError" style="color: red; font-size: 0.8rem; display: none; font-weight: 900;"></p>
        </div>
        <div class="confirm-modal-buttons">
            <button onclick="closeRevertModal()" class="neobrutalist-btn" style="background: white;">Cancelar</button>
            <button id="revertSubmitBtn" onclick="submitRevert()" class="neobrutalist-btn bg-amarillo">Confirmar Reverso</button>
        </div>
    </div>
</div>

<script>
    let pendingRevertId = null;
    window.openRevertModal = function(id, actionName) {
        pendingRevertId = id;
        document.getElementById('revertDescription').innerText = '¿Estás segura de revertir "' + actionName.toUpperCase() + '"?';
        document.getElementById('revertPassword').value = '';
        document.getElementById('revertError').style.display = 'none';
        document.getElementById('revertModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    window.closeRevertModal = function() {
        document.getElementById('revertModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.submitRevert = async function() {
        const password = document.getElementById('revertPassword').value;
        const btn = document.getElementById('revertSubmitBtn');
        const err = document.getElementById('revertError');

        if (!password) {
            err.innerText = 'Por favor ingresá tu contraseña.';
            err.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Procesando...';
        err.style.display = 'none';

        try {
            const response = await fetch(`/admin/activity-logs/${pendingRevertId}/revert`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password })
            });

            const data = await response.json();

            if (response.ok) {
                location.reload();
            } else {
                err.innerText = data.error || 'Error desconocido.';
                err.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Confirmar Reverso';
            }
        } catch (e) {
            err.innerText = 'Error de conexión.';
            err.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Confirmar Reverso';
        }
    }
</script>
