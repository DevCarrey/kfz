<?php
declare(strict_types=1);

$appPrefix = rtrim((string)($GLOBALS['appPrefix'] ?? ''), '/');
$url = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/' . ltrim($path, '/');
};
?>

<section class="kfz-section" aria-labelledby="about-title">
    <div class="container">
        <span class="kfz-section-kicker">Kfz Digital</span>
        <h1 id="about-title" class="kfz-section-title">
            Fahrzeugvorgänge digital vorbereiten.
        </h1>
        <p class="kfz-section-text">
            Kfz Digital bündelt Fahrzeugdaten und Vorgänge an einem Ort.
            Anträge werden zunächst als Entwurf gespeichert und können so
            vollständig geprüft werden, bevor eine spätere Übermittlung über
            eine offiziell dokumentierte i-Kfz- oder GKS-Schnittstelle erfolgt.
        </p>
        <p class="kfz-section-text">
            Bis ein offizieller Testzugang und die technische Dokumentation
            vorliegen, werden keine echten Zulassungsvorgänge an Behörden
            übermittelt.
        </p>
        <a class="kfz-button kfz-button-primary" href="<?= htmlspecialchars($url('/vorgang-starten/'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
            Vorgang starten
        </a>
    </div>
</section>
