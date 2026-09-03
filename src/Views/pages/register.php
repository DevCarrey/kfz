<?php
declare(strict_types=1);

/**
 * Kfz Digital – Registrierung
 *
 * Header und Footer werden durch index.php eingebunden.
 */

$appPrefix = rtrim(
    (string)($GLOBALS['appPrefix'] ?? ''),
    '/'
);

$url = static function (string $path) use ($appPrefix): string {
    $path = '/' . ltrim($path, '/');

    return $appPrefix . ($path === '/' ? '/' : $path);
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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = (string)$_SESSION['csrf_token'];


/*
|--------------------------------------------------------------------------
| Formularwerte
|--------------------------------------------------------------------------
*/

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);

$firstName = trim(
    (string)($_POST['first_name'] ?? '')
);

$lastName = trim(
    (string)($_POST['last_name'] ?? '')
);

$email = strtolower(
    trim((string)($_POST['email'] ?? ''))
);

$password = (string)($_POST['password'] ?? '');
$passwordConfirmation = (string)(
    $_POST['password_confirmation'] ?? ''
);

$termsAccepted = isset($_POST['terms']);

$errors = [];
$registrationSuccessful = false;


/*
|--------------------------------------------------------------------------
| Formular verarbeiten
|--------------------------------------------------------------------------
*/

if ($requestMethod === 'POST') {

    $submittedToken = (string)(
        $_POST['csrf_token'] ?? ''
    );

    if (
        $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $errors[] =
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.';
    }

    if ($firstName === '') {
        $errors[] = 'Bitte geben Sie Ihren Vornamen ein.';
    } elseif (mb_strlen($firstName) > 100) {
        $errors[] = 'Der Vorname ist zu lang.';
    }

    if ($lastName === '') {
        $errors[] = 'Bitte geben Sie Ihren Nachnamen ein.';
    } elseif (mb_strlen($lastName) > 100) {
        $errors[] = 'Der Nachname ist zu lang.';
    }

    if ($email === '') {
        $errors[] =
            'Bitte geben Sie Ihre E-Mail-Adresse ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] =
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    } elseif (mb_strlen($email) > 190) {
        $errors[] =
            'Die E-Mail-Adresse ist zu lang.';
    }

    if ($password === '') {
        $errors[] =
            'Bitte geben Sie ein Passwort ein.';
    } elseif (strlen($password) < 8) {
        $errors[] =
            'Das Passwort muss mindestens 8 Zeichen enthalten.';
    } elseif (strlen($password) > 72) {
        $errors[] =
            'Das Passwort darf maximal 72 Zeichen enthalten.';
    }

    if ($password !== $passwordConfirmation) {
        $errors[] =
            'Die Passwörter stimmen nicht überein.';
    }

    if (!$termsAccepted) {
        $errors[] =
            'Bitte akzeptieren Sie die Nutzungsbedingungen.';
    }


    /*
     * Prüfen, ob E-Mail-Adresse bereits existiert.
     */

    if ($errors === []) {

        $emailCheckStatement = $pdo->prepare(
            'SELECT id
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $emailCheckStatement->execute([
            'email' => $email,
        ]);

        $existingUser = $emailCheckStatement->fetch();

        if ($existingUser !== false) {
            $errors[] =
                'Für diese E-Mail-Adresse existiert bereits ein Konto.';
        }
    }


    /*
     * Benutzer speichern.
     */

    if ($errors === []) {

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            $errors[] =
                'Das Passwort konnte nicht sicher verarbeitet werden.';
        } else {

            try {

                $insertStatement = $pdo->prepare(
                    'INSERT INTO users
                    (
                        first_name,
                        last_name,
                        email,
                        password_hash
                    )
                    VALUES
                    (
                        :first_name,
                        :last_name,
                        :email,
                        :password_hash
                    )'
                );

                $insertStatement->execute([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password_hash' => $passwordHash,
                ]);

                $registrationSuccessful = true;

                $firstName = '';
                $lastName = '';
                $email = '';
                $password = '';
                $passwordConfirmation = '';
                $termsAccepted = false;

                $_SESSION['csrf_token'] = bin2hex(
                    random_bytes(32)
                );

            } catch (PDOException $exception) {

                if (
                    isset($exception->errorInfo[1])
                    && (int)$exception->errorInfo[1] === 1062
                ) {
                    $errors[] =
                        'Für diese E-Mail-Adresse existiert bereits ein Konto.';
                } else {
                    $errors[] =
                        'Das Konto konnte nicht erstellt werden.';
                }
            }
        }
    }
}
?>

<section
    class="kfz-section kfz-register-page"
    aria-labelledby="register-title"
>

    <div class="container">

        <div class="kfz-register-layout">

            <div class="kfz-register-intro">

                <span class="kfz-section-kicker">
                    Kfz Digital
                </span>

                <h1
                    id="register-title"
                    class="kfz-section-title"
                >
                    Ihr digitales Fahrzeugkonto.
                </h1>

                <p class="kfz-section-text">
                    Erstellen Sie jetzt Ihr persönliches Konto und verwalten
                    Sie Fahrzeuge, Vorgänge und Dokumente zentral an einem Ort.
                </p>

                <div class="kfz-register-benefits">

                    <div class="kfz-register-benefit">

                        <span
                            class="kfz-register-benefit-icon"
                            aria-hidden="true"
                        >
                            ✓
                        </span>

                        <div>
                            <strong>
                                Fahrzeuge verwalten
                            </strong>

                            <p>
                                Speichern und verwalten Sie Ihre Fahrzeuge
                                übersichtlich.
                            </p>
                        </div>

                    </div>

                    <div class="kfz-register-benefit">

                        <span
                            class="kfz-register-benefit-icon"
                            aria-hidden="true"
                        >
                            ✓
                        </span>

                        <div>
                            <strong>
                                Vorgänge verfolgen
                            </strong>

                            <p>
                                Behalten Sie den Status Ihrer Anträge im Blick.
                            </p>
                        </div>

                    </div>

                    <div class="kfz-register-benefit">

                        <span
                            class="kfz-register-benefit-icon"
                            aria-hidden="true"
                        >
                            ✓
                        </span>

                        <div>
                            <strong>
                                Sicher anmelden
                            </strong>

                            <p>
                                Ihr Passwort wird sicher verschlüsselt gespeichert.
                            </p>
                        </div>

                    </div>

                </div>

            </div>


            <div class="kfz-register-card">

                <div class="kfz-register-card-header">

                    <span class="kfz-register-card-icon">
                        +
                    </span>

                    <div>

                        <h2>
                            Konto erstellen
                        </h2>

                        <p>
                            Registrieren Sie sich kostenlos bei Kfz Digital.
                        </p>

                    </div>

                </div>


                <?php if ($registrationSuccessful): ?>

                    <div
                        class="kfz-register-success"
                        role="status"
                        aria-live="polite"
                    >

                        <strong>
                            Registrierung erfolgreich
                        </strong>

                        <p>
                            Ihr Konto wurde erfolgreich erstellt.
                            Sie können sich jetzt anmelden.
                        </p>

                        <a
                            href="<?= $escape($url('/login/')) ?>"
                            class="kfz-button kfz-button-primary"
                        >
                            Jetzt anmelden
                        </a>

                    </div>

                <?php else: ?>

                    <?php if ($errors !== []): ?>

                        <div
                            class="kfz-register-alert"
                            role="alert"
                        >

                            <strong>
                                Bitte prüfen Sie Ihre Angaben.
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
                        action="<?= $escape($url('/register/')) ?>"
                        method="post"
                        class="kfz-register-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $escape($csrfToken) ?>"
                        >


                        <div class="kfz-form-grid kfz-form-grid-two">

                            <div class="kfz-form-group">

                                <label
                                    for="first-name"
                                    class="kfz-form-label"
                                >
                                    Vorname
                                </label>

                                <input
                                    type="text"
                                    id="first-name"
                                    name="first_name"
                                    class="kfz-form-control"
                                    value="<?= $escape($firstName) ?>"
                                    autocomplete="given-name"
                                    maxlength="100"
                                    required
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="last-name"
                                    class="kfz-form-label"
                                >
                                    Nachname
                                </label>

                                <input
                                    type="text"
                                    id="last-name"
                                    name="last_name"
                                    class="kfz-form-control"
                                    value="<?= $escape($lastName) ?>"
                                    autocomplete="family-name"
                                    maxlength="100"
                                    required
                                >

                            </div>

                        </div>


                        <div class="kfz-form-group">

                            <label
                                for="register-email"
                                class="kfz-form-label"
                            >
                                E-Mail-Adresse
                            </label>

                            <input
                                type="email"
                                id="register-email"
                                name="email"
                                class="kfz-form-control"
                                value="<?= $escape($email) ?>"
                                placeholder="name@beispiel.de"
                                autocomplete="email"
                                maxlength="190"
                                required
                            >

                        </div>


                        <div class="kfz-form-group">

                            <label
                                for="register-password"
                                class="kfz-form-label"
                            >
                                Passwort
                            </label>

                            <input
                                type="password"
                                id="register-password"
                                name="password"
                                class="kfz-form-control"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >

                            <small class="kfz-form-help">
                                Mindestens 8 Zeichen.
                            </small>

                        </div>


                        <div class="kfz-form-group">

                            <label
                                for="register-password-confirmation"
                                class="kfz-form-label"
                            >
                                Passwort wiederholen
                            </label>

                            <input
                                type="password"
                                id="register-password-confirmation"
                                name="password_confirmation"
                                class="kfz-form-control"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >

                        </div>


                        <div class="kfz-register-checkbox">

                            <input
                                type="checkbox"
                                id="terms"
                                name="terms"
                                value="1"
                                required
                                <?= $termsAccepted ? 'checked' : '' ?>
                            >

                            <label for="terms">

                                Ich akzeptiere die
                                <a
                                    href="<?= $escape($url('/nutzungsbedingungen/')) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Nutzungsbedingungen
                                </a>
                                und habe die
                                <a
                                    href="<?= $escape($url('/datenschutz/')) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Datenschutzerklärung
                                </a>
                                gelesen.

                            </label>

                        </div>


                        <button
                            type="submit"
                            class="kfz-button kfz-button-primary kfz-button-full"
                        >
                            Konto erstellen

                            <svg
                                class="kfz-button-icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg>

                        </button>

                    </form>


                    <div class="kfz-register-login-link">

                        <span>
                            Sie haben bereits ein Konto?
                        </span>

                        <a
                            href="<?= $escape($url('/login/')) ?>"
                        >
                            Jetzt anmelden
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>
