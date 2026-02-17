<style>
    /* =========================================
       FOOTER STYLES (Professional SaaS Theme)
       ========================================= */
    .site-footer {
        background-color: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 64px 0 32px;
        margin-top: 80px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .footer-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr; /* Brand lebih lebar */
        gap: 48px;
        margin-bottom: 48px;
    }

    /* --- Brand Section --- */
    .footer-brand {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        text-decoration: none;
        margin-bottom: 16px;
    }
    
    .footer-logo svg { color: #2563eb; }

    .footer-desc {
        color: #64748b;
        line-height: 1.6;
        font-size: 0.95rem;
        max-width: 360px;
        margin: 0;
    }

    /* --- Links Section --- */
    .footer-col h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
        margin-top: 0;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        text-decoration: none;
        color: #64748b;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .footer-links a:hover {
        color: #2563eb;
        transform: translateX(4px); /* Efek geser saat hover */
    }

    /* --- Bottom Bar --- */
    .footer-bottom {
        border-top: 1px solid #f1f5f9;
        padding-top: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .copyright {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    /* --- Status Badge (Pulsing Effect) --- */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ecfdf5;
        border: 1px solid #d1fae5;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #059669;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        position: relative;
    }

    .status-dot::after {
        content: '';
        position: absolute;
        top: -1px; left: -1px;
        width: 10px; height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        opacity: 0.7;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.7; }
        70% { transform: scale(2.5); opacity: 0; }
        100% { transform: scale(1); opacity: 0; }
    }

    /* --- Mobile Responsive --- */
    @media (max-width: 768px) {
        .footer-grid {
            grid-template-columns: 1fr; /* Stack jadi 1 kolom */
            gap: 32px;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-brand {
            align-items: flex-start; /* Tetap rata kiri di HP agar rapi */
        }
    }
</style>

<footer class="site-footer">
    <div class="footer-container">
        
        <div class="footer-grid">
            <div class="footer-brand">
                <a class="footer-logo" href="?page=home">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 15h0M2 9.5h20"/></svg>
                    BendeharaKu
                </a>
                <p class="footer-desc">
                    Platform manajemen keuangan kelas modern. Transparansi total untuk guru, bendahara, dan orang tua siswa dalam satu aplikasi.
                </p>
            </div>

            <div class="footer-col">
                <h4>Navigasi</h4>
                <ul class="footer-links">
                    <li><a href="?page=home">Beranda</a></li>
                    <li><a href="#features">Fitur Utama</a></li>
                    <li><a href="?page=login">Login Siswa</a></li>
                    <li><a href="?page=register">Daftar</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Dukungan</h4>
                <ul class="footer-links">
                    <li><a href="#">Pusat Bantuan</a></li>
                    <li><a href="#">Keamanan & Privasi</a></li>
                    <li><a href="#">Syarat Ketentuan</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> BendeharaKu. Dibuat dengan ❤️ oleh Raditt10.
            </div>
            
            <div class="status-badge">
                <div class="status-dot"></div>
                System Operational
            </div>
        </div>

    </div>
</footer>
<script>
// IntersectionObserver to reveal elements with slide-in classes
document.addEventListener('DOMContentLoaded', function(){
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                // if parent has .reveal-stagger, stagger children
                const parent = entry.target.parentElement;
                if (parent && parent.classList.contains('reveal-stagger')) {
                    Array.from(parent.children).forEach((child, i) => {
                        setTimeout(() => child.classList.add('in-view'), i * 90);
                    });
                }
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal, .slide-in-left, .slide-in-right, .reveal-stagger').forEach(el => io.observe(el));
});
</script>