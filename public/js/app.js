/**
 * BookVerse — App JavaScript (Desktop Layout)
 */

document.addEventListener('DOMContentLoaded', () => {

    // ═══════════════════════════════════════
    // NAVBAR SCROLL EFFECT
    // ═══════════════════════════════════════
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 10);
        }, { passive: true });
    }

    // ═══════════════════════════════════════
    // SEARCH OVERLAY
    // ═══════════════════════════════════════
    const searchToggle  = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose   = document.getElementById('searchClose');

    searchToggle?.addEventListener('click', () => {
        searchOverlay?.classList.add('open');
        setTimeout(() => searchOverlay?.querySelector('input')?.focus(), 100);
    });

    searchClose?.addEventListener('click', () => searchOverlay?.classList.remove('open'));

    searchOverlay?.addEventListener('click', e => {
        if (e.target === searchOverlay) searchOverlay.classList.remove('open');
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            searchOverlay?.classList.remove('open');
            mobileOverlay?.classList.remove('open');
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchOverlay?.classList.add('open');
            setTimeout(() => searchOverlay?.querySelector('input')?.focus(), 100);
        }
    });

    // ═══════════════════════════════════════
    // MOBILE DRAWER
    // ═══════════════════════════════════════
    const hamburgerBtn  = document.getElementById('hamburgerBtn');
    const mobileOverlay = document.getElementById('mobileOverlay');

    hamburgerBtn?.addEventListener('click', () => {
        mobileOverlay?.classList.add('open');
        document.body.style.overflow = 'hidden';
    });

    mobileOverlay?.addEventListener('click', e => {
        if (e.target === mobileOverlay || !e.target.closest('[style*="280px"]')) {
            mobileOverlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    });

    // Active nav is handled server-side by Blade (request()->is())

    // ═══════════════════════════════════════
    // ALERT AUTO-DISMISS
    // ═══════════════════════════════════════
    document.querySelectorAll('.close-alert').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.alert')?.remove());
    });
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ═══════════════════════════════════════
    // SINOPSIS TOGGLE
    // ═══════════════════════════════════════
    const sinopsisToggle = document.querySelector('.sinopsis-toggle');
    const sinopsisText   = document.querySelector('.sinopsis-text');
    if (sinopsisToggle && sinopsisText) {
        sinopsisToggle.addEventListener('click', () => {
            const collapsed = sinopsisText.classList.toggle('collapsed');
            sinopsisToggle.textContent = collapsed ? 'Baca Selengkapnya' : 'Sembunyikan';
        });
    }

    // ═══════════════════════════════════════
    // FILE INPUT LABEL UPDATE
    // ═══════════════════════════════════════
    document.querySelectorAll('input[type=file]').forEach(input => {
        const label = input.nextElementSibling;
        if (!label || !label.classList.contains('file-input-label')) return;
        input.addEventListener('change', () => {
            if (input.files[0]) label.textContent = '✅ ' + input.files[0].name;
        });
    });

    // ═══════════════════════════════════════
    // NOTIFICATION BADGE POLLING
    // ═══════════════════════════════════════
    const notifBadge = document.getElementById('notifBadge');
    if (notifBadge) {
        const fetchCount = () => {
            fetch('/api/notifications/count')
                .then(r => r.json())
                .then(data => {
                    const count = data.count ?? 0;
                    notifBadge.textContent = count > 99 ? '99+' : count;
                    notifBadge.classList.toggle('show', count > 0);
                })
                .catch(() => {});
        };
        fetchCount();
        setInterval(fetchCount, 30000);
    }

    // ═══════════════════════════════════════
    // ADMIN SIDEBAR (mobile toggle)
    // ═══════════════════════════════════════
    const adminMenuBtn = document.getElementById('adminMenuBtn');
    const adminSidebar = document.getElementById('adminSidebar');
    adminMenuBtn?.addEventListener('click', () => adminSidebar?.classList.toggle('open'));

    // ═══════════════════════════════════════
    // CLICK STAR EFFECT (EXPLOSION)
    // ═══════════════════════════════════════
    document.addEventListener('click', (e) => {
        // 1. Munculkan 1 titik pusat (spark)
        const spark = document.createElement('div');
        spark.classList.add('click-star-center');
        spark.textContent = '✨';
        spark.style.left = e.clientX + 'px';
        spark.style.top = e.clientY + 'px';
        document.body.appendChild(spark);
        setTimeout(() => spark.remove(), 150);

        // 2. Ledakan (explosion) sedikit delay setelah titik pusat muncul
        setTimeout(() => {
            const particleCount = Math.floor(Math.random() * 3) + 4; // 4 to 6
            for (let i = 0; i < particleCount; i++) {
                const star = document.createElement('div');
                star.classList.add('click-star-particle');
                star.textContent = '✨';
                
                // Randomize angle and distance
                const angle = Math.random() * Math.PI * 2;
                const distance = Math.random() * 35 + 25; // 25px to 60px
                const tx = Math.cos(angle) * distance;
                const ty = Math.sin(angle) * distance - 5;
                
                star.style.setProperty('--tx', `${tx}px`);
                star.style.setProperty('--ty', `${ty}px`);
                star.style.left = e.clientX + 'px';
                star.style.top = e.clientY + 'px';
                
                document.body.appendChild(star);
                setTimeout(() => star.remove(), 800);
            }
        }, 50); // Ledakan partikel muncul 50ms setelah titik pusat
    });

});
