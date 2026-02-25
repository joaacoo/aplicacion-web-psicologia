<!-- Payment Proof Modal -->
<div id="proofModal" class="modal-overlay no-select">
    <div class="modal-container">
        <div class="modal-header">
            <h3 style="margin:0;">Comprobante de Pago</h3>
            <button class="close-modal" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-image-col" style="background: #222; display: flex; align-items: center; justify-content: center; min-height: 200px; flex-direction: column; position: relative;">
                
                <!-- Loading Spinner -->
                <div id="modalLoader" style="color: white; font-size: 2rem; display: none;">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </div>

                <img id="modalImage" src="" alt="Comprobante" style="max-height: 50vh; max-width: 100%; object-fit: contain; border: 5px solid #fff; box-shadow: 10px 10px 0px #000; display: none;">
                <iframe id="modalPdf" src="" style="width: 100%; height: 50vh; border: 5px solid #fff; box-shadow: 10px 10px 0px #000; display: none;"></iframe>
                <p id="modalError" style="display: none; color: white; font-weight: bold; font-size: 1.2rem;">Archivo no encontrado / No disponible</p>
            </div>
            <div class="modal-info-col">
                <div>
                    <span class="custom-date-label">Paciente</span>
                    <p id="modalPatient" style="font-weight: 900; font-size: 1.2rem; margin:0;"></p>
                </div>
                <div>
                    <span class="custom-date-label">Subido el</span>
                    <p id="modalDate" style="font-weight: 700; margin:0;"></p>
                </div>
                <div style="margin-top:auto;">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generic Action Confirmation Modal -->
<div id="actionConfirmModal" class="confirm-modal-overlay" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000;">
    <div class="neobrutalist-card" style="background: white; max-width: 400px; width: 90%; border: 4px solid #000; box-shadow: 8px 8px 0px #000;">
        <h3 style="margin-top: 0; border-bottom: 2px solid #000; padding-bottom: 0.5rem; text-align: center;">Confirmar Acción</h3>
        <p id="actionConfirmText" style="font-weight: 700; margin: 1.5rem 0; font-size: 1.1rem; text-align: center;"></p>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <button class="neobrutalist-btn bg-lila" onclick="closeActionModal()">Cancelar</button>
            <button id="actionConfirmBtn" class="neobrutalist-btn bg-verde">Confirmar</button>
        </div>
    </div>
</div>

<!-- Modal: Agendar Recuperación -->
<div id="recoverAssignModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="neobrutalist-card" style="background: #fff; width: 100%; max-width: 500px; padding: 2rem; position: relative;">
        <button onclick="closeRecoverAssignModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h3 style="margin-top: 0; border-bottom: 3px solid #000; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-calendar-plus"></i> Agendar Recuperación
        </h3>
        <form action="{{ route('admin.appointments.storeRecovery') }}" method="POST">
            @csrf
            <input type="hidden" id="recover_waitlist_id" name="waitlist_id">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Paciente</label>
                <input type="text" id="recover_patient_name" readonly style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px; background: #eee;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Fecha</label>
                    <input type="date" name="fecha" required style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Hora</label>
                    <input type="time" name="hora" required style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 800; margin-bottom: 0.5rem;">Modalidad</label>
                <select name="modalidad" id="recover_modalidad" required style="width: 100%; padding: 0.5rem; border: 2px solid #000; border-radius: 4px;">
                    <option value="virtual">Virtual</option>
                    <option value="presencial">Presencial</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeRecoverAssignModal()" class="neobrutalist-btn bg-white" style="padding: 0.5rem 1rem;">Cancelar</button>
                <button type="submit" class="neobrutalist-btn bg-verde" style="padding: 0.5rem 1rem;">Confirmar y Agendar</button>
            </div>
        </form>
    </div>
</div>
