<?php
declare(strict_types=1);

/**
 * Kfz Digital – Vorgang prüfen
 *
 * Öffentliche Statusabfrage ohne Kundenlogin.
 * Suche über:
 * - Vorgangsnummer
 * - E-Mail-Adresse
 */

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);

$escape = static fn (mixed $value): string => kfz_escape($value);

$getInput = static function (
    array $source,
    string $key
): string {
    $value = $source[$key] ?? '';

    if (!is_scalar($value)) {
        return '';
    }

    return trim((string)$value);
};


/*
|--------------------------------------------------------------------------
| Werte
|--------------------------------------------------------------------------
*/

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);

$formErrors = [];
$application = null;

$referenceNumber = strtoupper(
    $getInput($_POST, 'vorgangsnummer')
);

$email = strtolower(
    $getInput($_POST, 'email')
);


/*
|--------------------------------------------------------------------------
| Statusbezeichnungen
|--------------------------------------------------------------------------
*/

$statusLabels = [
    'zahlung_offen' => 'Zahlung offen',
    'bezahlt' => 'Bezahlt',
    'eingegangen' => 'Eingegangen',
    'in_bearbeitung' => 'In Bearbeitung',
    'rueckfrage' => 'Rückfrage erforderlich',
    'abgeschlossen' => 'Abgeschlossen',
    'abgelehnt' => 'Abgelehnt',
    'storniert' => 'Storniert',
    'erstattet' => 'Erstattet',
];

$statusDescriptions = [
    'zahlung_offen' =>
        'Für diesen Vorgang steht die Zahlung noch aus.',

    'bezahlt' =>
        'Die Zahlung wurde bestätigt. Ihr Vorgang wartet auf die weitere Bearbeitung.',

    'eingegangen' =>
        'Ihr Vorgang ist bei uns eingegangen.',

    'in_bearbeitung' =>
        'Ihr Vorgang wird derzeit bearbeitet.',

    'rueckfrage' =>
        'Für diesen Vorgang wird eine Rückmeldung von Ihnen benötigt.',

    'abgeschlossen' =>
        'Ihr Vorgang wurde erfolgreich abgeschlossen.',

    'abgelehnt' =>
        'Ihr Vorgang konnte nicht bearbeitet werden.',

    'storniert' =>
        'Ihr Vorgang wurde storniert.',

    'erstattet' =>
        'Der Vorgang wurde erstattet.',
];


/*
|--------------------------------------------------------------------------
| Vorgang suchen
|--------------------------------------------------------------------------
*/

if ($requestMethod === 'POST') {
    if (
        $referenceNumber === ''
        || !preg_match(
            '/^KD-[0-9]{8}-[0-9]{6}$/',
            $referenceNumber
        )
    ) {
        $formErrors[] =
            'Bitte geben Sie eine gültige Vorgangsnummer ein.';
    }

    if (
        $email === ''
        || !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $formErrors[] =
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }

    if ($formErrors === []) {
        try {
            $pdo = require __DIR__ . '/../../Config/database.php';

            $statement = $pdo->prepare(
                'SELECT
                    id,
                    reference_number,
                    process_type,
                    status,
                    license_plate,
                    first_name,
                    last_name,
                    created_at,
                    updated_at,
                    submitted_at,
                    completed_at
                 FROM applications
                 WHERE reference_number = :reference_number
                   AND LOWER(email) = :email
                 LIMIT 1'
            );

            $statement->execute([
                'reference_number' => $referenceNumber,
                'email' => $email,
            ]);

            $result = $statement->fetch();

            if (is_array($result)) {
                $application = $result;
            } else {
                $formErrors[] =
                    'Es wurde kein Vorgang mit diesen Angaben gefunden.';
            }
        } catch (Throwable $exception) {
            $formErrors[] =
                'Der Vorgang konnte nicht geprüft werden.';
        }
    }
}
?>

<section
    class="kfz-section kfz-process-page"
    aria-labelledby="process-check-title"
>
    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Ohne Kundenkonto
            </span>

            <h1
                id="process-check-title"
                class="kfz-section-title"
            >
                Vorgang prüfen
            </h1>

            <p class="kfz-section-text">
                Prüfen Sie den aktuellen Bearbeitungsstand Ihres Vorgangs.
            </p>

        </div>


        <?php if ($formErrors !== []): ?>

            <div
                class="kfz-process-error"
                role="alert"
            >
                <h2>
                    Prüfung nicht möglich
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


        <?php if (is_array($application)): ?>

            <?php
            $applicationStatus = (string)(
                $application['status'] ?? ''
            );

            $statusLabel = $statusLabels[
                $applicationStatus
            ] ?? ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $applicationStatus
                )
            );

            $statusDescription = $statusDescriptions[
                $applicationStatus
            ] ?? 'Der aktuelle Status Ihres Vorgangs wurde geladen.';
            ?>

            <div class="kfz-process-form-wrapper">

                <div class="kfz-process-form-header">

                    <div>
                        <span class="kfz-section-kicker">
                            Vorgang gefunden
                        </span>

                        <h2>
                            <?= $escape(
                                $application['reference_number']
                            ) ?>
                        </h2>
                    </div>

                </div>


                <div class="kfz-process-info-card">

                    <strong>
                        Aktueller Status
                    </strong>

                    <p class="kfz-process-status">
                        <?= $escape($statusLabel) ?>
                    </p>

                    <p>
                        <?= $escape($statusDescription) ?>
                    </p>

                </div>


                <div class="kfz-payment-summary">

                    <div class="kfz-payment-summary-row">
                        <span>
                            Vorgangsnummer
                        </span>

                        <strong>
                            <?= $escape(
                                $application['reference_number']
                            ) ?>
                        </strong>
                    </div>

                    <div class="kfz-payment-summary-row">
                        <span>
                            Kennzeichen
                        </span>

                        <strong>
                            <?= $escape(
                                $application['license_plate']
                            ) ?>
                        </strong>
                    </div>

                    <div class="kfz-payment-summary-row">
                        <span>
                            Vorgang erstellt am
                        </span>

                        <strong>
                            <?= $escape(
                                date(
                                    'd.m.Y H:i',
                                    strtotime(
                                        (string)$application[
                                            'created_at'
                                        ]
                                    )
                                )
                            ) ?>
                        </strong>
                    </div>

                    <?php if (
                        !empty($application['updated_at'])
                    ): ?>

                        <div class="kfz-payment-summary-row">
                            <span>
                                Zuletzt aktualisiert
                            </span>

                            <strong>
                                <?= $escape(
                                    date(
                                        'd.m.Y H:i',
                                        strtotime(
                                            (string)$application[
                                                'updated_at'
                                            ]
                                        )
                                    )
                                ) ?>
                            </strong>
                        </div>

                    <?php endif; ?>

                </div>


                <div class="kfz-process-form-actions">

                    <a
                        href="<?= $escape(
                            $url('/vorgang-pruefen/')
                        ) ?>"
                        class="kfz-button kfz-button-outline"
                    >
                        Neue Prüfung
                    </a>

                    <a
                        href="<?= $escape($url('/')) ?>"
                        class="kfz-button kfz-button-primary"
                    >
                        Zur Startseite
                    </a>

                </div>

            </div>


        <?php else: ?>

            <div class="kfz-process-form-wrapper">

                <div class="kfz-process-form-header">

                    <div>
                        <span class="kfz-section-kicker">
                            Statusabfrage
                        </span>

                        <h2>
                            Ihre Vorgangsdaten
                        </h2>

                        <p>
                            Geben Sie Ihre Vorgangsnummer und die verwendete
                            E-Mail-Adresse ein.
                        </p>
                    </div>

                </div>


                <form
                    action="<?= $escape(
                        $url('/vorgang-pruefen/')
                    ) ?>"
                    method="post"
                    class="kfz-process-form"
                >

                    <div class="kfz-form-group">

                        <label
                            for="vorgangsnummer"
                            class="kfz-form-label"
                        >
                            Vorgangsnummer*
                        </label>

                        <input
                            type="text"
                            id="vorgangsnummer"
                            name="vorgangsnummer"
                            class="kfz-form-control"
                            value="<?= $escape(
                                $referenceNumber
                            ) ?>"
                            placeholder="KD-20260904-123456"
                            maxlength="40"
                            autocomplete="off"
                            required
                        >

                    </div>


                    <div class="kfz-form-group">

                        <label
                            for="status-email"
                            class="kfz-form-label"
                        >
                            E-Mail-Adresse*
                        </label>

                        <input
                            type="email"
                            id="status-email"
                            name="email"
                            class="kfz-form-control"
                            value="<?= $escape($email) ?>"
                            placeholder="name@beispiel.de"
                            autocomplete="email"
                            maxlength="190"
                            required
                        >

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
                            Vorgang prüfen
                        </button>

                    </div>

                </form>

            </div>

        <?php endif; ?>

    </div>
</section>