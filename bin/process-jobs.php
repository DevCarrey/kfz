<?php
declare(strict_types=1);

/**
 * Kfz Digital – automatischer Job-Worker
 *
 * Unterstützte Jobs:
 * - api_submit
 * - api_status_check
 * - send_email
 *
 * Aktuell:
 * - Mock-API
 * - Mock-E-Mail-Versand
 * - automatische Wiederholungsversuche
 * - Statushistorie
 *
 * Start:
 * C:\xampp\php\php.exe bin\process-jobs.php
 */

$projectDir = dirname(__DIR__);


/*
|--------------------------------------------------------------------------
| Services laden
|--------------------------------------------------------------------------
*/

$apiServiceFile = $projectDir
    . '/src/Services/IkfzApiService.php';

if (is_file($apiServiceFile)) {
    require_once $apiServiceFile;
}

$notificationServiceFile = $projectDir
    . '/src/Services/NotificationService.php';

if (is_file($notificationServiceFile)) {
    require_once $notificationServiceFile;
}


/*
|--------------------------------------------------------------------------
| Datenbank laden
|--------------------------------------------------------------------------
*/

try {
    $pdo = require $projectDir
        . '/src/Config/database.php';

    if (!$pdo instanceof PDO) {
        throw new RuntimeException(
            'Die Datenbankverbindung ist ungültig.'
        );
    }
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        'Datenbankverbindung fehlgeschlagen: '
        . $exception->getMessage()
        . PHP_EOL
    );

    exit(1);
}


/*
|--------------------------------------------------------------------------
| Konfigurationen laden
|--------------------------------------------------------------------------
*/

$paymentConfigFile = $projectDir
    . '/src/Config/payment.php';

$paymentConfig = is_file($paymentConfigFile)
    ? require $paymentConfigFile
    : [
        'mode' => 'mock',
    ];

$mailConfigFile = $projectDir
    . '/src/Config/mail.php';

$mailConfig = is_file($mailConfigFile)
    ? require $mailConfigFile
    : [
        'mode' => 'mock',
    ];

$apiMode = strtolower(
    (string)($paymentConfig['mode'] ?? 'mock')
);

$apiService = null;

if (class_exists('IkfzApiService')) {
    $apiService = new IkfzApiService($apiMode);
}

$notificationService = null;

if (class_exists('NotificationService')) {
    $notificationService = new NotificationService(
        $projectDir,
        is_array($mailConfig)
            ? $mailConfig
            : []
    );
}


/*
|--------------------------------------------------------------------------
| Worker-Konfiguration
|--------------------------------------------------------------------------
*/

$workerName = (string)(gethostname() ?: 'worker')
    . '-'
    . getmypid();

$maxJobsPerRun = 25;
$defaultMaxAttempts = 5;

$retryDelays = [
    1 => 60,
    2 => 300,
    3 => 900,
    4 => 3600,
];


/*
|--------------------------------------------------------------------------
| Ausgabe
|--------------------------------------------------------------------------
*/

$writeOutput = static function (
    string $message
): void {
    echo '['
        . date('Y-m-d H:i:s')
        . '] '
        . $message
        . PHP_EOL;
};


/*
|--------------------------------------------------------------------------
| Statushistorie schreiben
|--------------------------------------------------------------------------
*/

$addHistory = static function (
    PDO $pdo,
    int $applicationId,
    ?string $oldStatus,
    string $newStatus,
    string $comment
): void {
    $statement = $pdo->prepare(
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

    $statement->execute([
        'application_id' => $applicationId,
        'old_status' => $oldStatus,
        'new_status' => $newStatus,
        'comment' => $comment,
    ]);
};


/*
|--------------------------------------------------------------------------
| E-Mail-Job anlegen
|--------------------------------------------------------------------------
*/

$createEmailJob = static function (
    PDO $pdo,
    int $applicationId,
    string $notificationType
): void {
    $payloadJson = json_encode(
        [
            'notification_type' => $notificationType,
        ],
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR
    );

    $checkStatement = $pdo->prepare(
        "SELECT id
         FROM application_jobs
         WHERE application_id = :application_id
           AND job_type = 'send_email'
           AND status IN ('offen', 'in_bearbeitung')
           AND JSON_UNQUOTE(
                JSON_EXTRACT(
                    payload_json,
                    '$.notification_type'
                )
           ) = :notification_type
         LIMIT 1"
    );

    $checkStatement->execute([
        'application_id' => $applicationId,
        'notification_type' => $notificationType,
    ]);

    if ($checkStatement->fetchColumn() !== false) {
        return;
    }

    $insertStatement = $pdo->prepare(
        "INSERT INTO application_jobs
        (
            application_id,
            job_type,
            status,
            attempts,
            max_attempts,
            available_at,
            payload_json
        )
        VALUES
        (
            :application_id,
            'send_email',
            'offen',
            0,
            5,
            NOW(),
            :payload_json
        )"
    );

    $insertStatement->execute([
        'application_id' => $applicationId,
        'payload_json' => $payloadJson,
    ]);
};


/*
|--------------------------------------------------------------------------
| Offene Jobs laden
|--------------------------------------------------------------------------
*/

$jobSql = "
    SELECT
        j.id,
        j.application_id,
        j.job_type,
        j.status,
        j.attempts,
        j.max_attempts,
        j.error_message,
        j.payload_json,
        a.reference_number,
        a.status AS application_status,
        a.email,
        a.first_name,
        a.last_name,
        a.license_plate,
        a.api_provider,
        a.api_reference,
        a.api_status,
        a.api_submitted_at
    FROM application_jobs j
    INNER JOIN applications a
        ON a.id = j.application_id
    WHERE j.status = 'offen'
      AND j.available_at <= NOW()
      AND j.attempts < j.max_attempts
    ORDER BY j.created_at ASC, j.id ASC
    LIMIT {$maxJobsPerRun}
";

$jobStatement = $pdo->prepare($jobSql);
$jobStatement->execute();

$jobs = $jobStatement->fetchAll();

if (!is_array($jobs)) {
    $jobs = [];
}

if ($jobs === []) {
    $writeOutput('Keine offenen Jobs gefunden.');
    exit(0);
}

$writeOutput(
    count($jobs) . ' offene Jobs gefunden.'
);


/*
|--------------------------------------------------------------------------
| Jobs verarbeiten
|--------------------------------------------------------------------------
*/

foreach ($jobs as $job) {
    $jobId = (int)($job['id'] ?? 0);

    $applicationId = (int)(
        $job['application_id'] ?? 0
    );

    $jobType = (string)(
        $job['job_type'] ?? ''
    );

    $referenceNumber = (string)(
        $job['reference_number'] ?? ''
    );

    $attemptsBefore = (int)(
        $job['attempts'] ?? 0
    );

    $maxAttempts = (int)(
        $job['max_attempts'] ?? $defaultMaxAttempts
    );

    if ($maxAttempts <= 0) {
        $maxAttempts = $defaultMaxAttempts;
    }

    if (
        $jobId <= 0
        || $applicationId <= 0
    ) {
        $writeOutput(
            'Ungültiger Job wurde übersprungen.'
        );

        continue;
    }

    if (
        !in_array(
            $jobType,
            [
                'api_submit',
                'api_status_check',
                'send_email',
            ],
            true
        )
    ) {
        $writeOutput(
            'Unbekannter Jobtyp bei Job #'
            . $jobId
            . ': '
            . $jobType
        );

        continue;
    }

    $writeOutput(
        'Job #'
        . $jobId
        . ' für '
        . $referenceNumber
        . ' wird verarbeitet.'
    );

    $currentAttempt = $attemptsBefore + 1;

    try {
        /*
         * Job reservieren
         */
        $lockStatement = $pdo->prepare(
            "UPDATE application_jobs
             SET
                status = 'in_bearbeitung',
                attempts = attempts + 1,
                last_attempt_at = NOW(),
                locked_at = NOW(),
                locked_by = :locked_by
             WHERE id = :job_id
               AND status = 'offen'
               AND attempts < max_attempts"
        );

        $lockStatement->execute([
            'job_id' => $jobId,
            'locked_by' => $workerName,
        ]);

        if ($lockStatement->rowCount() !== 1) {
            $writeOutput(
                'Job #'
                . $jobId
                . ' wurde bereits verarbeitet.'
            );

            continue;
        }


        /*
         * Anwendung aus der Datenbank laden
         */
        $applicationStatement = $pdo->prepare(
            'SELECT
                id,
                reference_number,
                status,
                email,
                first_name,
                last_name,
                license_plate,
                api_provider,
                api_reference,
                api_status,
                api_submitted_at
             FROM applications
             WHERE id = :application_id
             LIMIT 1'
        );

        $applicationStatement->execute([
            'application_id' => $applicationId,
        ]);

        $application = $applicationStatement->fetch();

        if (!is_array($application)) {
            throw new RuntimeException(
                'Der Vorgang wurde nicht gefunden.'
            );
        }


        /*
         |--------------------------------------------------------------------------
         | Job: send_email
         |--------------------------------------------------------------------------
         */
        if ($jobType === 'send_email') {
            if (
                $notificationService === null
                || !method_exists(
                    $notificationService,
                    'sendApplicationNotification'
                )
            ) {
                throw new RuntimeException(
                    'Der NotificationService ist nicht verfügbar.'
                );
            }

            $payload = [];

            if (!empty($job['payload_json'])) {
                $decodedPayload = json_decode(
                    (string)$job['payload_json'],
                    true
                );

                if (is_array($decodedPayload)) {
                    $payload = $decodedPayload;
                }
            }

            $notificationType = (string)(
                $payload['notification_type']
                ?? 'status_update'
            );

            $notificationService
                ->sendApplicationNotification(
                    $application,
                    $notificationType
                );

            $emailJobUpdate = $pdo->prepare(
                "UPDATE application_jobs
                 SET
                    status = 'erfolgreich',
                    processed_at = NOW(),
                    error_message = NULL,
                    locked_at = NULL,
                    locked_by = NULL
                 WHERE id = :job_id"
            );

            $emailJobUpdate->execute([
                'job_id' => $jobId,
            ]);

            $writeOutput(
                'E-Mail-Job #'
                . $jobId
                . ' erfolgreich verarbeitet.'
            );

            continue;
        }


        /*
         * API-Service für API-Jobs prüfen
         */
        if (
            $apiService === null
            || !method_exists(
                $apiService,
                'submitApplication'
            )
            || !method_exists(
                $apiService,
                'getApplicationStatus'
            )
        ) {
            throw new RuntimeException(
                'Der API-Service ist nicht verfügbar.'
            );
        }


        /*
         |--------------------------------------------------------------------------
         | Job: api_submit
         |--------------------------------------------------------------------------
         */
        if ($jobType === 'api_submit') {
            $oldStatus = (string)(
                $application['status'] ?? ''
            );

            $apiResponse = $apiService->submitApplication(
                $application
            );

            if (
                !is_array($apiResponse)
                || empty($apiResponse['success'])
            ) {
                throw new RuntimeException(
                    'Die API-Übermittlung war nicht erfolgreich.'
                );
            }

            $apiReference = trim(
                (string)(
                    $apiResponse['reference'] ?? ''
                )
            );

            $apiStatus = trim(
                (string)(
                    $apiResponse['status'] ?? 'submitted'
                )
            );

            if ($apiReference === '') {
                throw new RuntimeException(
                    'Die API lieferte keine Referenznummer.'
                );
            }

            $apiResponseJson = json_encode(
                $apiResponse,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            );

            $applicationUpdate = $pdo->prepare(
                "UPDATE applications
                 SET
                    status = 'api_uebermittelt',
                    api_provider = :api_provider,
                    api_reference = :api_reference,
                    api_status = :api_status,
                    api_last_response = :api_last_response,
                    api_submitted_at = NOW(),
                    api_last_checked_at = NOW(),
                    api_error = NULL
                 WHERE id = :application_id"
            );

            $applicationUpdate->execute([
                'api_provider' => $apiMode,
                'api_reference' => $apiReference,
                'api_status' => $apiStatus,
                'api_last_response' => $apiResponseJson,
                'application_id' => $applicationId,
            ]);

            $jobUpdate = $pdo->prepare(
                "UPDATE application_jobs
                 SET
                    status = 'erfolgreich',
                    processed_at = NOW(),
                    error_message = NULL,
                    locked_at = NULL,
                    locked_by = NULL
                 WHERE id = :job_id"
            );

            $jobUpdate->execute([
                'job_id' => $jobId,
            ]);

            $addHistory(
                $pdo,
                $applicationId,
                $oldStatus,
                'api_uebermittelt',
                'Vorgang automatisch an die API übermittelt. '
                . 'Referenz: '
                . $apiReference
                . '.'
            );

            $createEmailJob(
                $pdo,
                $applicationId,
                'api_submitted'
            );

            /*
             * Nächsten Statusjob nur einmal anlegen
             */
            $statusJobCheck = $pdo->prepare(
                "SELECT id
                 FROM application_jobs
                 WHERE application_id = :application_id
                   AND job_type = 'api_status_check'
                   AND status IN ('offen', 'in_bearbeitung')
                 LIMIT 1"
            );

            $statusJobCheck->execute([
                'application_id' => $applicationId,
            ]);

            if ($statusJobCheck->fetchColumn() === false) {
                $statusJobInsert = $pdo->prepare(
                    "INSERT INTO application_jobs
                    (
                        application_id,
                        job_type,
                        status,
                        attempts,
                        max_attempts,
                        available_at
                    )
                    VALUES
                    (
                        :application_id,
                        'api_status_check',
                        'offen',
                        0,
                        50,
                        DATE_ADD(NOW(), INTERVAL 60 SECOND)
                    )"
                );

                $statusJobInsert->execute([
                    'application_id' => $applicationId,
                ]);
            }

            $writeOutput(
                'Submit-Job #'
                . $jobId
                . ' erfolgreich verarbeitet.'
            );

            continue;
        }


        /*
         |--------------------------------------------------------------------------
         | Job: api_status_check
         |--------------------------------------------------------------------------
         */
        if ($jobType === 'api_status_check') {
            $apiReference = trim(
                (string)(
                    $application['api_reference'] ?? ''
                )
            );

            if ($apiReference === '') {
                throw new RuntimeException(
                    'Keine API-Referenz vorhanden.'
                );
            }

            $apiResponse = $apiService->getApplicationStatus(
                $apiReference,
                $application['api_submitted_at'] ?? null
            );

            if (
                !is_array($apiResponse)
                || empty($apiResponse['success'])
            ) {
                throw new RuntimeException(
                    'Die API-Statusabfrage war nicht erfolgreich.'
                );
            }

            $apiStatus = strtolower(
                trim(
                    (string)(
                        $apiResponse['status'] ?? ''
                    )
                )
            );

            $apiResponseJson = json_encode(
                $apiResponse,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            );

            $isCompleted = in_array(
                $apiStatus,
                [
                    'completed',
                    'complete',
                    'abgeschlossen',
                    'success',
                    'successful',
                ],
                true
            );

            $isRejected = in_array(
                $apiStatus,
                [
                    'rejected',
                    'abgelehnt',
                    'failed',
                    'failure',
                ],
                true
            );

            if ($isCompleted) {
                $newStatus = 'abgeschlossen';
            } elseif ($isRejected) {
                $newStatus = 'abgelehnt';
            } else {
                $newStatus = 'in_bearbeitung';
            }

            $applicationUpdate = $pdo->prepare(
                'UPDATE applications
                 SET
                    status = :status,
                    api_status = :api_status,
                    api_last_response = :api_last_response,
                    api_last_checked_at = NOW(),
                    api_error = NULL
                 WHERE id = :application_id'
            );

            $applicationUpdate->execute([
                'status' => $newStatus,
                'api_status' => $apiStatus,
                'api_last_response' => $apiResponseJson,
                'application_id' => $applicationId,
            ]);

            if ($isCompleted || $isRejected) {
                $jobUpdate = $pdo->prepare(
                    "UPDATE application_jobs
                     SET
                        status = 'erfolgreich',
                        processed_at = NOW(),
                        error_message = NULL,
                        locked_at = NULL,
                        locked_by = NULL
                     WHERE id = :job_id"
                );

                $jobUpdate->execute([
                    'job_id' => $jobId,
                ]);

                $createEmailJob(
                    $pdo,
                    $applicationId,
                    $isCompleted
                        ? 'completed'
                        : 'error'
                );
            } else {
                $jobUpdate = $pdo->prepare(
                    "UPDATE application_jobs
                     SET
                        status = 'offen',
                        available_at = DATE_ADD(
                            NOW(),
                            INTERVAL 300 SECOND
                        ),
                        processed_at = NULL,
                        error_message = NULL,
                        locked_at = NULL,
                        locked_by = NULL
                     WHERE id = :job_id"
                );

                $jobUpdate->execute([
                    'job_id' => $jobId,
                ]);
            }

            $addHistory(
                $pdo,
                $applicationId,
                (string)($application['status'] ?? ''),
                $newStatus,
                'API-Status automatisch aktualisiert: '
                . $apiStatus
                . '.'
            );

            $writeOutput(
                'Statusjob #'
                . $jobId
                . ' verarbeitet: '
                . $apiStatus
            );

            continue;
        }
    } catch (Throwable $exception) {
        $errorMessage = mb_substr(
            $exception->getMessage(),
            0,
            1000
        );

        $writeOutput(
            'Job #'
            . $jobId
            . ' fehlgeschlagen: '
            . $errorMessage
        );

        /*
         * Retry oder endgültiger Fehler
         */
        try {
            if ($currentAttempt < $maxAttempts) {
                $delaySeconds = $retryDelays[
                    $currentAttempt
                ] ?? 3600;

                $retrySql = sprintf(
                    "UPDATE application_jobs
                     SET
                        status = 'offen',
                        available_at = DATE_ADD(
                            NOW(),
                            INTERVAL %d SECOND
                        ),
                        error_message = :error_message,
                        processed_at = NULL,
                        locked_at = NULL,
                        locked_by = NULL
                     WHERE id = :job_id",
                    $delaySeconds
                );

                $retryStatement = $pdo->prepare(
                    $retrySql
                );

                $retryStatement->execute([
                    'error_message' => $errorMessage,
                    'job_id' => $jobId,
                ]);

                $writeOutput(
                    'Neuer Versuch für Job #'
                    . $jobId
                    . ' in '
                    . $delaySeconds
                    . ' Sekunden.'
                );
            } else {
                $failureStatement = $pdo->prepare(
                    "UPDATE application_jobs
                     SET
                        status = 'fehlgeschlagen',
                        error_message = :error_message,
                        processed_at = NOW(),
                        locked_at = NULL,
                        locked_by = NULL
                     WHERE id = :job_id"
                );

                $failureStatement->execute([
                    'error_message' => $errorMessage,
                    'job_id' => $jobId,
                ]);

                $applicationError = $pdo->prepare(
                    "UPDATE applications
                     SET
                        status = 'fehler',
                        api_error = :api_error
                     WHERE id = :application_id"
                );

                $applicationError->execute([
                    'api_error' => $errorMessage,
                    'application_id' => $applicationId,
                ]);

                $createEmailJob(
                    $pdo,
                    $applicationId,
                    'error'
                );

                $addHistory(
                    $pdo,
                    $applicationId,
                    (string)(
                        $job['application_status'] ?? ''
                    ),
                    'fehler',
                    'Automatische Verarbeitung nach '
                    . $currentAttempt
                    . ' Versuchen endgültig fehlgeschlagen: '
                    . $errorMessage
                );

                $writeOutput(
                    'Job #'
                    . $jobId
                    . ' endgültig fehlgeschlagen.'
                );
            }
        } catch (Throwable $loggingException) {
            $writeOutput(
                'Fehler beim Protokollieren von Job #'
                . $jobId
                . ': '
                . $loggingException->getMessage()
            );
        }
    }
}

$writeOutput('Worker beendet.');