<div id="manageModal" class="confirm-modal-overlay" style="display: none; z-index: 100000; background: rgba(168, 226, 250, 0.3); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
    
    <!-- Modal Container -->
    <div class="confirm-modal confirm-modal-minimal" style="max-width: 550px; width: 95%; border: 3px solid #000; box-shadow: 10px 10px 0px #000; border-radius: 20px; background: white; padding: 0 !important; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh;">
        
        <!-- Header (Yellow Pastel - Centered) -->
        <div class="confirm-modal-title-minimal" id="manageTitle" style="padding: 1.5rem 2rem; background: #fef9c3; border-bottom: 3px solid #000; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.4rem; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.5rem;">
            <span>Gestionar</span>
            <span id="manageNameHighlight" style="background: transparent; padding: 0; border: none; box-shadow: none; font-size: 1.6rem; text-decoration: underline; text-decoration-thickness: 3px;">Juan Paciente</span>
        </div>

        <div class="confirm-modal-message" style="text-align: left; padding: 2rem; overflow-y: auto; font-family: 'Inter', sans-serif; flex: 1;">
            
            <!-- Contact Section (First) -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 3px solid #000; border-radius: 15px; background: #d1fae5; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-bottom: 1rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #111; font-size: 1.1rem;">Contacto & Enlaces</h4>
                
                <div style="background: rgba(255,255,255,0.7); padding: 1.2rem; border: 2px solid #000; border-radius: 12px; margin-bottom: 1.2rem;">
                    <p style="margin-bottom: 0.6rem; font-size: 0.9rem; font-weight: 600;"><strong>Email:</strong> <span id="manageEmail" style="color: #444;">test@example.com</span></p>
                    <p style="margin-bottom: 0px; font-size: 0.9rem; font-weight: 600;"><strong>Teléfono:</strong> <span id="managePhone" style="color: #444;">No registrado</span></p>
                    
                    <!-- Meet Link Form -->
                    <form id="manage-link-form" method="POST" style="margin-top: 1.2rem; padding-top: 1.2rem; border-top: 2px dashed #000;" action="">
                        @csrf
                        <label style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #333; display: block; margin-bottom: 0.5rem;">Link de Google Meet:</label>
                        <div style="display: flex; gap: 0.5rem; margin-top: 5px;">
                            <input type="url" name="meet_link" id="manageMeetLink" class="neobrutalist-input" placeholder="https://meet.google.com/..." style="flex: 1; font-size: 0.75rem; padding: 6px; border: 2px solid #000; border-radius: 8px; box-shadow: none;">
                            <button type="submit" class="neobrutalist-btn" style="background: #bae6fd; border: 2px solid #000; box-shadow: 3px 3px 0px #000; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; padding: 0; font-size: 0.75rem;">
                                <i class="fa-solid fa-save"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;">
                    <a id="manageMailBtn" href="#" class="neobrutalist-btn text-center" style="background: white; border: 2px solid #000; box-shadow: 3px 3px 0px #000; border-radius: 8px; font-weight: 700; padding: 0.5rem; font-size: 0.75rem; text-decoration: none; color: #000;">
                        <i class="fa-solid fa-envelope" style="margin-right: 4px; font-size: 0.9rem;"></i> Mail
                    </a>
                    <a id="manageWhatsAppBtn" href="#" target="_blank" class="neobrutalist-btn text-center" style="background: #25D366; color: white; border: 2px solid #000; box-shadow: 3px 3px 0px #000; border-radius: 8px; font-weight: 700; padding: 0.5rem; font-size: 0.75rem; display: none; text-decoration: none;">
                        <i class="fa-brands fa-whatsapp" style="margin-right: 4px; font-size: 0.9rem;"></i> WhatsApp
                    </a>
                </div>
            </div>

            <!-- Classification & Fees (Second) -->
            <div class="manage-grid-container" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div style="padding: 1.2rem; background: #fff; border: 3px solid #000; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                    <h3 style="margin-bottom: 0.5rem; font-size: 1rem; font-family: 'Syne', sans-serif; font-weight: 800;">Clasificación</h3>
                    <p style="font-size: 0.75rem; margin-bottom: 0.8rem; color: #444; font-weight: 600;">Frecuentes no necesitan subir comprobante.</p>
                    <form id="manage-type-form" method="POST" action="">
                        @csrf
                        <div style="display: flex; gap: 0.8rem;">
                            <button type="submit" name="tipo_paciente" value="nuevo" id="btnTypeNuevo" class="neobrutalist-btn flex-1" style="font-size: 0.85rem; padding: 8px; border: 3px solid #000; background: #fef08a; box-shadow: 4px 4px 0px #000; border-radius: 8px; font-weight: 800;">NUEVO</button>
                            <button type="submit" name="tipo_paciente" value="frecuente" id="btnTypeFrecuente" class="neobrutalist-btn flex-1" style="font-size: 0.85rem; padding: 8px; border: 3px solid #000; background: white; box-shadow: 4px 4px 0px #000; border-radius: 8px; font-weight: 800;">FRECUENTE</button>
                        </div>
                    </form>
                </div>

                <div style="padding: 1.2rem; background: #e9d5ff; border: 3px solid #000; border-radius: 15px; box-shadow: 4px 4px 0px #000;">
                    <h3 style="margin-bottom: 0.6rem; font-size: 1rem; font-family: 'Syne', sans-serif; font-weight: 800;">Honorarios</h3>
                    <form id="manage-honorario-form" method="POST" action="">
                        @csrf
                        <div style="margin-bottom: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.8rem; cursor: pointer;">
                                <input type="checkbox" name="use_custom_price" id="chkCustomPrice" onchange="toggleCustomPrice()" value="1" style="width: 1.1rem; height: 1.1rem; accent-color: #000;">
                                <span style="font-weight: 700; font-size: 0.85rem;">Precio personalizado</span>
                            </label>
                        </div>
                        <div id="customPriceContainer" style="display: none; margin-bottom: 1rem; max-width: 250px; margin-left: auto; margin-right: auto;">
                            <div style="display: flex; align-items: center; justify-content: center; border: 3px solid #000; border-radius: 10px; background: #fff; padding: 10px; height: 45px; box-sizing: border-box;">
                                
                                <span style="font-family: 'Syne', sans-serif; font-weight: 900; font-size: 1.3rem; color: #000; display: flex; align-items: center; margin-right: 6px; letter-spacing: -0.05em;">$</span>
                                
                                <input type="number" 
                                       name="honorario_pactado" 
                                       id="inputCustomPrice" 
                                       placeholder="18000" 
                                       style="width: 100px; border: none; padding: 0; margin: 0; font-size: 1rem; font-weight: 800; font-family: 'Inter', sans-serif; background: transparent; text-align: left; outline: none; display: flex; align-items: center; height: 100%; line-height: normal;">
                            </div>
                        </div>

                        <button type="submit" id="btnUpdateHonorario" class="neobrutalist-btn" style="width: 100%; background: #86efac; color: black; border: 3px solid #000; box-shadow: 4px 4px 0px #000; border-radius: 10px; font-weight: 800; padding: 0.6rem; font-size: 0.85rem; display: none;">
                            Actualizar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Disassociate Section (Moved to Bottom) -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 3px solid #000; border-radius: 15px; background: #ff8888; box-shadow: 4px 4px 0px #000;">
                <h4 style="margin-bottom: 0.8rem; font-family: 'Syne', sans-serif; font-weight: 800; color: #fff; font-size: 1.1rem;">Dar de Baja al Paciente</h4>
                <p style="font-size: 0.85rem; margin-bottom: 1.2rem; color: #fff; line-height: 1.4; font-weight: 600;">Si el tratamiento terminó o el paciente dejó de asistir, podés desasociarlo aquí.</p>
                
                <form id="manage-disassociate-form" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #fff; letter-spacing: 0.5px; display: block; margin-bottom: 0.5rem;">Motivo de la baja (opcional):</label>
                        <textarea name="motivo" placeholder="Ej: Fin del tratamiento..." class="neobrutalist-input" style="min-height: 120px; font-size: 0.9rem; padding: 10px; border: 2px solid #000; border-radius: 10px; box-shadow: none; background: #fff;"></textarea>
                    </div>
                    <button type="button" class="neobrutalist-btn" style="width: 100%; background: #dc2626; color: white; border: 3px solid #000; box-shadow: 4px 4px 0px #333; border-radius: 10px; font-weight: 800; padding: 0.6rem; font-size: 0.85rem;" onclick="confirmDisassociate()">
                        Confirmar Baja
                    </button>
                </form>
            </div>
            
            <!-- Close Button (Smaller) -->
            <div style="display: flex; justify-content: center; margin-top: 1rem;">
                <button onclick="closeManageModal()" class="neobrutalist-btn" style="background: #eee; border: 3px solid #000; box-shadow: 3px 3px 0px #000; border-radius: 8px; font-weight: 800; padding: 0.5rem 1.5rem; font-size: 0.8rem; width: auto; min-width: 120px;">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<style>
    /* Quita las flechas del input de número para que no desvíen el texto */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    
    /* Hide number input spinners/arrows specifically for inputCustomPrice */
    #inputCustomPrice::-webkit-outer-spin-button,
    #inputCustomPrice::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .action-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px;
        border: 2px solid #000;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.8rem;
        text-decoration: none;
        transition: transform 0.1s;
    }
    .action-pill:active { transform: translate(1px, 1px); }

    .type-btn {
        background: #fff;
        border: 2px solid #000;
        padding: 6px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.75rem;
        cursor: pointer;
        width: 100%; 
    }

    .manage-grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Tablet and landscape phone support */
    @media (min-width: 601px) and (max-width: 1024px) {
        .confirm-modal {
            max-width: 700px !important;
        }
        .confirm-modal-message {
            padding: 1.5rem !important;
        }
    }

    @media (max-width: 600px) {
        .manage-grid-container {
            grid-template-columns: 1fr;
            margin-left: 0;
            margin-right: 0;
            padding-left: 0;
            padding-right: 0;
            gap: 1rem;
        }
        .manage-grid-container > div {
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: 100% !important;
        }
        .confirm-modal-title-minimal { padding: 1rem !important; }
        .confirm-modal-title-minimal span#manageNameHighlight { font-size: 1rem !important; }
        
        /* Prevent horizontal scroll */
        .confirm-modal-message {
            padding: 1rem !important;
            overflow-x: hidden !important;
        }
    }

    /* Landscape phone optimization */
    @media (max-height: 600px) and (orientation: landscape) {
        .confirm-modal {
            max-height: 95vh !important;
        }
        .confirm-modal-message {
            padding: 1rem !important;
        }
    }
</style>
