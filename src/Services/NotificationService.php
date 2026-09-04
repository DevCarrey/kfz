<?php
declare(strict_types=1);

final class NotificationService
{
    public function __construct(
        private readonly string $projectDir,
        private readonly array $config = []
    ) {
    }

    public function sendApplicationNotification(
        array $application,
        string $notificationType
    ): void {
        $email = trim(
            (string)($application['email'] ?? '')
        );

        if (
            $email === ''
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            throw new RuntimeException(
                'Keine gültige Empfänger-E-Mail vorhanden.'
            );
        }

        $referenceNumber = (string)(
            $application['reference_number'] ?? ''
        );

        $licensePlate = (string)(
            $application['license_plate'] ?? ''
        );

        $status = (string)(
            $application['status'] ?? ''
        );

        $content = $this->buildMessage(
            $notificationType,
            $referenceNumber,
            $licensePlate,
            $status
        );

        $mode = strtolower(
            (string)($this->config['mode'] ?? 'mock')
        );

        if ($mode === 'mock') {
            $this->writeMockMail(
                $email,
                $content['subject'],
                $content['body']
            );

            return;
        }

        throw new RuntimeException(
            'SMTP-Versand ist noch nicht eingerichtet.'
        );
    }

    private function buildMessage(
        string $notificationType,
        string $referenceNumber,
        string $licensePlate,
        string $status
    ): array {
        $messages = [
            'payment_confirmed' => [
                'subject' =>
                    'Zahlung bestätigt – ' . $referenceNumber,
                'body' =>
                    "Ihre Zahlung wurde bestätigt.\n\n"
                    . "Vorgangsnummer: "
                    . $referenceNumber
                    . "\n"
                    . "Kennzeichen: "
                    . $licensePlate
                    . "\n\n"
                    . "Der Vorgang wird automatisch weiterverarbeitet.",
            ],

            'api_submitted' => [
                'subject' =>
                    'Vorgang übermittelt – ' . $referenceNumber,
                'body' =>
                    "Ihr Vorgang wurde automatisch an die "
                    . "Verarbeitungsschnittstelle übermittelt.\n\n"
                    . "Vorgangsnummer: "
                    . $referenceNumber
                    . "\n"
                    . "Kennzeichen: "
                    . $licensePlate,
            ],

            'completed' => [
                'subject' =>
                    'Vorgang abgeschlossen – ' . $referenceNumber,
                'body' =>
                    "Ihr Fahrzeugvorgang wurde abgeschlossen.\n\n"
                    . "Vorgangsnummer: "
                    . $referenceNumber
                    . "\n"
                    . "Kennzeichen: "
                    . $licensePlate,
            ],

            'error' => [
                'subject' =>
                    'Rückfrage zu Ihrem Vorgang – '
                    . $referenceNumber,
                'body' =>
                    "Bei der automatischen Verarbeitung ist ein "
                    . "Problem aufgetreten.\n\n"
                    . "Vorgangsnummer: "
                    . $referenceNumber
                    . "\n"
                    . "Bitte wenden Sie sich an den Kundenservice.",
            ],
        ];

        return $messages[$notificationType]
            ?? [
                'subject' =>
                    'Statusupdate – ' . $referenceNumber,
                'body' =>
                    "Der Status Ihres Vorgangs wurde aktualisiert.\n\n"
                    . "Vorgangsnummer: "
                    . $referenceNumber
                    . "\n"
                    . "Status: "
                    . $status,
            ];
    }

    private function writeMockMail(
        string $recipient,
        string $subject,
        string $body
    ): void {
        $logDirectory = $this->projectDir
            . '/storage/logs';

        if (!is_dir($logDirectory)) {
            mkdir(
                $logDirectory,
                0775,
                true
            );
        }

        $message = PHP_EOL
            . str_repeat('=', 70)
            . PHP_EOL
            . 'MOCK E-MAIL'
            . PHP_EOL
            . 'Datum: '
            . date('Y-m-d H:i:s')
            . PHP_EOL
            . 'An: '
            . $recipient
            . PHP_EOL
            . 'Betreff: '
            . $subject
            . PHP_EOL
            . PHP_EOL
            . $body
            . PHP_EOL
            . str_repeat('=', 70)
            . PHP_EOL;

        file_put_contents(
            $logDirectory . '/emails.log',
            $message,
            FILE_APPEND | LOCK_EX
        );
    }
}