/**
 * BookVerse — Main JavaScript
 * Vanilla JS for all interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initHeader();
    initBannerSlider();
    initStarRating();
    initSinopsisToggle();
    initAlerts();
    initNotifications();
    initModals();
    initFileInputs();
    initSearchBar();
});

/* ============================================
   SIDEBAR DRAWER
   ============================================ */
function initSidebar() {
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebarDrawer');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');

    if (!menuBtn || !sidebar) return;

    const toggleSidebar = (show) => {
        sidebar.classList.toggle('active', show);
        overlay.classList.toggle('active', show);
        document.body.style.overflow = show ? 'hidden' : '';
    };

    menuBtn.addEventListener('click', () => toggleSidebar(true));
    closeBtn?.addEventListener('click', () => toggleSidebar(false));
    overlay?.addEventListener('click', () => toggleSidebar(false));

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            toggleSidebar(false);
        }
    });
}

/* ============================================
   HEADER SCROLL EFFECT
   ============================================ */
function initHeader() {
    const header = document.querySelector('.header');
    if (!header) return;

    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const currentScroll = window.scrollY;
        header.classList.toggle('scrolled', currentScroll > 10);
        lastScroll = currentScroll;
    }, { passive: true });
}

/* ============================================
   BANNER SLIDER
   ============================================ */
function initBannerSlider() {
    const track = document.querySelector('.banner-track');
    const dots = document.querySelectorAll('.banner-dot');
    if (!track || dots.length === 0) return;

    let currentSlide = 0;
    const totalSlides = dots.length;

    function goToSlide(index) {
        currentSlide = index;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
    }

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => goToSlide(i));
    });

    // Auto-advance
    setInterval(() => {
        goToSlide((currentSlide + 1) % totalSlides);
    }, 5000);
}

/* ============================================
   STAR RATING INPUT
   ============================================ */
function initStarRating() {
    const starInputs = document.querySelectorAll('.star-input');
    starInputs.forEach(container => {
        const inputs = container.querySelectorAll('input[type="radio"]');
        const labels = container.querySelectorAll('label');

        labels.forEach((label, index) => {
            label.addEventListener('mouseenter', () => {
                labels.forEach((l, i) => {
                    // RTL layout: higher index = lower star value
                    l.textContent = i >= index ? '★' : '☆';
                });
            });

            label.addEventListener('mouseleave', () => {
                const checkedInput = container.querySelector('input:checked');
                labels.forEach((l, i) => {
                    if (checkedInput) {
                        const checkedLabel = container.querySelector(`label[for="${checkedInput.id}"]`);
                        const checkedIndex = Array.from(labels).indexOf(checkedLabel);
                        l.textContent = i >= checkedIndex ? '★' : '☆';
                    } else {
                        l.textContent = '☆';
                    }
                });
            });
        });
    });
}

/* ============================================
   SINOPSIS TOGGLE
   ============================================ */
function initSinopsisToggle() {
    const toggleBtns = document.querySelectorAll('.sinopsis-toggle');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const text = btn.previousElementSibling;
            if (!text) return;
            
            const isCollapsed = text.classList.contains('collapsed');
            text.classList.toggle('collapsed', !isCollapsed);
            btn.textContent = isCollapsed ? 'Sembunyikan' : 'Baca Selengkapnya';
        });
    });
}

/* ============================================
   FLASH ALERTS AUTO-DISMISS
   ============================================ */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);

        // Close button
        const closeBtn = alert.querySelector('.close-alert');
        closeBtn?.addEventListener('click', () => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        });
    });
}

/* ============================================
   NOTIFICATIONS
   ============================================ */
function initNotifications() {
    updateNotificationBadge();
    // Poll every 30 seconds
    setInterval(updateNotificationBadge, 30000);
}

function updateNotificationBadge() {
    const badge = document.getElementById('notifBadge');
    if (!badge) return;

    fetch(BASE_URL + '/notifications/count')
        .then(res => res.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.add('show');
            } else {
                badge.classList.remove('show');
            }
        })
        .catch(() => {});
}

function markNotificationRead(id, element) {
    fetch(BASE_URL + '/notifications/read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            element.classList.remove('unread');
            updateNotificationBadge();
        }
    })
    .catch(() => {});
}

/* ============================================
   MODALS
   ============================================ */
function initModals() {
    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
        }
    });
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('active');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('active');
}

/* ============================================
   FILE INPUTS
   ============================================ */
function initFileInputs() {
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const label = input.closest('.file-input-wrapper')?.querySelector('.file-input-label');
            if (label) {
                label.textContent = file.name;
            }

            // Preview image
            const previewId = input.dataset.preview;
            if (previewId) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        preview.src = ev.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    });
}

/* ============================================
   SEARCH BAR
   ============================================ */
function initSearchBar() {
    const searchClear = document.querySelector('.search-clear');
    const searchInput = document.querySelector('.search-bar .form-input');

    if (searchClear && searchInput) {
        searchClear.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.focus();
        });
    }
}

/* ============================================
   ADMIN SIDEBAR TOGGLE (Mobile)
   ============================================ */
function toggleAdminSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

/* ============================================
   CONFIRM DELETE
   ============================================ */
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus?');
}

/* ============================================
   TAB SWITCHING (Client-side)
   ============================================ */
function switchTab(tabGroup, tabName) {
    // Hide all tab contents
    document.querySelectorAll(`[data-tab-group="${tabGroup}"]`).forEach(el => {
        el.classList.add('hidden');
    });
    // Show target
    const target = document.querySelector(`[data-tab-group="${tabGroup}"][data-tab="${tabName}"]`);
    if (target) target.classList.remove('hidden');

    // Update tab buttons
    document.querySelectorAll(`[data-tab-btn-group="${tabGroup}"]`).forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tabBtn === tabName);
    });
}
