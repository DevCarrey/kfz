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
<section class="py-5 bg-light border-bottom" aria-labelledby="privacy-title">
    <div class="container py-lg-4">

        <div class="row">
            <div class="col-lg-9">

                <p class="text-warning text-uppercase fw-bold mb-2">
                    Rechtliche Informationen
                </p>

                <h1 id="privacy-title" class="display-5 fw-bold mb-3">
                    Datenschutzerklärung
                </h1>

                <p class="lead text-secondary mb-0">
                    Informationen zum Schutz Ihrer personenbezogenen Daten
                </p>

            </div>
        </div>

    </div>
</section>


<!-- Datenschutz -->
<section class="py-5">
    <div class="container">

        <div class="row">
            <main class="col-lg-9">

                <!-- Verantwortlicher -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Verantwortlicher
                    </h2>

                    <p class="text-secondary">
                        Verantwortlicher im Sinne der Datenschutzgesetze,
                        insbesondere der EU-Datenschutzgrundverordnung (DSGVO), ist:
                    </p>

                    <p class="text-secondary mb-4">
                        <strong class="text-dark">
                            Getränke Ludwig Vogt GmbH
                        </strong>
                        <br>
                        Specksloh 12
                        <br>
                        59757 Arnsberg-Voßwinkel
                    </p>

                    <p class="text-secondary">
                        <strong class="text-dark">
                            Vertreten durch:
                        </strong>
                        <br>
                        Andreas Vogt
                    </p>

                    <p class="text-secondary mb-0">
                        <strong class="text-dark">
                            Kontakt:
                        </strong>
                        <br>

                        Telefon:
                        <a
                            href="tel:+49293233116"
                            class="text-decoration-none"
                        >
                            02932 33116
                        </a>
                        <br>

                        Telefax: 02932 51333
                        <br>

                        E-Mail:
                        <a
                            href="mailto:info@getraenke-ludwig-vogt.de"
                            class="text-decoration-none"
                        >
                            info@getraenke-ludwig-vogt.de
                        </a>
                    </p>

                </section>


                <hr class="my-5">


                <!-- Betroffenenrechte -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Ihre Betroffenenrechte
                    </h2>

                    <p class="text-secondary">
                        Unter den angegebenen Kontaktdaten unseres
                        Datenschutzbeauftragten können Sie jederzeit folgende
                        Rechte ausüben:
                    </p>

                    <ul class="text-secondary ps-4">

                        <li class="mb-2">
                            Auskunft über Ihre bei uns gespeicherten Daten und
                            deren Verarbeitung (Art. 15 DSGVO).
                        </li>

                        <li class="mb-2">
                            Berichtigung unrichtiger personenbezogener Daten
                            (Art. 16 DSGVO).
                        </li>

                        <li class="mb-2">
                            Löschung Ihrer bei uns gespeicherten Daten
                            (Art. 17 DSGVO).
                        </li>

                        <li class="mb-2">
                            Einschränkung der Datenverarbeitung, sofern wir Ihre
                            Daten aufgrund gesetzlicher Pflichten noch nicht
                            löschen dürfen (Art. 18 DSGVO).
                        </li>

                        <li class="mb-2">
                            Widerspruch gegen die Verarbeitung Ihrer Daten bei
                            uns (Art. 21 DSGVO).
                        </li>

                        <li>
                            Datenübertragbarkeit, sofern Sie in die
                            Datenverarbeitung eingewilligt haben oder einen
                            Vertrag mit uns abgeschlossen haben (Art. 20 DSGVO).
                        </li>

                    </ul>

                    <p class="text-secondary">
                        Sofern Sie uns eine Einwilligung erteilt haben, können
                        Sie diese jederzeit mit Wirkung für die Zukunft widerrufen.
                    </p>

                    <p class="text-secondary mb-0">
                        Sie können sich jederzeit mit einer Beschwerde an eine
                        Aufsichtsbehörde wenden, z. B. an die zuständige
                        Aufsichtsbehörde des Bundeslands Ihres Wohnsitzes oder
                        an die für uns als verantwortliche Stelle zuständige
                        Behörde.
                    </p>

                    <p class="text-secondary mt-3 mb-0">
                        Eine Liste der Aufsichtsbehörden für den nichtöffentlichen
                        Bereich mit Anschrift finden Sie beim
                        <a
                            href="https://www.bfdi.bund.de/DE/Service/Anschriften/Laender/Laender-node.html"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            Bundesbeauftragten für den Datenschutz und die
                            Informationsfreiheit
                        </a>.
                    </p>

                </section>


                <hr class="my-5">


                <!-- Allgemeine Informationen -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Erfassung allgemeiner Informationen beim Besuch unserer Website
                    </h2>

                    <h3 class="h5 fw-bold mt-4 mb-3">
                        Art und Zweck der Verarbeitung
                    </h3>

                    <p class="text-secondary">
                        Wenn Sie auf unsere Website zugreifen, d. h., wenn Sie
                        sich nicht registrieren oder anderweitig Informationen
                        übermitteln, werden automatisch Informationen allgemeiner
                        Natur erfasst.
                    </p>

                    <p class="text-secondary">
                        Diese Informationen (Server-Logfiles) beinhalten etwa die
                        Art des Webbrowsers, das verwendete Betriebssystem, den
                        Domainnamen Ihres Internet-Service-Providers, Ihre
                        IP-Adresse und ähnliche Informationen.
                    </p>

                    <p class="text-secondary">
                        Sie werden insbesondere zu folgenden Zwecken verarbeitet:
                    </p>

                    <ul class="text-secondary ps-4">

                        <li class="mb-2">
                            Sicherstellung eines problemlosen Verbindungsaufbaus
                            der Website
                        </li>

                        <li class="mb-2">
                            Sicherstellung einer reibungslosen Nutzung unserer
                            Website
                        </li>

                        <li class="mb-2">
                            Auswertung der Systemsicherheit und -stabilität
                        </li>

                        <li>
                            Optimierung unserer Website
                        </li>

                    </ul>

                    <p class="text-secondary">
                        Wir verwenden Ihre Daten nicht, um Rückschlüsse auf Ihre
                        Person zu ziehen. Informationen dieser Art werden von uns
                        gegebenenfalls anonymisiert statistisch ausgewertet, um
                        unseren Internetauftritt und die dahinterstehende Technik
                        zu optimieren.
                    </p>


                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Rechtsgrundlage und berechtigtes Interesse
                    </h3>

                    <p class="text-secondary">
                        Die Verarbeitung erfolgt gemäß Art. 6 Abs. 1 lit. f DSGVO
                        auf Basis unseres berechtigten Interesses an der
                        Verbesserung der Stabilität und Funktionalität unserer
                        Website.
                    </p>


                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Empfänger
                    </h3>

                    <p class="text-secondary">
                        Empfänger der Daten sind gegebenenfalls technische
                        Dienstleister, die für den Betrieb und die Wartung unserer
                        Website als Auftragsverarbeiter tätig werden.
                    </p>


                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Speicherdauer
                    </h3>

                    <p class="text-secondary">
                        Die Daten werden gelöscht, sobald diese für den Zweck
                        der Erhebung nicht mehr erforderlich sind. Dies ist für
                        die Daten, die der Bereitstellung der Website dienen,
                        grundsätzlich der Fall, wenn die jeweilige Sitzung
                        beendet ist.
                    </p>


                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Bereitstellung vorgeschrieben oder erforderlich
                    </h3>

                    <p class="text-secondary mb-0">
                        Die Bereitstellung der vorgenannten personenbezogenen
                        Daten ist weder gesetzlich noch vertraglich vorgeschrieben.
                        Ohne die IP-Adresse ist jedoch der Dienst und die
                        Funktionsfähigkeit unserer Website nicht gewährleistet.
                        Zudem können einzelne Dienste und Services nicht verfügbar
                        oder eingeschränkt sein. Aus diesem Grund ist ein
                        Widerspruch ausgeschlossen.
                    </p>

                </section>


                <hr class="my-5">


                <!-- Google Analytics -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Verwendung von Google Analytics
                    </h2>

                    <p class="text-secondary">
                        Soweit Sie Ihre Einwilligung gegeben haben, wird auf
                        dieser Website Google Analytics eingesetzt, ein
                        Webanalysedienst der Google LLC, 1600 Amphitheatre Parkway,
                        Mountain View, CA 94043, USA (nachfolgend „Google“).
                    </p>

                    <p class="text-secondary">
                        Google Analytics verwendet sogenannte „Cookies“, also
                        Textdateien, die auf Ihrem Computer gespeichert werden
                        und die eine Analyse der Benutzung der Webseite durch
                        Sie ermöglichen.
                    </p>

                    <p class="text-secondary">
                        Die durch das Cookie erzeugten Informationen über Ihre
                        Benutzung dieser Webseite werden in der Regel an einen
                        Server von Google in den USA übertragen und dort
                        gespeichert.
                    </p>

                    <p class="text-secondary">
                        Aufgrund der Aktivierung der IP-Anonymisierung auf
                        diesen Webseiten wird Ihre IP-Adresse von Google jedoch
                        innerhalb von Mitgliedstaaten der Europäischen Union
                        oder in anderen Vertragsstaaten des Abkommens über den
                        Europäischen Wirtschaftsraum zuvor gekürzt.
                    </p>

                    <p class="text-secondary">
                        Nur in Ausnahmefällen wird die volle IP-Adresse an einen
                        Server von Google in den USA übertragen und dort gekürzt.
                        Die im Rahmen von Google Analytics von Ihrem Browser
                        übermittelte IP-Adresse wird nicht mit anderen Daten
                        von Google zusammengeführt.
                    </p>

                    <p class="text-secondary">
                        Nähere Informationen zu Nutzungsbedingungen und
                        Datenschutz finden Sie unter
                        <a
                            href="https://www.google.com/analytics/terms/de.html"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            Google Analytics
                        </a>
                        sowie in den
                        <a
                            href="https://policies.google.com/?hl=de"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            Datenschutzbestimmungen von Google
                        </a>.
                    </p>

                    <p class="text-secondary">
                        Im Auftrag des Betreibers dieser Website wird Google
                        diese Informationen benutzen, um Ihre Nutzung der
                        Webseite auszuwerten, um Reports über die
                        Webseitenaktivitäten zusammenzustellen und um weitere
                        mit der Websitenutzung und der Internetnutzung verbundene
                        Dienstleistungen gegenüber dem Webseitenbetreiber zu
                        erbringen.
                    </p>

                    <p class="text-secondary">
                        Die von uns gesendeten und mit Cookies, Nutzerkennungen
                        (z. B. User-ID) oder Werbe-IDs verknüpften Daten werden
                        nach 14 Monaten automatisch gelöscht. Die Löschung von
                        Daten, deren Aufbewahrungsdauer erreicht ist, erfolgt
                        automatisch einmal im Monat.
                    </p>


                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Widerruf der Einwilligung
                    </h3>

                    <p class="text-secondary mb-0">
                        Sie können das Tracking durch Google Analytics auf
                        unserer Website unterbinden, indem Sie die entsprechende
                        Einstellung in unserem Cookie-Consent-Tool ändern.
                        Dabei wird die weitere Erfassung durch Google Analytics
                        entsprechend Ihrer Auswahl verhindert.
                    </p>

                </section>


                <hr class="my-5">


                <!-- Google Maps -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Verwendung von Google Maps
                    </h2>

                    <p class="text-secondary">
                        Auf dieser Website nutzen wir das Angebot von Google Maps.
                        Google Maps wird von Google LLC, 1600 Amphitheatre Parkway,
                        Mountain View, CA 94043, USA (nachfolgend „Google“)
                        betrieben.
                    </p>

                    <p class="text-secondary">
                        Dadurch können wir Ihnen interaktive Karten direkt in
                        der Webseite anzeigen und ermöglichen Ihnen die
                        komfortable Nutzung der Karten-Funktion.
                    </p>

                    <p class="text-secondary">
                        Nähere Informationen über die Datenverarbeitung durch
                        Google können Sie den
                        <a
                            href="https://policies.google.com/privacy"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            Google-Datenschutzhinweisen
                        </a>
                        entnehmen.
                    </p>

                    <p class="text-secondary">
                        Dort können Sie im Datenschutzcenter auch Ihre persönlichen
                        Datenschutz-Einstellungen verändern.
                    </p>

                    <p class="text-secondary">
                        Durch den Besuch der Website erhält Google Informationen,
                        dass Sie die entsprechende Unterseite unserer Webseite
                        aufgerufen haben. Dies erfolgt unabhängig davon, ob
                        Google ein Nutzerkonto bereitstellt, über das Sie
                        eingeloggt sind, oder ob kein Nutzerkonto besteht.
                    </p>

                    <p class="text-secondary">
                        Wenn Sie bei Google eingeloggt sind, werden Ihre Daten
                        direkt Ihrem Konto zugeordnet.
                    </p>

                    <p class="text-secondary">
                        Wenn Sie die Zuordnung in Ihrem Profil bei Google nicht
                        wünschen, müssen Sie sich vor Aktivierung des Buttons
                        bei Google ausloggen.
                    </p>

                    <p class="text-secondary">
                        Google speichert Ihre Daten als Nutzungsprofile und nutzt
                        sie für Zwecke der Werbung, Marktforschung und/oder
                        bedarfsgerechten Gestaltung seiner Websites.
                    </p>

                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Widerruf der Einwilligung
                    </h3>

                    <p class="text-secondary mb-0">
                        Wenn Sie eine Nachverfolgung Ihrer Aktivitäten auf unserer
                        Website verhindern wollen, widerrufen Sie bitte im
                        Cookie-Consent-Tool Ihre Einwilligung für die entsprechende
                        Cookie-Kategorie oder alle technisch nicht notwendigen
                        Cookies und Datenübertragungen.
                    </p>

                </section>


                <hr class="my-5">


                <!-- YouTube -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Eingebettete YouTube-Videos
                    </h2>

                    <p class="text-secondary">
                        Auf unserer Website betten wir YouTube-Videos ein.
                        Betreiber der entsprechenden Dienste ist die YouTube,
                        LLC, 901 Cherry Ave., San Bruno, CA 94066, USA
                        (nachfolgend „YouTube“).
                    </p>

                    <p class="text-secondary">
                        Die YouTube, LLC ist eine Tochtergesellschaft der
                        Google LLC, 1600 Amphitheatre Pkwy, Mountain View,
                        CA 94043, USA (nachfolgend „Google“).
                    </p>

                    <p class="text-secondary">
                        Wenn Sie eine Seite mit einem YouTube-Video besuchen,
                        kann eine Verbindung zu Servern von YouTube hergestellt
                        werden. Dabei wird YouTube mitgeteilt, welche Seiten
                        Sie besuchen.
                    </p>

                    <p class="text-secondary">
                        Wenn Sie in Ihrem YouTube-Account eingeloggt sind,
                        kann YouTube Ihr Surfverhalten Ihnen persönlich
                        zuordnen. Dies verhindern Sie, indem Sie sich vorher
                        aus Ihrem YouTube-Account ausloggen.
                    </p>

                    <p class="text-secondary">
                        Wird ein YouTube-Video gestartet, können Cookies gesetzt
                        werden, die Hinweise über das Nutzerverhalten sammeln.
                    </p>

                    <p class="text-secondary">
                        Weitere Informationen zu Zweck und Umfang der Datenerhebung
                        und ihrer Verarbeitung durch YouTube erhalten Sie in den
                        <a
                            href="https://policies.google.com/privacy"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            Datenschutzerklärungen von Google
                        </a>.
                    </p>

                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Widerruf der Einwilligung
                    </h3>

                    <p class="text-secondary mb-0">
                        Wenn Sie eine Nachverfolgung Ihrer Aktivitäten auf unserer
                        Website verhindern wollen, widerrufen Sie bitte im
                        Cookie-Consent-Tool Ihre Einwilligung für die entsprechende
                        Cookie-Kategorie oder alle technisch nicht notwendigen
                        Cookies und Datenübertragungen.
                    </p>

                </section>


                <hr class="my-5">


                <!-- Google Ads -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Google Ads / Conversion-Tracking
                    </h2>

                    <p class="text-secondary">
                        Unsere Website nutzt gegebenenfalls das Google
                        Conversion-Tracking. Betreibergesellschaft der Dienste
                        von Google Ads ist die Google LLC, 1600 Amphitheatre
                        Parkway, Mountain View, CA 94043, USA.
                    </p>

                    <p class="text-secondary">
                        Sind Sie über eine von Google geschaltete Anzeige auf
                        unsere Webseite gelangt, kann von Google ein Cookie
                        auf Ihrem Rechner gesetzt werden.
                    </p>

                    <p class="text-secondary">
                        Das Cookie für Conversion-Tracking wird gesetzt, wenn
                        ein Nutzer auf eine von Google geschaltete Anzeige klickt.
                    </p>

                    <p class="text-secondary">
                        Besucht der Nutzer bestimmte Seiten unserer Website und
                        das Cookie ist noch nicht abgelaufen, können wir und
                        Google erkennen, dass der Nutzer auf die Anzeige geklickt
                        hat und zu dieser Seite weitergeleitet wurde.
                    </p>

                    <p class="text-secondary">
                        Die mithilfe des Conversion-Cookies eingeholten
                        Informationen dienen dazu, Conversion-Statistiken
                        für Google-Ads-Kunden zu erstellen.
                    </p>

                    <p class="text-secondary">
                        Weitere Informationen über die Datenverarbeitung durch
                        Google finden Sie in den
                        <a
                            href="https://policies.google.com/privacy"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-decoration-none"
                        >
                            Google-Datenschutzhinweisen
                        </a>.
                    </p>

                    <h3 class="h5 fw-bold mt-5 mb-3">
                        Widerruf der Einwilligung
                    </h3>

                    <p class="text-secondary mb-0">
                        Wenn Sie eine Nachverfolgung Ihrer Aktivitäten auf unserer
                        Website verhindern wollen, widerrufen Sie bitte im
                        Cookie-Consent-Tool Ihre Einwilligung für die entsprechende
                        Cookie-Kategorie oder alle technisch nicht notwendigen
                        Cookies und Datenübertragungen.
                    </p>

                </section>


                <hr class="my-5">


                <!-- SSL -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        SSL-Verschlüsselung
                    </h2>

                    <p class="text-secondary mb-0">
                        Um die Sicherheit Ihrer Daten bei der Übertragung zu
                        schützen, verwenden wir dem aktuellen Stand der Technik
                        entsprechende Verschlüsselungsverfahren, beispielsweise
                        SSL bzw. TLS, über HTTPS.
                    </p>

                </section>


                <hr class="my-5">


                <!-- Widerspruch -->
                <section class="mb-5">

                    <h2 class="h3 fw-bold mb-4">
                        Information über Ihr Widerspruchsrecht nach Art. 21 DSGVO
                    </h2>

                    <h3 class="h5 fw-bold mt-4 mb-3">
                        Einzelfallbezogenes Widerspruchsrecht
                    </h3>

                    <p class="text-secondary">
                        Sie haben das Recht, aus Gründen, die sich aus Ihrer
                        besonderen Situation ergeben, jederzeit gegen die
                        Verarbeitung Sie betreffender personenbezogener Daten,
                        die aufgrund Art. 6 Abs. 1 lit. f DSGVO
                        (Datenverarbeitung auf der Grundlage einer
                        Interessenabwägung) erfolgt, Widerspruch einzulegen.
                    </p>

                    <p class="text-secondary">
                        Dies gilt auch für ein auf diese Bestimmung gestütztes
                        Profiling im Sinne von Art. 4 Nr. 4 DSGVO.
                    </p>

                    <p class="text-secondary mb-0">
                        Legen Sie Widerspruch ein, werden wir Ihre
                        personenbezogenen Daten nicht mehr verarbeiten, es sei
                        denn, wir können zwingende schutzwürdige Gründe für die
                        Verarbeitung nachweisen, die Ihre Interessen, Rechte und
                        Freiheiten überwiegen, oder die Verarbeitung dient der
                        Geltendmachung, Ausübung oder Verteidigung von
                        Rechtsansprüchen.
                    </p>

                </section>


                <!-- Widerspruch Kontakt -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Empfänger eines Widerspruchs
                    </h2>

                    <p class="text-secondary mb-0">
                        <strong class="text-dark">
                            Getränke Ludwig Vogt GmbH
                        </strong>
                        <br>
                        Specksloh 12
                        <br>
                        59757 Arnsberg-Voßwinkel
                        <br><br>

                        Vertreten durch:<br>
                        Andreas Vogt
                        <br><br>

                        Telefon:
                        <a
                            href="tel:+49293233116"
                            class="text-decoration-none"
                        >
                            02932 33116
                        </a>
                        <br>

                        Telefax: 02932 51333
                        <br>

                        E-Mail:
                        <a
                            href="mailto:info@getraenke-ludwig-vogt.de"
                            class="text-decoration-none"
                        >
                            info@getraenke-ludwig-vogt.de
                        </a>
                    </p>

                </section>


                <hr class="my-5">


                <!-- Änderungen -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Änderung unserer Datenschutzbestimmungen
                    </h2>

                    <p class="text-secondary mb-0">
                        Wir behalten uns vor, diese Datenschutzerklärung
                        anzupassen, damit sie stets den aktuellen rechtlichen
                        Anforderungen entspricht oder um Änderungen unserer
                        Leistungen in der Datenschutzerklärung umzusetzen,
                        z. B. bei der Einführung neuer Services.
                    </p>

                </section>


                <!-- Datenschutzbeauftragter -->
                <section class="mb-5">

                    <h2 class="h4 fw-bold mb-3">
                        Fragen zum Datenschutz
                    </h2>

                    <p class="text-secondary">
                        Wenn Sie Fragen zum Datenschutz haben, schreiben Sie
                        uns bitte eine E-Mail oder wenden Sie sich direkt an
                        die für den Datenschutz verantwortliche Person in
                        unserer Organisation:
                    </p>

                    <p class="text-secondary mb-0">
                        <strong class="text-dark">
                            Andreas Vogt
                        </strong>
                        <br>
                        Getränke Ludwig Vogt GmbH
                        <br>
                        Specksloh 12
                        <br>
                        59757 Arnsberg-Voßwinkel
                        <br><br>

                        Telefon:
                        <a
                            href="tel:+49293233116"
                            class="text-decoration-none"
                        >
                            02932 33116
                        </a>
                        <br>

                        Telefax: 02932 51333
                        <br>

                        E-Mail:
                        <a
                            href="mailto:info@getraenke-ludwig-vogt.de"
                            class="text-decoration-none"
                        >
                            info@getraenke-ludwig-vogt.de
                        </a>
                    </p>

                </section>


                <!-- Generator -->
                <p class="small text-secondary mt-5">
                    Die Datenschutzerklärung wurde mithilfe der