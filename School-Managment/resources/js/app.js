/**
 * ZiberEibar - School Management System JS
 */
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // Theme Toggle (Dark/Light Mode)
    // ==========================================
    const themeToggles = document.querySelectorAll('.theme-toggle');
    const themeChoiceCards = document.querySelectorAll('.theme-choice-card');
    const savedTheme = localStorage.getItem('theme') || 'dark';
    
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // Update settings theme cards if present
        themeChoiceCards.forEach(function (card) {
            if (card.getAttribute('data-theme-target') === theme) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });
    }

    applyTheme(savedTheme);

    themeToggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        });
    });

    themeChoiceCards.forEach(function (card) {
        card.addEventListener('click', function () {
            const target = card.getAttribute('data-theme-target');
            if (target) {
                applyTheme(target);
                showSettingsToast('Tema cambiado a ' + (target === 'dark' ? 'Modo Oscuro' : 'Modo Claro'));
            }
        });
    });

    // ==========================================
    // Font Size Preference (Small / Normal / Large)
    // ==========================================
    const savedFontSize = localStorage.getItem('fontSize') || 'normal';
    function applyFontSize(size) {
        document.documentElement.setAttribute('data-font-size', size);
        localStorage.setItem('fontSize', size);

        // Check matching radio on settings page
        const radio = document.querySelector('input[name="fontSize"][value="' + size + '"]');
        if (radio) {
            radio.checked = true;
        }
    }

    applyFontSize(savedFontSize);

    const fontSizeRadios = document.querySelectorAll('input[name="fontSize"]');
    fontSizeRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (radio.checked) {
                applyFontSize(radio.value);
                const labels = { small: 'Pequeño', normal: 'Normal', large: 'Grande' };
                showSettingsToast('Tamaño de texto: ' + (labels[radio.value] || radio.value));
            }
        });
    });

    // ==========================================
    // Language Preference (Settings custom dropdown & select)
    // ==========================================
    const languageSelect = document.getElementById('languageSelect');
    const customLangDropdown = document.getElementById('customLanguageDropdown');
    const customLangTrigger = document.getElementById('customLanguageTrigger');
    const customLangMenu = document.getElementById('customLanguageMenu');
    const customLangSelected = document.getElementById('customLanguageSelected');

    const langData = {
        es: { flag: '🇪🇸', name: 'Español', full: 'Español (España)' },
        eu: { flag: '🏛️', name: 'Euskera', full: 'Euskera (Euskal Herria)' },
        en: { flag: '🇬🇧', name: 'English', full: 'English (UK)' }
    };

    function setLanguage(locale) {
        const item = langData[locale] || langData['es'];
        localStorage.setItem('app_locale', locale);

        if (languageSelect) {
            languageSelect.value = locale;
        }

        if (customLangSelected) {
            customLangSelected.innerHTML = '<span class="lang-flag">' + item.flag + '</span><span class="lang-label">' + item.full + '</span>';
        }

        if (customLangMenu) {
            customLangMenu.querySelectorAll('.custom-dropdown-option').forEach(function (opt) {
                const isSelected = opt.getAttribute('data-value') === locale;
                opt.classList.toggle('active', isSelected);
                opt.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            });
        }
    }

    if (languageSelect || customLangDropdown) {
        const savedLocale = localStorage.getItem('app_locale') || 'es';
        setLanguage(savedLocale);

        if (languageSelect) {
            languageSelect.addEventListener('change', function () {
                setLanguage(languageSelect.value);
                const item = langData[languageSelect.value] || { name: languageSelect.value };
                showSettingsToast('Idioma cambiado a ' + item.name);
            });
        }

        if (customLangTrigger && customLangMenu) {
            customLangTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = customLangMenu.classList.contains('show');
                closeAllCustomDropdowns();
                if (!isOpen) {
                    customLangMenu.classList.add('show');
                    customLangTrigger.setAttribute('aria-expanded', 'true');
                }
            });

            customLangMenu.querySelectorAll('.custom-dropdown-option').forEach(function (opt) {
                opt.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const chosen = opt.getAttribute('data-value');
                    setLanguage(chosen);
                    closeAllCustomDropdowns();
                    const item = langData[chosen] || { name: chosen };
                    showSettingsToast('Idioma cambiado a ' + item.name);
                });
            });
        }
    }

    function closeAllCustomDropdowns() {
        document.querySelectorAll('.custom-dropdown-menu.show').forEach(function (m) {
            m.classList.remove('show');
        });
        document.querySelectorAll('.custom-dropdown-trigger[aria-expanded="true"]').forEach(function (t) {
            t.setAttribute('aria-expanded', 'false');
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.custom-dropdown-container')) {
            closeAllCustomDropdowns();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllCustomDropdowns();
        }
    });

    // ==========================================
    // Settings Feedback Toast Helper
    // ==========================================
    let toastTimeout;
    function showSettingsToast(message) {
        const toast = document.getElementById('settingsToast');
        const toastMsg = document.getElementById('settingsToastMessage');
        if (!toast || !toastMsg) return;

        toastMsg.textContent = message;
        toast.classList.add('show');

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(function () {
            toast.classList.remove('show');
        }, 3200);
    }

    // ==========================================
    // User Dropdown (Navbar & Admin Header)
    // ==========================================
    const dropdownWrappers = document.querySelectorAll('.user-dropdown-wrapper');

    dropdownWrappers.forEach(function (wrapper) {
        const btn = wrapper.querySelector('.user-dropdown-btn');
        const menu = wrapper.querySelector('.user-dropdown-menu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains('show');

            // Close all open dropdowns first
            closeAllUserDropdowns();

            if (!isOpen) {
                menu.classList.add('show');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    function closeAllUserDropdowns() {
        document.querySelectorAll('.user-dropdown-menu.show').forEach(function (m) {
            m.classList.remove('show');
        });
        document.querySelectorAll('.user-dropdown-btn[aria-expanded="true"]').forEach(function (b) {
            b.setAttribute('aria-expanded', 'false');
        });
    }

    // Close user dropdown on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.user-dropdown-wrapper')) {
            closeAllUserDropdowns();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllUserDropdowns();
        }
    });


    // ==========================================
    // Mobile Navbar Toggle
    // ==========================================
    const navToggle = document.querySelector('.navbar-toggle');
    const navLinks = document.querySelector('.navbar-links');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function () {
            const isOpen = navLinks.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!navToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
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
    // ==========================================
    document.querySelectorAll('[data-auto-submit]').forEach(function (field) {
        field.addEventListener('change', function () {
            if (field.form) field.form.submit();
        });
    });

    // ==========================================
    // Course browser: pick a course in the list to show its panel
    // (links still open the course page on small screens or without JS)
    // ==========================================
    document.querySelectorAll('[data-browser]').forEach(function (browser) {
        const rows = browser.querySelectorAll('[data-course-row]');
        const wide = window.matchMedia('(min-width: 1181px)');

        rows.forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (!wide.matches || e.metaKey || e.ctrlKey || e.shiftKey) return;
                e.preventDefault();

                rows.forEach(function (other) {
                    const selected = other === row;
                    other.classList.toggle('is-sel', selected);
                    other.setAttribute('aria-current', selected ? 'true' : 'false');
                    const mark = other.querySelector('.ls__mark');
                    if (mark) mark.textContent = selected ? '>' : '';
                    const panel = document.getElementById(other.getAttribute('data-target'));
                    if (panel) panel.hidden = !selected;
                });
            });
        });
    });

    // ==========================================
    // Show / hide password
    // ==========================================
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        const input = document.getElementById(btn.getAttribute('data-toggle-password'));
        if (!input) return;

        btn.addEventListener('click', function () {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'ocultar' : 'mostrar';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
        });
    });

    // ==========================================
    // Live password requirements (same rules as the backend)
    // ==========================================
    document.querySelectorAll('[data-password-rules]').forEach(function (box) {
        const form = box.closest('form');
        const pw = form && form.querySelector('[data-password]');
        const confirmation = form && form.querySelector('[data-password-confirm]');
        const submit = form && form.querySelector('[type="submit"]');
        const meter = box.querySelector('[data-rules-meter]');
        if (!pw || !confirmation) return;

        const checks = {
            length: function (v) { return v.length >= 8; },
            letters: function (v) { return /\p{L}/u.test(v); },
            numbers: function (v) { return /\d/.test(v); },
            match: function (v, c) { return v.length > 0 && v === c; }
        };

        function update() {
            let passed = 0;
            const keys = Object.keys(checks);
            keys.forEach(function (key) {
                const ok = checks[key](pw.value, confirmation.value);
                const rule = box.querySelector('[data-rule="' + key + '"]');
                if (rule) rule.classList.toggle('is-ok', ok);
                if (ok) passed++;
            });
            const lit = Math.round((passed / keys.length) * 10);
            if (meter) meter.textContent = '[' + '#'.repeat(lit) + '-'.repeat(10 - lit) + ']';
            if (submit) submit.disabled = passed !== keys.length;
        }

        pw.addEventListener('input', update);
        confirmation.addEventListener('input', update);
        update();
    });

    // ==========================================
    // Countdown on buttons that are rate limited (data-countdown="seconds")
    // ==========================================
    document.querySelectorAll('[data-countdown]').forEach(function (btn) {
        let left = parseInt(btn.getAttribute('data-countdown'), 10);
        if (!(left > 0)) return;

        // Keep the original content (icon + text) to restore it afterwards
        const original = Array.from(btn.childNodes).map(function (node) { return node.cloneNode(true); });
        btn.disabled = true;

        function tick() {
            if (left <= 0) {
                btn.disabled = false;
                btn.replaceChildren.apply(btn, original);
                return;
            }
            btn.textContent = 'Espera ' + left + ' s';
            left--;
            setTimeout(tick, 1000);
        }

        tick();
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
