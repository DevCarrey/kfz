<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);
$escape = static fn (mixed $value): string => kfz_escape($value);
?>

</div>
</main>

<footer class="kfz-footer">

    <div class="container">

        <div class="kfz-footer-grid">

            <div>
                <a
                    href="<?= $escape($url('/')) ?>"
                    class="kfz-footer-brand"
                >
                    Kfz Digital
                </a>

                <p>
                    Fahrzeug abmelden – einfach, sicher und digital.
                </p>

                <p>
                    Starten Sie Ihren Abmeldevorgang ohne Registrierung
                    direkt online.
                </p>
            </div>


            <div>
                <h2>Service</h2>

                <ul>
                    <li>
                        <a href="<?= $escape($url('/')) ?>">
                            Fahrzeug abmelden
                        </a>
                    </li>

                    <li>
                        <a href="<?= $escape(
                            $url('/vorgang-pruefen/')
                        ) ?>">
                            Vorgang prüfen
                        </a>
                    </li>

                    <li>
                        <a href="<?= $escape($url('/hilfe/')) ?>">
                            Hilfe
                        </a>
                    </li>

                    <li>
                        <a href="<?= $escape($url('/faq/')) ?>">
                            FAQ
                        </a>
                    </li>
                </ul>
            </div>


            <div>
                <h2>Informationen</h2>

                <ul>
                    <li>
                        <a href="<?= $escape(
                            $url('/ueber-kfz-digital/')
                        ) ?>">
                            Über Kfz Digital
                        </a>
                    </li>

                    <li>
                        <a href="<?= $escape($url('/kontakt/')) ?>">
                            Kontakt
                        </a>
                    </li>

                    <li>
                        <a href="<?= $escape($url('/impressum/')) ?>">
                            Impressum
                        </a>
                    </li>
                </ul>
            </div>


            <div>
                <h2>Rechtliches</h2>

                <ul>
                    <li>
                        <a href="<?= $escape($url('/datenschutz/')) ?>">
                            Datenschutz
                        </a>
                    </li>

                    <li>
                        <a href="<?= $escape(
                            $url('/nutzungsbedingungen/')
                        ) ?>">
                            Nutzungsbedingungen
                        </a>
                    </li>
                </ul>
            </div>

        </div>


        <div class="kfz-footer-bottom">
            <span>
                &copy; <?= date('Y') ?> Kfz Digital.
                Alle Rechte vorbehalten.
            </span>
        </div>

    </div>

</footer>

<script
    src="<?= $escape(
        $url('/public/assets/js/header.js')
    ) ?>"
    defer
></script>

</body>
</html>