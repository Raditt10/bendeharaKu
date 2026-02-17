<?php
// Mencegah double include
if (defined('WARNING_MODAL_LOADED')) return;
define('WARNING_MODAL_LOADED', true);
?>

<div id="customWarningModal" class="modal-overlay">
    <div class="modal-box reveal-modal">
        <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        
        <h3 id="warningModalTitle">Konfirmasi Hapus</h3>
        <p id="warningModalText">Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?</p>
        
        <div class="confirmation-wrapper">
            <label class="custom-checkbox-container">
                <input type="checkbox" id="confirmDeleteCheckbox">
                <div class="checkbox-visual">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <span class="checkbox-text" id="warningCheckboxText">Saya mengerti dan ingin menghapus</span>
            </label>
        </div>

        <div class="modal-actions">
            <button onclick="closeWarning()" class="btn-cancel-modal">Batal</button>
            <a id="btnConfirmDelete" href="#" class="btn-confirm-modal disabled">
                <span id="warningModalBtnText">Hapus Data</span>
            </a>
        </div>
    </div>
</div>

<style>
    /* 1. Modal Overlay & Box */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);
        z-index: 9999; display: none; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    
    .modal-box {
        background: #fff; width: 90%; max-width: 380px; padding: 32px 24px;
        border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        text-align: center; transform: scale(0.95) translateY(10px);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modal-overlay.show .modal-box { transform: scale(1) translateY(0); }

    .modal-icon {
        background: #fef2f2; width: 72px; height: 72px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
        box-shadow: 0 0 0 8px #fff, 0 0 0 10px #fef2f2; /* Efek Ring Ganda */
    }
    .modal-box h3 { margin: 0 0 8px; color: #1e293b; font-size: 1.35rem; font-weight: 800; letter-spacing: -0.02em; }
    .modal-box p { margin: 0 0 24px; color: #64748b; font-size: 0.95rem; line-height: 1.5; }

    /* 2. CUSTOM CHECKBOX (PROFESSIONAL LOOK) */
    .confirmation-wrapper { margin-bottom: 24px; text-align: left; }
    
    .custom-checkbox-container {
        display: flex; align-items: center; gap: 12px;
        background: #f8fafc; border: 2px solid #e2e8f0;
        padding: 12px 16px; border-radius: 12px;
        cursor: pointer; transition: all 0.2s ease;
        user-select: none;
    }
    
    /* Efek Hover */
    .custom-checkbox-container:hover { border-color: #cbd5e1; background: #f1f5f9; }

    /* Sembunyikan Checkbox Asli */
    .custom-checkbox-container input { display: none; }

    /* Kotak Centang Custom */
    .checkbox-visual {
        width: 22px; height: 22px; flex-shrink: 0;
        border: 2px solid #94a3b8; border-radius: 6px; background: #fff;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .checkbox-visual svg { width: 14px; height: 14px; color: white; opacity: 0; transform: scale(0.5); transition: all 0.2s; }

    /* Teks Label */
    .checkbox-text { font-size: 0.92rem; color: #475569; font-weight: 600; transition: color 0.2s; }

    /* STATE: CHECKED (Saat Dicentang) */
    .custom-checkbox-container input:checked ~ .checkbox-visual {
        background: #ef4444; border-color: #ef4444;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }
    .custom-checkbox-container input:checked ~ .checkbox-visual svg { opacity: 1; transform: scale(1); }
    
    .custom-checkbox-container input:checked ~ .checkbox-text { color: #1e293b; }
    
    /* Ubah Border Container jadi Merah saat dicentang */
    .custom-checkbox-container:has(input:checked) {
        border-color: #fca5a5; background: #fef2f2;
    }

    /* 3. Buttons */
    .modal-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    
    .btn-cancel-modal {
        padding: 12px; border-radius: 10px; border: 1px solid #e2e8f0;
        background: #fff; color: #64748b; font-weight: 700; cursor: pointer; font-size: 0.95rem;
    }
    .btn-cancel-modal:hover { background: #f8fafc; color: #1e293b; }

    .btn-confirm-modal {
        padding: 12px; border-radius: 10px; background: #ef4444; color: white;
        font-weight: 700; text-decoration: none; border: none; font-size: 0.95rem;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }
    .btn-confirm-modal:hover { background: #dc2626; transform: translateY(-1px); }
    
    /* Disabled State untuk Tombol Hapus */
    .btn-confirm-modal.disabled {
        background: #e2e8f0; color: #94a3b8; pointer-events: none;
        box-shadow: none; transform: none;
    }
</style>

<script>
    const modal = document.getElementById('customWarningModal');
    const confirmBtn = document.getElementById('btnConfirmDelete');
    const checkbox = document.getElementById('confirmDeleteCheckbox');

    function openWarning(deleteUrl, type = 'delete') {
        // Reset State setiap kali dibuka
        checkbox.checked = false;
        confirmBtn.classList.add('disabled'); // Disable tombol hapus
        confirmBtn.setAttribute('href', deleteUrl);
        const title = document.getElementById('warningModalTitle');
        const text = document.getElementById('warningModalText');
        const checkboxText = document.getElementById('warningCheckboxText');
        const btnText = document.getElementById('warningModalBtnText');
        if(type === 'logout') {
            title.textContent = 'Konfirmasi Logout';
            text.textContent = 'Apakah Anda yakin ingin keluar dari akun?';
            checkboxText.textContent = 'Saya yakin ingin logout';
            btnText.textContent = 'Logout';
        } else {
            title.textContent = 'Konfirmasi Hapus';
            text.textContent = 'Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?';
            checkboxText.textContent = 'Saya mengerti dan ingin menghapus';
            btnText.textContent = 'Hapus Data';
        }
        modal.style.display = 'flex';
        // Delay dikit biar animasi CSS jalan
        setTimeout(() => { modal.classList.add('show'); }, 10);
    }

    function closeWarning() {
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    // Tutup jika klik area gelap (backdrop)
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            closeWarning();
        }
    });

    // Logic Checkbox Profesional
    if (checkbox && confirmBtn) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                confirmBtn.classList.remove('disabled');
            } else {
                confirmBtn.classList.add('disabled');
            }
        });
    }
</script>