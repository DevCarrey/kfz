(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const openButton = document.getElementById('openBooking');
        const closeButton = document.getElementById('closeBooking');
        const overlay = document.getElementById('bookingOverlay');

        if (!openButton || !overlay) {
            return;
        }

        let lastFocusedElement = openButton;

        /**
         * Öffnet das Buchungsfenster.
         */
        function openBooking() {
            lastFocusedElement = document.activeElement || openButton;

            overlay.hidden = false;
            overlay.inert = false;
            overlay.setAttribute('aria-hidden', 'false');

            openButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('no-scroll');

            requestAnimationFrame(function () {
                if (closeButton) {
                    closeButton.focus();
                }
            });
        }

        /**
         * Schließt das Buchungsfenster.
         */
        function closeBooking() {
            if (
                lastFocusedElement
                && typeof lastFocusedElement.focus === 'function'
            ) {
                lastFocusedElement.focus();
            }

            overlay.inert = true;
            overlay.setAttribute('aria-hidden', 'true');
            overlay.hidden = true;

            openButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('no-scroll');
        }

        /**
         * Öffnen über den Button in der Navigation.
         */
        openButton.addEventListener('click', openBooking);

        /**
         * Schließen über X-Button.
         */
        if (closeButton) {
            closeButton.addEventListener('click', closeBooking);
        }

        /**
         * Schließen beim Klick auf den dunklen Hintergrund.
         */
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeBooking();
            }
        });

        /**
         * Schließen mit Escape.
         */
        document.addEventListener('keydown', function (event) {
            if (
                event.key === 'Escape'
                && !overlay.hidden
            ) {
                closeBooking();
            }
        });

        /**
         * Anfangszustand.
         */
        overlay.hidden = true;
        overlay.inert = true;
        overlay.setAttribute('aria-hidden', 'true');
        openButton.setAttribute('aria-expanded', 'false');
    });
})();