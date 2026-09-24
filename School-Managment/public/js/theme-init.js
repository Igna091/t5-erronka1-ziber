/**
 * Applies the saved theme and text size before the page is painted (no flash).
 * Loaded synchronously in <head>; kept external so the Content-Security-Policy
 * can keep blocking inline scripts.
 */
(function () {
    try {
        var root = document.documentElement;
        root.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');
        var fontSize = localStorage.getItem('fontSize');
        if (fontSize) {
            root.setAttribute('data-font-size', fontSize);
        }
    } catch (e) {
        // Storage blocked (private mode): keep the defaults from the markup
    }
})();
