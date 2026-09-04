<?php
declare(strict_types=1);

/**
 * Kfz Digital – Admin-Vorgang
 *
 * Interne Detailansicht eines Vorgangs.
 * Kunden benötigen keinen Login.
 */

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);

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


/*
|--------------------------------------------------------------------------
| Adminschutz
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['admin_user_id'])):
?>

<section class="kfz-section">
    <div class="container">

        <div
            class="kfz-process-error"
            role="alert"
        >
            Sie müssen als Administrator angemeldet sein.
        </div>

        <a
            href="<?= $escape($url('/admin/')) ?>"
            class="kfz-button kfz-button-primary"
        >
            Zur Administration
        </a>

    </div>
</section>

<?php
return;
endif;


/*
|--------------------------------------------------------------------------
| CSRF-Token
|--------------------------------------------------------------------------
*/

if (
    empty($_SESSION['admin_csrf_token'])
    || !is_string($_SESSION['admin_csrf_token'])
) {
    $_SESSION['admin_csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrfToken = (string)$_SESSION['admin_csrf_token'];


/*
|--------------------------------------------------------------------------
| Vorgangs-ID
|--------------------------------------------------------------------------
*/

$applicationIdValue = $_POST['application_id']
    ?? $_GET['id']
    ?? null;

$applicationId = 0;

if (is_scalar($applicationIdValue)) {
    $validatedId = filter_var(
        $applicationIdValue,
        FILTER_VALIDATE_INT,
        [
            'options' => [
                'min_range' => 1,
            ],
        ]
    );

    if ($validatedId !== false) {
        $applicationId = (int)$validatedId;
    }
}

$errors = [];
$successMessage = '';

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);


/*
|--------------------------------------------------------------------------
| Erlaubte Statuswerte
|--------------------------------------------------------------------------
*/

$statusLabels = [
    'zahlung_offen' => 'Zahlung offen',
    'bezahlt' => 'Bezahlt',
    'in_bearbeitung' => 'In Bearbeitung',
    'rueckfrage' => 'Rückfrage erforderlich',
    'abgeschlossen' => 'Abgeschlossen',
    'abgelehnt' => 'Abgelehnt',
    'storniert' => 'Storniert',
    'erstattet' => 'Erstattet',
];

$allowedStatuses = array_keys($statusLabels);


/*
|--------------------------------------------------------------------------
| Status ändern
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    && $getInput($_POST, 'admin_action') === 'update_status'
) {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($submittedToken)
        || $submittedToken === ''
        || !hash_equals($csrfToken, $submittedToken)
    ) {
        $addError(
            $errors,
            'Die Sitzung ist abgelaufen. Bitte laden Sie die Seite neu.'
        );
    }

    $newStatus = $getInput(
        $_POST,
        'status'
    );

    $comment = $getInput(
        $_POST,
        'comment'
    );

    if (!in_array($newStatus, $allowedStatuses, true)) {
        $addError(
            $errors,
            'Der ausgewählte Status ist ungültig.'
        );
    }

    if (mb_strlen($comment) > 2000) {
        $addError(
            $errors,
            'Der Kommentar ist zu lang.'
        );
    }

    if (
        $applicationId <= 0
        && $errors === []
    ) {
        $addError(
            $errors,
            'Der Vorgang ist ungültig.'
        );
    }

    if ($errors === []) {
        try {
            $pdo = require __DIR__ . '/../../Config/database.php';

            $pdo->beginTransaction();

            $currentStatement = $pdo->prepare(
                'SELECT status
                 FROM applications
                 WHERE id = :application_id
                 LIMIT 1'
            );

            $currentStatement->execute([
                'application_id' => $applicationId,
            ]);

            $oldStatus = $currentStatement->fetchColumn();

            if (!is_string($oldStatus)) {
                throw new RuntimeException(
                    'Der Vorgang wurde nicht gefunden.'
                );
            }

            if ($oldStatus !== $newStatus) {
                $updateStatement = $pdo->prepare(
                    'UPDATE applications
                     SET status = :new_status
                     WHERE id = :application_id'
                );

                $updateStatement->execute([
                    'new_status' => $newStatus,
                    'application_id' => $applicationId,
                ]);

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
                        :old_status,
                        :new_status,
                        :comment
                    )'
                );

                $historyStatement->execute([
                    'application_id' => $applicationId,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'comment' => $comment !== ''
                        ? $comment
                        : 'Status durch Administration geändert.',
                ]);
            } elseif ($comment !== '') {
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
                        :old_status,
                        :new_status,
                        :comment
                    )'
                );

                $historyStatement->execute([
                    'application_id' => $applicationId,
                    'old_status' => $oldStatus,
                    'new_status' => $oldStatus,
                    'comment' => $comment,
                ]);
            }

            $pdo->commit();

            $successMessage =
                'Der Vorgang wurde erfolgreich aktualisiert.';
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $addError(
                $errors,
                'Der Status konnte nicht gespeichert werden.'
            );
        }
    }
}


/*
|--------------------------------------------------------------------------
| Vorgang und Historie laden
|--------------------------------------------------------------------------
*/

$application = null;
$history = [];
$payment = null;

if ($applicationId > 0) {
    try {
        $pdo = require __DIR__ . '/../../Config/database.php';

        $applicationStatement = $pdo->prepare(
            'SELECT *
             FROM applications
             WHERE id = :application_id
             LIMIT 1'
        );

        $applicationStatement->execute([
            'application_id' => $applicationId,
        ]);

        $application = $applicationStatement->fetch();

        if (is_array($application)) {
            $historyStatement = $pdo->prepare(
                'SELECT
                    old_status,
                    new_status,
                    comment,
                    created_at
                 FROM application_status_history
                 WHERE application_id = :application_id
                 ORDER BY created_at DESC, id DESC'
            );

            $historyStatement->execute([
                'application_id' => $applicationId,
            ]);

            $historyResult = $historyStatement->fetchAll();

            if (is_array($historyResult)) {
                $history = $historyResult;
            }

            $paymentStatement = $pdo->prepare(
                'SELECT
                    provider,
                    amount_cents,
                    currency,
                    status,
                    paid_at,
                    created_at
                 FROM payments
                 WHERE application_id = :application_id
                 ORDER BY id DESC
                 LIMIT 1'
            );

            $paymentStatement->execute([
                'application_id' => $applicationId,
            ]);

            $paymentResult = $paymentStatement->fetch();

            if (is_array($paymentResult)) {
                $payment = $paymentResult;
            }
        }
    } catch (Throwable $exception) {
        $addError(
            $errors,
            'Die Vorgangsdaten konnten nicht geladen werden.'
        );
    }
}
?>

<section
    class="kfz-section kfz-admin-page"
    aria-labelledby="admin-application-title"
>
    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Administration
            </span>

            <h1
                id="admin-application-title"
                class="kfz-section-title"
            >
                Vorgang bearbeiten
            </h1>

            <p class="kfz-section-text">
                Prüfen und aktualisieren Sie die Vorgangsdaten.
            </p>

        </div>


        <?php if ($errors !== []): ?>

            <div
                class="kfz-process-error"
                role="alert"
            >
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= $escape($error) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>


        <?php if ($successMessage !== ''): ?>

            <div
                class="kfz-process-success"
                role="status"
            >
                <?= $escape($successMessage) ?>
            </div>

        <?php endif; ?>


        <?php if (!is_array($application)): ?>

            <div
                class="kfz-process-error"
                role="alert"
            >
                Der Vorgang wurde nicht gefunden.
            </div>

            <a
                href="<?= $escape($url('/admin/')) ?>"
                class="kfz-button kfz-button-primary"
            >
                Zur Vorgangsübersicht
            </a>

        <?php else: ?>

            <?php
            $currentStatus = (string)(
                $application['status'] ?? ''
            );

            $currentStatusLabel = $statusLabels[
                $currentStatus
            ] ?? $currentStatus;
            ?>

            <div class="kfz-process-form-wrapper">

                <div class="kfz-process-form-header">

                    <div>
                        <span class="kfz-section-kicker">
                            Vorgang
                        </span>

                        <h2>
                            <?= $escape(
                                $application['reference_number']
                                ?? ''
                            ) ?>
                        </h2>

                        <p>
                            Aktueller Status:
                            <strong>
                                <?= $escape($currentStatusLabel) ?>
                            </strong>
                        </p>
                    </div>

                </div>


                <div class="kfz-payment-summary">

                    <div class="kfz-payment-summary-row">
                        <span>
                            Kennzeichen
                        </span>

                        <strong>
                            <?= $escape(
                                $application['license_plate']
                                ?? ''
                            ) ?>
                        </strong>
                    </div>

                    <div class="kfz-payment-summary-row">
                        <span>
                            Name
                        </span>

                        <strong>
                            <?= $escape(
                                trim(
                                    (string)(
                                        $application['first_name']
                                        ?? ''
                                    )
                                    . ' '
                                    . (string)(
                                        $application['last_name']
                                        ?? ''
                                    )
                                )
                            ) ?>
                        </strong>
                    </div>

                    <div class="kfz-payment-summary-row">
                        <span>
                            E-Mail
                        </span>

                        <strong>
                            <?= $escape(
                                $application['email'] ?? ''
                            ) ?>
                        </strong>
                    </div>

                    <div class="kfz-payment-summary-row">
                        <span>
                            FIN
                        </span>

                        <strong>
                            <?= $escape(
                                $application['vin'] ?? ''
                            ) ?>
                        </strong>
                    </div>

                    <div class="kfz-payment-summary-row">
                        <span>
                            Erstellt
                        </span>

                        <strong>
                            <?= $escape(
                                date(
                                    'd.m.Y H:i',
                                    strtotime(
                                        (string)(
                                            $application[
                                                'created_at'
                                            ] ?? ''
                                        )
                                    )
                                )
                            ) ?>
                        </strong>
                    </div>

                </div>


                <?php if (is_array($payment)): ?>

                    <div class="kfz-process-info-card">

                        <strong>
                            Zahlung
                        </strong>

                        <p>
                            Anbieter:
                            <?= $escape(
                                $payment['provider'] ?? ''
                            ) ?>
                        </p>

                        <p>
                            Betrag:
                            <strong>
                                <?= $escape(
                                    number_format(
                                        ((int)(
                                            $payment[
                                                'amount_cents'
                                            ] ?? 0
                                        )) / 100,
                                        2,
                                        ',',
                                        '.'
                                    )
                                ) ?>
                                <?= $escape(
                                    $payment['currency'] ?? 'EUR'
                                ) ?>
                            </strong>
                        </p>

                        <p>
                            Status:
                            <strong>
                                <?= $escape(
                                    $payment['status'] ?? ''
                                ) ?>
                            </strong>
                        </p>

                    </div>

                <?php endif; ?>


                <form
                    action="<?= $escape(
                        $url('/admin-vorgang/')
                    ) ?>"
                    method="post"
                    class="kfz-process-form"
                >

                    <input
                        type="hidden"
                        name="admin_action"
                        value="update_status"
                    >

                    <input
                        type="hidden"
                        name="application_id"
                        value="<?= $escape(
                            $application['id'] ?? ''
                        ) ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $escape($csrfToken) ?>"
                    >

                    <div class="kfz-form-group">

                        <label
                            for="status"
                            class="kfz-form-label"
                        >
                            Status ändern
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="kfz-form-select"
                            required
                        >

                            <?php foreach (
                                $statusLabels as $statusKey
                                => $statusLabel
                            ): ?>

                                <option
                                    value="<?= $escape(
                                        $statusKey
                                    ) ?>"
                                    <?= $statusKey === $currentStatus
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= $escape($statusLabel) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="kfz-form-group">

                        <label
                            for="comment"
                            class="kfz-form-label"
                        >
                            Interne Notiz
                        </label>

                        <textarea
                            id="comment"
                            name="comment"
                            class="kfz-form-control kfz-form-textarea"
                            rows="4"
                            maxlength="2000"
                            placeholder="Notiz zum Statuswechsel"
                        ></textarea>

                    </div>


                    <div class="kfz-process-form-actions">

                        <a
                            href="<?= $escape($url('/admin/')) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Zur Übersicht
                        </a>

                        <button
                            type="submit"
                            class="kfz-button kfz-button-primary"
                        >
                            Änderungen speichern
                        </button>

                    </div>

                </form>

            </div>


            <div class="kfz-process-form-wrapper">

                <div class="kfz-process-form-header">

                    <div>
                        <span class="kfz-section-kicker">
                            Historie
                        </span>

                        <h2>
                            Statusverlauf
                        </h2>
                    </div>

                </div>


                <?php if ($history === []): ?>

                    <p>
                        Noch keine Statusänderungen vorhanden.
                    </p>

                <?php else: ?>

                    <div class="kfz-admin-history">

                        <?php foreach ($history as $historyEntry): ?>

                            <?php
                            $historyStatus = (string)(
                                $historyEntry['new_status']
                                ?? ''
                            );

                            $historyLabel = $statusLabels[
                                $historyStatus
                            ] ?? $historyStatus;
                            ?>

                            <div class="kfz-admin-history-item">

                                <strong>
                                    <?= $escape($historyLabel) ?>
                                </strong>

                                <small>
                                    <?= $escape(
                                        date(
                                            'd.m.Y H:i',
                                            strtotime(
                                                (string)(
                                                    $historyEntry[
                                                        'created_at'
                                                    ] ?? ''
                                                )
                                            )
                                        )
                                    ) ?>
                                </small>

                                <?php if (
                                    !empty(
                                        $historyEntry['comment']
                                    )
                                ): ?>

                                    <p>
                                        <?= $escape(
                                            $historyEntry['comment']
                                        ) ?>
                                    </p>

                                <?php endif; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>
</section>