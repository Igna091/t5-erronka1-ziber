/**
 * EduCenter - School Management System JS
 */
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // Theme Toggle (Dark/Light Mode)
    // ==========================================
    const themeToggle = document.querySelectorAll('.theme-toggle');
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    themeToggle.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        });
    });

    // ==========================================
    // Mobile Navbar Toggle
    // ==========================================
    const navToggle = document.querySelector('.navbar-toggle');
    const navLinks = document.querySelector('.navbar-links');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            navLinks.classList.toggle('open');
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!navToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open');
            }
        });
    }

    // ==========================================
    // Admin Sidebar Toggle (Mobile)
    // ==========================================
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const sidebarClose = document.querySelector('.sidebar-close');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (sidebarOverlay) sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);

    // ==========================================
    // Delete Confirmation Modal
    // ==========================================
    document.querySelectorAll('[data-confirm]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var form = btn.closest('form');
            var message = btn.getAttribute('data-confirm') || '¿Estás seguro?';

            var overlay = document.getElementById('confirmModal');
            if (overlay) {
                document.getElementById('confirmMessage').textContent = message;
                overlay.classList.add('active');

                document.getElementById('confirmAccept').onclick = function () {
                    overlay.classList.remove('active');
                    form.submit();
                };

                document.getElementById('confirmCancel').onclick = function () {
                    overlay.classList.remove('active');
                };
            } else {
                if (confirm(message)) {
                    form.submit();
                }
            }
        });
    });

    // ==========================================
    // Filters that submit their form on change
    // (no inline onchange="" so the Content-Security-Policy can block inline JS)
    // ==========================================
    document.querySelectorAll('[data-auto-submit]').forEach(function (field) {
        field.addEventListener('change', function () {
            if (field.form) field.form.submit();
        });
    });

    // ==========================================
    // Auto-hide flash messages
    // ==========================================
    document.querySelectorAll('.alert[data-auto-hide]').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.3s ease';
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });
});
