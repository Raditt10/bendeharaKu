<?php
// Set default jika variabel belum didefinisikan di halaman utama
$back_href = $back_href ?? '?page=dashboard';
$back_label = $back_label ?? 'Kembali';
?>

<div class="action-header">
    <a href="<?= htmlspecialchars($back_href) ?>" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span><?= htmlspecialchars($back_label) ?></span>
    </a>
</div>

<style>
    /* Style Khusus Tombol Kembali (Mobile Friendly) */
    .action-header {
        max-width: 600px;
        margin: 0 auto 16px auto;
        padding: 0 4px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 10px 12px; /* Padding cukup untuk sentuhan jari */
        border-radius: 8px;
        background-color: transparent;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        color: #2563eb;
        background-color: rgba(37, 99, 235, 0.05);
        transform: translateX(-4px);
    }

    .btn-back svg {
        transition: transform 0.2s ease;
    }
    
    /* Responsive adjustment */
    @media (max-width: 768px) {
        .action-header {
            margin-bottom: 12px;
        }
    }
</style>