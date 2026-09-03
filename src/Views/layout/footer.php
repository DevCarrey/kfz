<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Support/view_helpers.php';

$url = static fn (string $path): string => kfz_url($path);

$escape = static fn (string $value): string => kfz_escape($value);
?>

</div><!-- /.page-content -->
</main><!-- /#content -->

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
                    Bereiten Sie die Abmeldung Ihres Fahrzeugs bequem
                    und übersichtlich online vor.
                </p>

            </div>


            <div>

                <h2>Leistung</h2>

                <ul>

                    <li>
                        <a
                            href="<?= $escape($url('/vorgang-starten/?vorgang=abmeldung')) ?>"
                        >
                            Fahrzeug abmelden
                        </a>
                    </li>


                </ul>

            </div>


            <div>

                <h2>
                    Informationen
                </h2>

                <ul>

                    <li>
                        <a
                            href="<?= $escape($url('/vorgaenge/')) ?>"
                        >
                            Meine Vorgänge
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/fahrzeuge/')) ?>"
                        >
                            Meine Fahrzeuge
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/hilfe/')) ?>"
                        >
                            Hilfe
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/faq/')) ?>"
                        >
                            FAQ
                        </a>
                    </li>

                </ul>

            </div>


            <div>

                <h2>
                    Rechtliches
                </h2>

                <ul>

                    <li>
                        <a
                            href="<?= $escape($url('/kontakt/')) ?>"
                        >
                            Kontakt
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/ueber-kfz-digital/')) ?>"
                        >
                            Über Kfz Digital
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/impressum/')) ?>"
                        >
                            Impressum
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/datenschutz/')) ?>"
                        >
                            Datenschutz
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?= $escape($url('/nutzungsbedingungen/')) ?>"
                        >
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
    src="<?= $escape($url('/public/assets/js/header.js')) ?>"
    defer>
</script>

</body>
</html>
