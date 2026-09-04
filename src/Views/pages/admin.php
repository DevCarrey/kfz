<?php
declare(strict_types=1);

/**
 * Kfz Digital – Admin-Dashboard
 *
 * Der Kundenprozess läuft automatisch.
 * Der Adminbereich dient zur Kontrolle:
 *
 * - Vorgänge
 * - Zahlungen
 * - API-Warteschlange
 * - Fehler
 * - Statusübersicht
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


/*
|--------------------------------------------------------------------------
| Admin-CSRF
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
| Grundwerte
|--------------------------------------------------------------------------
*/

$requestMethod = strtoupper(
    (string)($_SERVER['REQUEST_METHOD'] ?? 'GET')
);

$errors = [];
$successMessage = '';

$isAdminLoggedIn = !empty(
    $_SESSION['admin_user_id']
);

$adminEmail = strtolower(
    $getInput($_POST, 'email')
);


/*
|--------------------------------------------------------------------------
| Admin abmelden
|--------------------------------------------------------------------------
*/

if (
    $requestMethod === 'POST'
    && $getInput($_POST, 'admin_action') === 'logout'
) {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        is_string($submittedToken)
        && $submittedToken !== ''
        && hash_equals($csrfToken, $submittedToken)
    ) {
        unset(
            $_SESSION['admin_user_id'],
            $_SESSION['admin_user_email'],
            $_SESSION['admin_user_name']
        );

        $isAdminLoggedIn = false;
    }
}


/*
|--------------------------------------------------------------------------
| Admin anmelden
|--------------------------------------------------------------------------
*/

if (
    !$isAdminLoggedIn
    && $requestMethod === 'POST'
    && $getInput($_POST, 'admin_action') === 'login'
) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $password = $getInput($_POST, 'password');

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

    if (
        $adminEmail === ''
        || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)
    ) {
        $addError(
            $errors,
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.'
        );
    }

    if ($password === '') {
        $addError(
            $errors,
            'Bitte geben Sie Ihr Passwort ein.'
        );
    }

    if ($errors === []) {
        try {
            $pdo = require __DIR__ . '/../../Config/database.php';

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
                 WHERE LOWER(email) = :email
                   AND role = :role
                   AND is_active = 1
                 LIMIT 1'
            );

            $statement->execute([
                'email' => $adminEmail,
                'role' => 'admin',
            ]);

            $admin = $statement->fetch();

            if (
                !is_array($admin)
                || !password_verify(
                    $password,
                    (string)($admin['password_hash'] ?? '')
                )
            ) {
                $addError(
                    $errors,
                    'E-Mail-Adresse oder Passwort ist falsch.'
                );
            } else {
                session_regenerate_id(true);

                $_SESSION['admin_user_id'] =
                    (int)$admin['id'];

                $_SESSION['admin_user_email'] =
                    (string)$admin['email'];

                $_SESSION['admin_user_name'] = trim(
                    (string)$admin['first_name']
                    . ' '
                    . (string)$admin['last_name']
                );

                $isAdminLoggedIn = true;
            }
        } catch (Throwable $exception) {
            $addError(
                $errors,
                'Die Anmeldung konnte nicht verarbeitet werden.'
            );
        }
    }
}


/*
|--------------------------------------------------------------------------
| Login anzeigen
|--------------------------------------------------------------------------
*/

if (!$isAdminLoggedIn):
?>

<section
    class="kfz-section kfz-admin-page"
    aria-labelledby="admin-login-title"
>
    <div class="container">

        <div class="kfz-process-form-wrapper">

            <div class="kfz-section-header">
                <span class="kfz-section-kicker">
                    Interner Bereich
                </span>

                <h1
                    id="admin-login-title"
                    class="kfz-section-title"
                >
                    Administration
                </h1>

                <p class="kfz-section-text">
                    Dieser Bereich ist ausschließlich für Administratoren.
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


            <form
                action="<?= $escape($url('/admin/')) ?>"
                method="post"
                class="kfz-process-form"
            >

                <input
                    type="hidden"
                    name="admin_action"
                    value="login"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $escape($csrfToken) ?>"
                >

                <div class="kfz-form-group">
                    <label
                        for="admin-email"
                        class="kfz-form-label"
                    >
                        E-Mail-Adresse
                    </label>

                    <input
                        type="email"
                        id="admin-email"
                        name="email"
                        class="kfz-form-control"
                        value="<?= $escape($adminEmail) ?>"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="kfz-form-group">
                    <label
                        for="admin-password"
                        class="kfz-form-label"
                    >
                        Passwort
                    </label>

                    <input
                        type="password"
                        id="admin-password"
                        name="password"
                        class="kfz-form-control"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="kfz-process-form-actions">
                    <a
                        href="<?= $escape($url('/')) ?>"
                        class="kfz-button kfz-button-outline"
                    >
                        Zur Startseite
                    </a>

                    <button
                        type="submit"
                        class="kfz-button kfz-button-primary"
                    >
                        Anmelden
                    </button>
                </div>

            </form>

        </div>

    </div>
</section>

<?php
return;
endif;


/*
|--------------------------------------------------------------------------
| Dashboarddaten laden
|--------------------------------------------------------------------------
*/

$pdo = null;
$statistics = [
    'total' => 0,
    'today' => 0,
    'payment_open' => 0,
    'paid' => 0,
    'api_queue' => 0,
    'api_errors' => 0,
    'completed' => 0,
];

$applications = [];
$jobs = [];

$statusLabels = [
    'zahlung_offen' => 'Zahlung offen',
    'bezahlt' => 'Bezahlt',
    'api_warteschlange' => 'API-Warteschlange',
    'api_uebertragung' => 'API-Übertragung',
    'api_uebermittelt' => 'API übermittelt',
    'api_rueckfrage' => 'API-Rückfrage',
    'in_bearbeitung' => 'In Bearbeitung',
    'rueckfrage' => 'Rückfrage',
    'abgeschlossen' => 'Abgeschlossen',
    'abgelehnt' => 'Abgelehnt',
    'storniert' => 'Storniert',
    'fehler' => 'Fehler',
];

try {
    $pdo = require __DIR__ . '/../../Config/database.php';

    $statistics['total'] = (int)$pdo
        ->query(
            'SELECT COUNT(*)
             FROM applications'
        )
        ->fetchColumn();

    $statistics['today'] = (int)$pdo
        ->query(
            'SELECT COUNT(*)
             FROM applications
             WHERE created_at >= CURDATE()'
        )
        ->fetchColumn();

    $statistics['payment_open'] = (int)$pdo
        ->query(
            "SELECT COUNT(*)
             FROM applications
             WHERE status = 'zahlung_offen'"
        )
        ->fetchColumn();

$statistics['paid'] = (int)$pdo
    ->query(
        "SELECT COUNT(*)
         FROM payments
         WHERE status = 'bezahlt'"
    )
    ->fetchColumn();

    $statistics['api_queue'] = (int)$pdo
        ->query(
            "SELECT COUNT(*)
             FROM applications
             WHERE status IN
             (
                'api_warteschlange',
                'api_uebertragung'
             )"
        )
        ->fetchColumn();

    $statistics['api_errors'] = (int)$pdo
        ->query(
            "SELECT COUNT(*)
             FROM applications
             WHERE status = 'fehler'
                OR api_error IS NOT NULL"
        )
        ->fetchColumn();

    $statistics['completed'] = (int)$pdo
        ->query(
            "SELECT COUNT(*)
             FROM applications
             WHERE status = 'abgeschlossen'"
        )
        ->fetchColumn();


    /*
     * Letzte Vorgänge
     */
    $applicationStatement = $pdo->query(
        'SELECT
            id,
            reference_number,
            status,
            license_plate,
            first_name,
            last_name,
            email,
            api_reference,
            api_status,
            api_error,
            created_at
         FROM applications
         ORDER BY created_at DESC, id DESC
         LIMIT 25'
    );

    $applicationResult = $applicationStatement->fetchAll();

    if (is_array($applicationResult)) {
        $applications = $applicationResult;
    }


    /*
     * Letzte Jobs
     */
    $jobStatement = $pdo->query(
        'SELECT
            j.id,
            j.application_id,
            j.job_type,
            j.status,
            j.attempts,
            j.error_message,
            j.available_at,
            j.created_at,
            a.reference_number
         FROM application_jobs j
         LEFT JOIN applications a
            ON a.id = j.application_id
         ORDER BY j.created_at DESC, j.id DESC
         LIMIT 25'
    );

    $jobResult = $jobStatement->fetchAll();

    if (is_array($jobResult)) {
        $jobs = $jobResult;
    }
} catch (Throwable $exception) {
    $addError(
        $errors,
        'Die Dashboarddaten konnten nicht vollständig geladen werden.'
    );
}
?>

<section
    class="kfz-section kfz-admin-page"
    aria-labelledby="admin-dashboard-title"
>
    <div class="container">

        <div class="kfz-section-header">

            <span class="kfz-section-kicker">
                Automatisches Kontrollzentrum
            </span>

            <h1
                id="admin-dashboard-title"
                class="kfz-section-title"
            >
                Admin-Dashboard
            </h1>

            <p class="kfz-section-text">
                Willkommen,
                <?= $escape(
                    $_SESSION['admin_user_name']
                    ?? 'Administrator'
                ) ?>.
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


        <div class="kfz-admin-actions">

            <form
                action="<?= $escape($url('/admin/')) ?>"
                method="post"
            >
                <input
                    type="hidden"
                    name="admin_action"
                    value="logout"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $escape($csrfToken) ?>"
                >

                <button
                    type="submit"
                    class="kfz-button kfz-button-outline"
                >
                    Abmelden
                </button>
            </form>

        </div>


        <div class="kfz-admin-stat-grid">

            <div class="kfz-admin-stat-card">
                <span>Vorgänge insgesamt</span>
                <strong>
                    <?= $escape($statistics['total']) ?>
                </strong>
            </div>

            <div class="kfz-admin-stat-card">
                <span>Heute eingegangen</span>
                <strong>
                    <?= $escape($statistics['today']) ?>
                </strong>
            </div>

            <div class="kfz-admin-stat-card">
                <span>Zahlung offen</span>
                <strong>
                    <?= $escape($statistics['payment_open']) ?>
                </strong>
            </div>

            <div class="kfz-admin-stat-card">
                <span>Bezahlt</span>
                <strong>
                    <?= $escape($statistics['paid']) ?>
                </strong>
            </div>

            <div class="kfz-admin-stat-card">
                <span>API-Warteschlange</span>
                <strong>
                    <?= $escape($statistics['api_queue']) ?>
                </strong>
            </div>

            <div class="kfz-admin-stat-card kfz-admin-stat-card-warning">
                <span>API-Fehler</span>
                <strong>
                    <?= $escape($statistics['api_errors']) ?>
                </strong>
            </div>

            <div class="kfz-admin-stat-card">
                <span>Abgeschlossen</span>
                <strong>
                    <?= $escape($statistics['completed']) ?>
                </strong>
            </div>

        </div>


        <div class="kfz-process-form-wrapper">

            <div class="kfz-process-form-header">

                <div>
                    <span class="kfz-section-kicker">
                        Vorgänge
                    </span>

                    <h2>
                        Letzte Vorgänge
                    </h2>

                    <p>
                        Automatisch eingegangene Vorgänge im Überblick.
                    </p>
                </div>

            </div>


            <div class="kfz-admin-table-wrapper">

                <table class="kfz-admin-table">

                    <thead>
                        <tr>
                            <th>Vorgang</th>
                            <th>Kennzeichen</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>API</th>
                            <th>Erstellt</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($applications === []): ?>

                            <tr>
                                <td colspan="6">
                                    Noch keine Vorgänge vorhanden.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach (
                                $applications
                                as $application
                            ): ?>

                                <?php
                                $status = (string)(
                                    $application['status']
                                    ?? ''
                                );

                                $statusLabel =
                                    $statusLabels[$status]
                                    ?? ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $status
                                        )
                                    );

                                $apiStatus = trim(
                                    (string)(
                                        $application[
                                            'api_status'
                                        ] ?? ''
                                    )
                                );

                                $apiError = trim(
                                    (string)(
                                        $application[
                                            'api_error'
                                        ] ?? ''
                                    )
                                );
                                ?>

                                <tr>

                                    <td>
                                        <a
                                            href="<?= $escape(
                                                $url(
                                                    '/admin-vorgang/?id='
                                                    . (int)(
                                                        $application[
                                                            'id'
                                                        ] ?? 0
                                                    )
                                                )
                                            ) ?>"
                                        >
                                            <strong>
                                                <?= $escape(
                                                    $application[
                                                        'reference_number'
                                                    ] ?? ''
                                                ) ?>
                                            </strong>
                                        </a>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $application[
                                                'license_plate'
                                            ] ?? ''
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            trim(
                                                (string)(
                                                    $application[
                                                        'first_name'
                                                    ] ?? ''
                                                )
                                                . ' '
                                                . (string)(
                                                    $application[
                                                        'last_name'
                                                    ] ?? ''
                                                )
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <span
                                            class="kfz-admin-status"
                                        >
                                            <?= $escape(
                                                $statusLabel
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($apiError !== ''): ?>

                                            <span
                                                class="kfz-admin-status kfz-admin-status-error"
                                            >
                                                Fehler
                                            </span>

                                        <?php elseif ($apiStatus !== ''): ?>

                                            <?= $escape(
                                                $apiStatus
                                            ) ?>

                                        <?php else: ?>

                                            <span
                                                class="kfz-admin-status"
                                            >
                                                Noch nicht übermittelt
                                            </span>

                                        <?php endif; ?>
                                    </td>

                                    <td>
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
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <div class="kfz-process-form-wrapper">

            <div class="kfz-process-form-header">

                <div>
                    <span class="kfz-section-kicker">
                        Automatisierung
                    </span>

                    <h2>
                        Job-Warteschlange
                    </h2>

                    <p>
                        API- und Systemjobs zur automatischen Verarbeitung.
                    </p>
                </div>

            </div>


            <div class="kfz-admin-table-wrapper">

                <table class="kfz-admin-table">

                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Vorgang</th>
                            <th>Status</th>
                            <th>Versuche</th>
                            <th>Verfügbar ab</th>
                            <th>Fehler</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($jobs === []): ?>

                            <tr>
                                <td colspan="6">
                                    Keine Jobs vorhanden.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($jobs as $job): ?>

                                <tr>

                                    <td>
                                        <?= $escape(
                                            $job['job_type']
                                            ?? ''
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $job['reference_number']
                                            ?? (
                                                'ID '
                                                . (
                                                    (int)(
                                                        $job[
                                                            'application_id'
                                                        ] ?? 0
                                                    )
                                                )
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $job['status'] ?? ''
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $job['attempts'] ?? 0
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            date(
                                                'd.m.Y H:i',
                                                strtotime(
                                                    (string)(
                                                        $job[
                                                            'available_at'
                                                        ] ?? ''
                                                    )
                                                )
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $job['error_message']
                                            ?? ''
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</section>