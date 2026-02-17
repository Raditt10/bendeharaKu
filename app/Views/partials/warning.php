<?php
// Mencegah double include yang bisa bikin error JS
if (defined('WARNING_MODAL_LOADED')) return;
define('WARNING_MODAL_LOADED', true);
?>

<div id="customWarningModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h3>Konfirmasi Hapus</h3>
        <p>Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button onclick="closeWarning()" class="btn-cancel-modal">Batal</button>
            <a id="btnConfirmDelete" href="#" class="btn-confirm-modal">Ya, Hapus</a>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
        z-index: 9999; display: none; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-box {
        background: #fff; width: 90%; max-width: 400px; padding: 30px;
        border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        text-align: center; transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal-overlay.show .modal-box { transform: scale(1); }
    .modal-icon {
        background: #fef2f2; width: 70px; height: 70px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
    }
    .modal-box h3 { margin: 0 0 10px; color: #1e293b; font-size: 1.25rem; font-weight: 700; }
    .modal-box p { margin: 0 0 25px; color: #64748b; font-size: 0.95rem; }
    .modal-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-cancel-modal {
        padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1;
        background: #fff; color: #475569; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-cancel-modal:hover { background: #f1f5f9; color: #1e293b; }
    .btn-confirm-modal {
        padding: 10px 20px; border-radius: 8px; background: #ef4444; color: white;
        font-weight: 600; text-decoration: none; border: none; transition: all 0.2s;
    }
    .btn-confirm-modal:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); }
</style>

<script>
    function openWarning(deleteUrl) {
        const modal = document.getElementById('customWarningModal');
        const confirmBtn = document.getElementById('btnConfirmDelete');
        confirmBtn.setAttribute('href', deleteUrl);
        modal.style.display = 'flex';
        setTimeout(() => { modal.classList.add('show'); }, 10);
    }

    function closeWarning() {
        const modal = document.getElementById('customWarningModal');
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    // Tutup jika klik backdrop
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('customWarningModal');
        if (event.target == modal) {
            closeWarning();
        }
    });
</script>