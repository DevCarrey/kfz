<?php
declare(strict_types=1);

/**
 * Kfz Digital – i-Kfz-API-Service
 *
 * Aktuell Mockbetrieb.
 * Später wird hier die echte API angebunden.
 */

final class IkfzApiService
{
    public function __construct(
        private readonly string $mode = 'mock'
    ) {
    }

    /**
     * Vorgang an die API übermitteln.
     */
    public function submitApplication(
        array $application
    ): array {
        $applicationId = (int)(
            $application['id'] ?? 0
        );

        if ($applicationId <= 0) {
            throw new RuntimeException(
                'Ungültige Vorgangs-ID.'
            );
        }

        if ($this->mode !== 'mock') {
            throw new RuntimeException(
                'Die echte API ist noch nicht konfiguriert.'
            );
        }

        $apiReference = 'MOCK-'
            . date('Ymd')
            . '-'
            . str_pad(
                (string)$applicationId,
                6,
                '0',
                STR_PAD_LEFT
            );

        return [
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
    }

    /**
     * Status bei der API abfragen.
     */
    public function getApplicationStatus(
        string $apiReference,
        ?string $submittedAt = null
    ): array {
        if ($apiReference === '') {
            throw new RuntimeException(
                'Keine externe API-Referenz vorhanden.'
            );
        }

        if ($this->mode !== 'mock') {
            throw new RuntimeException(
                'Die echte API ist noch nicht konfiguriert.'
            );
        }

        $status = 'in_progress';

        if ($submittedAt !== null) {
            $submittedTimestamp = strtotime($submittedAt);

            if (
                $submittedTimestamp !== false
                && time() - $submittedTimestamp >= 120
            ) {
                $status = 'completed';
            }
        }

        return [
            'success' => true,
            'provider' => 'mock',
            'reference' => $apiReference,
            'status' => $status,
            'message' => $status === 'completed'
                ? 'Vorgang im Mockbetrieb abgeschlossen.'
                : 'Vorgang wird noch bearbeitet.',
            'checked_at' => date(
                'Y-m-d H:i:s'
            ),
        ];
    }
}