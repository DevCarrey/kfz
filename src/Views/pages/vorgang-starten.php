<?php
declare(strict_types=1);

/**
 * Kfz Digital – Fahrzeugabmeldung
 *
 * Ablauf:
 * 1. Formular ausfüllen
 * 2. Bestätigungscode erzeugen
 * 3. Bestätigungscode prüfen
 * 4. Vorgang mit "zahlung_offen" speichern
 * 5. Zahlungseintrag erzeugen
 * 6. Zur Zahlung weiterleiten
 *
 * Kein Kundenlogin erforderlich.
 */

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);

$escape = static fn (mixed $value): string => kfz_escape($value);


/*
|--------------------------------------------------------------------------
| Hilfsfunktionen
|--------------------------------------------------------------------------
*/

$getInput = static function (
    array $source,
    string $key,
    string $default = ''
): string {
    $value = $source[$key] ?? $default;

    if (!is_scalar($value)) {
        return $default;
    }

    return trim((string)$value);
};

$addError = static function (
    array &$errors,
    string $message
): void {
    if (!in_array($message, $errors, true)) {
        $errors[] = $message;
    }
};

$nullable = static function (
    string $value
): ?string {
    return $value === '' ? null : $value;
};


/*
|--------------------------------------------------------------------------
| Weiterleitung
|--------------------------------------------------------------------------
*/

$redirect = static function (
    string $location
): never {
    if (!headers_sent()) {
        header(
            'Location: ' . $location,
            true,
            303
        );

        exit;
    }

    $safeLocation = htmlspecialchars(
        $location,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

    $jsonLocation = json_encode(
        $location,
        JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    );

    echo '<meta http-equiv="refresh" content="0;url='
        . $safeLocation
        . '">';

    echo '<script>';
    echo 'window.location.replace(';
    echo $jsonLocation;
    echo ');';
    echo '</script>';

    exit;
};


/*
|--------------------------------------------------------------------------
| Datenbank laden
|--------------------------------------------------------------------------
*/

try {
    $pdo = require __DIR__ . '/../../Config/database.php';
} catch (Throwable $exception) {
    ?>
    <section class="kfz-section">
        <div class="container">
            <div
                class="kfz-process-error"
                role="alert"
            >
                Die Datenbankverbindung konnte nicht hergestellt werden.
            </div>
        </div>
    </section>
    <?php
    return;
}


/*
|--------------------------------------------------------------------------
| Zahlungs-Konfiguration
|--------------------------------------------------------------------------
*/

$paymentConfigFile = __DIR__ . '/../../Config/payment.php';

if (is_file($paymentConfigFile)) {
    $paymentConfig = require $paymentConfigFile;
} else {
    $paymentConfig = [
        'mode' => 'mock',
        'amount_cents' => 4990,
        'currency' => 'EUR',
    ];
}

$paymentProvider = strtolower(
    (string)($paymentConfig['mode'] ?? 'mock')
);

$paymentAmountCents = (int)(
    $paymentConfig['amount_cents'] ?? 4990
);

$paymentCurrency = strtoupper(
    (string)($paymentConfig['currency'] ?? 'EUR')
);

if ($paymentAmountCents <= 0) {
    $paymentAmountCents = 4990;
}

if ($paymentCurrency === '') {
    $paymentCurrency = 'EUR';
}


/*
|--------------------------------------------------------------------------
| CSRF-Token
|--------------------------------------------------------------------------
*/

if (
    empty($_SESSION['process_csrf_token'])
    || !is_string($_SESSION['process_csrf_token'])
) {
    $_SESSION['process_csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = (string)$_SESSION['process_csrf_token'];


/*
|--------------------------------------------------------------------------
| Grundwerte
|--------------------------------------------------------------------------
*/

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);

$formErrors = [];
$showVerification = false;

$licensePlate = '';
$licensePlateCity = '';
$licensePlateLetters = '';
$licensePlateNumbers = '';

$securityCodeBack = '';
$securityCodeFront = '';
$securityCodeZb1 = '';

$noFrontPlate = false;
$vin = '';

$firstName = '';
$lastName = '';
$email = '';
$phone = '';
$postalCode = '';
$city = '';
$notes = '';

$privacyAccepted = false;


/*
|--------------------------------------------------------------------------
| Formularziel
|--------------------------------------------------------------------------
*/

$isHomeProcess = !empty(
    $GLOBALS['render_process_on_home']
);

$formAction = $isHomeProcess
    ? $url('/')
    : $url('/vorgang-starten/');


/*
|--------------------------------------------------------------------------
| Vorgang abbrechen
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'GET'
    && isset($_GET['abbrechen'])
) {
    unset(
        $_SESSION['pending_application'],
        $_SESSION['application_verification_code_hash'],
        $_SESSION['application_verification_code_mock'],
        $_SESSION['application_verification_expires'],
        $_SESSION['application_verification_attempts'],
        $_SESSION['pending_payment_application_id'],
        $_SESSION['pending_payment_reference']
    );

    $redirect($url('/'));
}


/*
|--------------------------------------------------------------------------
| Kennzeichen einlesen
|--------------------------------------------------------------------------
*/

$licensePlateCity = strtoupper(
    $getInput($_POST, 'kennzeichen_ort')
);

$licensePlateLetters = strtoupper(
    $getInput($_POST, 'kennzeichen_buchstaben')
);

$licensePlateNumbers = $getInput(
    $_POST,
    'kennzeichen_zahlen'
);


/*
|--------------------------------------------------------------------------
| Schnellstart mit altem Kennzeichenformat
|--------------------------------------------------------------------------
|
| Beispiel:
| ?kennzeichen=DO-XY%201234
|--------------------------------------------------------------------------
*/

$legacyLicensePlate = strtoupper(
    $getInput($_GET, 'kennzeichen')
);

if (
    $requestMethod !== 'POST'
    && $legacyLicensePlate !== ''
    && $licensePlateCity === ''
    && $licensePlateLetters === ''
    && $licensePlateNumbers === ''
) {
    $legacyLicensePlate = preg_replace(
        '/\s+/',
        ' ',
        $legacyLicensePlate
    ) ?? '';

    if (preg_match(
        '/^([A-ZÄÖÜ]{1,3})[- ]?([A-Z]{1,2})[ ]?([0-9]{1,4})$/u',
        $legacyLicensePlate,
        $matches
    )) {
        $licensePlateCity = $matches[1];
        $licensePlateLetters = $matches[2];
        $licensePlateNumbers = $matches[3];
    }
}


/*
|--------------------------------------------------------------------------
| Kennzeichen zusammensetzen
|--------------------------------------------------------------------------
*/

$licensePlate = trim(
    $licensePlateCity
    . '-'
    . $licensePlateLetters
    . ' '
    . $licensePlateNumbers
);


/*
|--------------------------------------------------------------------------
| Weitere Eingaben
|--------------------------------------------------------------------------
*/

$securityCodeBack = strtoupper(
    $getInput($_POST, 'sicherheitscode_hinten')
);

$securityCodeFront = strtoupper(
    $getInput($_POST, 'sicherheitscode_vorne')
);

$securityCodeZb1 = strtoupper(
    $getInput($_POST, 'sicherheitscode_zb1')
);

$noFrontPlate = isset(
    $_POST['kein_vorderes_kennzeichen']
);

$vin = strtoupper(
    $getInput($_POST, 'fahrzeugidentifikationsnummer')
);

$firstName = $getInput(
    $_POST,
    'vorname'
);

$lastName = $getInput(
    $_POST,
    'nachname'
);

$email = strtolower(
    $getInput($_POST, 'email')
);

$phone = $getInput(
    $_POST,
    'telefon'
);

$postalCode = $getInput(
    $_POST,
    'plz'
);

$city = $getInput(
    $_POST,
    'ort'
);

$notes = $getInput(
    $_POST,
    'hinweise'
);

$privacyAccepted = isset(
    $_POST['datenschutz']
);


/*
|--------------------------------------------------------------------------
| Bestätigungscode prüfen
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    && $getInput($_POST, 'form_action') === 'verify'
) {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken)
        || $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $addError(
            $formErrors,
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.'
        );
    }

    $enteredCode = $getInput(
        $_POST,
        'bestaetigungscode'
    );

    $codeHash = (string)(
        $_SESSION['application_verification_code_hash']
        ?? ''
    );

    $expiresAt = (int)(
        $_SESSION['application_verification_expires']
        ?? 0
    );

    $attempts = (int)(
        $_SESSION['application_verification_attempts']
        ?? 0
    );

    if (
        $codeHash === ''
        || $expiresAt < time()
    ) {
        $addError(
            $formErrors,
            'Der Bestätigungscode ist abgelaufen. Bitte starten Sie den Vorgang erneut.'
        );
    } elseif ($attempts >= 5) {
        $addError(
            $formErrors,
            'Zu viele Fehlversuche. Bitte starten Sie den Vorgang erneut.'
        );
    } elseif (!preg_match('/^[0-9]{6}$/', $enteredCode)) {
        $_SESSION['application_verification_attempts'] =
            $attempts + 1;

        $addError(
            $formErrors,
            'Bitte geben Sie einen sechsstelligen Bestätigungscode ein.'
        );
    } elseif (!password_verify($enteredCode, $codeHash)) {
        $_SESSION['application_verification_attempts'] =
            $attempts + 1;

        $addError(
            $formErrors,
            'Der Bestätigungscode ist nicht korrekt.'
        );
    }


    /*
     * Code korrekt: Vorgang und Zahlung speichern
     */

    if ($formErrors === []) {
        $pendingApplication = $_SESSION[
            'pending_application'
        ] ?? null;

        if (!is_array($pendingApplication)) {
            $addError(
                $formErrors,
                'Die Vorgangsdaten sind nicht mehr verfügbar.'
            );

            $showVerification = true;
        } else {
            try {
                /*
                 * Vorgangsnummer erzeugen
                 */
                do {
                    $referenceNumber = sprintf(
                        'KD-%s-%06d',
                        date('Ymd'),
                        random_int(100000, 999999)
                    );

                    $referenceStatement = $pdo->prepare(
                        'SELECT id
                         FROM applications
                         WHERE reference_number = :reference_number
                         LIMIT 1'
                    );

                    $referenceStatement->execute([
                        'reference_number' => $referenceNumber,
                    ]);

                    $referenceExists =
                        $referenceStatement->fetchColumn() !== false;
                } while ($referenceExists);


                $pdo->beginTransaction();


                /*
                 * Anwendung speichern
                 */
                $applicationStatement = $pdo->prepare(
                    'INSERT INTO applications
                    (
                        user_id,
                        vehicle_id,
                        reference_number,
                        process_type,
                        status,
                        license_plate,
                        vin,
                        first_name,
                        last_name,
                        email,
                        phone,
                        postal_code,
                        city,
                        notes,
                        privacy_accepted_at,
                        security_code_front,
                        security_code_back,
                        security_code_zb1,
                        no_front_plate
                    )
                    VALUES
                    (
                        NULL,
                        NULL,
                        :reference_number,
                        :process_type,
                        :status,
                        :license_plate,
                        :vin,
                        :first_name,
                        :last_name,
                        :email,
                        :phone,
                        :postal_code,
                        :city,
                        :notes,
                        :privacy_accepted_at,
                        :security_code_front,
                        :security_code_back,
                        :security_code_zb1,
                        :no_front_plate
                    )'
                );

                $applicationStatement->execute([
                    'reference_number' => $referenceNumber,
                    'process_type' => 'abmeldung',
                    'status' => 'zahlung_offen',

                    'license_plate' => $nullable(
                        (string)(
                            $pendingApplication['license_plate']
                            ?? ''
                        )
                    ),

                    'vin' => $nullable(
                        (string)(
                            $pendingApplication['vin']
                            ?? ''
                        )
                    ),

                    'first_name' => (string)(
                        $pendingApplication['first_name']
                        ?? ''
                    ),

                    'last_name' => (string)(
                        $pendingApplication['last_name']
                        ?? ''
                    ),

                    'email' => (string)(
                        $pendingApplication['email']
                        ?? ''
                    ),

                    'phone' => $nullable(
                        (string)(
                            $pendingApplication['phone']
                            ?? ''
                        )
                    ),

                    'postal_code' => $nullable(
                        (string)(
                            $pendingApplication['postal_code']
                            ?? ''
                        )
                    ),

                    'city' => $nullable(
                        (string)(
                            $pendingApplication['city']
                            ?? ''
                        )
                    ),

                    'notes' => $nullable(
                        (string)(
                            $pendingApplication['notes']
                            ?? ''
                        )
                    ),

                    'privacy_accepted_at' =>
                        date('Y-m-d H:i:s'),

                    'security_code_front' => $nullable(
                        (string)(
                            $pendingApplication[
                                'security_code_front'
                            ] ?? ''
                        )
                    ),

                    'security_code_back' => $nullable(
                        (string)(
                            $pendingApplication[
                                'security_code_back'
                            ] ?? ''
                        )
                    ),

                    'security_code_zb1' => $nullable(
                        (string)(
                            $pendingApplication[
                                'security_code_zb1'
                            ] ?? ''
                        )
                    ),

                    'no_front_plate' => !empty(
                        $pendingApplication['no_front_plate']
                    ) ? 1 : 0,
                ]);

                $applicationId = (int)$pdo->lastInsertId();

                if ($applicationId <= 0) {
                    throw new RuntimeException(
                        'Der Vorgang konnte nicht angelegt werden.'
                    );
                }


                /*
                 * Statushistorie speichern
                 */
                $historyStatement = $pdo->prepare(
                    'INSERT INTO application_status_history
                    (
                        application_id,
                        old_status,
                        new_status,
                        comment
                    )
                    VALUES
                    (
                        :application_id,
                        NULL,
                        :new_status,
                        :comment
                    )'
                );

                $historyStatement->execute([
                    'application_id' => $applicationId,
                    'new_status' => 'zahlung_offen',
                    'comment' =>
                        'Vorgang angelegt. Zahlung steht noch aus.',
                ]);


                /*
                 * Zahlungseintrag speichern
                 */
                $paymentStatement = $pdo->prepare(
                    'INSERT INTO payments
                    (
                        application_id,
                        provider,
                        amount_cents,
                        currency,
                        status
                    )
                    VALUES
                    (
                        :application_id,
                        :provider,
                        :amount_cents,
                        :currency,
                        :status
                    )'
                );

                $paymentStatement->execute([
                    'application_id' => $applicationId,
                    'provider' => $paymentProvider,
                    'amount_cents' => $paymentAmountCents,
                    'currency' => $paymentCurrency,
                    'status' => 'offen',
                ]);

                $pdo->commit();


                /*
                 * Temporäre Daten löschen
                 */
                unset(
                    $_SESSION['pending_application'],
                    $_SESSION['application_verification_code_hash'],
                    $_SESSION['application_verification_code_mock'],
                    $_SESSION['application_verification_expires'],
                    $_SESSION['application_verification_attempts']
                );


                /*
                 * Zahlungsvorgang merken
                 */
                $_SESSION[
                    'pending_payment_application_id'
                ] = $applicationId;

                $_SESSION[
                    'pending_payment_reference'
                ] = $referenceNumber;


                /*
                 * CSRF-Token erneuern
                 */
                $_SESSION['process_csrf_token'] = bin2hex(
                    random_bytes(32)
                );


                /*
                 * Zur Zahlung weiterleiten
                 */
                $redirect(
                    $url('/zahlung/')
                );
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $addError(
                    $formErrors,
                    'Der Vorgang konnte nicht gespeichert werden.'
                );

                $showVerification = true;
            }
        }
    } else {
        $showVerification = true;
    }
}


/*
|--------------------------------------------------------------------------
| Formular validieren
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    && $getInput($_POST, 'form_action') === 'start'
) {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken)
        || $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $addError(
            $formErrors,
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.'
        );
    }


    /*
     * Kennzeichen prüfen
     */

    if (
        $licensePlateCity === ''
        || !preg_match(
            '/^[A-ZÄÖÜ]{1,3}$/u',
            $licensePlateCity
        )
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie ein gültiges Ortskürzel mit maximal drei Buchstaben ein.'
        );
    }

    if (
        $licensePlateLetters === ''
        || !preg_match(
            '/^[A-Z]{1,2}$/',
            $licensePlateLetters
        )
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie maximal zwei Buchstaben nach dem Ortskürzel ein.'
        );
    }

    if (
        $licensePlateNumbers === ''
        || !preg_match(
            '/^[0-9]{1,4}$/',
            $licensePlateNumbers
        )
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie maximal vier Zahlen für das Kennzeichen ein.'
        );
    }

    $licensePlate = trim(
        $licensePlateCity
        . '-'
        . $licensePlateLetters
        . ' '
        . $licensePlateNumbers
    );


    /*
     * Persönliche Daten prüfen
     */

    if ($firstName === '') {
        $addError(
            $formErrors,
            'Bitte geben Sie Ihren Vornamen ein.'
        );
    } elseif (mb_strlen($firstName) > 100) {
        $addError(
            $formErrors,
            'Der Vorname ist zu lang.'
        );
    }

    if ($lastName === '') {
        $addError(
            $formErrors,
            'Bitte geben Sie Ihren Nachnamen ein.'
        );
    } elseif (mb_strlen($lastName) > 100) {
        $addError(
            $formErrors,
            'Der Nachname ist zu lang.'
        );
    }

    if (
        $email === ''
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.'
        );
    } elseif (mb_strlen($email) > 190) {
        $addError(
            $formErrors,
            'Die E-Mail-Adresse ist zu lang.'
        );
    }


    /*
     * Sicherheitscodes prüfen
     */

    if (
        $securityCodeBack === ''
        || !preg_match(
            '/^[A-Z0-9]{4,20}$/',
            $securityCodeBack
        )
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie den Sicherheitscode hinten ein.'
        );
    }

    if ($noFrontPlate) {
        $securityCodeFront = '';
    } elseif (
        $securityCodeFront === ''
        || !preg_match(
            '/^[A-Z0-9]{4,20}$/',
            $securityCodeFront
        )
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie den Sicherheitscode vorne ein.'
        );
    }


    /*
     * FIN prüfen
     */

    if (
        $vin === ''
        || !preg_match(
            '/^[A-HJ-NPR-Z0-9]{17}$/',
            $vin
        )
    ) {
        $addError(
            $formErrors,
            'Die FIN muss genau 17 gültige Zeichen enthalten und darf I, O und Q nicht enthalten.'
        );
    }


    /*
     * Sicherheitscode ZB I prüfen
     */

    if (
        $securityCodeZb1 === ''
        || !preg_match(
            '/^[A-Z0-9]{4,20}$/',
            $securityCodeZb1
        )
    ) {
        $addError(
            $formErrors,
            'Bitte geben Sie den Sicherheitscode ZB I ein.'
        );
    }


    /*
     * Optionale Daten prüfen
     */

    if ($phone !== '' && mb_strlen($phone) > 50) {
        $addError(
            $formErrors,
            'Die Telefonnummer ist zu lang.'
        );
    }

    if (
        $postalCode !== ''
        && !preg_match(
            '/^[0-9]{5}$/',
            $postalCode
        )
    ) {
        $addError(
            $formErrors,
            'Die Postleitzahl muss aus fünf Ziffern bestehen.'
        );
    }

    if ($postalCode !== '' && $city === '') {
        $addError(
            $formErrors,
            'Bitte geben Sie auch den Ort an.'
        );
    }

    if ($postalCode === '' && $city !== '') {
        $addError(
            $formErrors,
            'Bitte geben Sie auch die Postleitzahl an.'
        );
    }

    if ($city !== '' && mb_strlen($city) > 100) {
        $addError(
            $formErrors,
            'Der Ort ist zu lang.'
        );
    }

    if (mb_strlen($notes) > 5000) {
        $addError(
            $formErrors,
            'Die Hinweise sind zu lang.'
        );
    }

    if (!$privacyAccepted) {
        $addError(
            $formErrors,
            'Bitte bestätigen Sie die Datenschutzhinweise.'
        );
    }


    /*
     * Temporären Vorgang speichern
     */

    if ($formErrors === []) {
        $verificationCode = (string)random_int(
            100000,
            999999
        );

        $_SESSION['pending_application'] = [
            'license_plate' => $licensePlate,
            'vin' => $vin,
            'security_code_front' => $securityCodeFront,
            'security_code_back' => $securityCodeBack,
            'security_code_zb1' => $securityCodeZb1,
            'no_front_plate' => $noFrontPlate ? 1 : 0,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'postal_code' => $postalCode,
            'city' => $city,
            'notes' => $notes,
        ];

        $_SESSION['application_verification_code_hash'] =
            password_hash(
                $verificationCode,
                PASSWORD_DEFAULT
            );

        /*
         * Nur im Mockbetrieb sichtbar.
         */
        $_SESSION['application_verification_code_mock'] =
            $verificationCode;

        $_SESSION['application_verification_expires'] =
            time() + 600;

        $_SESSION['application_verification_attempts'] =
            0;

        $showVerification = true;
    }
}


/*
|--------------------------------------------------------------------------
| Offene Bestätigung nach GET anzeigen
|--------------------------------------------------------------------------
*/

if (
    !empty($_SESSION['pending_application'])
    && $requestMethod === 'GET'
) {
    $showVerification = true;
}
?>

<section
    id="fahrzeug-abmelden"
    class="kfz-section kfz-process-page"
    aria-labelledby="process-page-title"
>
    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Ohne Registrierung
            </span>

            <h1
                id="process-page-title"
                class="kfz-section-title"
            >
                Fahrzeug online abmelden
            </h1>

            <p class="kfz-section-text">
                Füllen Sie das Formular aus und bestätigen Sie Ihre Angaben
                anschließend mit einem sechsstelligen Code.
            </p>

        </div>


        <?php if ($showVerification): ?>

            <?php if ($formErrors !== []): ?>

                <div
                    class="kfz-process-error"
                    role="alert"
                >
                    <h2>
                        Bitte prüfen Sie den Bestätigungscode
                    </h2>

                    <ul>
                        <?php foreach ($formErrors as $error): ?>
                            <li>
                                <?= $escape($error) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endif; ?>


            <div class="kfz-process-form-wrapper">

                <div class="kfz-process-form-header">

                    <div>
                        <span class="kfz-section-kicker">
                            Schritt 2 von 2
                        </span>

                        <h2>
                            Vorgang bestätigen
                        </h2>

                        <p>
                            Geben Sie den sechsstelligen Bestätigungscode ein.
                        </p>
                    </div>

                </div>


                <div class="kfz-process-info-card">

                    <strong>
                        Testbetrieb
                    </strong>

                    <p>
                        Ihr Bestätigungscode lautet:
                    </p>

                    <p class="kfz-verification-code">
                        <?= $escape(
                            $_SESSION[
                                'application_verification_code_mock'
                            ] ?? ''
                        ) ?>
                    </p>

                    <small>
                        Im Produktivbetrieb wird der Code per E-Mail
                        versendet und nicht auf der Website angezeigt.
                    </small>

                </div>


                <form
                    action="<?= $escape($formAction) ?>"
                    method="post"
                    class="kfz-process-form"
                >

                    <input
                        type="hidden"
                        name="form_action"
                        value="verify"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape($csrfToken) ?>"
                    >

                    <div class="kfz-form-group">

                        <label
                            for="bestaetigungscode"
                            class="kfz-form-label"
                        >
                            Bestätigungscode*
                        </label>

                        <input
                            type="text"
                            id="bestaetigungscode"
                            name="bestaetigungscode"
                            class="kfz-form-control"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            minlength="6"
                            maxlength="6"
                            autocomplete="one-time-code"
                            placeholder="123456"
                            required
                        >

                    </div>


                    <div class="kfz-process-form-actions">

                        <a
                            href="<?= $escape(
                                $url(
                                    '/vorgang-starten/?abbrechen=1'
                                )
                            ) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Abbrechen
                        </a>

                        <button
                            type="submit"
                            class="kfz-button kfz-button-primary"
                        >
                            Weiter zur Zahlung
                        </button>

                    </div>

                </form>

            </div>


        <?php else: ?>

            <?php if ($formErrors !== []): ?>

                <div
                    class="kfz-process-error"
                    role="alert"
                >
                    <h2>
                        Bitte prüfen Sie Ihre Angaben
                    </h2>

                    <ul>
                        <?php foreach ($formErrors as $error): ?>
                            <li>
                                <?= $escape($error) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endif; ?>


            <div class="kfz-process-form-wrapper">

                <div class="kfz-process-form-header">

                    <div>
                        <span class="kfz-section-kicker">
                            Schritt 1 von 2
                        </span>

                        <h2>
                            Fahrzeugdaten eingeben
                        </h2>

                        <p>
                            Eine Registrierung ist nicht erforderlich.
                        </p>
                    </div>

                </div>


                <form
                    action="<?= $escape($formAction) ?>"
                    method="post"
                    class="kfz-process-form"
                >

                    <input
                        type="hidden"
                        name="form_action"
                        value="start"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape($csrfToken) ?>"
                    >


                    <fieldset class="kfz-form-fieldset">

                        <legend>
                            Kennzeichen
                        </legend>

                        <div class="kfz-form-group">

                            <label
                                class="kfz-form-label"
                                for="kennzeichen-ort"
                            >
                                Kennzeichen*
                            </label>

                            <div
                                class="kfz-license-plate"
                                role="group"
                                aria-label="Kennzeichen eingeben"
                            >

                                <input
                                    type="text"
                                    id="kennzeichen-ort"
                                    name="kennzeichen_ort"
                                    class="kfz-form-control kfz-license-input kfz-license-city"
                                    value="<?= $escape(
                                        $licensePlateCity
                                    ) ?>"
                                    placeholder="DO"
                                    maxlength="3"
                                    minlength="1"
                                    pattern="[A-Za-zÄÖÜäöü]{1,3}"
                                    autocomplete="off"
                                    autocapitalize="characters"
                                    spellcheck="false"
                                    aria-label="Ortskürzel"
                                    required
                                >

                                <span
                                    class="kfz-license-separator"
                                    aria-hidden="true"
                                >
                                    -
                                </span>

                                <input
                                    type="text"
                                    id="kennzeichen-buchstaben"
                                    name="kennzeichen_buchstaben"
                                    class="kfz-form-control kfz-license-input kfz-license-letters"
                                    value="<?= $escape(
                                        $licensePlateLetters
                                    ) ?>"
                                    placeholder="XY"
                                    maxlength="2"
                                    minlength="1"
                                    pattern="[A-Za-z]{1,2}"
                                    autocomplete="off"
                                    autocapitalize="characters"
                                    spellcheck="false"
                                    aria-label="Buchstaben"
                                    required
                                >

                                <input
                                    type="text"
                                    id="kennzeichen-zahlen"
                                    name="kennzeichen_zahlen"
                                    class="kfz-form-control kfz-license-input kfz-license-numbers"
                                    value="<?= $escape(
                                        $licensePlateNumbers
                                    ) ?>"
                                    placeholder="1234"
                                    maxlength="4"
                                    minlength="1"
                                    pattern="[0-9]{1,4}"
                                    inputmode="numeric"
                                    autocomplete="off"
                                    aria-label="Zahlen"
                                    required
                                >

                            </div>

                            <small class="kfz-form-help">
                                Beispiel: DO-XY 1234
                            </small>

                        </div>

                    </fieldset>


                    <fieldset class="kfz-form-fieldset">

                        <legend>
                            Sicherheitscodes
                        </legend>

                        <div class="kfz-form-group">

                            <label
                                for="sicherheitscode-hinten"
                                class="kfz-form-label"
                            >
                                Sicherheitscode hinten*
                            </label>

                            <input
                                type="text"
                                id="sicherheitscode-hinten"
                                name="sicherheitscode_hinten"
                                class="kfz-form-control"
                                value="<?= $escape(
                                    $securityCodeBack
                                ) ?>"
                                maxlength="20"
                                autocomplete="off"
                                required
                            >

                        </div>


                        <div class="kfz-form-group">

                            <label
                                for="sicherheitscode-vorne"
                                class="kfz-form-label"
                            >
                                Sicherheitscode vorne*
                            </label>

                            <input
                                type="text"
                                id="sicherheitscode-vorne"
                                name="sicherheitscode_vorne"
                                class="kfz-form-control"
                                value="<?= $escape(
                                    $securityCodeFront
                                ) ?>"
                                maxlength="20"
                                autocomplete="off"
                            >

                        </div>


                        <div class="kfz-form-checkbox">

                            <input
                                type="checkbox"
                                id="kein-vorderes-kennzeichen"
                                name="kein_vorderes_kennzeichen"
                                value="1"
                            >

                            <label
                                for="kein-vorderes-kennzeichen"
                            >
                                Ich habe vorne kein Kennzeichen
                            </label>

                        </div>

                    </fieldset>


                    <fieldset class="kfz-form-fieldset">

                        <legend>
                            Fahrzeugschein
                        </legend>

                        <div class="kfz-form-group">

                            <label
                                for="fahrzeugidentifikationsnummer"
                                class="kfz-form-label"
                            >
                                FIN*
                            </label>

                            <input
                                type="text"
                                id="fahrzeugidentifikationsnummer"
                                name="fahrzeugidentifikationsnummer"
                                class="kfz-form-control"
                                value="<?= $escape($vin) ?>"
                                placeholder="17-stellige FIN"
                                maxlength="17"
                                autocomplete="off"
                                required
                            >

                            <small class="kfz-form-help">
                                Die FIN darf die Buchstaben I, O und Q
                                nicht enthalten.
                            </small>

                        </div>


                        <div class="kfz-form-group">

                            <label
                                for="sicherheitscode-zb1"
                                class="kfz-form-label"
                            >
                                Sicherheitscode ZB I*
                            </label>

                            <input
                                type="text"
                                id="sicherheitscode-zb1"
                                name="sicherheitscode_zb1"
                                class="kfz-form-control"
                                value="<?= $escape(
                                    $securityCodeZb1
                                ) ?>"
                                maxlength="20"
                                autocomplete="off"
                                required
                            >

                        </div>

                    </fieldset>


                    <fieldset class="kfz-form-fieldset">

                        <legend>
                            Ihre Kontaktdaten
                        </legend>

                        <div class="kfz-form-grid kfz-form-grid-two">

                            <div class="kfz-form-group">

                                <label
                                    for="vorname"
                                    class="kfz-form-label"
                                >
                                    Vorname*
                                </label>

                                <input
                                    type="text"
                                    id="vorname"
                                    name="vorname"
                                    class="kfz-form-control"
                                    value="<?= $escape($firstName) ?>"
                                    autocomplete="given-name"
                                    maxlength="100"
                                    required
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="nachname"
                                    class="kfz-form-label"
                                >
                                    Nachname*
                                </label>

                                <input
                                    type="text"
                                    id="nachname"
                                    name="nachname"
                                    class="kfz-form-control"
                                    value="<?= $escape($lastName) ?>"
                                    autocomplete="family-name"
                                    maxlength="100"
                                    required
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="email"
                                    class="kfz-form-label"
                                >
                                    E-Mail-Adresse*
                                </label>

                                <input
                                    type="email"
                                    id="email"
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
                                    for="telefon"
                                    class="kfz-form-label"
                                >
                                    Telefonnummer
                                    <span class="kfz-form-optional">
                                        optional
                                    </span>
                                </label>

                                <input
                                    type="tel"
                                    id="telefon"
                                    name="telefon"
                                    class="kfz-form-control"
                                    value="<?= $escape($phone) ?>"
                                    autocomplete="tel"
                                    maxlength="50"
                                >

                            </div>

                        </div>

                    </fieldset>


                    <fieldset class="kfz-form-fieldset">

                        <legend>
                            Adresse und Hinweise
                        </legend>

                        <div class="kfz-form-grid kfz-form-grid-two">

                            <div class="kfz-form-group">

                                <label
                                    for="plz"
                                    class="kfz-form-label"
                                >
                                    Postleitzahl
                                </label>

                                <input
                                    type="text"
                                    id="plz"
                                    name="plz"
                                    class="kfz-form-control"
                                    value="<?= $escape(
                                        $postalCode
                                    ) ?>"
                                    maxlength="5"
                                    inputmode="numeric"
                                    autocomplete="postal-code"
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="ort"
                                    class="kfz-form-label"
                                >
                                    Ort
                                </label>

                                <input
                                    type="text"
                                    id="ort"
                                    name="ort"
                                    class="kfz-form-control"
                                    value="<?= $escape($city) ?>"
                                    maxlength="100"
                                    autocomplete="address-level2"
                                >

                            </div>

                        </div>


                        <div class="kfz-form-group">

                            <label
                                for="hinweise"
                                class="kfz-form-label"
                            >
                                Hinweise
                                <span class="kfz-form-optional">
                                    optional
                                </span>
                            </label>

                            <textarea
                                id="hinweise"
                                name="hinweise"
                                class="kfz-form-control kfz-form-textarea"
                                rows="4"
                                maxlength="5000"
                                placeholder="Weitere Informationen"
                            ><?= $escape($notes) ?></textarea>

                        </div>

                    </fieldset>


                    <div class="kfz-form-checkbox">

                        <input
                            type="checkbox"
                            id="datenschutz"
                            name="datenschutz"
                            value="1"
                            required
                            <?= $privacyAccepted
                                ? 'checked'
                                : '' ?>
                        >

                        <label for="datenschutz">

                            Ich habe die
                            <a
                                href="<?= $escape(
                                    $url('/datenschutz/')
                                ) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Datenschutzerklärung
                            </a>
                            gelesen und akzeptiere die Verarbeitung
                            meiner Angaben.

                        </label>

                    </div>


                    <div class="kfz-process-form-actions">

                        <a
                            href="<?= $escape($url('/')) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Abbrechen
                        </a>

                        <button
                            type="submit"
                            class="kfz-button kfz-button-primary"
                        >
                            Weiter zur Bestätigung
                        </button>

                    </div>

                </form>

            </div>

        <?php endif; ?>

    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const cityInput = document.getElementById(
        'kennzeichen-ort'
    );

    const lettersInput = document.getElementById(
        'kennzeichen-buchstaben'
    );

    const numbersInput = document.getElementById(
        'kennzeichen-zahlen'
    );

    const frontCheckbox = document.getElementById(
        'kein-vorderes-kennzeichen'
    );

    const frontInput = document.getElementById(
        'sicherheitscode-vorne'
    );


    function onlyLetters(value, maxLength) {
        return value
            .toUpperCase()
            .replace(/[^A-ZÄÖÜ]/g, '')
            .slice(0, maxLength);
    }


    function onlyNumbers(value, maxLength) {
        return value
            .replace(/[^0-9]/g, '')
            .slice(0, maxLength);
    }


    if (cityInput) {
        cityInput.addEventListener(
            'input',
            function () {
                cityInput.value = onlyLetters(
                    cityInput.value,
                    3
                );

                if (
                    cityInput.value.length >= 3
                    && lettersInput
                ) {
                    lettersInput.focus();
                }
            }
        );
    }


    if (lettersInput) {
        lettersInput.addEventListener(
            'input',
            function () {
                lettersInput.value = onlyLetters(
                    lettersInput.value,
                    2
                );

                if (
                    lettersInput.value.length >= 2
                    && numbersInput
                ) {
                    numbersInput.focus();
                }
            }
        );
    }


    if (numbersInput) {
        numbersInput.addEventListener(
            'input',
            function () {
                numbersInput.value = onlyNumbers(
                    numbersInput.value,
                    4
                );
            }
        );
    }


    if (frontCheckbox && frontInput) {
        function updateFrontCode() {
            const disabled = frontCheckbox.checked;

            frontInput.disabled = disabled;
            frontInput.required = !disabled;
            frontInput.setAttribute(
                'aria-disabled',
                disabled ? 'true' : 'false'
            );

            if (disabled) {
                frontInput.value = '';
            }
        }

        frontCheckbox.addEventListener(
            'change',
            updateFrontCode
        );

        updateFrontCode();
    }
});
</script>