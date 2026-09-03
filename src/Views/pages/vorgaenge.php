<?php
declare(strict_types=1);
/**
 * Kfz Digital – Meine Vorgänge
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
| Anmeldung prüfen
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['user_id'])):

?>

<section
    class="kfz-section kfz-account-page"
    aria-labelledby="applications-login-title"
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
                id="applications-login-title"
                class="kfz-section-title"
            >
                Bitte melden Sie sich an.
            </h1>

            <p class="kfz-section-text">
                Ihre Vorgänge können Sie nur nach einer Anmeldung einsehen.
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

if ($userId <= 0) {

    ?>

    <section class="kfz-section">

        <div class="container">

            <div
                class="kfz-process-error"
                role="alert"
            >
                Ihre Sitzung ist ungültig.
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
}


/*
|--------------------------------------------------------------------------
| Vorgangsarten
|--------------------------------------------------------------------------
*/

$processLabels = [
    'abmeldung' => 'Fahrzeug abmelden',
];


/*
|--------------------------------------------------------------------------
| Statuswerte
|--------------------------------------------------------------------------
*/

$statusLabels = [
    'entwurf' => 'Entwurf',
    'daten_pruefung' => 'Datenprüfung',
    'dokumente_offen' => 'Dokumente offen',
    'bereit_zur_uebermittlung' => 'Bereit zur Übermittlung',
    'uebermittelt' => 'Übermittelt',
    'in_bearbeitung' => 'In Bearbeitung',
    'genehmigt' => 'Genehmigt',
    'abgelehnt' => 'Abgelehnt',
    'abgeschlossen' => 'Abgeschlossen',
];


/*
|--------------------------------------------------------------------------
| Vorgänge laden
|--------------------------------------------------------------------------
*/

$applications = [];

$errors = [];

try {

    $applicationStatement = $pdo->prepare(
        'SELECT
            a.id,
            a.vehicle_id,
            a.process_type,
            a.status,
            a.license_plate,
            a.vin,
            a.first_name,
            a.last_name,
            a.email,
            a.phone,
            a.postal_code,
            a.city,
            a.notes,
            a.created_at,
            a.updated_at,
            a.submitted_at,
            a.completed_at,
            v.manufacturer,
            v.model,
            v.vehicle_type
         FROM applications a
         LEFT JOIN vehicles v
            ON v.id = a.vehicle_id
         WHERE a.user_id = :user_id
         ORDER BY a.created_at DESC, a.id DESC'
    );

    $applicationStatement->execute([
        'user_id' => $userId,
    ]);

    $result = $applicationStatement->fetchAll();

    if (is_array($result)) {
        $applications = $result;
    }

} catch (PDOException $exception) {

    $errors[] =
        'Ihre Vorgänge konnten nicht geladen werden.';
}

/*
 * Detailansicht: Die ID wird ausschließlich als positive Ganzzahl akzeptiert
 * und in jeder Abfrage zusätzlich auf den angemeldeten Benutzer begrenzt.
 */
$requestedApplicationId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$selectedApplication = null;
$statusHistory = [];

if ($requestedApplicationId !== false && $requestedApplicationId !== null) {
    try {
        $detailStatement = $pdo->prepare(
            'SELECT
                id, process_type, status, license_plate, vin, first_name,
                last_name, email, phone, postal_code, city, notes, created_at,
                updated_at, submitted_at, completed_at
             FROM applications
             WHERE id = :id AND user_id = :user_id
             LIMIT 1'
        );
        $detailStatement->execute([
            'id' => (int)$requestedApplicationId,
            'user_id' => $userId,
        ]);
        $detailResult = $detailStatement->fetch();

        if (is_array($detailResult)) {
            $selectedApplication = $detailResult;

            $historyStatement = $pdo->prepare(
                'SELECT old_status, new_status, comment, created_at
                 FROM application_status_history
                 WHERE application_id = :application_id
                 ORDER BY created_at ASC, id ASC'
            );
            $historyStatement->execute([
                'application_id' => (int)$requestedApplicationId,
            ]);
            $historyResult = $historyStatement->fetchAll();
            $statusHistory = is_array($historyResult) ? $historyResult : [];
        } else {
            $errors[] = 'Der angeforderte Vorgang wurde nicht gefunden.';
        }
    } catch (PDOException $exception) {
        $errors[] = 'Die Details des Vorgangs konnten nicht geladen werden.';
    }
}
?>

<section
    class="kfz-section kfz-applications-page"
    aria-labelledby="applications-title"
>

    <div class="container">

        <!-- Kopfbereich -->

        <div class="kfz-applications-header">

            <div>

                <span class="kfz-section-kicker">
                    Persönlicher Bereich
                </span>

                <h1
                    id="applications-title"
                    class="kfz-section-title"
                >
                    Meine Vorgänge
                </h1>

                <p class="kfz-section-text">
                    Hier sehen Sie alle von Ihnen gestarteten
                    Fahrzeugvorgänge und deren aktuellen Status.
                </p>

            </div>

            <div>

                <a
                    href="<?= $escape($url('/vorgang-starten/')) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    <svg
                        class="kfz-button-icon"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M12 5v14M5 12h14" />
                    </svg>

                    Fahrzeug abmelden
                </a>

            </div>

        </div>


        <?php if ($errors !== []): ?>

            <div
                class="kfz-process-error"
                role="alert"
            >

                <h2>
                    Fehler
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


        <?php if ($selectedApplication !== null): ?>

            <article class="kfz-card mb-4" aria-labelledby="application-detail-title">
                <span class="kfz-section-kicker">Fahrzeugabmeldung</span>
                <h2 id="application-detail-title">
                    Vorgang KD-<?= $escape((string)$selectedApplication['id']) ?>
                </h2>
                <p>
                    Status: <strong><?= $escape(
                        $statusLabels[(string)$selectedApplication['status']]
                        ?? (string)$selectedApplication['status']
                    ) ?></strong>
                </p>
                <div class="kfz-application-details">
                    <div class="kfz-application-detail"><span>Kennzeichen</span><strong><?= $escape((string)($selectedApplication['license_plate'] ?? 'Nicht angegeben')) ?></strong></div>
                    <div class="kfz-application-detail"><span>FIN</span><strong><?= $escape((string)($selectedApplication['vin'] ?? 'Nicht angegeben')) ?></strong></div>
                    <div class="kfz-application-detail"><span>Erstellt am</span><strong><?= $escape(date('d.m.Y H:i', strtotime((string)$selectedApplication['created_at']) ?: time())) ?></strong></div>
                </div>
                <h3 class="mt-4">Statusverlauf</h3>
                <?php if ($statusHistory === []): ?>
                    <p>Noch kein Statusverlauf vorhanden.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($statusHistory as $historyEntry): ?>
                            <li>
                                <strong><?= $escape($statusLabels[(string)$historyEntry['new_status']] ?? (string)$historyEntry['new_status']) ?></strong>
                                – <?= $escape(date('d.m.Y H:i', strtotime((string)$historyEntry['created_at']) ?: time())) ?>
                                <?php if (!empty($historyEntry['comment'])): ?>
                                    <br><?= $escape((string)$historyEntry['comment']) ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a class="kfz-button kfz-button-outline" href="<?= $escape($url('/vorgaenge/')) ?>">Zur Übersicht</a>
            </article>

        <?php endif; ?>


        <?php if ($applications === []): ?>

            <!-- Keine Vorgänge -->

            <div class="kfz-applications-empty">

                <div class="kfz-applications-empty-icon">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M6 3h9l3 3v15H6V3Z" />
                        <path d="M14 3v4h4M9 12h6M9 16h6" />
                    </svg>

                </div>

                <h2>
                    Noch keine Vorgänge vorhanden
                </h2>

                <p>
                    Sie haben bisher noch keine Fahrzeugabmeldung gestartet.
                </p>

                <a
                    href="<?= $escape($url('/vorgang-starten/')) ?>"
                    class="kfz-button kfz-button-primary"
                >
                    Fahrzeug abmelden
                </a>

            </div>

        <?php else: ?>

            <!-- Vorgangsliste -->

            <div class="kfz-application-list">

                <?php foreach ($applications as $application): ?>

                    <?php
                    $applicationId = (int)(
                        $application['id'] ?? 0
                    );

                    $processType = (string)(
                        $application['process_type'] ?? ''
                    );

                    $status = (string)(
                        $application['status'] ?? 'entwurf'
                    );

                    $licensePlate = (string)(
                        $application['license_plate'] ?? ''
                    );

                    $manufacturer = (string)(
                        $application['manufacturer'] ?? ''
                    );

                    $model = (string)(
                        $application['model'] ?? ''
                    );

                    $vehicleTitle = trim(
                        $manufacturer . ' ' . $model
                    );

                    if ($vehicleTitle === '') {
                        $vehicleTitle = 'Fahrzeugabmeldung';
                    }

                    $processLabel = $processLabels[$processType]
                        ?? 'Archivierter Vorgang';

                    $statusLabel = $statusLabels[$status]
                        ?? ucfirst($status);

                    $statusClass = match ($status) {
                        'genehmigt',
                        'abgeschlossen' => 'kfz-status-success',

                        'abgelehnt' => 'kfz-status-danger',

                        'in_bearbeitung',
                        'uebermittelt',
                        'daten_pruefung',
                        'bereit_zur_uebermittlung' =>
                            'kfz-status-warning',

                        default => 'kfz-status-neutral',
                    };

                    $createdAt = '';

                    if (!empty($application['created_at'])) {

                        $createdTimestamp = strtotime(
                            (string)$application['created_at']
                        );

                        if ($createdTimestamp !== false) {
                            $createdAt = date(
                                'd.m.Y H:i',
                                $createdTimestamp
                            );
                        }
                    }
                    ?>

                    <article class="kfz-application-card">

                        <div class="kfz-application-card-top">

                            <div class="kfz-application-icon">

                                <svg
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path d="M6 3h9l3 3v15H6V3Z" />
                                    <path d="M14 3v4h4M9 12h6M9 16h6" />
                                </svg>

                            </div>

                            <div class="kfz-application-title">

                                <span class="kfz-section-kicker">
                                    Vorgang #<?= $escape(
                                        (string)$applicationId
                                    ) ?>
                                </span>

                                <h2>
                                    <?= $escape($processLabel) ?>
                                </h2>

                            </div>

                            <span
                                class="kfz-status <?= $escape($statusClass) ?>"
                            >
                                <?= $escape($statusLabel) ?>
                            </span>

                        </div>


                        <div class="kfz-application-details">

                            <div class="kfz-application-detail">

                                <span>
                                    Fahrzeug
                                </span>

                                <strong>
                                    <?= $escape($vehicleTitle) ?>
                                </strong>

                            </div>


                            <div class="kfz-application-detail">

                                <span>
                                    Kennzeichen
                                </span>

                                <strong>
                                    <?= $licensePlate !== ''
                                        ? $escape($licensePlate)
                                        : 'Nicht angegeben' ?>
                                </strong>

                            </div>


                            <div class="kfz-application-detail">

                                <span>
                                    Erstellt am
                                </span>

                                <strong>
                                    <?= $escape(
                                        $createdAt !== ''
                                            ? $createdAt
                                            : 'Nicht verfügbar'
                                    ) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="kfz-application-card-bottom">

                            <span class="kfz-application-info">
                                Vorgangsnummer:
                                <strong>
                                    KD-<?= $escape(
                                        (string)$applicationId
                                    ) ?>
                                </strong>
                            </span>

                            <a
                                href="<?= $escape(
                                    $url(
                                        '/vorgaenge/?id='
                                        . $applicationId
                                    )
                                ) ?>"
                                class="kfz-button kfz-button-outline"
                            >
                                Details anzeigen
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>
