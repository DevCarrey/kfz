(function () {
    const HOURS = {
        0: [], // Sonntag – geschlossen

        1: [
            { start: "07:00", end: "18:00" }
        ], // Montag

        2: [
            { start: "07:00", end: "18:00" }
        ], // Dienstag

        3: [
            { start: "07:00", end: "18:00" }
        ], // Mittwoch

        4: [
            { start: "07:00", end: "18:00" }
        ], // Donnerstag

        5: [
            { start: "07:00", end: "18:00" }
        ], // Freitag

        6: [
            { start: "07:00", end: "13:00" }
        ] // Samstag
    };

  function toMin(t) {
    const [h, m] = t.split(":").map(Number);
    return h * 60 + m;
  }

  function isOpenAt(now) {
    const day = now.getDay();
    const intervals = HOURS[day] || [];
    const mins = now.getHours() * 60 + now.getMinutes();

    return intervals.some(({ start, end }) => {
      const s = toMin(start);
      const e = toMin(end);
      return mins >= s && mins <= e;
    });
  }

  function getNextOpenDate(now) {
    for (let delta = 0; delta < 14; delta++) {
      const date = new Date(now);
      date.setDate(now.getDate() + delta);

      const day = date.getDay();
      const intervals = HOURS[day] || [];

      for (const interval of intervals) {
        const [sh, sm] = interval.start.split(":").map(Number);
        const candidate = new Date(date);
        candidate.setHours(sh, sm, 0, 0);

        if (delta === 0) {
          if (candidate > now) return candidate;
        } else {
          return candidate;
        }
      }
    }
    return null;
  }

  function formatDiff(ms) {
    if (!ms || ms <= 0) return "";
    const totalMin = Math.ceil(ms / 60000);
    const h = Math.floor(totalMin / 60);
    const m = totalMin % 60;

    if (h > 0 && m > 0) return `${h}h ${m}m`;
    if (h > 0) return `${h}h`;
    return `${m}m`;
  }

  function updateStatus() {
    const now = new Date();
    const btn = document.getElementById("openHours");
    if (!btn) return;

    const todayHours = HOURS[now.getDay()];

    if (!todayHours || todayHours.length === 0) {
      btn.textContent = "Geschlossen";
      btn.className = "btn btn-sm btn-danger";
      return;
    }

 if (isOpenAt(now)) {
  btn.textContent = "Geöffnet";
  btn.className = "btn btn-sm btn-success";
} else {
  const nextOpen = getNextOpenDate(now);

  if (nextOpen) {
    const msDiff = nextOpen - now;
    const twoHours = 2 * 60 * 60 * 1000;

    if (msDiff < twoHours) {
      btn.textContent = "Geschlossen – öffnet in " + formatDiff(msDiff);
      btn.className = "btn btn-sm btn-warning";
    } else {
      btn.textContent = "Geschlossen";
      btn.className = "btn btn-sm btn-danger";
    }
  } else {
    btn.textContent = "Geschlossen";
    btn.className = "btn btn-sm btn-danger";
  }
}
  }

  document.addEventListener("DOMContentLoaded", function () {
    const openBtn = document.getElementById("openHours");
    const overlay = document.getElementById("hoursOverlay");
    const closeBtn = document.getElementById("closeHours");

    updateStatus();
    setInterval(updateStatus, 60000);

    if (!openBtn || !overlay) return;

    function openOverlay() {
      overlay.hidden = false;
      overlay.setAttribute("aria-hidden", "false");
      openBtn.setAttribute("aria-expanded", "true");
      document.body.classList.add("no-scroll");
    }

    function closeOverlay() {
      overlay.hidden = true;
      overlay.setAttribute("aria-hidden", "true");
      openBtn.setAttribute("aria-expanded", "false");
      document.body.classList.remove("no-scroll");
    }

    openBtn.addEventListener("click", openOverlay);

    if (closeBtn) {
      closeBtn.addEventListener("click", closeOverlay);
    }

    overlay.addEventListener("click", function (e) {
      if (e.target === overlay) {
        closeOverlay();
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !overlay.hidden) {
        closeOverlay();
      }
    });
  });
})();

//NEW

document.addEventListener('DOMContentLoaded', () => {
    const openButton = document.getElementById('openHours');
    const closeButton = document.getElementById('closeHours');
    const overlay = document.getElementById('hoursOverlay');

    if (!openButton || !closeButton || !overlay) {
        return;
    }

    let lastFocusedElement = openButton;

    /**
     * Öffnet das Öffnungszeiten-Overlay.
     */
    function openHours() {
        lastFocusedElement = document.activeElement || openButton;

        overlay.hidden = false;
        overlay.setAttribute('aria-hidden', 'false');

        // Verhindert den Zugriff auf den Hintergrund
        overlay.inert = false;

        openButton.setAttribute('aria-expanded', 'true');
        document.body.classList.add('no-scroll');

        // Fokus erst setzen, nachdem das Overlay sichtbar ist
        requestAnimationFrame(() => {
            closeButton.focus();
        });
    }

    /**
     * Schließt das Öffnungszeiten-Overlay.
     */
    function closeHours() {
        /*
         * Wichtig:
         * Erst den Fokus aus dem Overlay entfernen.
         * Dadurch entsteht keine aria-hidden-Warnung.
         */
        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            lastFocusedElement.focus();
        } else {
            openButton.focus();
        }

        /*
         * Erst jetzt das Overlay für Screenreader und Tastatur
         * deaktivieren.
         */
        overlay.inert = true;
        overlay.setAttribute('aria-hidden', 'true');
        overlay.hidden = true;

        openButton.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('no-scroll');
    }

    /**
     * Öffnen.
     */
    openButton.addEventListener('click', openHours);

    /**
     * Schließen über den X-Button.
     */
    closeButton.addEventListener('click', closeHours);

    /**
     * Schließen mit Escape.
     */
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !overlay.hidden) {
            closeHours();
        }
    });

    /**
     * Schließen beim Klick auf den dunklen Hintergrund.
     * Ein Klick innerhalb des Dialogs bleibt geöffnet.
     */
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) {
            closeHours();
        }
    });

    /**
     * Initialer Zustand.
     */
    overlay.inert = true;
    overlay.hidden = true;
    overlay.setAttribute('aria-hidden', 'true');
    openButton.setAttribute('aria-expanded', 'false');
});