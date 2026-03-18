(function () {
    'use strict';

    if (typeof elpData === 'undefined' || !elpData) return;

    var excluded = (Array.isArray(elpData.excludedDomains) ? elpData.excludedDomains : []).slice();
    if (elpData.siteDomain) excluded.push(elpData.siteDomain);

    // Cache DOM references
    var overlay = document.getElementById('elpOverlay');
    var modal = document.getElementById('exitPopup');
    var continueBtn = document.getElementById('exitPopupContinue');
    var closeBtn = document.getElementById('elpClose');
    var returnBtn = document.getElementById('elpReturn');

    if (!modal || !continueBtn || !overlay) return;

    function isExternal(href) {
        if (!href || href.charAt(0) === '#' || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return false;
        if (href.indexOf('http') !== 0) return false;
        try {
            var hostname = new URL(href, window.location.origin).hostname;
            for (var i = 0; i < excluded.length; i++) {
                if (hostname === excluded[i] || hostname.endsWith('.' + excluded[i])) return false;
            }
            return true;
        } catch (e) {
            return false;
        }
    }

    function showModal(href, target) {
        continueBtn.href = href || '#';
        if (target && target !== '_self') {
            continueBtn.target = target;
            continueBtn.rel = 'noopener noreferrer';
        } else {
            continueBtn.removeAttribute('target');
            continueBtn.removeAttribute('rel');
        }
        overlay.classList.add('elp-active');
        modal.classList.add('elp-active');
        overlay.setAttribute('aria-hidden', 'false');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function hideModal() {
        overlay.classList.remove('elp-active');
        modal.classList.remove('elp-active');
        overlay.setAttribute('aria-hidden', 'true');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    // Event delegation — single listener on document for all links
    document.addEventListener('click', function (e) {
        var anchor = e.target.closest ? e.target.closest('a[href]') : null;
        if (!anchor) return;
        if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (!isExternal(anchor.href)) return;

        e.preventDefault();
        showModal(anchor.href, anchor.target);
    });

    // Close handlers
    closeBtn.addEventListener('click', hideModal);
    returnBtn.addEventListener('click', hideModal);
    overlay.addEventListener('click', hideModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('elp-active')) {
            hideModal();
        }
    });
})();
