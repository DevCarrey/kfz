<?php
declare(strict_types=1);

// Kfz Digital konzentriert sich auf die Fahrzeugabmeldung. Die frühere
// umfangreiche Hilfesammlung bleibt unten als Archivbestand erhalten, wird
// jedoch nicht mehr ausgegeben.
$appPrefix = rtrim((string)($GLOBALS['appPrefix'] ?? ''), '/');
$helpUrl = static function (string $path) use ($appPrefix): string {
    return $appPrefix . '/' . ltrim($path, '/');
};
?>
<section class="kfz-section" aria-labelledby="help-title">
    <div class="container">
        <span class="kfz-section-kicker">Hilfe</span>
        <h1 id="help-title" class="kfz-section-title">Hilfe zur Fahrzeugabmeldung</h1>
        <div class="kfz-card">
            <h2>Abmeldung vorbereiten</h2>
            <p>Halten Sie Kennzeichen und, soweit vorhanden, die 17-stellige Fahrzeug-Identifizierungsnummer bereit. Sie können ein bereits gespeichertes Fahrzeug auswählen oder die Daten im Formular eingeben.</p>
            <p>Nach dem Speichern finden Sie Ihre Abmeldung mit Status und Vorgangsnummer unter „Meine Vorgänge“.</p>
            <a class="kfz-button kfz-button-primary" href="<?= htmlspecialchars($helpUrl('/vorgang-starten/'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">Fahrzeug abmelden</a>
        </div>
    </div>
</section>
<?php
return;

/**
 * Kfz Digital – Hilfe
 *
 * Header und Footer werden automatisch über index.php geladen.
 */


/*
|--------------------------------------------------------------------------
| URL-Hilfsfunktionen
|--------------------------------------------------------------------------
*/

$appPrefix = rtrim(
    (string)($GLOBALS['appPrefix'] ?? ''),
    '/'
);

$url = static function (string $path) use ($appPrefix): string {
    $path = '/' . ltrim($path, '/');

    return $appPrefix . (
        $path === '/'
            ? '/'
            : $path
    );
};

$escape = static function (string $value): string {
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
};


/*
|--------------------------------------------------------------------------
| Häufige Fragen
|--------------------------------------------------------------------------
*/

$faqItems = [
    [
        'question' => 'Was ist Kfz Digital?',
        'answer' => 'Kfz Digital ist eine zentrale Plattform zur Vorbereitung und Verwaltung digitaler Fahrzeugvorgänge. Sie können Fahrzeugdaten speichern, Vorgänge starten und den Bearbeitungsstatus im Blick behalten.',
    ],

    [
        'question' => 'Welche Fahrzeugvorgänge kann ich starten?',
        'answer' => 'Über Kfz Digital können Sie unter anderem eine Fahrzeuganmeldung, Ummeldung, Abmeldung oder Wiederzulassung vorbereiten. Welche Vorgänge tatsächlich online durchgeführt werden können, hängt vom jeweiligen Verfahren und den technischen Voraussetzungen ab.',
    ],

    [
        'question' => 'Benötige ich ein Benutzerkonto?',
        'answer' => 'Ja. Für die Verwaltung Ihrer Fahrzeuge und Vorgänge benötigen Sie ein persönliches Benutzerkonto. Dadurch können Ihre Angaben und der Bearbeitungsstatus sicher miteinander verknüpft werden.',
    ],

    [
        'question' => 'Wie kann ich ein Fahrzeug speichern?',
        'answer' => 'Melden Sie sich an und öffnen Sie den Bereich „Meine Fahrzeuge“. Dort können Sie ein Fahrzeug mit Kennzeichen, Fahrzeug-Identifizierungsnummer und weiteren Angaben speichern.',
    ],

    [
        'question' => 'Wo sehe ich meine gestarteten Vorgänge?',
        'answer' => 'Ihre gespeicherten Vorgänge finden Sie im Bereich „Meine Vorgänge“. Dort werden Vorgangsnummer, Vorgangsart, Kennzeichen und der aktuelle Status angezeigt.',
    ],

    [
        'question' => 'Was bedeutet der Status „Entwurf“?',
        'answer' => 'Der Status „Entwurf“ bedeutet, dass der Vorgang angelegt und gespeichert wurde, aber noch nicht vollständig geprüft oder an eine angebundene Schnittstelle übermittelt wurde.',
    ],

    [
        'question' => 'Was mache ich, wenn ich mein Passwort vergessen habe?',
        'answer' => 'Nutzen Sie auf der Anmeldeseite den Link „Passwort vergessen?“. Die Funktion zur Passwort-Wiederherstellung wird eingerichtet, sobald der E-Mail-Versand in Kfz Digital aktiviert ist.',
    ],

    [
        'question' => 'Sind meine Daten sicher?',
        'answer' => 'Kfz Digital ist für eine geschützte und übersichtliche Verarbeitung von Fahrzeug- und Kontodaten konzipiert. Verwenden Sie ein sicheres Passwort und melden Sie sich nach der Nutzung auf fremden Geräten ab.',
    ],
];

$searchTerm = trim(
    (string)($_GET['suche'] ?? '')
);

$filteredFaqItems = $faqItems;

if ($searchTerm !== '') {

    $filteredFaqItems = array_filter(
        $faqItems,
        static function (array $faqItem) use ($searchTerm): bool {
            $searchText = mb_strtolower(
                $faqItem['question'] . ' ' . $faqItem['answer']
            );

            return str_contains(
                $searchText,
                mb_strtolower($searchTerm)
            );
        }
    );
}
?>

<section
    class="kfz-section kfz-help-page"
    aria-labelledby="help-title"
>

    <div class="container">

        <!-- Seitenkopf -->

        <div class="kfz-help-hero">

            <div class="kfz-help-hero-content">

                <span class="kfz-section-kicker">
                    Kfz Digital
                </span>

                <h1
                    id="help-title"
                    class="kfz-section-title"
                >
                    Wie können wir Ihnen helfen?
                </h1>

                <p class="kfz-section-text">
                    Finden Sie Antworten auf häufige Fragen zu Ihrem Konto,
                    Ihren Fahrzeugen und digitalen Fahrzeugvorgängen.
                </p>

            </div>

            <div class="kfz-help-hero-icon">

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                    />

                    <path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-1.1.9-1.7 1.3-1.7 2.7" />
                    <path d="M12 17h.01" />
                </svg>

            </div>

        </div>


        <!-- Suchfeld -->

        <div class="kfz-help-search-card">

            <form
                action="<?= $escape($url('/hilfe/')) ?>"
                method="get"
                class="kfz-help-search-form"
            >

                <label
                    for="help-search"
                    class="kfz-form-label"
                >
                    Hilfe durchsuchen
                </label>

                <div class="kfz-help-search-row">

                    <div class="kfz-help-search-input-wrapper">

                        <svg
                            class="kfz-help-search-icon"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path d="m16 16 5 5" />
                        </svg>

                        <input
                            type="search"
                            id="help-search"
                            name="suche"
                            class="kfz-form-control"
                            value="<?= $escape($searchTerm) ?>"
                            placeholder="Zum Beispiel: Passwort, Fahrzeug oder Vorgang"
                            autocomplete="off"
                        >

                    </div>

                    <button
                        type="submit"
                        class="kfz-button kfz-button-primary"
                    >
                        Suchen
                    </button>

                    <?php if ($searchTerm !== ''): ?>

                        <a
                            href="<?= $escape($url('/hilfe/')) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Zurücksetzen
                        </a>

                    <?php endif; ?>

                </div>

            </form>

        </div>


        <!-- Schnellzugriff -->

        <section
            class="kfz-help-section"
            aria-labelledby="quick-help-title"
        >

            <div class="kfz-section-header">

                <span class="kfz-section-kicker">
                    Schnellzugriff
                </span>

                <h2
                    id="quick-help-title"
                    class="kfz-section-title"
                >
                    Was möchten Sie erledigen?
                </h2>

                <p class="kfz-section-text">
                    Über diese Bereiche gelangen Sie direkt zu den wichtigsten
                    Funktionen von Kfz Digital.
                </p>

            </div>


            <div class="kfz-help-quick-grid">

                <a
                    href="<?= $escape($url('/vorgang-starten/')) ?>"
                    class="kfz-help-quick-card"
                >

                    <span class="kfz-help-quick-icon">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M12 5v14M5 12h14" />
                        </svg>

                    </span>

                    <span>

                        <strong>
                            Vorgang starten
                        </strong>

                        <small>
                            Anmeldung, Ummeldung oder Abmeldung beginnen
                        </small>

                    </span>

                    <span
                        class="kfz-help-arrow"
                        aria-hidden="true"
                    >
                        →
                    </span>

                </a>


                <a
                    href="<?= $escape($url('/fahrzeuge/')) ?>"
                    class="kfz-help-quick-card"
                >

                    <span class="kfz-help-quick-icon kfz-help-quick-icon-teal">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M5 16h14l-1.5-6H6.5L5 16Z" />
                            <path d="M4 16h16v4H4v-4ZM7 20v1m10-1v1M7 10l1.5-3h7L17 10" />
                            <circle
                                cx="8"
                                cy="16"
                                r="1"
                            />

                            <circle
                                cx="16"
                                cy="16"
                                r="1"
                            />
                        </svg>

                    </span>

                    <span>

                        <strong>
                            Fahrzeug verwalten
                        </strong>

                        <small>
                            Fahrzeuge hinzufügen und bearbeiten
                        </small>

                    </span>

                    <span
                        class="kfz-help-arrow"
                        aria-hidden="true"
                    >
                        →
                    </span>

                </a>


                <a
                    href="<?= $escape($url('/vorgaenge/')) ?>"
                    class="kfz-help-quick-card"
                >

                    <span class="kfz-help-quick-icon kfz-help-quick-icon-purple">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M6 3h9l3 3v15H6V3Z" />
                            <path d="M14 3v4h4M9 12h6M9 16h6" />
                        </svg>

                    </span>

                    <span>

                        <strong>
                            Vorgänge ansehen
                        </strong>

                        <small>
                            Status und Vorgangsnummern prüfen
                        </small>

                    </span>

                    <span
                        class="kfz-help-arrow"
                        aria-hidden="true"
                    >
                        →
                    </span>

                </a>


                <a
                    href="<?= $escape($url('/konto/')) ?>"
                    class="kfz-help-quick-card"
                >

                    <span class="kfz-help-quick-icon kfz-help-quick-icon-orange">

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="8"
                                r="3.5"
                            />

                            <path d="M5 20a7 7 0 0 1 14 0" />
                        </svg>

                    </span>

                    <span>

                        <strong>
                            Mein Konto
                        </strong>

                        <small>
                            Kontodaten und Sicherheit verwalten
                        </small>

                    </span>

                    <span
                        class="kfz-help-arrow"
                        aria-hidden="true"
                    >
                        →
                    </span>

                </a>

            </div>

        </section>


        <!-- FAQ -->

        <section
            class="kfz-help-section"
            aria-labelledby="faq-title"
        >

            <div class="kfz-section-header">

                <span class="kfz-section-kicker">
                    Häufige Fragen
                </span>

                <h2
                    id="faq-title"
                    class="kfz-section-title"
                >
                    Antworten auf Ihre Fragen
                </h2>

                <?php if ($searchTerm !== ''): ?>

                    <p class="kfz-section-text">

                        Suchergebnisse für:
                        <strong>
                            <?= $escape($searchTerm) ?>
                        </strong>

                    </p>

                <?php else: ?>

                    <p class="kfz-section-text">
                        Hier finden Sie die wichtigsten Informationen
                        zur Nutzung von Kfz Digital.
                    </p>

                <?php endif; ?>

            </div>


            <?php if ($filteredFaqItems === []): ?>

                <div
                    class="kfz-help-no-results"
                    role="status"
                >

                    <div class="kfz-help-no-results-icon">
                        ?
                    </div>

                    <h3>
                        Keine passende Antwort gefunden
                    </h3>

                    <p>
                        Versuchen Sie einen anderen Suchbegriff oder nehmen
                        Sie direkt Kontakt mit uns auf.
                    </p>

                    <a
                        href="<?= $escape($url('/kontakt/')) ?>"
                        class="kfz-button kfz-button-primary"
                    >
                        Kontakt aufnehmen
                    </a>

                </div>

            <?php else: ?>

                <div
                    class="kfz-faq-list"
                    id="faq-list"
                >

                    <?php foreach (
                        $filteredFaqItems
                        as $faqIndex => $faqItem
                    ): ?>

                        <?php
                        $faqId = 'faq-' . $faqIndex;
                        ?>

                        <article class="kfz-faq-item">

                            <button
                                type="button"
                                class="kfz-faq-question"
                                aria-expanded="false"
                                aria-controls="<?= $escape($faqId) ?>"
                            >

                                <span>
                                    <?= $escape($faqItem['question']) ?>
                                </span>

                                <span
                                    class="kfz-faq-question-icon"
                                    aria-hidden="true"
                                >
                                    +
                                </span>

                            </button>

                            <div
                                id="<?= $escape($faqId) ?>"
                                class="kfz-faq-answer"
                                hidden
                            >

                                <p>
                                    <?= $escape($faqItem['answer']) ?>
                                </p>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- Ablauf -->

        <section
            class="kfz-help-section"
            aria-labelledby="help-steps-title"
        >

            <div class="kfz-section-header">

                <span class="kfz-section-kicker">
                    So funktioniert es
                </span>

                <h2
                    id="help-steps-title"
                    class="kfz-section-title"
                >
                    Ihr Weg durch Kfz Digital
                </h2>

            </div>


            <div class="kfz-help-steps">

                <div class="kfz-help-step">

                    <span class="kfz-help-step-number">
                        1
                    </span>

                    <div>

                        <h3>
                            Konto erstellen
                        </h3>

                        <p>
                            Registrieren Sie sich mit Ihrer E-Mail-Adresse
                            und einem sicheren Passwort.
                        </p>

                    </div>

                </div>


                <div class="kfz-help-step">

                    <span class="kfz-help-step-number">
                        2
                    </span>

                    <div>

                        <h3>
                            Fahrzeug hinterlegen
                        </h3>

                        <p>
                            Speichern Sie die Daten Ihres Fahrzeugs in Ihrem
                            persönlichen Bereich.
                        </p>

                    </div>

                </div>


                <div class="kfz-help-step">

                    <span class="kfz-help-step-number">
                        3
                    </span>

                    <div>

                        <h3>
                            Vorgang starten
                        </h3>

                        <p>
                            Wählen Sie den gewünschten Fahrzeugvorgang aus
                            und ergänzen Sie die erforderlichen Angaben.
                        </p>

                    </div>

                </div>


                <div class="kfz-help-step">

                    <span class="kfz-help-step-number">
                        4
                    </span>

                    <div>

                        <h3>
                            Status verfolgen
                        </h3>

                        <p>
                            Prüfen Sie den Bearbeitungsstand anschließend
                            unter „Meine Vorgänge“.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- Kontakt-CTA -->

        <section
            class="kfz-help-contact"
            aria-labelledby="help-contact-title"
        >

            <div class="kfz-help-contact-icon">

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path d="M4 5h16v12H8l-4 4V5Z" />
                    <path d="M8 9h8M8 13h5" />
                </svg>

            </div>

            <div>

                <span class="kfz-section-kicker">
                    Noch Fragen?
                </span>

                <h2 id="help-contact-title">
                    Wir helfen Ihnen gerne weiter.
                </h2>

                <p>
                    Wenn Sie keine passende Antwort gefunden haben, können
                    Sie uns direkt kontaktieren.
                </p>

            </div>

            <a
                href="<?= $escape($url('/kontakt/')) ?>"
                class="kfz-button kfz-button-light"
            >
                Kontakt aufnehmen
            </a>

        </section>

    </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const faqQuestions = document.querySelectorAll(
        '.kfz-faq-question'
    );

    faqQuestions.forEach(function (question) {
        question.addEventListener('click', function () {
            const answerId = question.getAttribute(
                'aria-controls'
            );

            const answer = document.getElementById(answerId);

            if (!answer) {
                return;
            }

            const isExpanded =
                question.getAttribute('aria-expanded') === 'true';

            document
                .querySelectorAll('.kfz-faq-question')
                .forEach(function (otherQuestion) {
                    if (otherQuestion !== question) {
                        otherQuestion.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                        const otherAnswerId =
                            otherQuestion.getAttribute(
                                'aria-controls'
                            );

                        const otherAnswer =
                            document.getElementById(otherAnswerId);

                        if (otherAnswer) {
                            otherAnswer.hidden = true;
                        }

                        const otherIcon =
                            otherQuestion.querySelector(
                                '.kfz-faq-question-icon'
                            );

                        if (otherIcon) {
                            otherIcon.textContent = '+';
                        }
                    }
                });

            question.setAttribute(
                'aria-expanded',
                String(!isExpanded)
            );

            answer.hidden = isExpanded;

            const icon = question.querySelector(
                '.kfz-faq-question-icon'
            );

            if (icon) {
                icon.textContent = isExpanded
                    ? '+'
                    : '−';
            }
        });
    });
});
</script>
