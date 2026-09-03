<?php
declare(strict_types=1);

/**
 * Kfz Digital – Meine Fahrzeuge
 *
 * Header und Footer werden durch index.php eingebunden.
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
| Session prüfen
|--------------------------------------------------------------------------
*/

$isLoggedIn = !empty($_SESSION['user_id']);

if (!$isLoggedIn):

?>

<section
    class="kfz-section kfz-account-page"
    aria-labelledby="vehicles-login-title"
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
                id="vehicles-login-title"
                class="kfz-section-title"
            >
                Bitte melden Sie sich an.
            </h1>

            <p class="kfz-section-text">
                Ihre gespeicherten Fahrzeuge können Sie nur nach einer
                Anmeldung verwalten.
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
|
| Erst nach der Zugangskontrolle verbinden. Nicht angemeldete Besucher
| benötigen für die reine Hinweisseite keine Datenbankverbindung.
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

        <div class="kfz-process-error" role="alert">
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


/*
|--------------------------------------------------------------------------
| CSRF-Token erzeugen
|--------------------------------------------------------------------------
*/

if (
    empty($_SESSION['vehicle_csrf_token'])
    || !is_string($_SESSION['vehicle_csrf_token'])
) {
    $_SESSION['vehicle_csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = (string)$_SESSION['vehicle_csrf_token'];


/*
|--------------------------------------------------------------------------
| Formularwerte
|--------------------------------------------------------------------------
*/

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);

$action = trim(
    (string)($_POST['action'] ?? '')
);

$licensePlate = strtoupper(
    trim((string)($_POST['license_plate'] ?? ''))
);

$vin = strtoupper(
    trim((string)($_POST['vin'] ?? ''))
);

$manufacturer = trim(
    (string)($_POST['manufacturer'] ?? '')
);

$model = trim(
    (string)($_POST['model'] ?? '')
);

$vehicleType = trim(
    (string)($_POST['vehicle_type'] ?? '')
);

$firstRegistrationDate = trim(
    (string)($_POST['first_registration_date'] ?? '')
);

$errors = [];

$successMessage = '';

$showAddForm = isset($_GET['neu'])
    || $action === 'add';


/*
|--------------------------------------------------------------------------
| Fahrzeug löschen
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    && $action === 'delete'
) {
    $submittedToken = (string)(
        $_POST['csrf_token'] ?? ''
    );

    $vehicleId = filter_var(
        $_POST['vehicle_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $errors[] =
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.';
    }

    if (
        $vehicleId === false
        || (int)$vehicleId <= 0
    ) {
        $errors[] =
            'Das Fahrzeug konnte nicht gefunden werden.';
    }

    if ($errors === []) {

        try {

            $deleteStatement = $pdo->prepare(
                'DELETE FROM vehicles
                 WHERE id = :id
                 AND user_id = :user_id
                 LIMIT 1'
            );

            $deleteStatement->execute([
                'id' => (int)$vehicleId,
                'user_id' => $userId,
            ]);

            if ($deleteStatement->rowCount() > 0) {
                $successMessage =
                    'Das Fahrzeug wurde erfolgreich gelöscht.';
            } else {
                $errors[] =
                    'Das Fahrzeug wurde nicht gefunden oder gehört nicht zu Ihrem Konto.';
            }

        } catch (PDOException $exception) {

            $errors[] =
                'Das Fahrzeug konnte nicht gelöscht werden.';
        }
    }
}


/*
|--------------------------------------------------------------------------
| Fahrzeug hinzufügen
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    && $action === 'add'
) {
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


    /*
     * Kennzeichen prüfen
     */

    if ($licensePlate === '') {
        $errors[] =
            'Bitte geben Sie ein Kennzeichen ein.';
    } elseif (mb_strlen($licensePlate) > 20) {
        $errors[] =
            'Das Kennzeichen ist zu lang.';
    }


    /*
     * FIN prüfen
     */

    if ($vin !== '') {

        if (strlen($vin) !== 17) {
            $errors[] =
                'Die Fahrzeug-Identifizierungsnummer muss 17 Zeichen enthalten.';
        }

        if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $vin)) {
            $errors[] =
                'Die Fahrzeug-Identifizierungsnummer enthält ungültige Zeichen.';
        }
    }


    /*
     * Hersteller prüfen
     */

    if ($manufacturer !== '') {
        if (mb_strlen($manufacturer) > 100) {
            $errors[] =
                'Der Herstellername ist zu lang.';
        }
    }


    /*
     * Modell prüfen
     */

    if ($model !== '') {
        if (mb_strlen($model) > 100) {
            $errors[] =
                'Der Modellname ist zu lang.';
        }
    }


    /*
     * Fahrzeugart prüfen
     */

    $allowedVehicleTypes = [
        '',
        'Pkw',
        'Motorrad',
        'Lkw',
        'Anhänger',
        'Wohnmobil',
        'Sonstiges',
    ];

    if (!in_array($vehicleType, $allowedVehicleTypes, true)) {
        $errors[] =
            'Bitte wählen Sie eine gültige Fahrzeugart aus.';
    }


    /*
     * Erstzulassungsdatum prüfen
     */

    if ($firstRegistrationDate !== '') {

        $dateObject = DateTime::createFromFormat(
            'Y-m-d',
            $firstRegistrationDate
        );

        $validDate = $dateObject !== false
            && $dateObject->format('Y-m-d') === $firstRegistrationDate;

        if (!$validDate) {
            $errors[] =
                'Bitte geben Sie ein gültiges Erstzulassungsdatum ein.';
        }
    }


    /*
     * Fahrzeug speichern
     */

    if ($errors === []) {

        try {

            $insertStatement = $pdo->prepare(
                'INSERT INTO vehicles
                (
                    user_id,
                    license_plate,
                    vin,
                    manufacturer,
                    model,
                    vehicle_type,
                    first_registration_date
                )
                VALUES
                (
                    :user_id,
                    :license_plate,
                    :vin,
                    :manufacturer,
                    :model,
                    :vehicle_type,
                    :first_registration_date
                )'
            );

            $insertStatement->execute([
                'user_id' => $userId,
                'license_plate' => $licensePlate !== ''
                    ? $licensePlate
                    : null,
                'vin' => $vin !== ''
                    ? $vin
                    : null,
                'manufacturer' => $manufacturer !== ''
                    ? $manufacturer
                    : null,
                'model' => $model !== ''
                    ? $model
                    : null,
                'vehicle_type' => $vehicleType !== ''
                    ? $vehicleType
                    : null,
                'first_registration_date' =>
                    $firstRegistrationDate !== ''
                        ? $firstRegistrationDate
                        : null,
            ]);

            $successMessage =
                'Das Fahrzeug wurde erfolgreich hinzugefügt.';

            $licensePlate = '';
            $vin = '';
            $manufacturer = '';
            $model = '';
            $vehicleType = '';
            $firstRegistrationDate = '';

            $showAddForm = false;

        } catch (PDOException $exception) {

            /*
             * MySQL Duplicate-Key-Fehler.
             */
            if (
                isset($exception->errorInfo[1])
                && (int)$exception->errorInfo[1] === 1062
            ) {
                $errors[] =
                    'Diese Fahrzeug-Identifizierungsnummer ist bereits gespeichert.';
            } else {
                $errors[] =
                    'Das Fahrzeug konnte nicht gespeichert werden.';
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Fahrzeuge des Benutzers laden
|--------------------------------------------------------------------------
*/

$vehicles = [];

try {

    $vehicleStatement = $pdo->prepare(
        'SELECT
            id,
            license_plate,
            vin,
            manufacturer,
            model,
            vehicle_type,
            first_registration_date,
            created_at
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

    $errors[] =
        'Die Fahrzeuge konnten nicht geladen werden.';
}
?>

<section
    class="kfz-section kfz-vehicles-page"
    aria-labelledby="vehicles-title"
>

    <div class="container">

        <!-- Kopfbereich -->

        <div class="kfz-vehicles-header">

            <div>

                <span class="kfz-section-kicker">
                    Persönlicher Bereich
                </span>

                <h1
                    id="vehicles-title"
                    class="kfz-section-title"
                >
                    Meine Fahrzeuge
                </h1>

                <p class="kfz-section-text">
                    Speichern Sie Ihre Fahrzeuge und verwenden Sie sie
                    später für digitale Fahrzeugvorgänge.
                </p>

            </div>

            <div>

                <a
                    href="<?= $escape($url('/fahrzeuge/?neu=1')) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    <svg
                        class="kfz-button-icon"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M12 5v14M5 12h14" />
                    </svg>

                    Fahrzeug hinzufügen
                </a>

            </div>

        </div>


        <?php if ($successMessage !== ''): ?>

            <div
                class="kfz-process-success"
                role="status"
                aria-live="polite"
            >

                <div class="kfz-process-alert-icon">
                    ✓
                </div>

                <div>
                    <?= $escape($successMessage) ?>
                </div>

            </div>

        <?php endif; ?>


        <?php if ($errors !== []): ?>

            <div
                class="kfz-process-error"
                role="alert"
                aria-labelledby="vehicles-error-title"
            >

                <h2 id="vehicles-error-title">
                    Es ist ein Fehler aufgetreten
                </h2>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= $escape($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <?php if ($showAddForm): ?>

            <!-- Formular zum Hinzufügen -->

            <section
                class="kfz-card kfz-vehicle-form-card"
                aria-labelledby="add-vehicle-title"
            >

                <div class="kfz-vehicle-form-header">

                    <div>

                        <span class="kfz-section-kicker">
                            Neues Fahrzeug
                        </span>

                        <h2
                            id="add-vehicle-title"
                            class="kfz-card-title"
                        >
                            Fahrzeug hinzufügen
                        </h2>

                        <p class="kfz-card-text">
                            Hinterlegen Sie die wichtigsten Fahrzeugdaten.
                        </p>

                    </div>

                    <a
                        href="<?= $escape($url('/fahrzeuge/')) ?>"
                        class="kfz-vehicle-close"
                        aria-label="Formular schließen"
                    >
                        &times;
                    </a>

                </div>


                <form
                    action="<?= $escape($url('/fahrzeuge/')) ?>"
                    method="post"
                    class="kfz-vehicle-form"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="add"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape($csrfToken) ?>"
                    >


                    <fieldset class="kfz-form-fieldset">

                        <legend>
                            Fahrzeugdaten
                        </legend>

                        <div class="kfz-form-grid kfz-form-grid-two">

                            <div class="kfz-form-group">

                                <label
                                    for="license-plate"
                                    class="kfz-form-label"
                                >
                                    Kennzeichen
                                    <span aria-hidden="true">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="license-plate"
                                    name="license_plate"
                                    class="kfz-form-control"
                                    value="<?= $escape($licensePlate) ?>"
                                    placeholder="z. B. MK-KD 123"
                                    maxlength="20"
                                    autocomplete="off"
                                    required
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="vin"
                                    class="kfz-form-label"
                                >
                                    Fahrzeug-Identifizierungsnummer
                                </label>

                                <input
                                    type="text"
                                    id="vin"
                                    name="vin"
                                    class="kfz-form-control"
                                    value="<?= $escape($vin) ?>"
                                    placeholder="17-stellige FIN"
                                    maxlength="17"
                                    autocomplete="off"
                                >

                                <small class="kfz-form-help">
                                    Optional – die FIN finden Sie meistens
                                    in den Fahrzeugpapieren.
                                </small>

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="manufacturer"
                                    class="kfz-form-label"
                                >
                                    Hersteller
                                </label>

                                <input
                                    type="text"
                                    id="manufacturer"
                                    name="manufacturer"
                                    class="kfz-form-control"
                                    value="<?= $escape($manufacturer) ?>"
                                    placeholder="z. B. Volkswagen"
                                    maxlength="100"
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="model"
                                    class="kfz-form-label"
                                >
                                    Modell
                                </label>

                                <input
                                    type="text"
                                    id="model"
                                    name="model"
                                    class="kfz-form-control"
                                    value="<?= $escape($model) ?>"
                                    placeholder="z. B. Golf"
                                    maxlength="100"
                                >

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="vehicle-type"
                                    class="kfz-form-label"
                                >
                                    Fahrzeugart
                                </label>

                                <select
                                    id="vehicle-type"
                                    name="vehicle_type"
                                    class="kfz-form-select"
                                >
                                    <option value="">
                                        Bitte auswählen
                                    </option>

                                    <option
                                        value="Pkw"
                                        <?= $vehicleType === 'Pkw' ? 'selected' : '' ?>
                                    >
                                        Pkw
                                    </option>

                                    <option
                                        value="Motorrad"
                                        <?= $vehicleType === 'Motorrad' ? 'selected' : '' ?>
                                    >
                                        Motorrad
                                    </option>

                                    <option
                                        value="Lkw"
                                        <?= $vehicleType === 'Lkw' ? 'selected' : '' ?>
                                    >
                                        Lkw
                                    </option>

                                    <option
                                        value="Anhänger"
                                        <?= $vehicleType === 'Anhänger' ? 'selected' : '' ?>
                                    >
                                        Anhänger
                                    </option>

                                    <option
                                        value="Wohnmobil"
                                        <?= $vehicleType === 'Wohnmobil' ? 'selected' : '' ?>
                                    >
                                        Wohnmobil
                                    </option>

                                    <option
                                        value="Sonstiges"
                                        <?= $vehicleType === 'Sonstiges' ? 'selected' : '' ?>
                                    >
                                        Sonstiges
                                    </option>
                                </select>

                            </div>


                            <div class="kfz-form-group">

                                <label
                                    for="first-registration-date"
                                    class="kfz-form-label"
                                >
                                    Erstzulassung
                                </label>

                                <input
                                    type="date"
                                    id="first-registration-date"
                                    name="first_registration_date"
                                    class="kfz-form-control"
                                    value="<?= $escape($firstRegistrationDate) ?>"
                                >

                            </div>

                        </div>

                    </fieldset>


                    <div class="kfz-vehicle-form-actions">

                        <a
                            href="<?= $escape($url('/fahrzeuge/')) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Abbrechen
                        </a>

                        <button
                            type="submit"
                            class="kfz-button kfz-button-primary"
                        >
                            Fahrzeug speichern

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

            </section>

        <?php endif; ?>


        <!-- Fahrzeugübersicht -->

        <?php if ($vehicles === []): ?>

            <div class="kfz-vehicles-empty">

                <div class="kfz-vehicles-empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                        <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                        <circle cx="8" cy="16" r="1" />
                        <circle cx="16" cy="16" r="1" />
                    </svg>

                </div>

                <h2>
                    Noch kein Fahrzeug gespeichert
                </h2>

                <p>
                    Fügen Sie jetzt Ihr erstes Fahrzeug hinzu, damit Sie es
                    später für einen digitalen Vorgang verwenden können.
                </p>

                <a
                    href="<?= $escape($url('/fahrzeuge/?neu=1')) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    Erstes Fahrzeug hinzufügen
                </a>

            </div>

        <?php else: ?>

            <div class="kfz-vehicle-list">

                <?php foreach ($vehicles as $vehicle): ?>

                    <?php
                    $vehicleId = (int)($vehicle['id'] ?? 0);

                    $vehicleLicensePlate = (string)(
                        $vehicle['license_plate'] ?? ''
                    );

                    $vehicleVin = (string)(
                        $vehicle['vin'] ?? ''
                    );

                    $vehicleManufacturer = (string)(
                        $vehicle['manufacturer'] ?? ''
                    );

                    $vehicleModel = (string)(
                        $vehicle['model'] ?? ''
                    );

                    $vehicleTypeValue = (string)(
                        $vehicle['vehicle_type'] ?? ''
                    );

                    $vehicleDate = (string)(
                        $vehicle['first_registration_date'] ?? ''
                    );

                    $vehicleTitle = trim(
                        $vehicleManufacturer . ' ' . $vehicleModel
                    );

                    if ($vehicleTitle === '') {
                        $vehicleTitle = 'Gespeichertes Fahrzeug';
                    }
                    ?>

                    <article class="kfz-vehicle-card">

                        <div class="kfz-vehicle-card-main">

                            <div class="kfz-vehicle-icon">

                                <svg
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                                    <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                                    <circle cx="8" cy="16" r="1" />
                                    <circle cx="16" cy="16" r="1" />
                                </svg>

                            </div>

                            <div class="kfz-vehicle-card-content">

                                <div class="kfz-vehicle-card-title-row">

                                    <div>

                                        <h2>
                                            <?= $escape($vehicleTitle) ?>
                                        </h2>

                                        <?php if ($vehicleLicensePlate !== ''): ?>

                                            <span class="kfz-license-plate">
                                                <?= $escape($vehicleLicensePlate) ?>
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <span class="kfz-status kfz-status-success">
                                        Gespeichert
                                    </span>

                                </div>


                                <div class="kfz-vehicle-details">

                                    <?php if ($vehicleTypeValue !== ''): ?>

                                        <div class="kfz-vehicle-detail">

                                            <span>
                                                Fahrzeugart
                                            </span>

                                            <strong>
                                                <?= $escape($vehicleTypeValue) ?>
                                            </strong>

                                        </div>

                                    <?php endif; ?>


                                    <?php if ($vehicleVin !== ''): ?>

                                        <div class="kfz-vehicle-detail">

                                            <span>
                                                FIN
                                            </span>

                                            <strong>
                                                <?= $escape($vehicleVin) ?>
                                            </strong>

                                        </div>

                                    <?php endif; ?>


                                    <?php if ($vehicleDate !== ''): ?>

                                        <div class="kfz-vehicle-detail">

                                            <span>
                                                Erstzulassung
                                            </span>

                                            <strong>
                                                <?= $escape(
                                                    date(
                                                        'd.m.Y',
                                                        strtotime($vehicleDate)
                                                    )
                                                ) ?>
                                            </strong>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>


                        <div class="kfz-vehicle-card-actions">

                            <a
                                href="<?= $escape($url('/vorgang-starten/?fahrzeug_id=' . $vehicleId)) ?>"
                                class="kfz-button kfz-button-primary"
                            >
                                Vorgang starten
                            </a>


                            <form
                                action="<?= $escape($url('/fahrzeuge/')) ?>"
                                method="post"
                                onsubmit="return confirm('Möchten Sie dieses Fahrzeug wirklich löschen?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="vehicle_id"
                                    value="<?= $escape((string)$vehicleId) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= $escape($csrfToken) ?>"
                                >

                                <button
                                    type="submit"
                                    class="kfz-button kfz-button-danger"
                                >
                                    Löschen
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>
