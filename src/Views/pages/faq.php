<?php
declare(strict_types=1);

// Ausschließlich die aktuelle Leistung Fahrzeugabmeldung ausgeben; die
// frühere allgemeine FAQ bleibt als nicht ausgeführter Archivbestand erhalten.
?>
<section class="kfz-section" aria-labelledby="faq-title">
    <div class="container">
        <span class="kfz-section-kicker">FAQ</span>
        <h1 id="faq-title" class="kfz-section-title">Fragen zur Fahrzeugabmeldung</h1>
        <div class="kfz-card mb-3"><h2>Kann ich hier ein Fahrzeug anmelden oder ummelden?</h2><p>Nein. Kfz Digital bietet ausschließlich die Vorbereitung und Verwaltung von Fahrzeugabmeldungen an.</p></div>
        <div class="kfz-card mb-3"><h2>Welche Angaben benötige ich?</h2><p>Sie benötigen Fahrzeugdaten und persönliche Kontaktdaten. Je nach späterem behördlichen Verfahren können weitere Nachweise erforderlich sein.</p></div>
        <div class="kfz-card"><h2>Wird die Abmeldung sofort übermittelt?</h2><p>Nein. Der aktuelle Mock- und Testbetrieb übermittelt keine echten Anträge an Behörden.</p></div>
    </div>
</section>
<?php
return;

/**
 * Kfz Digital – FAQ
 *
 * Header und Footer werden durch index.php geladen.
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
| FAQ-Daten
|--------------------------------------------------------------------------
*/

$faqCategories = [
    'Allgemein' => [
        [
            'question' => 'Was ist Kfz Digital?',
            'answer' => 'Kfz Digital ist eine digitale Plattform zur Verwaltung und Vorbereitung von Fahrzeugvorgängen. Dazu gehören unter anderem Fahrzeuganmeldung, Ummeldung, Abmeldung und Wiederzulassung.',
        ],

        [
            'question' => 'Welche Vorgänge kann ich mit Kfz Digital starten?',
            'answer' => 'Aktuell können Sie die Vorgänge Fahrzeug anmelden, Fahrzeug ummelden, Fahrzeug abmelden und Wiederzulassung vorbereiten. Welche Schritte tatsächlich online durchgeführt werden können, hängt vom jeweiligen Vorgang, den vorhandenen Unterlagen und den technischen Voraussetzungen ab.',
        ],

        [
            'question' => 'Ist Kfz Digital eine Behörde?',
            'answer' => 'Nein. Kfz Digital ist eine digitale Plattform zur Vorbereitung, Verwaltung und Nachverfolgung von Fahrzeugvorgängen. Die eigentliche behördliche Entscheidung trifft die zuständige Zulassungsstelle.',
        ],

        [
            'question' => 'Kann ich Kfz Digital auch ohne Benutzerkonto verwenden?',
            'answer' => 'Für die dauerhafte Verwaltung von Fahrzeugen und Vorgängen benötigen Sie ein Benutzerkonto. Dadurch können Ihre Daten, Dokumente und Vorgänge sicher miteinander verknüpft werden.',
        ],
    ],

    'Benutzerkonto' => [
        [
            'question' => 'Wie erstelle ich ein Benutzerkonto?',
            'answer' => 'Klicken Sie auf „Anmelden“ und anschließend auf „Konto erstellen“. Geben Sie Ihre persönlichen Daten, eine gültige E-Mail-Adresse und ein sicheres Passwort ein.',
        ],

        [
            'question' => 'Was mache ich, wenn ich mein Passwort vergessen habe?',
            'answer' => 'Auf der Anmeldeseite finden Sie den Link „Passwort vergessen?“. Über diesen Bereich kann später eine Wiederherstellung per E-Mail eingerichtet werden.',
        ],

        [
            'question' => 'Wie kann ich mich abmelden?',
            'answer' => 'Nach der Anmeldung finden Sie in Ihrem Konto den Button „Abmelden“. Zusätzlich steht die Abmeldung im Benutzerbereich zur Verfügung.',
        ],

        [
            'question' => 'Warum muss ich mich für bestimmte Bereiche anmelden?',
            'answer' => 'Fahrzeugdaten, persönliche Daten und Vorgänge sind geschützt. Deshalb werden diese Bereiche nur für angemeldete Benutzer freigeschaltet.',
        ],
    ],

    'Fahrzeuge' => [
        [
            'question' => 'Wie speichere ich ein Fahrzeug?',
            'answer' => 'Öffnen Sie den Bereich „Meine Fahrzeuge“ und wählen Sie „Fahrzeug hinzufügen“. Dort können Sie Kennzeichen, Fahrzeug-Identifizierungsnummer, Hersteller, Modell und weitere Angaben speichern.',
        ],

        [
            'question' => 'Welche Daten benötige ich für ein Fahrzeug?',
            'answer' => 'Für die Fahrzeugverwaltung können Sie unter anderem das Kennzeichen, die Fahrzeug-Identifizierungsnummer, den Hersteller, das Modell, die Fahrzeugart und das Erstzulassungsdatum hinterlegen.',
        ],

        [
            'question' => 'Kann ich ein gespeichertes Fahrzeug wieder löschen?',
            'answer' => 'Ja. Öffnen Sie „Meine Fahrzeuge“ und wählen Sie beim entsprechenden Fahrzeug die Funktion „Löschen“. Das Fahrzeug wird anschließend aus Ihrem persönlichen Bereich entfernt.',
        ],

        [
            'question' => 'Kann ich ein gespeichertes Fahrzeug für einen Vorgang verwenden?',
            'answer' => 'Ja. Beim Starten eines neuen Vorgangs können Sie ein bereits gespeichertes Fahrzeug auswählen. Die vorhandenen Daten werden anschließend in das Formular übernommen.',
        ],
    ],

    'Vorgänge' => [
        [
            'question' => 'Wie starte ich einen neuen Fahrzeugvorgang?',
            'answer' => 'Klicken Sie auf „Vorgang starten“. Wählen Sie anschließend den passenden Vorgang aus und geben Sie die erforderlichen Daten ein.',
        ],

        [
            'question' => 'Wo sehe ich meine Vorgänge?',
            'answer' => 'Ihre gespeicherten Vorgänge finden Sie im Bereich „Meine Vorgänge“. Dort sehen Sie unter anderem die Vorgangsnummer, den Vorgangstyp, das Kennzeichen und den aktuellen Status.',
        ],

        [
            'question' => 'Was bedeutet der Status „Entwurf“?',
            'answer' => 'Der Status „Entwurf“ bedeutet, dass der Vorgang gespeichert, aber noch nicht vollständig bearbeitet oder an eine angebundene Schnittstelle übermittelt wurde.',
        ],

        [
            'question' => 'Was bedeutet „in Bearbeitung“?',
            'answer' => 'Der Status „in Bearbeitung“ bedeutet, dass der Vorgang geprüft oder von einer zuständigen Stelle weiterbearbeitet wird.',
        ],

        [
            'question' => 'Was passiert nach dem Speichern eines Vorgangs?',
            'answer' => 'Nach dem Speichern erhalten Sie eine Vorgangsnummer. Der Vorgang wird in Ihrem Bereich „Meine Vorgänge“ angezeigt und kann dort weiterverfolgt werden.',
        ],
    ],

    'Dokumente und Sicherheit' => [
        [
            'question' => 'Welche Dokumente können benötigt werden?',
            'answer' => 'Je nach Vorgang können beispielsweise Zulassungsbescheinigungen, ein Identitätsnachweis, eine elektronische Versicherungsbestätigung, ein SEPA-Lastschriftmandat oder weitere Nachweise erforderlich sein.',
        ],

        [
            'question' => 'Sind meine Daten geschützt?',
            'answer' => 'Kfz Digital ist für eine geschützte Verarbeitung von Konto-, Fahrzeug- und Vorgangsdaten ausgelegt. Verwenden Sie ein sicheres Passwort und melden Sie sich auf fremden Geräten immer ab.',
        ],

        [
            'question' => 'Soll ich meine Zugangsdaten weitergeben?',
            'answer' => 'Nein. Geben Sie Ihr Passwort niemals an andere Personen weiter. Mitarbeiter von Kfz Digital werden Sie nicht nach Ihrem vollständigen Passwort fragen.',
        ],

        [
            'question' => 'Was muss ich bei einem fremden Computer beachten?',
            'answer' => 'Melden Sie sich nach der Nutzung vollständig ab. Verwenden Sie außerdem möglichst keine Funktion zum Speichern des Passworts im Browser.',
        ],
    ],

    'i-Kfz und Zulassungsstelle' => [
        [
            'question' => 'Was ist i-Kfz?',
            'answer' => 'i-Kfz steht für internetbasierte Fahrzeugzulassung. Dabei können bestimmte Fahrzeugzulassungsvorgänge digital vorbereitet oder durchgeführt werden, sofern die jeweiligen Voraussetzungen erfüllt sind.',
        ],

        [
            'question' => 'Wer entscheidet über meinen Zulassungsvorgang?',
            'answer' => 'Die zuständige Zulassungsbehörde entscheidet über den Antrag. Kfz Digital kann die Datenerfassung, Dokumentenverwaltung und Statusanzeige unterstützen.',
        ],

        [
            'question' => 'Warum kann ein Vorgang möglicherweise nicht online abgeschlossen werden?',
            'answer' => 'Nicht jeder Vorgang erfüllt automatisch die technischen oder rechtlichen Voraussetzungen für eine vollständige Online-Bearbeitung. Gründe können fehlende Unterlagen, fehlende Sicherheitscodes, eine notwendige Identitätsprüfung oder Vorgaben der zuständigen Behörde sein.',
        ],

        [
            'question' => 'Ersetzt Kfz Digital den Behördengang immer vollständig?',
            'answer' => 'Das hängt vom jeweiligen Vorgang und den Voraussetzungen ab. Wenn eine digitale Bearbeitung nicht möglich ist, kann ein persönlicher Termin bei der zuständigen Zulassungsstelle erforderlich sein.',
        ],
    ],
];


/*
|--------------------------------------------------------------------------
| Suchfunktion
|--------------------------------------------------------------------------
*/

$searchTerm = trim(
    (string)($_GET['suche'] ?? '')
);

$selectedCategory = trim(
    (string)($_GET['kategorie'] ?? '')
);

$visibleCategories = $faqCategories;


/*
 * Nach Kategorie filtern
 */

if (
    $selectedCategory !== ''
    && array_key_exists($selectedCategory, $faqCategories)
) {
    $visibleCategories = [
        $selectedCategory => $faqCategories[$selectedCategory],
    ];
}


/*
 * Nach Suchbegriff filtern
 */

if ($searchTerm !== '') {

    $searchLower = mb_strtolower($searchTerm);
    $filteredCategories = [];

    foreach ($visibleCategories as $categoryName => $questions) {

        $matchingQuestions = [];

        foreach ($questions as $faq) {

            $searchContent = mb_strtolower(
                $faq['question'] . ' ' . $faq['answer']
            );

            if (str_contains($searchContent, $searchLower)) {
                $matchingQuestions[] = $faq;
            }
        }

        if ($matchingQuestions !== []) {
            $filteredCategories[$categoryName] =
                $matchingQuestions;
        }
    }

    $visibleCategories = $filteredCategories;
}

$totalQuestions = 0;

foreach ($visibleCategories as $questions) {
    $totalQuestions += count($questions);
}
?>

<section
    class="kfz-section kfz-faq-page"
    aria-labelledby="faq-title"
>

    <div class="container">

        <!-- Kopfbereich -->

        <div class="kfz-faq-hero">

            <div class="kfz-faq-hero-content">

                <span class="kfz-section-kicker">
                    Kfz Digital
                </span>

                <h1
                    id="faq-title"
                    class="kfz-section-title"
                >
                    Häufig gestellte Fragen
                </h1>

                <p class="kfz-section-text">
                    Hier finden Sie Antworten zu Ihrem Konto, Ihren Fahrzeugen,
                    Fahrzeugvorgängen und der digitalen Zulassung.
                </p>

            </div>

            <div class="kfz-faq-hero-icon">

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


        <!-- Suche -->

        <div class="kfz-faq-search-card">

            <form
                action="<?= $escape($url('/faq/')) ?>"
                method="get"
                class="kfz-faq-search-form"
            >

                <label
                    for="faq-search"
                    class="kfz-form-label"
                >
                    Fragen durchsuchen
                </label>

                <div class="kfz-faq-search-row">

                    <div class="kfz-faq-search-input-wrapper">

                        <svg
                            class="kfz-faq-search-icon"
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
                            id="faq-search"
                            name="suche"
                            class="kfz-form-control"
                            value="<?= $escape($searchTerm) ?>"
                            placeholder="Zum Beispiel: Passwort oder Fahrzeug"
                            autocomplete="off"
                        >

                    </div>

                    <button
                        type="submit"
                        class="kfz-button kfz-button-primary"
                    >
                        Suchen
                    </button>

                    <?php if (
                        $searchTerm !== ''
                        || $selectedCategory !== ''
                    ): ?>

                        <a
                            href="<?= $escape($url('/faq/')) ?>"
                            class="kfz-button kfz-button-outline"
                        >
                            Zurücksetzen
                        </a>

                    <?php endif; ?>

                </div>

            </form>

        </div>


        <!-- Kategorien -->

        <div class="kfz-faq-categories">

            <a
                href="<?= $escape($url('/faq/')) ?>"
                class="kfz-faq-category-link <?= $selectedCategory === '' ? 'is-active' : '' ?>"
                <?= $selectedCategory === '' ? 'aria-current="page"' : '' ?>
            >
                Alle Fragen
            </a>

            <?php foreach ($faqCategories as $categoryName => $questions): ?>

                <a
                    href="<?= $escape(
                        $url(
                            '/faq/?kategorie='
                            . rawurlencode($categoryName)
                        )
                    ) ?>"
                    class="kfz-faq-category-link <?= $selectedCategory === $categoryName ? 'is-active' : '' ?>"
                    <?= $selectedCategory === $categoryName ? 'aria-current="page"' : '' ?>
                >
                    <?= $escape($categoryName) ?>
                </a>

            <?php endforeach; ?>

        </div>


        <!-- Ergebnisinformation -->

        <?php if ($searchTerm !== ''): ?>

            <div
                class="kfz-faq-result-info"
                role="status"
            >
                Suchergebnisse für:
                <strong>
                    <?= $escape($searchTerm) ?>
                </strong>

                <?php if ($totalQuestions > 0): ?>

                    <span>
                        – <?= $escape((string)$totalQuestions) ?>
                        <?= $totalQuestions === 1
                            ? 'Treffer'
                            : 'Treffer' ?>
                    </span>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- FAQ-Inhalte -->

        <?php if ($visibleCategories === []): ?>

            <div
                class="kfz-faq-no-results"
                role="status"
            >

                <div class="kfz-faq-no-results-icon">
                    ?
                </div>

                <h2>
                    Keine passende Antwort gefunden
                </h2>

                <p>
                    Versuchen Sie einen anderen Suchbegriff oder nehmen Sie
                    direkt Kontakt mit Kfz Digital auf.
                </p>

                <div class="kfz-faq-no-results-actions">

                    <a
                        href="<?= $escape($url('/faq/')) ?>"
                        class="kfz-button kfz-button-outline"
                    >
                        Alle Fragen anzeigen
                    </a>

                    <a
                        href="<?= $escape($url('/kontakt/')) ?>"
                        class="kfz-button kfz-button-primary"
                    >
                        Kontakt aufnehmen
                    </a>

                </div>

            </div>

        <?php else: ?>

            <div class="kfz-faq-content">

                <?php $faqNumber = 0; ?>

                <?php foreach (
                    $visibleCategories
                    as $categoryName => $questions
                ): ?>

                    <section
                        class="kfz-faq-category"
                        aria-labelledby="category-<?= $escape(
                            (string)$faqNumber
                        ) ?>"
                    >

                        <div class="kfz-faq-category-header">

                            <span class="kfz-section-kicker">
                                Fragen und Antworten
                            </span>

                            <h2
                                id="category-<?= $escape(
                                    (string)$faqNumber
                                ) ?>"
                            >
                                <?= $escape($categoryName) ?>
                            </h2>

                        </div>


                        <div class="kfz-faq-list">

                            <?php foreach (
                                $questions
                                as $questionIndex => $faq
                            ): ?>

                                <?php
                                $faqId = 'faq-answer-'
                                    . $faqNumber
                                    . '-'
                                    . $questionIndex;
                                ?>

                                <article class="kfz-faq-item">

                                    <button
                                        type="button"
                                        class="kfz-faq-question"
                                        aria-expanded="false"
                                        aria-controls="<?= $escape(
                                            $faqId
                                        ) ?>"
                                    >

                                        <span>
                                            <?= $escape(
                                                $faq['question']
                                            ) ?>
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
                                            <?= $escape(
                                                $faq['answer']
                                            ) ?>
                                        </p>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                    <?php $faqNumber++; ?>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <!-- Kontaktbereich -->

        <section
            class="kfz-faq-contact"
            aria-labelledby="faq-contact-title"
        >

            <div class="kfz-faq-contact-icon">

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

                <h2 id="faq-contact-title">
                    Wir helfen Ihnen gerne weiter.
                </h2>

                <p>
                    Wenn Sie keine passende Antwort gefunden haben,
                    können Sie uns direkt kontaktieren.
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

            const currentlyExpanded =
                question.getAttribute('aria-expanded') === 'true';

            document
                .querySelectorAll('.kfz-faq-question')
                .forEach(function (otherQuestion) {
                    if (otherQuestion === question) {
                        return;
                    }

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
                });

            question.setAttribute(
                'aria-expanded',
                String(!currentlyExpanded)
            );

            answer.hidden = currentlyExpanded;

            const icon = question.querySelector(
                '.kfz-faq-question-icon'
            );

            if (icon) {
                icon.textContent = currentlyExpanded
                    ? '+'
                    : '−';
            }
        });
    });
});
</script>
