<style>
    /* =========================================
       FOOTER STYLES (PREMIUM)
       ========================================= */
    .site-footer {
        background: linear-gradient(to bottom, rgba(248, 250, 252, 0.5), rgba(241, 245, 249, 1));
        border-top: 1px solid rgba(226, 232, 240, 0.8);
        padding: 80px 0 40px;
        margin-top: 100px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        position: relative;
        overflow: hidden;
    }

    /* Subtle Dot Grid Background in Footer */
    .site-footer::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 32px 32px;
        opacity: 0.3;
        mask-image: linear-gradient(to top, black 20%, transparent 100%);
        -webkit-mask-image: linear-gradient(to top, black 20%, transparent 100%);
        pointer-events: none;
        z-index: 0;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        position: relative;
        z-index: 10;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 60px;
        margin-bottom: 60px;
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
        gap: 12px;
        font-weight: 800;
        font-size: 1.3rem;
        color: #0f172a;
        text-decoration: none;
        margin-bottom: 16px;
        letter-spacing: -0.03em;
    }

    .footer-logo-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.4);
    }

    .footer-desc {
        color: #475569;
        line-height: 1.7;
        font-size: 1rem;
        max-width: 380px;
        margin: 0;
        font-weight: 500;
    }

    /* --- Links Section --- */
    .footer-col h4 {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 24px;
        margin-top: 0;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 16px;
    }

    .footer-links a {
        text-decoration: none;
        color: #64748b;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .footer-links a::before {
        content: '';
        width: 0;
        height: 2px;
        background: #4f46e5;
        border-radius: 2px;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .footer-links a:hover {
        color: #4f46e5;
        transform: translateX(4px);
    }

    .footer-links a:hover::before {
        width: 12px;
    }

    /* --- Bottom Bar --- */
    .footer-bottom {
        border-top: 1px solid rgba(226, 232, 240, 0.8);
        padding-top: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .copyright {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
    }



    /* --- Mobile Responsive --- */
    @media (max-width: 768px) {
        .site-footer {
            padding: 60px 0 30px;
            margin-top: 60px;
        }

        .footer-grid {
            grid-template-columns: 1fr;
            gap: 48px;
            margin-bottom: 40px;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
            padding-top: 24px;
        }

        .footer-brand {
            align-items: flex-start;
        }
    }
</style>

<footer class="site-footer">
    <div class="footer-container">

        <div class="footer-grid">
            <div class="footer-brand">
                <a class="footer-logo" href="?page=home">
                    <div class="footer-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="M7 15h0M2 9.5h20" />
                        </svg>
                    </div>
                    BendeharaKu
                </a>
                <p class="footer-desc">
                    Platform manajemen keuangan kelas modern. Transparansi total dalam satu aplikasi.
                </p>
            </div>

            <div class="footer-col">
                <h4>Navigasi</h4>
                <ul class="footer-links">
                    <li><a href="?page=home">Beranda</a></li>
                    <li><a href="#features">Fitur & Keunggulan</a></li>
                    <li><a href="?page=login">Login</a></li>
                    <li><a href="?page=register">Daftar</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Dukungan</h4>
                <ul class="footer-links">
                    <li><a href="?page=terms">Syarat Ketentuan</a></li>
                    <li><a href="?page=contact">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> BendeharaKu. All rights reserved.
            </div>


        </div>

    </div>
</footer>

<script>
    (function() {
        if (window.innerWidth > 992) return;

        var footer = document.querySelector('.site-footer');
        if (!footer) return;

        var GAP = 24;

        // Candidates — we check which ones are actually position:fixed at runtime
        var SELECTORS = [
            '.action-bar-header',
            '.action-bar',
            '.action-bar-header .btn-primary'
        ];

        function getFixedEls() {
            var fixed = [];
            SELECTORS.forEach(function(sel) {
                document.querySelectorAll(sel).forEach(function(el) {
                    if (window.getComputedStyle(el).position === 'fixed') {
                        fixed.push(el);
                    }
                });
            });
            return fixed;
        }

        var fixedEls = [];

        function adjust() {
            var footerTop = footer.getBoundingClientRect().top;
            var viewH = window.innerHeight;
            var overlap = viewH - footerTop;
            var bottom = overlap > 0 ? overlap + GAP : GAP;
            fixedEls.forEach(function(el) {
                el.style.bottom = bottom + 'px';
            });
        }

        function init() {
            fixedEls = getFixedEls();
            if (fixedEls.length) {
                window.addEventListener('scroll', adjust, {
                    passive: true
                });
                adjust();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>