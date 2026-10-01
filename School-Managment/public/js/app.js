/**
 * ZiberEibar - School Management System JS
 */
document.addEventListener('DOMContentLoaded', function () {

    // Texts in the current language, from partials/js-i18n.blade.php
    let texts = {};
    try {
        const holder = document.getElementById('i18n');
        texts = JSON.parse((holder && holder.getAttribute('data-texts')) || '{}');
    } catch (e) {
        texts = {};
    }

    function t(key, fallback, replace) {
        let text = texts[key] || fallback;
        Object.keys(replace || {}).forEach(function (name) {
            text = text.replace(':' + name, replace[name]);
        });
        return text;
    }

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
                showSettingsToast(target === 'dark'
                    ? t('themeDark', 'Tema cambiado a modo oscuro')
                    : t('themeLight', 'Tema cambiado a modo claro'));
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
                const messages = {
                    small: t('fontSmall', 'Tamaño de texto: pequeño'),
                    normal: t('fontNormal', 'Tamaño de texto: normal'),
                    large: t('fontLarge', 'Tamaño de texto: grande')
                };
                showSettingsToast(messages[radio.value] || radio.value);
            }
        });
    });

    // ==========================================
    // Language (settings page): the choice is saved by the server in a cookie.
    // The select sends its form by itself ([data-auto-submit]), so the button is only for no-JS
    // ==========================================
    document.querySelectorAll('[data-js-hide]').forEach(function (el) {
        el.hidden = true;
    });

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
            var message = btn.getAttribute('data-confirm') || t('confirm', '¿Seguro que quieres continuar?');

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
    // Dropdowns: the browser's own option list can't be styled (some draw it see-through),
    // so every select.form-select gets a list drawn here. The real <select> stays hidden in
    // the form: it keeps the value that is sent and fires the usual "change" event.
    // ==========================================
    document.querySelectorAll('select.form-select').forEach(function (select, n) {
        const listId = 'select-list-' + n;
        const label = select.id ? document.querySelector('label[for="' + select.id + '"]') : null;
        const optionText = function (option) { return option.textContent.replace(/\s+/g, ' ').trim(); };

        const wrap = document.createElement('div');
        wrap.className = 'select';
        select.parentNode.insertBefore(wrap, select);

        // Same classes as the select (form-select, is-invalid...), so it looks like the field
        const box = document.createElement('div');
        box.className = select.className + ' select__box';
        box.tabIndex = 0;
        box.setAttribute('role', 'combobox');
        box.setAttribute('aria-haspopup', 'listbox');
        box.setAttribute('aria-expanded', 'false');
        box.setAttribute('aria-controls', listId);
        const value = document.createElement('span');
        value.className = 'select__value';
        box.appendChild(value);

        const list = document.createElement('ul');
        list.className = 'select__list';
        list.id = listId;
        list.setAttribute('role', 'listbox');
        list.hidden = true;

        if (label) {
            label.id = label.id || select.id + '-label';
            box.setAttribute('aria-labelledby', label.id);
            list.setAttribute('aria-labelledby', label.id);
            label.addEventListener('click', function (e) {
                e.preventDefault();
                box.focus();
            });
        } else if (select.hasAttribute('aria-label')) {
            box.setAttribute('aria-label', select.getAttribute('aria-label'));
        }

        const items = Array.from(select.options).map(function (option, i) {
            const item = document.createElement('li');
            item.id = listId + '-' + i;
            item.className = 'select__option';
            item.setAttribute('role', 'option');
            item.textContent = optionText(option);
            if (option.disabled) item.setAttribute('aria-disabled', 'true');
            item.addEventListener('click', function () {
                if (option.disabled) return;
                choose(i);
                close();
            });
            list.appendChild(item);
            return item;
        });

        select.classList.add('select__native');
        select.tabIndex = -1;
        wrap.append(select, box, list);

        let active = -1;

        function render() {
            const option = select.options[select.selectedIndex];
            value.textContent = option ? optionText(option) : '';
            box.classList.toggle('is-placeholder', !option || option.value === '');
            items.forEach(function (item, i) {
                item.setAttribute('aria-selected', i === select.selectedIndex ? 'true' : 'false');
            });
        }

        function setActive(i) {
            active = i;
            items.forEach(function (item, j) { item.classList.toggle('is-active', j === i); });
            if (i >= 0) {
                box.setAttribute('aria-activedescendant', items[i].id);
                items[i].scrollIntoView({ block: 'nearest' });
            } else {
                box.removeAttribute('aria-activedescendant');
            }
        }

        // Next option that can be chosen, from "from" in the direction of "step"
        function next(from, step) {
            for (let i = from + step; i >= 0 && i < items.length; i += step) {
                if (!select.options[i].disabled) return i;
            }
            return from;
        }

        function open() {
            list.hidden = false;
            box.setAttribute('aria-expanded', 'true');
            list.scrollIntoView({ block: 'nearest' });
            setActive(select.selectedIndex);
        }

        function close() {
            list.hidden = true;
            box.setAttribute('aria-expanded', 'false');
            setActive(-1);
        }

        function choose(i) {
            if (i < 0 || i === select.selectedIndex) return;
            select.selectedIndex = i;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        box.addEventListener('click', function () {
            if (list.hidden) open(); else close();
        });

        box.addEventListener('keydown', function (e) {
            if (list.hidden) {
                if (['ArrowDown', 'ArrowUp', 'Enter', ' '].indexOf(e.key) !== -1) {
                    e.preventDefault();
                    open();
                }
                return;
            }

            if (e.key === 'ArrowDown') setActive(next(active, 1));
            else if (e.key === 'ArrowUp') setActive(next(active, -1));
            else if (e.key === 'Home') setActive(next(-1, 1));
            else if (e.key === 'End') setActive(next(items.length, -1));
            else if (e.key === 'Enter' || e.key === ' ') { choose(active); close(); }
            else if (e.key === 'Escape') close();
            else if (e.key === 'Tab') { close(); return; }
            else return;

            e.preventDefault();
        });

        // Clicking the list keeps the focus on the field; clicking anywhere else closes it
        list.addEventListener('mousedown', function (e) { e.preventDefault(); });
        box.addEventListener('blur', close);
        select.addEventListener('change', render);

        render();
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
    // New teacher: only the subjects of the chosen course are shown and sent
    // (without JS every course's subjects are listed; the server checks the course)
    // ==========================================
    document.querySelectorAll('[data-course-select]').forEach(function (select) {
        const groups = select.form.querySelectorAll('[data-course-subjects]');

        function update() {
            groups.forEach(function (group) {
                const shown = group.getAttribute('data-course-subjects') === select.value;
                group.hidden = !shown;
                group.querySelectorAll('input').forEach(function (input) { input.disabled = !shown; });
            });
        }

        select.addEventListener('change', update);
        update();
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
            btn.textContent = show ? t('hide', 'ocultar') : t('show', 'mostrar');
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
        const segments = box.querySelectorAll('[data-rules-meter] .seg');
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
            segments.forEach(function (seg, i) {
                seg.classList.toggle('is-on', i < passed);
            });
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
            btn.textContent = t('wait', 'Espera :seconds s', { seconds: left });
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
