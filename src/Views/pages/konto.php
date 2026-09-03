<?php
declare(strict_types=1);

/**
 * Kfz Digital – Benutzerkonto
 *
 * Header und Footer werden durch index.php geladen.
 *
 * Wichtig:
 * Diese Datei verwendet keine header()-Weiterleitung,
 * weil der Header bereits vorher ausgegeben wurde.
 */


/*
|--------------------------------------------------------------------------
| URL-Hilfsfunktionen
|--------------------------------------------------------------------------
*/

$appPrefix = rtrim(
    (string)($GLOBALS['appPrefix'] ?? ''),
    '/'
);

$url = static function (string $path) use ($appPrefix): string {
    $path = '/' . ltrim($path, '/');

    return $appPrefix . (
        $path === '/'
            ? '/'
            : $path
    );
};

$escape = static function (string $value): string {
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};


/*
|--------------------------------------------------------------------------
| Benutzerstatus
|--------------------------------------------------------------------------
*/

$isLoggedIn = !empty($_SESSION['user_id']);


/*
|--------------------------------------------------------------------------
| Nicht angemeldete Benutzer
|--------------------------------------------------------------------------
*/

if (!$isLoggedIn):

?>

<section
    class="kfz-section kfz-account-page"
    aria-labelledby="account-login-title"
>

    <div class="container">

        <div class="kfz-account-locked">

            <div class="kfz-account-locked-icon">
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <rect
                        x="5"
                        y="10"
                        width="14"
                        height="10"
                        rx="2"
                    />

                    <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    <path d="M12 14v2" />
                </svg>
            </div>

            <span class="kfz-section-kicker">
                Persönlicher Bereich
            </span>

            <h1
                id="account-login-title"
                class="kfz-section-title"
            >
                Bitte melden Sie sich an.
            </h1>

            <p class="kfz-section-text">
                Für den Zugriff auf Ihr Konto müssen Sie angemeldet sein.
                Dort können Sie Fahrzeuge, Vorgänge und Dokumente verwalten.
            </p>

            <div class="kfz-account-locked-actions">

                <a
                    href="<?= $escape($url('/login/')) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    Jetzt anmelden
                </a>

                <a
                    href="<?= $escape($url('/register/')) ?>"
                    class="kfz-button kfz-button-outline"
                >
                    Konto erstellen
                </a>

            </div>

        </div>

    </div>

</section>

<?php

return;

endif;


/*
|--------------------------------------------------------------------------
| Benutzerinformationen
|--------------------------------------------------------------------------
*/

$userId = (int)(
    $_SESSION['user_id'] ?? 0
);

$firstName = trim(
    (string)($_SESSION['user_first_name'] ?? '')
);

$lastName = trim(
    (string)($_SESSION['user_last_name'] ?? '')
);

$email = trim(
    (string)($_SESSION['user_email'] ?? '')
);

$role = trim(
    (string)($_SESSION['user_role'] ?? 'user')
);

$loggedInAt = (int)(
    $_SESSION['logged_in_at'] ?? 0
);

$displayName = trim(
    $firstName . ' ' . $lastName
);

if ($displayName === '') {
    $displayName = 'Kfz-Digital-Nutzer';
}

$initials = '';

if ($firstName !== '') {
    $initials .= mb_strtoupper(
        mb_substr($firstName, 0, 1)
    );
}

if ($lastName !== '') {
    $initials .= mb_strtoupper(
        mb_substr($lastName, 0, 1)
    );
}

if ($initials === '') {
    $initials = 'KD';
}

$roleLabel = match ($role) {
    'admin' => 'Administrator',
    default => 'Kunde',
};

$loginDate = $loggedInAt > 0
    ? date('d.m.Y H:i', $loggedInAt)
    : 'Nicht verfügbar';
?>

<section
    class="kfz-section kfz-account-page"
    aria-labelledby="account-title"
>

    <div class="container">

        <!-- Kopfbereich -->

        <div class="kfz-account-header">

            <div class="kfz-account-welcome">

                <span class="kfz-section-kicker">
                    Persönlicher Bereich
                </span>

                <h1
                    id="account-title"
                    class="kfz-section-title"
                >
                    Willkommen,
                    <?= $escape($firstName !== '' ? $firstName : 'zurück') ?>.
                </h1>

                <p class="kfz-section-text">
                    Verwalten Sie Ihre Fahrzeuge, Vorgänge und Dokumente
                    zentral an einem Ort.
                </p>

            </div>


            <div
                class="kfz-account-user-badge"
                aria-label="Angemeldet als <?= $escape($displayName) ?>"
            >

                <span class="kfz-account-avatar">
                    <?= $escape($initials) ?>
                </span>

                <span class="kfz-account-user-info">

                    <strong>
                        <?= $escape($displayName) ?>
                    </strong>

                    <small>
                        <?= $escape($email) ?>
                    </small>

                </span>

            </div>

        </div>


        <!-- Schnellzugriff -->

        <div
            class="kfz-account-grid"
            aria-label="Schnellzugriff"
        >

            <a
                href="<?= $escape($url('/fahrzeuge/')) ?>"
                class="kfz-card kfz-account-action-card"
            >

                <span class="kfz-card-icon">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                        <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                        <circle cx="8" cy="16" r="1" />
                        <circle cx="16" cy="16" r="1" />
                    </svg>

                </span>

                <h2 class="kfz-card-title">
                    Meine Fahrzeuge
                </h2>

                <p class="kfz-card-text">
                    Verwalten Sie Ihre gespeicherten Fahrzeuge und
                    Fahrzeugdaten.
                </p>

                <span class="kfz-account-card-link">
                    Fahrzeuge öffnen
                    <span aria-hidden="true">→</span>
                </span>

            </a>


            <a
                href="<?= $escape($url('/vorgaenge/')) ?>"
                class="kfz-card kfz-account-action-card"
            >

                <span class="kfz-card-icon kfz-card-icon-teal">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M6 3h9l3 3v15H6V3Z" />
                        <path d="M14 3v4h4M9 12h6M9 16h6" />
                    </svg>

                </span>

                <h2 class="kfz-card-title">
                    Meine Vorgänge
                </h2>

                <p class="kfz-card-text">
                    Prüfen Sie den Status Ihrer laufenden und abgeschlossenen
                    Fahrzeugvorgänge.
                </p>

                <span class="kfz-account-card-link">
                    Vorgänge öffnen
                    <span aria-hidden="true">→</span>
                </span>

            </a>


            <a
                href="<?= $escape($url('/vorgang-starten/')) ?>"
                class="kfz-card kfz-account-action-card"
            >

                <span class="kfz-card-icon kfz-card-icon-purple">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M12 5v14M5 12h14" />
                    </svg>

                </span>

                <h2 class="kfz-card-title">
                    Neuen Vorgang starten
                </h2>

                <p class="kfz-card-text">
                    Starten Sie eine neue Anmeldung, Ummeldung, Abmeldung
                    oder Wiederzulassung.
                </p>

                <span class="kfz-account-card-link">
                    Vorgang starten
                    <span aria-hidden="true">→</span>
                </span>

            </a>

        </div>


        <!-- Kontodaten -->

        <div class="kfz-account-content-grid">

            <section
                class="kfz-card kfz-account-profile"
                aria-labelledby="profile-title"
            >

                <div class="kfz-account-card-heading">

                    <div>

                        <span class="kfz-section-kicker">
                            Konto
                        </span>

                        <h2
                            id="profile-title"
                            class="kfz-card-title"
                        >
                            Ihre Kontodaten
                        </h2>

                    </div>

                    <span class="kfz-account-profile-icon">
                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="8"
                                r="3.5"
                            />

                            <path d="M5 20a7 7 0 0 1 14 0" />
                        </svg>
                    </span>

                </div>


                <div class="kfz-account-data-list">

                    <div class="kfz-account-data-row">

                        <span>
                            Name
                        </span>

                        <strong>
                            <?= $escape($displayName) ?>
                        </strong>

                    </div>


                    <div class="kfz-account-data-row">

                        <span>
                            E-Mail-Adresse
                        </span>

                        <strong>
                            <?= $escape($email) ?>
                        </strong>

                    </div>


                    <div class="kfz-account-data-row">

                        <span>
                            Kontoart
                        </span>

                        <strong>
                            <?= $escape($roleLabel) ?>
                        </strong>

                    </div>


                    <div class="kfz-account-data-row">

                        <span>
                            Benutzer-ID
                        </span>

                        <strong>
                            #<?= $escape((string)$userId) ?>
                        </strong>

                    </div>

                </div>

            </section>


            <!-- Sicherheit -->

            <section
                class="kfz-card kfz-account-security"
                aria-labelledby="security-title"
            >

                <div class="kfz-account-card-heading">

                    <div>

                        <span class="kfz-section-kicker">
                            Sicherheit
                        </span>

                        <h2
                            id="security-title"
                            class="kfz-card-title"
                        >
                            Ihr Konto ist geschützt
                        </h2>

                    </div>

                    <span class="kfz-account-security-icon">
                        ✓
                    </span>

                </div>

                <p class="kfz-card-text">
                    Ihre Anmeldung ist aktiv. Bitte melden Sie sich ab,
                    wenn Sie dieses Gerät nicht mehr verwenden.
                </p>

                <p class="kfz-account-login-time">
                    Angemeldet seit:
                    <strong>
                        <?= $escape($loginDate) ?>
                    </strong>
                </p>

                <a
                    href="<?= $escape($url('/logout/')) ?>"
                    class="kfz-button kfz-button-outline kfz-account-logout-button"
                >
                    Abmelden
                </a>

            </section>

        </div>


        <!-- Weiterer Hinweis -->

        <div class="kfz-account-information">

            <span class="kfz-account-information-icon">
                i
            </span>

            <div>

                <strong>
                    Ihre Daten an einem Ort
                </strong>

                <p>
                    Sobald die weiteren Bereiche aktiviert sind, können Sie
                    hier Fahrzeuge hinzufügen, Dokumente verwalten und den
                    Status Ihrer i-Kfz-Vorgänge verfolgen.
                </p>

            </div>

        </div>

    </div>

</section>
