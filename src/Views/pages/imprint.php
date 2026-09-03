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
 * Sicheres Escaping für HTML-Ausgaben.
 */
$escape = static function (string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
};
?>

<!-- Seitenkopf -->
<section class="py-5 bg-light border-bottom">
    <div class="container py-lg-4">
        <div class="row">
            <div class="col-lg-9">

                <p class="text-warning text-uppercase fw-bold mb-2">
                    Rechtliche Informationen
                </p>

                <h1 class="display-5 fw-bold mb-3">
                    Impressum
                </h1>

                <p class="lead text-secondary mb-0">
                    Angaben gemäß § 5 TMG
                </p>

            </div>
        </div>
    </div>
</section>


<!-- Inhalt -->
<section class="py-5">
    <div class="container">
        <div class="row">

            <main class="col-lg-9">

                <!-- Unternehmensangaben -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        STADT HOTEL Iserlohn
                    </h2>

                    <address class="text-secondary mb-4">
                        Theodor-Heuss-Ring 54<br>
                        58636 Iserlohn
                    </address>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <h3 class="h5 fw-bold">
                                Eigentümer
                            </h3>

                            <p class="text-secondary mb-0">
                                Ilir Mulaku
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h3 class="h5 fw-bold">
                                Kontakt
                            </h3>

                            <p class="text-secondary mb-0">
                                Telefon:
                                <a
                                    href="tel:+49293233116"
                                    class="text-decoration-none"
                                >
                                     0 23 71 – 15 97 90
                                </a>
                                <br>

                                Telefax: 0 23 71 – 15 97 9
                                <br>

                                E-Mail:
                                <a
                                    href="mailto:info@stadthotel-iserlohn.de"
                                    class="text-decoration-none"
                                >
                                    info@stadthotel-iserlohn.de
                                </a>
                            </p>
                        </div>

                    </div>

                </section>


                <hr class="my-5">


                <!-- Register -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-4">
                        Registereintrag
                    </h2>

                    <div class="row g-3 text-secondary">

                        <div class="col-md-4">
                            <strong class="d-block text-dark">
                                Registergericht
                            </strong>
                            Amtsgericht Iserlohn
                        </div>

                        <div class="col-md-4">
                            <strong class="d-block text-dark">
                                Registernummer
                            </strong>
                            1234
                        </div>

                        <div class="col-md-4">
                            <strong class="d-block text-dark">
                                Umsatzsteuer-ID
                            </strong>
                            1234
                        </div>

                    </div>

                </section>


                <!-- Umsatzsteuer -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Umsatzsteuer-ID
                    </h2>

                    <p class="text-secondary">
                        Umsatzsteuer-Identifikationsnummer gemäß
                        § 27a Umsatzsteuergesetz:
                        <strong class="text-dark">
                            DE205487910
                        </strong>
                    </p>

                </section>


                <!-- Verantwortlich -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Verantwortlich für den Inhalt
                    </h2>

                    <p class="text-secondary mb-0">
                        Verantwortlich für den Inhalt nach
                        § 55 Abs. 2 RStV:
                    </p>

                    <p class="text-secondary">
                        <strong class="text-dark">
                            Platzhalter
                        </strong>
                        <br>
                        Platzhalter
                        <br>
                        Platzhalter
                    </p>

                </section>


                <hr class="my-5">


                <!-- Bildnachweise -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Quellenangaben für Bilder und Grafiken
                    </h2>

                    <p class="text-secondary mb-0">
                        Photocase: Hr. Oliver Pohl<br>
                        dechenhöhle.de
                    </p>

                </section>


                <!-- Gestaltung -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Gestaltung und technische Umsetzung
                    </h2>

                    <p class="text-secondary mb-0">
                        <strong class="text-dark">
                            robbencarrey.com - IT Service
                        </strong>
                        <br>

                        <a
                            href="https://www.robbencarrey.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            robbencarrey.com
                        </a>
                    </p>

                </section>


                <hr class="my-5">


                <!-- Haftungsausschluss -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-5">
                        Haftungsausschluss
                    </h2>


                    <article class="mb-5">

                        <h3 class="h4 fw-bold mb-3">
                            Haftung für Inhalte
                        </h3>

                        <div class="text-secondary">

                            <p>
                                Die Inhalte unserer Seiten wurden mit größter
                                Sorgfalt erstellt. Für die Richtigkeit,
                                Vollständigkeit und Aktualität der Inhalte
                                können wir jedoch keine Gewähr übernehmen.
                            </p>

                            <p>
                                Als Diensteanbieter sind wir gemäß
                                § 7 Abs. 1 TMG für eigene Inhalte auf diesen
                                Seiten nach den allgemeinen Gesetzen
                                verantwortlich.
                            </p>

                            <p>
                                Nach §§ 8 bis 10 TMG sind wir als
                                Diensteanbieter jedoch nicht verpflichtet,
                                übermittelte oder gespeicherte fremde
                                Informationen zu überwachen oder nach
                                Umständen zu forschen, die auf eine
                                rechtswidrige Tätigkeit hinweisen.
                            </p>

                            <p>
                                Verpflichtungen zur Entfernung oder Sperrung
                                der Nutzung von Informationen nach den
                                allgemeinen Gesetzen bleiben hiervon
                                unberührt.
                            </p>

                            <p class="mb-0">
                                Eine diesbezügliche Haftung ist jedoch erst ab
                                dem Zeitpunkt der Kenntnis einer konkreten
                                Rechtsverletzung möglich. Bei Bekanntwerden
                                von entsprechenden Rechtsverletzungen werden
                                wir diese Inhalte umgehend entfernen.
                            </p>

                        </div>

                    </article>


                    <article class="mb-5">

                        <h3 class="h4 fw-bold mb-3">
                            Haftung für Links
                        </h3>

                        <div class="text-secondary">

                            <p>
                                Unser Angebot enthält Links zu externen
                                Webseiten Dritter, auf deren Inhalte wir
                                keinen Einfluss haben. Deshalb können wir
                                für diese fremden Inhalte auch keine Gewähr
                                übernehmen.
                            </p>

                            <p>
                                Für die Inhalte der verlinkten Seiten ist
                                stets der jeweilige Anbieter oder Betreiber
                                der Seiten verantwortlich.
                            </p>

                            <p>
                                Die verlinkten Seiten wurden zum Zeitpunkt
                                der Verlinkung auf mögliche Rechtsverstöße
                                überprüft. Rechtswidrige Inhalte waren zum
                                Zeitpunkt der Verlinkung nicht erkennbar.
                            </p>

                            <p>
                                Eine permanente inhaltliche Kontrolle der
                                verlinkten Seiten ist jedoch ohne konkrete
                                Anhaltspunkte einer Rechtsverletzung nicht
                                zumutbar.
                            </p>

                            <p class="mb-0">
                                Bei Bekanntwerden von Rechtsverletzungen
                                werden wir derartige Links umgehend
                                entfernen.
                            </p>

                        </div>

                    </article>


                    <article class="mb-5">

                        <h3 class="h4 fw-bold mb-3">
                            Urheberrecht
                        </h3>

                        <div class="text-secondary">

                            <p>
                                Die durch die Seitenbetreiber erstellten
                                Inhalte und Werke auf diesen Seiten
                                unterliegen dem deutschen Urheberrecht.
                            </p>

                            <p>
                                Die Vervielfältigung, Bearbeitung,
                                Verbreitung und jede Art der Verwertung
                                außerhalb der Grenzen des Urheberrechtes
                                bedürfen der schriftlichen Zustimmung des
                                jeweiligen Autors bzw. Erstellers.
                            </p>

                            <p>
                                Downloads und Kopien dieser Seite sind nur
                                für den privaten, nicht kommerziellen
                                Gebrauch gestattet.
                            </p>

                            <p>
                                Soweit die Inhalte auf dieser Seite nicht
                                vom Betreiber erstellt wurden, werden die
                                Urheberrechte Dritter beachtet.
                                Insbesondere werden Inhalte Dritter als
                                solche gekennzeichnet.
                            </p>

                            <p>
                                Sollten Sie trotzdem auf eine
                                Urheberrechtsverletzung aufmerksam werden,
                                bitten wir um einen entsprechenden Hinweis.
                            </p>

                            <p class="mb-0">
                                Bei Bekanntwerden von Rechtsverletzungen
                                werden wir derartige Inhalte umgehend
                                entfernen.
                            </p>

                        </div>

                    </article>

                </section>


                <hr class="my-5">


                <!-- Google Analytics -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Google Analytics
                    </h2>

                    <div class="text-secondary">

                        <p>
                            Diese Website benutzt Google Analytics, einen
                            Webanalysedienst der Google Ireland Limited
                            („Google“).
                        </p>

                        <p>
                            Google Analytics verwendet Technologien, die eine
                            Analyse der Benutzung der Website ermöglichen.
                            Dabei können Informationen über die Nutzung
                            dieser Website verarbeitet werden.
                        </p>

                        <p>
                            Weitere Informationen zur Datenverarbeitung durch
                            Google sowie zu Ihren Rechten und
                            Einstellungsmöglichkeiten finden Sie in unserer
                            <a
                                href="<?= $escape($url('/datenschutz')) ?>"
                                class="text-decoration-none"
                            >
                                Datenschutzerklärung
                            </a>.
                        </p>

                    </div>

                </section>


                <!-- Generator -->
                <p class="small text-secondary mt-5">
                    Impressum erstellt auf Grundlage des
                    Impressum-Generators der Kanzlei Hasselbach, Frankfurt.
                </p>

            </main>

        </div>
    </div>
</section>
