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
    aria-labelledby="privacy-title"
>
    <div class="container">

        <div class="legal-page-header text-center mb-5">

            <span class="hotel-section-kicker">
                Rechtliche Informationen
            </span>

            <h1 id="privacy-title">
                Datenschutzerklärung
            </h1>

            <p class="text-secondary">
                Informationen zum Datenschutz auf dieser Website
            </p>

        </div>

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="legal-card">

                    <p class="small text-secondary">
                        Stand: <?= date('d.m.Y') ?>
                    </p>

                    <h2>
                        1. Verantwortlicher
                    </h2>

                    <p>
                        Verantwortlich für die Datenverarbeitung auf dieser
                        Website ist:
                    </p>

                    <p>
                        <strong>[Name des Hotels / Unternehmens]</strong><br>
                        [Inhaber oder vertretungsberechtigte Person]<br>
                        [Straße und Hausnummer]<br>
                        [Postleitzahl und Ort]<br>
                        Deutschland
                    </p>

                    <p>
                        Telefon:
                        <a href="tel:[TELEFONNUMMER]">
                            [TELEFONNUMMER]
                        </a>
                        <br>

                        E-Mail:
                        <a href="mailto:[E-MAIL-ADRESSE]">
                            [E-MAIL-ADRESSE]
                        </a>
                    </p>

                    <h2>
                        2. Allgemeine Hinweise
                    </h2>

                    <p>
                        Der Schutz Ihrer persönlichen Daten ist uns wichtig.
                        Wir behandeln Ihre personenbezogenen Daten vertraulich
                        und entsprechend den gesetzlichen Datenschutzvorschriften
                        sowie dieser Datenschutzerklärung.
                    </p>

                    <p>
                        Personenbezogene Daten sind alle Informationen, mit
                        denen Sie persönlich identifiziert werden können.
                    </p>

                    <h2>
                        3. Bereitstellung der Website und Server-Logfiles
                    </h2>

                    <p>
                        Beim Aufrufen dieser Website übermittelt Ihr Browser
                        automatisch Informationen an den Server. Diese
                        Informationen können vorübergehend in sogenannten
                        Server-Logfiles gespeichert werden.
                    </p>

                    <p>
                        Dabei können insbesondere folgende Daten verarbeitet
                        werden:
                    </p>

                    <ul>
                        <li>IP-Adresse des zugreifenden Geräts</li>
                        <li>Datum und Uhrzeit des Zugriffs</li>
                        <li>aufgerufene Seite oder Datei</li>
                        <li>Referrer-URL</li>
                        <li>Browsertyp und Browserversion</li>
                        <li>Betriebssystem</li>
                        <li>Statuscode der Anfrage</li>
                    </ul>

                    <p>
                        Die Verarbeitung erfolgt, um die Website technisch
                        bereitzustellen, die Sicherheit zu gewährleisten und
                        Fehler zu erkennen. Die Logfiles werden gelöscht,
                        sobald sie für diese Zwecke nicht mehr erforderlich sind,
                        sofern keine gesetzlichen Aufbewahrungspflichten
                        entgegenstehen.
                    </p>

                    <p>
                        Rechtsgrundlage ist Art. 6 Abs. 1 lit. f DSGVO. Unser
                        berechtigtes Interesse liegt in der sicheren und
                        stabilen Bereitstellung dieser Website.
                    </p>

                    <h2>
                        4. Kontaktaufnahme per E-Mail oder Telefon
                    </h2>

                    <p>
                        Wenn Sie uns per E-Mail oder Telefon kontaktieren,
                        verarbeiten wir die von Ihnen übermittelten
                        personenbezogenen Daten ausschließlich zur Bearbeitung
                        Ihrer Anfrage.
                    </p>

                    <p>
                        Je nach Inhalt Ihrer Anfrage können insbesondere
                        folgende Daten verarbeitet werden:
                    </p>

                    <ul>
                        <li>Name</li>
                        <li>E-Mail-Adresse</li>
                        <li>Telefonnummer</li>
                        <li>Inhalte Ihrer Nachricht</li>
                        <li>weitere freiwillig übermittelte Informationen</li>
                    </ul>

                    <p>
                        Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO, sofern
                        Ihre Anfrage mit einem Vertrag oder einer vorvertraglichen
                        Maßnahme zusammenhängt. In allen anderen Fällen erfolgt
                        die Verarbeitung auf Grundlage unseres berechtigten
                        Interesses an der Bearbeitung Ihrer Anfrage gemäß
                        Art. 6 Abs. 1 lit. f DSGVO.
                    </p>

                    <p>
                        Die Daten werden gelöscht, sobald Ihre Anfrage
                        abschließend bearbeitet wurde und keine gesetzlichen
                        Aufbewahrungspflichten bestehen.
                    </p>

                    <h2>
                        5. Kontaktformular
                    </h2>

                    <p>
                        Wenn Sie unser Kontaktformular nutzen, werden die von
                        Ihnen eingegebenen Daten zur Bearbeitung Ihrer Anfrage
                        verarbeitet.
                    </p>

                    <p>
                        Dazu können insbesondere Ihr Name, Ihre E-Mail-Adresse,
                        Ihre Telefonnummer, der Betreff und der Inhalt Ihrer
                        Nachricht gehören.
                    </p>

                    <p>
                        Die mit dem Kontaktformular übermittelten Daten werden
                        nicht ohne Ihre Einwilligung an Dritte weitergegeben.
                        Eine Ausnahme gilt, wenn dies zur Bearbeitung Ihrer
                        Anfrage erforderlich ist oder eine gesetzliche
                        Verpflichtung besteht.
                    </p>

                    <p>
                        Rechtsgrundlage ist Art. 6 Abs. 1 lit. b DSGVO oder,
                        sofern kein Vertragsverhältnis besteht, Art. 6 Abs. 1
                        lit. f DSGVO.
                    </p>

                    <h2>
                        6. Buchungsanfragen
                    </h2>

                    <p>
                        Wenn Sie über diese Website eine Buchungsanfrage
                        stellen, verarbeiten wir Ihre Angaben zur Bearbeitung
                        und Beantwortung Ihrer Anfrage.
                    </p>

                    <p>
                        Dabei können folgende Daten verarbeitet werden:
                    </p>

                    <ul>
                        <li>Vor- und Nachname</li>
                        <li>E-Mail-Adresse</li>
                        <li>Telefonnummer</li>
                        <li>An- und Abreisedatum</li>
                        <li>Anzahl der Personen</li>
                        <li>Zimmerwunsch</li>
                        <li>Nachrichten und besondere Wünsche</li>
                    </ul>

                    <p>
                        Die Verarbeitung erfolgt zur Durchführung
                        vorvertraglicher Maßnahmen oder zur Erfüllung eines
                        Vertrags gemäß Art. 6 Abs. 1 lit. b DSGVO.
                    </p>

                    <p>
                        Die Daten werden gelöscht, sobald sie für die
                        Bearbeitung der Anfrage nicht mehr erforderlich sind,
                        sofern keine gesetzlichen Aufbewahrungspflichten
                        entgegenstehen.
                    </p>

                    <h2>
                        7. Rechtsgrundlagen
                    </h2>

                    <p>
                        Soweit in dieser Datenschutzerklärung keine andere
                        Rechtsgrundlage genannt wird, erfolgt die Verarbeitung
                        personenbezogener Daten auf Grundlage von Art. 6 DSGVO.
                    </p>

                    <p>
                        Je nach Einzelfall kommen insbesondere folgende
                        Rechtsgrundlagen in Betracht:
                    </p>

                    <ul>
                        <li>
                            Art. 6 Abs. 1 lit. a DSGVO – Einwilligung
                        </li>
                        <li>
                            Art. 6 Abs. 1 lit. b DSGVO – Vertrag oder
                            vorvertragliche Maßnahmen
                        </li>
                        <li>
                            Art. 6 Abs. 1 lit. c DSGVO – rechtliche Verpflichtung
                        </li>
                        <li>
                            Art. 6 Abs. 1 lit. f DSGVO – berechtigte Interessen
                        </li>
                    </ul>

                    <h2>
                        8. Cookies und ähnliche Technologien
                    </h2>

                    <p>
                        Diese Website verwendet möglicherweise Cookies oder
                        ähnliche Technologien. Cookies sind kleine Textdateien,
                        die auf Ihrem Endgerät gespeichert werden können.
                    </p>

                    <p>
                        Technisch notwendige Cookies können erforderlich sein,
                        damit die Website ordnungsgemäß funktioniert. Für nicht
                        notwendige Cookies oder vergleichbare Technologien wird
                        grundsätzlich Ihre Einwilligung eingeholt, sofern dies
                        gesetzlich erforderlich ist.
                    </p>

                    <p>
                        Sie können Ihren Browser so konfigurieren, dass Cookies
                        nur im Einzelfall zugelassen, abgelehnt oder beim
                        Schließen des Browsers automatisch gelöscht werden.
                    </p>

                    <p>
                        Falls diese Website keine Cookies verwendet, kann
                        dieser Abschnitt entfernt werden.
                    </p>

                    <h2>
                        9. Bootstrap über jsDelivr
                    </h2>

                    <p>
                        Diese Website verwendet möglicherweise Bootstrap,
                        das über das Content Delivery Network jsDelivr
                        eingebunden wird.
                    </p>

                    <p>
                        Beim Laden der Bootstrap-Dateien wird eine Verbindung
                        zu den Servern des Anbieters hergestellt. Dabei kann
                        unter anderem Ihre IP-Adresse an den Anbieter
                        übermittelt werden.
                    </p>

                    <p>
                        Wenn Sie eine externe Einbindung vermeiden möchten,
                        können Sie Bootstrap lokal auf Ihrem eigenen Server
                        speichern und einbinden.
                    </p>

                    <p>
                        Falls Bootstrap ausschließlich lokal eingebunden wird,
                        kann dieser Abschnitt entfernt werden.
                    </p>

                    <h2>
                        10. Google Maps und externe Inhalte
                    </h2>

                    <p>
                        Wenn auf dieser Website Karten, Videos, Schriftarten
                        oder andere externe Inhalte eingebunden werden, können
                        beim Aufruf dieser Inhalte personenbezogene Daten,
                        insbesondere die IP-Adresse, an den jeweiligen Anbieter
                        übertragen werden.
                    </p>

                    <p>
                        Externe Inhalte sollten nur eingebunden werden, wenn
                        hierfür eine geeignete Rechtsgrundlage besteht. Falls
                        keine externen Inhalte verwendet werden, kann dieser
                        Abschnitt entfernt werden.
                    </p>

                    <h2>
                        11. Empfänger personenbezogener Daten
                    </h2>

                    <p>
                        Eine Weitergabe Ihrer personenbezogenen Daten erfolgt
                        nur, wenn dies zur Erfüllung eines Vertrags, zur
                        Bearbeitung Ihrer Anfrage, aufgrund einer gesetzlichen
                        Verpflichtung oder aufgrund einer Einwilligung
                        erforderlich ist.
                    </p>

                    <p>
                        Mögliche Empfänger können insbesondere Hosting-Anbieter,
                        E-Mail-Provider, technische Dienstleister und
                        IT-Dienstleister sein. Mit Dienstleistern werden,
                        sofern erforderlich, Vereinbarungen zur
                        Auftragsverarbeitung gemäß Art. 28 DSGVO geschlossen.
                    </p>

                    <h2>
                        12. Übermittlung in Drittländer
                    </h2>

                    <p>
                        Eine Übermittlung personenbezogener Daten in Staaten
                        außerhalb der Europäischen Union oder des Europäischen
                        Wirtschaftsraums erfolgt nur, wenn die gesetzlichen
                        Voraussetzungen hierfür erfüllt sind.
                    </p>

                    <p>
                        Dazu können insbesondere ein Angemessenheitsbeschluss
                        der Europäischen Kommission oder geeignete Garantien
                        nach Art. 44 ff. DSGVO gehören.
                    </p>

                    <h2>
                        13. Speicherdauer
                    </h2>

                    <p>
                        Wir speichern personenbezogene Daten nur so lange, wie
                        dies für den jeweiligen Zweck erforderlich ist oder
                        gesetzliche Aufbewahrungspflichten bestehen.
                    </p>

                    <p>
                        Nach Wegfall des jeweiligen Verarbeitungszwecks werden
                        die Daten gelöscht, sofern keine gesetzlichen Gründe
                        einer Löschung entgegenstehen.
                    </p>

                    <h2>
                        14. Ihre Rechte
                    </h2>

                    <p>
                        Sie haben im Rahmen der gesetzlichen Voraussetzungen
                        folgende Rechte:
                    </p>

                    <ul>
                        <li>
                            Recht auf Auskunft gemäß Art. 15 DSGVO
                        </li>
                        <li>
                            Recht auf Berichtigung gemäß Art. 16 DSGVO
                        </li>
                        <li>
                            Recht auf Löschung gemäß Art. 17 DSGVO
                        </li>
                        <li>
                            Recht auf Einschränkung der Verarbeitung gemäß
                            Art. 18 DSGVO
                        </li>
                        <li>
                            Recht auf Datenübertragbarkeit gemäß Art. 20 DSGVO
                        </li>
                        <li>
                            Recht auf Widerspruch gemäß Art. 21 DSGVO
                        </li>
                        <li>
                            Recht auf Widerruf erteilter Einwilligungen
                        </li>
                    </ul>

                    <p>
                        Zur Ausübung Ihrer Rechte können Sie sich jederzeit an
                        die oben genannte verantwortliche Stelle wenden.
                    </p>

                    <h2>
                        15. Beschwerderecht bei einer Aufsichtsbehörde
                    </h2>

                    <p>
                        Sie haben das Recht, sich bei einer Datenschutz-
                        Aufsichtsbehörde über die Verarbeitung Ihrer
                        personenbezogenen Daten zu beschweren.
                    </p>

                    <p>
                        Zuständig kann beispielsweise die Datenschutzbehörde
                        des Bundeslandes sein, in dem unser Unternehmen seinen
                        Sitz hat oder in dem Sie Ihren gewöhnlichen Aufenthalt
                        haben.
                    </p>

                    <h2>
                        16. Datensicherheit
                    </h2>

                    <p>
                        Wir setzen angemessene technische und organisatorische
                        Sicherheitsmaßnahmen ein, um Ihre personenbezogenen
                        Daten vor Verlust, Missbrauch, unbefugtem Zugriff und
                        unberechtigter Veränderung zu schützen.
                    </p>

                    <p>
                        Bitte beachten Sie, dass die Datenübertragung im
                        Internet Sicherheitslücken aufweisen kann. Ein
                        vollständiger Schutz der Daten vor dem Zugriff Dritter
                        ist nicht möglich.
                    </p>

                    <h2>
                        17. Aktualität dieser Datenschutzerklärung
                    </h2>

                    <p>
                        Wir behalten uns vor, diese Datenschutzerklärung
                        anzupassen, wenn sich rechtliche, technische oder
                        organisatorische Änderungen ergeben.
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