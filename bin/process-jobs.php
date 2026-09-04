<?php
declare(strict_types=1);

/**
 * Kfz Digital – automatischer Job-Worker
 *
 * Aktuell:
 * - Mock-API
 * - Keine echte externe API
 * - Verarbeitet offene application_jobs
 *
 * Start:
 * C:\xampp\php\php.exe bin\process-jobs.php
 */

$projectDir = dirname(__DIR__);

require_once $projectDir
    . '/src/Support/view_helpers.php';


/*
|--------------------------------------------------------------------------
| Datenbank laden
|--------------------------------------------------------------------------
*/

try {
    $pdo = require $projectDir
        . '/src/Config/database.php';
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        "Datenbankverbindung fehlgeschlagen.\n"
    );

    exit(1);
}


/*
|--------------------------------------------------------------------------
| Hilfsfunktionen
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
| Offene Jobs laden
|--------------------------------------------------------------------------
*/

$jobStatement = $pdo->prepare(
    "SELECT
        j.id,
        j.application_id,
        j.job_type,
        j.status,
        j.attempts,
        a.reference_number,
        a.status AS application_status
     FROM application_jobs j
     INNER JOIN applications a
        ON a.id = j.application_id
     WHERE j.status = 'offen'
       AND j.available_at <= NOW()
       AND j.attempts < 5
     ORDER BY j.created_at ASC, j.id ASC
     LIMIT 25"
);

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

    if (
        $jobId <= 0
        || $applicationId <= 0
    ) {
        $writeOutput(
            'Ungültiger Job übersprungen.'
        );

        continue;
    }

    $writeOutput(
        'Job #' . $jobId
        . ' für '
        . $referenceNumber
        . ' wird verarbeitet.'
    );

    try {
        $pdo->beginTransaction();


        /*
         * Job auf in_bearbeitung setzen
         */
        $lockStatement = $pdo->prepare(
            "UPDATE application_jobs
             SET
                status = 'in_bearbeitung',
                attempts = attempts + 1
             WHERE id = :job_id
               AND status = 'offen'"
        );

        $lockStatement->execute([
            'job_id' => $jobId,
        ]);

        if ($lockStatement->rowCount() !== 1) {
            $pdo->rollBack();

            $writeOutput(
                'Job #' . $jobId
                . ' wurde bereits verarbeitet.'
            );

            continue;
        }


        /*
         * Nur api_submit im Mockbetrieb verarbeiten
         */
        if ($jobType !== 'api_submit') {
            throw new RuntimeException(
                'Unbekannter Jobtyp: ' . $jobType
            );
        }


        /*
         * Mock-API-Referenz erzeugen
         */
        $apiReference = 'MOCK-'
            . date('Ymd')
            . '-'
            . str_pad(
                (string)$applicationId,
                6,
                '0',
                STR_PAD_LEFT
            );

        $apiResponse = [
            'success' => true,
            'provider' => 'mock',
            'reference' => $apiReference,
            'status' => 'submitted',
            'message' =>
                'Vorgang im Mockbetrieb übermittelt.',
            'processed_at' => date(
                'Y-m-d H:i:s'
            ),
        ];

        $apiResponseJson = json_encode(
            $apiResponse,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );

        if (!is_string($apiResponseJson)) {
            throw new RuntimeException(
                'API-Antwort konnte nicht erstellt werden.'
            );
        }


        /*
         * Anwendung aktualisieren
         */
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
            'api_provider' => 'mock',
            'api_reference' => $apiReference,
            'api_status' => 'submitted',
            'api_last_response' => $apiResponseJson,
            'application_id' => $applicationId,
        ]);


        /*
         * Job erfolgreich markieren
         */
        $jobUpdate = $pdo->prepare(
            "UPDATE application_jobs
             SET
                status = 'erfolgreich',
                processed_at = NOW(),
                error_message = NULL
             WHERE id = :job_id"
        );

        $jobUpdate->execute([
            'job_id' => $jobId,
        ]);


        /*
         * Statushistorie schreiben
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
                :old_status,
                :new_status,
                :comment
            )'
        );

        $historyStatement->execute([
            'application_id' => $applicationId,
            'old_status' => 'api_warteschlange',
            'new_status' => 'api_uebermittelt',
            'comment' =>
                'Vorgang automatisch an die Mock-API übermittelt. Referenz: '
                . $apiReference,
        ]);

        $pdo->commit();

        $writeOutput(
            'Job #' . $jobId
            . ' erfolgreich abgeschlossen.'
        );
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        try {
            $errorMessage = mb_substr(
                $exception->getMessage(),
                0,
                1000
            );

            $errorStatement = $pdo->prepare(
                "UPDATE application_jobs
                 SET
                    status = 'fehlgeschlagen',
                    error_message = :error_message,
                    processed_at = NOW()
                 WHERE id = :job_id"
            );

            $errorStatement->execute([
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

            $historyError = $pdo->prepare(
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

            $historyError->execute([
                'application_id' => $applicationId,
                'old_status' => 'api_warteschlange',
                'new_status' => 'fehler',
                'comment' =>
                    'Automatische Verarbeitung fehlgeschlagen: '
                    . $errorMessage,
            ]);
        } catch (Throwable $loggingException) {
            // Fehler nicht weiter ausgeben.
        }

        $writeOutput(
            'Job #' . $jobId
            . ' fehlgeschlagen.'
        );
    }
}

$writeOutput('Worker beendet.');