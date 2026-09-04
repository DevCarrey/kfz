<?php
declare(strict_types=1);

return [
    /*
     * mock = schreibt E-Mails nur in storage/logs/emails.log
     * smtp = später echter Versand
     */
    'mode' => 'mock',

    'from_email' => 'noreply@kfz-digital.de',
    'from_name' => 'Kfz Digital',

    'smtp_host' => '',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'smtp_encryption' => 'tls',
];