<?php
declare(strict_types=1);

$appPrefix = rtrim((string)($GLOBALS['appPrefix'] ?? ''), '/');

/**
 * Erstellt interne URLs.
 */
$url = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/' . ltrim($path, '/');
};

/**
 * Sicheres Escaping.
 */
$escape = static function (string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};
?>

<section
    class="legal-page py-5"
    aria-labelledby="impressum-title"
>
    <div class="container">

        <div class="legal-page-header text-center mb-5">

            <span class="hotel-section-kicker">
                Rechtliche Informationen
            </span>

            <h1 id="impressum-title">
                Impressum
            </h1>

            <p class="text-secondary">
                Angaben gemäß § 5 Digitale-Dienste-Gesetz (DDG)
            </p>

        </div>

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="legal-card">

                    <h2>
                        Anbieter
                    </h2>

                    <p>
                        <strong>Stadt Hotel Iserlohn</strong><br>
                        Inhaber: Ilir Mulaku<br>
                        Musterstraße 1<br>
                        58636 Iserlohn<br>
                        Deutschland
                    </p>

                    <h2>
                        Kontakt
                    </h2>

                    <p>
                        Telefon:
                        <a href="tel:+49293233116">
                            02932 33116
                        </a>
                        <br>

                        E-Mail:
                        <a href="mailto:info@hotel.de">
                            info@hotel.de
                        </a>
                    </p>

                    <h2>
                        Vertretungsberechtigter
                    </h2>

                    <p>
                        Ilir Mulaku
                    </p>

                    <h2>
                        Registereintrag
                    </h2>

                    <p>
                        Registergericht: Bitte ergänzen<br>
                        Registernummer: Bitte ergänzen
                    </p>

                    <h2>
                        Umsatzsteuer-Identifikationsnummer
                    </h2>

                    <p>
                        Umsatzsteuer-ID gemäß § 27a Umsatzsteuergesetz:<br>
                        Bitte ergänzen, sofern vorhanden
                    </p>

                    <h2>
                        Verantwortlich für den Inhalt
                    </h2>

                    <p>
                        Verantwortlich für den Inhalt nach den allgemeinen
                        gesetzlichen Vorschriften ist:
                    </p>

                    <p>
                        Ilir Mulaku<br>
                        Stadt Hotel Iserlohn<br>
                        Musterstraße 1<br>
                        58636 Iserlohn
                    </p>

                    <h2>
                        Haftung für Inhalte
                    </h2>

                    <p>
                        Als Diensteanbieter sind wir gemäß den allgemeinen
                        gesetzlichen Vorschriften für eigene Inhalte auf
                        dieser Website verantwortlich. Wir sind jedoch nicht
                        verpflichtet, übermittelte oder gespeicherte fremde
                        Informationen zu überwachen oder nach Umständen zu
                        forschen, die auf eine rechtswidrige Tätigkeit hinweisen.
                    </p>

                    <p>
                        Verpflichtungen zur Entfernung oder Sperrung der Nutzung
                        von Informationen nach den allgemeinen gesetzlichen
                        Vorschriften bleiben hiervon unberührt.
                    </p>

                    <h2>
                        Haftung für Links
                    </h2>

                    <p>
                        Diese Website kann Links zu externen Websites Dritter
                        enthalten. Auf deren Inhalte haben wir keinen Einfluss.
                        Für die Inhalte der verlinkten Seiten ist stets der
                        jeweilige Anbieter oder Betreiber verantwortlich.
                    </p>

                    <p>
                        Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung
                        auf mögliche Rechtsverstöße überprüft. Eine permanente
                        inhaltliche Kontrolle der verlinkten Seiten ist ohne
                        konkrete Anhaltspunkte einer Rechtsverletzung jedoch
                        nicht zumutbar.
                    </p>

                    <h2>
                        Urheberrecht
                    </h2>

                    <p>
                        Die durch den Seitenbetreiber erstellten Inhalte und
                        Werke auf dieser Website unterliegen dem deutschen
                        Urheberrecht. Die Vervielfältigung, Bearbeitung,
                        Verbreitung und jede Art der Verwertung außerhalb der
                        Grenzen des Urheberrechts bedürfen der vorherigen
                        schriftlichen Zustimmung des jeweiligen Urhebers.
                    </p>

                    <p>
                        Soweit die Inhalte auf dieser Seite nicht vom Betreiber
                        erstellt wurden, werden die Urheberrechte Dritter
                        beachtet. Inhalte Dritter werden entsprechend
                        gekennzeichnet, soweit dies möglich ist.
                    </p>

                    <div class="legal-page-actions mt-5">

                        <a
                            class="btn btn-hotel-dark"
                            href="<?= $escape($url('/')) ?>"
                        >
                            Zur Startseite
                        </a>

                        <a
                            class="btn btn-hotel-outline-dark"
                            href="<?= $escape($url('/kontakt/')) ?>"
                        >
                            Kontakt aufnehmen
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>