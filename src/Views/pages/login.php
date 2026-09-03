<?php
declare(strict_types=1);

/**
 * Kfz Digital – Login
 *
 * Header und Footer werden durch index.php eingebunden.
 *
 * Die Seite wird erst nach dem Layout-Header geladen.
 * Deshalb werden hier keine PHP-Weiterleitungen mit header()
 * verwendet.
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
| Datenbank laden
|--------------------------------------------------------------------------
*/

$pdo = require __DIR__ . '/../../Config/database.php';


/*
|--------------------------------------------------------------------------
| CSRF-Token erzeugen
|--------------------------------------------------------------------------
*/

if (
    empty($_SESSION['login_csrf_token'])
    || !is_string($_SESSION['login_csrf_token'])
) {
    $_SESSION['login_csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = (string)$_SESSION['login_csrf_token'];


/*
|--------------------------------------------------------------------------
| Formularwerte
|--------------------------------------------------------------------------
*/

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);

$email = strtolower(
    trim((string)($_POST['email'] ?? ''))
);

$password = (string)($_POST['password'] ?? '');

$remember = isset($_POST['remember']);

$errors = [];

$loginSuccessful = false;

$loggedInUserName = '';


/*
|--------------------------------------------------------------------------
| Login verarbeiten
|--------------------------------------------------------------------------
*/

if ($requestMethod === 'POST') {

    $submittedToken = (string)(
        $_POST['csrf_token'] ?? ''
    );


    /*
     * CSRF-Schutz prüfen
     */

    if (
        $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $errors[] =
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.';
    }


    /*
     * E-Mail prüfen
     */

    if ($email === '') {
        $errors[] =
            'Bitte geben Sie Ihre E-Mail-Adresse ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] =
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }


    /*
     * Passwort prüfen
     */

    if ($password === '') {
        $errors[] =
            'Bitte geben Sie Ihr Passwort ein.';
    }


    /*
     * Benutzer aus der Datenbank laden
     */

    if ($errors === []) {

        try {

            $statement = $pdo->prepare(
                'SELECT
                    id,
                    first_name,
                    last_name,
                    email,
                    password_hash,
                    role,
                    is_active
                 FROM users
                 WHERE email = :email
                 LIMIT 1'
            );

            $statement->execute([
                'email' => $email,
            ]);

            $user = $statement->fetch();


            /*
             * Benutzer und Passwort prüfen
             */

            $validUser = is_array($user)
                && (int)$user['is_active'] === 1
                && password_verify(
                    $password,
                    (string)$user['password_hash']
                );


            if (!$validUser) {

                $errors[] =
                    'E-Mail-Adresse oder Passwort ist falsch.';

            } else {

                /*
                 * Session-ID nur regenerieren,
                 * wenn noch keine Ausgabe gesendet wurde.
                 *
                 * Da index.php den Output-Buffer verwenden sollte,
                 * funktioniert dies normalerweise problemlos.
                 */
                if (!headers_sent()) {
                    session_regenerate_id(true);
                }


                /*
                 * Benutzerdaten in der Session speichern.
                 */

                $_SESSION['user_id'] = (int)$user['id'];

                $_SESSION['user_first_name'] =
                    (string)$user['first_name'];

                $_SESSION['user_last_name'] =
                    (string)$user['last_name'];

                $_SESSION['user_email'] =
                    (string)$user['email'];

                $_SESSION['user_role'] =
                    (string)$user['role'];

                $_SESSION['logged_in_at'] = time();

                $_SESSION['remember_login'] = $remember;


                /*
                 * Login-CSRF-Token erneuern.
                 */

                $_SESSION['login_csrf_token'] = bin2hex(
                    random_bytes(32)
                );

                $loginSuccessful = true;

                $loggedInUserName = (string)$user['first_name'];

                /*
                 * Session-Daten direkt speichern.
                 */
                session_write_close();
            }

        } catch (PDOException $exception) {

            $errors[] =
                'Die Anmeldung ist momentan nicht möglich.';
        }
    }
}
?>

<section
    class="kfz-section kfz-login-page"
    aria-labelledby="login-title"
>

    <div class="container">

        <?php if ($loginSuccessful): ?>

            <!-- Erfolgreiche Anmeldung -->

            <div class="kfz-login-success-wrapper">

                <div
                    class="kfz-login-success"
                    role="status"
                    aria-live="polite"
                >

                    <div class="kfz-login-success-icon">
                        ✓
                    </div>

                    <span class="kfz-section-kicker">
                        Kfz Digital
                    </span>

                    <h1>
                        Willkommen,
                        <?= $escape($loggedInUserName) ?>.
                    </h1>

                    <p>
                        Sie wurden erfolgreich angemeldet.
                        Sie werden nun zu Ihrem Konto weitergeleitet.
                    </p>

                    <a
                        href="<?= $escape($url('/konto/')) ?>"
                        class="kfz-button kfz-button-primary"
                    >
                        Zum Konto
                    </a>

                </div>

            </div>

            <script>
                window.setTimeout(function () {
                    window.location.href =
                        <?= json_encode(
                            $url('/konto/'),
                            JSON_UNESCAPED_SLASHES |
                            JSON_UNESCAPED_UNICODE
                        ) ?>;
                }, 900);
            </script>


        <?php else: ?>

            <div class="kfz-login-layout">

                <!-- Linker Informationsbereich -->

                <div class="kfz-login-intro">

                    <span class="kfz-section-kicker">
                        Kfz Digital
                    </span>

                    <h1
                        id="login-title"
                        class="kfz-section-title"
                    >
                        Willkommen zurück.
                    </h1>

                    <p class="kfz-section-text">
                        Melden Sie sich an, um Ihre Fahrzeuge, Vorgänge
                        und Dokumente zentral zu verwalten.
                    </p>


                    <div class="kfz-login-benefits">

                        <div class="kfz-login-benefit">

                            <span
                                class="kfz-login-benefit-icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </span>

                            <div>

                                <strong>
                                    Sicher verwalten
                                </strong>

                                <p>
                                    Ihre Fahrzeugdaten und Vorgänge
                                    bleiben übersichtlich an einem Ort.
                                </p>

                            </div>

                        </div>


                        <div class="kfz-login-benefit">

                            <span
                                class="kfz-login-benefit-icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                    />

                                    <path d="M12 7v5l3 2" />
                                </svg>
                            </span>

                            <div>

                                <strong>
                                    Vorgänge verfolgen
                                </strong>

                                <p>
                                    Behalten Sie den Status Ihrer
                                    Fahrzeugvorgänge jederzeit im Blick.
                                </p>

                            </div>

                        </div>


                        <div class="kfz-login-benefit">

                            <span
                                class="kfz-login-benefit-icon"
                                aria-hidden="true"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path d="M4 5h16v12H8l-4 4V5Z" />
                                    <path d="M8 9h8M8 13h5" />
                                </svg>
                            </span>

                            <div>

                                <strong>
                                    Digital und übersichtlich
                                </strong>

                                <p>
                                    Verwalten Sie Ihre Anträge, Dokumente
                                    und Fahrzeuge zentral.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Login-Karte -->

                <div class="kfz-login-card">

                    <div class="kfz-login-card-header">

                        <span class="kfz-login-card-icon">

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

                        <div>

                            <h2>
                                Anmelden
                            </h2>

                            <p>
                                Zugang zu Ihrem Kfz-Digital-Konto
                            </p>

                        </div>

                    </div>


                    <?php if ($errors !== []): ?>

                        <div
                            class="kfz-login-alert"
                            role="alert"
                            aria-labelledby="login-error-title"
                        >

                            <strong id="login-error-title">
                                Anmeldung nicht möglich
                            </strong>

                            <ul>

                                <?php foreach ($errors as $error): ?>

                                    <li>
                                        <?= $escape($error) ?>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                    <?php endif; ?>


                    <form
                        action="<?= $escape($url('/login/')) ?>"
                        method="post"
                        class="kfz-login-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $escape($csrfToken) ?>"
                        >


                        <div class="kfz-form-group">

                            <label
                                for="login-email"
                                class="kfz-form-label"
                            >
                                E-Mail-Adresse
                            </label>

                            <input
                                type="email"
                                id="login-email"
                                name="email"
                                class="kfz-form-control"
                                value="<?= $escape($email) ?>"
                                placeholder="name@beispiel.de"
                                autocomplete="email"
                                required
                            >

                        </div>


                        <div class="kfz-form-group">

                            <div class="kfz-login-password-label">

                                <label
                                    for="login-password"
                                    class="kfz-form-label"
                                >
                                    Passwort
                                </label>

                                <a
                                    href="<?= $escape($url('/hilfe/')) ?>"
                                    class="kfz-login-forgot-link"
                                >
                                    Passwort vergessen?
                                </a>

                            </div>

                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                class="kfz-form-control"
                                placeholder="Ihr Passwort"
                                autocomplete="current-password"
                                required
                            >

                        </div>


                        <div class="kfz-login-options">

                            <label
                                class="kfz-login-remember"
                                for="remember"
                            >

                                <input
                                    type="checkbox"
                                    id="remember"
                                    name="remember"
                                    value="1"
                                >

                                <span>
                                    Angemeldet bleiben
                                </span>

                            </label>

                        </div>


                        <button
                            type="submit"
                            class="kfz-button kfz-button-primary kfz-button-full"
                        >
                            Anmelden

                            <svg
                                class="kfz-button-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg>

                        </button>

                    </form>


                    <div class="kfz-login-divider">
                        <span>oder</span>
                    </div>


                    <div class="kfz-login-register">

                        <p>
                            Sie haben noch kein Konto?
                        </p>

                        <a
                            href="<?= $escape($url('/register/')) ?>"
                            class="kfz-button kfz-button-outline kfz-button-full"
                        >
                            Konto erstellen
                        </a>

                    </div>


                    <p class="kfz-login-security-note">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6l-7-3Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>

                        Ihre Verbindung ist geschützt.

                    </p>

                </div>

            </div>

        <?php endif; ?>

    </div>

</section>
