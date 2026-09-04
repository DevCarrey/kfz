<?php
declare(strict_types=1);

/**
 * Kfz Digital – Stripe-Zahlungsservice
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2)
    . '/vendor/autoload.php';

final class StripePaymentService
{
    private \Stripe\StripeClient $stripe;

    public function __construct(
        private readonly array $config
    ) {
        $secretKey = trim(
            (string)($config['secret_key'] ?? '')
        );

        if ($secretKey === '') {
            throw new RuntimeException(
                'Stripe-Secret-Key ist nicht konfiguriert.'
            );
        }

        $this->stripe = new \Stripe\StripeClient(
            $secretKey
        );
    }

    public function isEnabled(): bool
    {
        return !empty($this->config['enabled'])
            && trim(
                (string)($this->config['secret_key'] ?? '')
            ) !== '';
    }

    public function createCheckoutSession(
        array $application,
        string $successUrl,
        string $cancelUrl
    ): array {
        $applicationId = (int)(
            $application['id'] ?? 0
        );

        $referenceNumber = trim(
            (string)(
                $application['reference_number'] ?? ''
            )
        );

        $email = trim(
            (string)(
                $application['email'] ?? ''
            )
        );

        if ($applicationId <= 0) {
            throw new InvalidArgumentException(
                'Ungültige Vorgangs-ID.'
            );
        }

        if ($referenceNumber === '') {
            throw new InvalidArgumentException(
                'Keine Vorgangsnummer vorhanden.'
            );
        }

        $amountCents = (int)(
            $this->config['amount_cents'] ?? 4990
        );

        $currency = strtolower(
            (string)(
                $this->config['currency'] ?? 'eur'
            )
        );

        $sessionData = [
            'mode' => 'payment',

            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,

            'client_reference_id' => $referenceNumber,

            'metadata' => [
                'application_id' => (string)$applicationId,
                'reference_number' => $referenceNumber,
            ],

            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $amountCents,
                        'product_data' => [
                            'name' =>
                                'Digitale Fahrzeugabmeldung',
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
        ];

        if (
            $email !== ''
            && filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $sessionData['customer_email'] = $email;
        }

        $session = $this->stripe
            ->checkout
            ->sessions
            ->create($sessionData);

        return [
            'id' => (string)$session->id,
            'url' => (string)($session->url ?? ''),
            'payment_intent_id' => is_string(
                $session->payment_intent
            )
                ? $session->payment_intent
                : null,
        ];
    }

    public function constructWebhookEvent(
        string $payload,
        string $signature,
        string $webhookSecret
    ): \Stripe\Event {
        return \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            $webhookSecret
        );
    }
}