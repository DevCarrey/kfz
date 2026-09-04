<?php
declare(strict_types=1);

return [
    /*
     * Aktuell ausschließlich Mock-Zahlung.
     * Keine echte Abbuchung.
     */
    'mode' => 'mock',

    /*
     * 49,90 EUR
     */
    'amount_cents' => 4990,
    'currency' => 'EUR',

    /*
     * Für späteren Stripe-Betrieb.
     * Vorläufig leer lassen.
     */
    'stripe_secret_key' => '',
    'stripe_publishable_key' => '',
    'stripe_webhook_secret' => '',
];