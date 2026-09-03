<?php
declare(strict_types=1);

/**
 * Kfz Digital – Fahrzeug abmelden
 *
 * Header und Footer werden automatisch über index.php geladen.
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
| Anmeldung prüfen
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['user_id'])):
?>

<section
    class="kfz-section kfz-account-page"
    aria-labelledby="process-login-title"
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
                Kfz Digital
            </span>

            <h1
                id="process-login-title"
                class="kfz-section-title"
            >
                Bitte melden Sie sich an.
            </h1>

            <p class="kfz-section-text">
                Für die Fahrzeugabmeldung benötigen Sie ein persönliches
                Kfz-Digital-Konto.
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
| Datenbank laden
|--------------------------------------------------------------------------
*/

$pdo = require __DIR__ . '/../../Config/database.php';


/*
|--------------------------------------------------------------------------
| Benutzer-ID
|--------------------------------------------------------------------------
*/

$userId = (int)(
    $_SESSION['user_id'] ?? 0
);

if ($userId <= 0):
?>

<section class="kfz-section">

    <div class="container">

        <div
            class="kfz-process-error"
            role="alert"
        >
            Ihre Sitzung ist ungültig. Bitte melden Sie sich erneut an.
        </div>

        <a
            href="<?= $escape($url('/login/')) ?>"
            class="kfz-button kfz-button-primary"
        >
            Zur Anmeldung
        </a>

    </div>

</section>

<?php
return;
endif;


/* Kfz Digital bietet ausschließlich die Fahrzeugabmeldung an. */
$processOptions = [
    'abmeldung' => [
        'title' => 'Fahrzeug abmelden',
        'description' => 'Melden Sie Ihr Fahrzeug digital ab.',
    ],
];


/*
|--------------------------------------------------------------------------
| CSRF-Token erzeugen
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

$formSubmitted = false;

$applicationId = 0;

$vehicles = [];

$selectedVehicle = null;


/*
|--------------------------------------------------------------------------
| Werte aus Formular und URL lesen
|--------------------------------------------------------------------------
*/

// Der Wert wird serverseitig festgesetzt; URL- und Formularwerte können
// keine anderen Vorgangsarten mehr erzeugen.
$selectedProcess = 'abmeldung';

$selectedVehicleId = filter_var(
    $_POST['fahrzeug_id']
        ?? $_GET['fahrzeug_id']
        ?? null,
    FILTER_VALIDATE_INT
);

$selectedVehicleId = $selectedVehicleId !== false
    ? (int)$selectedVehicleId
    : 0;

$licensePlate = strtoupper(
    trim((string)(
        $_POST['kennzeichen']
        ?? $_GET['kennzeichen']
        ?? ''
    ))
);

// Mehrfach-Leerzeichen vereinheitlichen, ohne das Kennzeichenformat zu raten.
$licensePlate = preg_replace('/\s+/', ' ', $licensePlate) ?? '';

$vin = strtoupper(
    trim((string)(
        $_POST['fahrzeugidentifikationsnummer']
        ?? ''
    ))
);

$firstName = trim(
    (string)(
        $_POST['vorname']
        ?? $_SESSION['user_first_name']
        ?? ''
    )
);

$lastName = trim(
    (string)(
        $_POST['nachname']
        ?? $_SESSION['user_last_name']
        ?? ''
    )
);

$email = strtolower(
    trim((string)(
        $_POST['email']
        ?? $_SESSION['user_email']
        ?? ''
    ))
);

$phone = trim(
    (string)($_POST['telefon'] ?? '')
);

$postalCode = trim(
    (string)($_POST['plz'] ?? '')
);

$city = trim(
    (string)($_POST['ort'] ?? '')
);

$notes = trim(
    (string)($_POST['hinweise'] ?? '')
);

$privacyAccepted = isset(
    $_POST['datenschutz']
);


/*
|--------------------------------------------------------------------------
| Fahrzeuge des Benutzers laden
|--------------------------------------------------------------------------
*/

try {

    $vehicleStatement = $pdo->prepare(
        'SELECT
            id,
            license_plate,
            vin,
            manufacturer,
            model,
            vehicle_type,
            first_registration_date
         FROM vehicles
         WHERE user_id = :user_id
         ORDER BY created_at DESC, id DESC'
    );

    $vehicleStatement->execute([
        'user_id' => $userId,
    ]);

    $vehicleResult = $vehicleStatement->fetchAll();

    if (is_array($vehicleResult)) {
        $vehicles = $vehicleResult;
    }

} catch (PDOException $exception) {

    $formErrors[] =
        'Ihre gespeicherten Fahrzeuge konnten nicht geladen werden.';
}


/*
|--------------------------------------------------------------------------
| Ausgewähltes Fahrzeug suchen
|--------------------------------------------------------------------------
*/

if ($selectedVehicleId > 0) {

    foreach ($vehicles as $vehicle) {

        if (
            (int)($vehicle['id'] ?? 0)
            === $selectedVehicleId
        ) {
            $selectedVehicle = $vehicle;
            break;
        }
    }

    if ($selectedVehicle === null) {

        $formErrors[] =
            'Das ausgewählte Fahrzeug gehört nicht zu Ihrem Konto.';

        $selectedVehicleId = 0;
    }
}


/*
|--------------------------------------------------------------------------
| Fahrzeugdaten automatisch übernehmen
|--------------------------------------------------------------------------
*/

if (
    $selectedVehicle !== null
    && $requestMethod !== 'POST'
) {
    $licensePlate = strtoupper(
        trim((string)(
            $selectedVehicle['license_plate'] ?? ''
        ))
    );

    $vin = strtoupper(
        trim((string)(
            $selectedVehicle['vin'] ?? ''
        ))
    );
}


/*
|--------------------------------------------------------------------------
| Formular verarbeiten
|--------------------------------------------------------------------------
*/

if ($requestMethod === 'POST') {

    $submittedToken = (string)(
        $_POST['csrf_token'] ?? ''
    );


    /*
     * CSRF prüfen
     */

    if (
        $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $formErrors[] =
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.';
    }


    /*
     * Vorgang prüfen
     */

    /*
     * Persönliche Daten prüfen
     */

    if ($firstName === '') {
        $formErrors[] =
            'Bitte geben Sie Ihren Vornamen ein.';
    }

    if (mb_strlen($firstName) > 100) {
        $formErrors[] = 'Der Vorname ist zu lang.';
    }

    if ($lastName === '') {
        $formErrors[] =
            'Bitte geben Sie Ihren Nachnamen ein.';
    }

    if (mb_strlen($lastName) > 100) {
        $formErrors[] = 'Der Nachname ist zu lang.';
    }

    if ($email === '') {
        $formErrors[] =
            'Bitte geben Sie Ihre E-Mail-Adresse ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formErrors[] =
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    } elseif (mb_strlen($email) > 190) {
        $formErrors[] = 'Die E-Mail-Adresse ist zu lang.';
    }


    /*
     * Kennzeichen prüfen
     */

    if ($licensePlate === '') {
        $formErrors[] =
            'Bitte geben Sie das Kennzeichen ein.';
    } elseif (mb_strlen($licensePlate) > 20) {
        $formErrors[] =
            'Das Kennzeichen ist zu lang.';
    } elseif (!preg_match('/^[A-ZÄÖÜ0-9 -]{2,20}$/u', $licensePlate)) {
        $formErrors[] = 'Das Kennzeichen enthält ungültige Zeichen.';
    }


    /*
     * FIN prüfen
     */

    if ($vin === '') {
        $formErrors[] =
            'Bitte geben Sie die Fahrzeug-Identifizierungsnummer ein.';
    } elseif (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $vin)) {
        $formErrors[] =
            'Die FIN muss 17 Zeichen enthalten und darf I, O oder Q nicht enthalten.';
    }


    /*
     * Telefonnummer prüfen
     */

    if ($phone !== '' && mb_strlen($phone) > 50) {
        $formErrors[] =
            'Die Telefonnummer ist zu lang.';
    }

    if ($postalCode === '' && $city !== '') {
        $formErrors[] = 'Bitte geben Sie auch die Postleitzahl an.';
    }

    if ($postalCode !== '' && $city === '') {
        $formErrors[] = 'Bitte geben Sie auch den Ort an.';
    }


    /*
     * Postleitzahl prüfen
     */

    if (
        $postalCode !== ''
        && !preg_match('/^[0-9]{5}$/', $postalCode)
    ) {
        $formErrors[] =
            'Die Postleitzahl muss aus fünf Ziffern bestehen.';
    }


    /*
     * Weitere Eingaben begrenzen
     */

    if (mb_strlen($city) > 100) {
        $formErrors[] =
            'Der Ort ist zu lang.';
    }

    if (mb_strlen($notes) > 5000) {
        $formErrors[] =
            'Die Hinweise sind zu lang.';
    }


    /*
     * Datenschutz prüfen
     */

    if (!$privacyAccepted) {
        $formErrors[] =
            'Bitte bestätigen Sie die Datenschutzhinweise.';
    }


    /*
     * Vorgang speichern
     */

    if ($formErrors === []) {

        try {

            $pdo->beginTransaction();

            $insertStatement = $pdo->prepare(
                'INSERT INTO applications
                (
                    user_id,
                    vehicle_id,
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
                    notes
                )
                VALUES
                (
                    :user_id,
                    :vehicle_id,
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
                    :notes
                )'
            );

            $insertStatement->execute([
                'user_id' => $userId,

                'vehicle_id' => $selectedVehicleId > 0
                    ? $selectedVehicleId
                    : null,

                'process_type' => $selectedProcess,

                'status' => 'entwurf',

                'license_plate' => $licensePlate !== ''
                    ? $licensePlate
                    : null,

                'vin' => $vin !== ''
                    ? $vin
                    : null,

                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,

                'phone' => $phone !== ''
                    ? $phone
                    : null,

                'postal_code' => $postalCode !== ''
                    ? $postalCode
                    : null,

                'city' => $city !== ''
                    ? $city
                    : null,

                'notes' => $notes !== ''
                    ? $notes
                    : null,
            ]);

            $applicationId = (int)$pdo->lastInsertId();

            if ($applicationId <= 0) {
                throw new RuntimeException('Die Vorgangsnummer konnte nicht erzeugt werden.');
            }

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
                'new_status' => 'entwurf',
                'comment' => 'Fahrzeugabmeldung im Mock- und Testbetrieb angelegt.',
            ]);

            $pdo->commit();

            $formSubmitted = true;

            $_SESSION['last_application_id'] =
                $applicationId;

            $_SESSION['process_csrf_token'] = bin2hex(
                random_bytes(32)
            );

            $csrfToken = (string)(
                $_SESSION['process_csrf_token']
            );

        } catch (Throwable $exception) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $formErrors[] =
                'Der Vorgang konnte nicht gespeichert werden.';
        }
    }
}


/*
|--------------------------------------------------------------------------
| Anzeigeinformationen
|--------------------------------------------------------------------------
*/

$selectedProcessTitle = 'Fahrzeug abmelden';

$selectedProcessDescription =
    'Geben Sie die Daten für die Abmeldung Ihres Fahrzeugs ein.';

if ($selectedProcess !== '') {

    $selectedProcessTitle =
        $processOptions[$selectedProcess]['title'];

    $selectedProcessDescription =
        $processOptions[$selectedProcess]['description'];
}
?>

<section
    class="kfz-section kfz-process-page"
    aria-labelledby="process-page-title"
>

    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Kfz Digital
            </span>

            <h1
                id="process-page-title"
                class="kfz-section-title"
            >
                Fahrzeug abmelden
            </h1>

            <p class="kfz-section-text">
                Geben Sie die Daten für die Abmeldung Ihres Fahrzeugs
                Schritt für Schritt ein.
            </p>

        </div>


        <?php if ($formSubmitted): ?>

            <div
                class="kfz-process-success"
                role="status"
                aria-live="polite"
            >

                <div class="kfz-process-alert-icon">
                    ✓
                </div>

                <div>

                    <h2>
                        Fahrzeugabmeldung als Entwurf gespeichert
                    </h2>

                    <p>
                        Ihre Angaben wurden geprüft und sicher als Entwurf
                        in Ihrem Kfz-Digital-Konto gespeichert.
                    </p>

                    <?php if ($applicationId > 0): ?>

                        <p class="kfz-application-number">
                            Vorgangsnummer:
                            <strong>
                                KD-<?= $escape(
                                    (string)$applicationId
                                ) ?>
                            </strong>
                        </p>

                    <?php endif; ?>

                    <p>
                        Ihre Abmeldung hat zunächst den Status
                        <strong>Entwurf</strong>.
                    </p>

                    <div class="kfz-process-form-actions">

                        <a
                            href="<?= $escape($url('/vorgaenge/')) ?>"
                            class="kfz-button kfz-button-primary"
                        >
                            Meine Vorgänge
                        </a>

                        <a
                            href="<?= $escape($url('/vorgang-starten/')) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Weitere Abmeldung anlegen
                        </a>

                    </div>

                </div>

            </div>


        <?php else: ?>


            <?php if ($formErrors !== []): ?>

                <div
                    class="kfz-process-error"
                    role="alert"
                    aria-labelledby="form-error-title"
                >

                    <h2 id="form-error-title">
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


            <div class="kfz-process-layout">

                <!-- Vorgangsauswahl -->

                <aside
                    class="kfz-process-sidebar"
                    aria-labelledby="process-selection-title"
                >

                    <div class="kfz-process-sidebar-card">

                        <span class="kfz-section-kicker">
                            Schritt 1
                        </span>

                        <h2 id="process-selection-title">
                            Fahrzeugabmeldung
                        </h2>

                        <p>
                            Die Fahrzeugabmeldung ist der einzige verfügbare Vorgang.
                        </p>

                        <div class="kfz-process-options">

                            <?php foreach (
                                $processOptions
                                as $processKey => $process
                            ): ?>

                                <?php
                                $processUrl =
                                    '/vorgang-starten/?vorgang='
                                    . rawurlencode($processKey);

                                if ($selectedVehicleId > 0) {
                                    $processUrl .=
                                        '&fahrzeug_id='
                                        . $selectedVehicleId;
                                }
                                ?>

                                <a
                                    href="<?= $escape(
                                        $url($processUrl)
                                    ) ?>"
                                    class="kfz-process-option <?= $selectedProcess === $processKey ? 'is-selected' : '' ?>"
                                    <?= $selectedProcess === $processKey ? 'aria-current="true"' : '' ?>
                                >

                                    <span class="kfz-process-option-icon">

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

                                    <span class="kfz-process-option-text">

                                        <strong>
                                            <?= $escape(
                                                $process['title']
                                            ) ?>
                                        </strong>

                                        <small>
                                            <?= $escape(
                                                $process['description']
                                            ) ?>
                                        </small>

                                    </span>

                                    <span
                                        class="kfz-process-option-arrow"
                                        aria-hidden="true"
                                    >
                                        →
                                    </span>

                                </a>

                            <?php endforeach; ?>

                        </div>

                    </div>


                    <div class="kfz-process-info-card">

                        <span class="kfz-process-info-icon">
                            i
                        </span>

                        <div>

                            <strong>
                                Gespeicherte Fahrzeuge
                            </strong>

                            <p>
                                Sie können ein gespeichertes Fahrzeug
                                direkt für den Vorgang auswählen.
                            </p>

                            <a
                                href="<?= $escape(
                                    $url('/fahrzeuge/')
                                ) ?>"
                            >
                                Fahrzeuge öffnen
                            </a>

                        </div>

                    </div>

                </aside>


                <!-- Formular -->

                <div class="kfz-process-form-wrapper">

                    <div class="kfz-process-form-header">

                        <div>

                            <span class="kfz-section-kicker">
                                Schritt 2
                            </span>

                            <h2>
                                <?= $escape(
                                    $selectedProcessTitle
                                ) ?>
                            </h2>

                            <p>
                                <?= $escape(
                                    $selectedProcessDescription
                                ) ?>
                            </p>

                        </div>

                        <span class="kfz-process-step-badge">
                            2 von 4
                        </span>

                    </div>


                    <form
                        action="<?= $escape(
                            $url('/vorgang-starten/')
                        ) ?>"
                        method="post"
                        class="kfz-process-form"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= $escape($csrfToken) ?>"
                        >

                        <input
                            type="hidden"
                            name="vorgang"
                            value="<?= $escape(
                                $selectedProcess
                            ) ?>"
                        >


                        <!-- Gespeichertes Fahrzeug -->

                        <?php if ($vehicles !== []): ?>

                            <fieldset class="kfz-form-fieldset">

                                <legend>
                                    Gespeichertes Fahrzeug
                                </legend>

                                <div class="kfz-form-group">

                                    <label
                                        for="fahrzeug-id"
                                        class="kfz-form-label"
                                    >
                                        Fahrzeug auswählen
                                    </label>

                                    <select
                                        id="fahrzeug-id"
                                        name="fahrzeug_id"
                                        class="kfz-form-select"
                                        onchange="kfzSelectVehicle(this)"
                                    >

                                        <option value="0">
                                            Kein gespeichertes Fahrzeug
                                        </option>

                                        <?php foreach (
                                            $vehicles
                                            as $vehicle
                                        ): ?>

                                            <?php
                                            $vehicleId = (int)(
                                                $vehicle['id'] ?? 0
                                            );

                                            $vehicleName = trim(
                                                (string)(
                                                    $vehicle['manufacturer']
                                                    ?? ''
                                                )
                                                . ' '
                                                . (string)(
                                                    $vehicle['model']
                                                    ?? ''
                                                )
                                            );

                                            if ($vehicleName === '') {
                                                $vehicleName =
                                                    'Gespeichertes Fahrzeug';
                                            }

                                            $vehiclePlate = trim(
                                                (string)(
                                                    $vehicle['license_plate']
                                                    ?? ''
                                                )
                                            );

                                            $vehicleLabel =
                                                $vehicleName;

                                            if ($vehiclePlate !== '') {
                                                $vehicleLabel .=
                                                    ' – '
                                                    . $vehiclePlate;
                                            }
                                            ?>

                                            <option
                                                value="<?= $escape(
                                                    (string)$vehicleId
                                                ) ?>"
                                                <?= $selectedVehicleId === $vehicleId ? 'selected' : '' ?>
                                            >
                                                <?= $escape(
                                                    $vehicleLabel
                                                ) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                    <small class="kfz-form-help">
                                        Die Fahrzeugdaten werden automatisch
                                        in das Formular übernommen.
                                    </small>

                                </div>

                            </fieldset>

                        <?php endif; ?>


                        <!-- Fahrzeugdaten -->

                        <fieldset class="kfz-form-fieldset">

                            <legend>
                                Fahrzeugdaten
                            </legend>

                            <div class="kfz-form-grid kfz-form-grid-two">

                                <div class="kfz-form-group">

                                    <label
                                        for="kennzeichen"
                                        class="kfz-form-label"
                                    >
                                        Kennzeichen
                                    </label>

                                    <input
                                        type="text"
                                        id="kennzeichen"
                                        name="kennzeichen"
                                        class="kfz-form-control"
                                        value="<?= $escape(
                                            $licensePlate
                                        ) ?>"
                                        placeholder="z. B. MK-KD 123"
                                        maxlength="20"
                                        autocomplete="off"
                                        required
                                    >

                                </div>


                                <div class="kfz-form-group">

                                    <label
                                        for="fahrzeugidentifikationsnummer"
                                        class="kfz-form-label"
                                    >
                                        Fahrzeug-Identifizierungsnummer
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

                                </div>

                            </div>

                        </fieldset>


                        <!-- Persönliche Daten -->

                        <fieldset class="kfz-form-fieldset">

                            <legend>
                                Persönliche Daten
                            </legend>

                            <div class="kfz-form-grid kfz-form-grid-two">

                                <div class="kfz-form-group">

                                    <label
                                        for="vorname"
                                        class="kfz-form-label"
                                    >
                                        Vorname
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
                                        Nachname
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
                                        E-Mail-Adresse
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
                                        placeholder="+49 151 12345678"
                                        autocomplete="tel"
                                        maxlength="50"
                                    >

                                </div>

                            </div>

                        </fieldset>


                        <!-- Adresse -->

                        <fieldset class="kfz-form-fieldset">

                            <legend>
                                Adresse
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
                                        placeholder="58636"
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
                                        placeholder="Iserlohn"
                                        autocomplete="address-level2"
                                        maxlength="100"
                                    >

                                </div>

                            </div>

                        </fieldset>


                        <!-- Hinweise -->

                        <fieldset class="kfz-form-fieldset">

                            <legend>
                                Weitere Angaben
                            </legend>

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
                                    placeholder="Gibt es wichtige Informationen zu Ihrem Vorgang?"
                                ><?= $escape($notes) ?></textarea>

                            </div>

                        </fieldset>


                        <!-- Datenschutz -->

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
                                meiner Angaben zur Bearbeitung dieses
                                Vorgangs.

                            </label>

                        </div>


                        <!-- Aktionen -->

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
                                Vorgang speichern

                                <svg
                                    class="kfz-button-icon"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                </svg>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        <?php endif; ?>

    </div>

</section>


<script>
function kfzSelectVehicle(selectElement) {
    const selectedVehicleId = selectElement.value;
    const currentUrl = new URL(window.location.href);

    if (selectedVehicleId === '0') {
        currentUrl.searchParams.delete('fahrzeug_id');
    } else {
        currentUrl.searchParams.set(
            'fahrzeug_id',
            selectedVehicleId
        );
    }

    window.location.href = currentUrl.toString();
}
</script>
