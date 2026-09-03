# Kfz Digital – Architektur

Kfz Digital fokussiert auf die sichere Vorbereitung von Fahrzeugabmeldungen.

## Verzeichnisübersicht

- `public/`: ausschließlich auslieferbare CSS-, JavaScript- und Bilddateien.
- `src/Bootstrap/`: zentraler Anwendungsstart, insbesondere Session-Initialisierung.
- `src/Config/`: Konfiguration und Datenbankverbindung.
- `src/Support/`: kleine, wiederverwendbare View- und URL-Helfer.
- `src/Services/`: fachliche Dienste; externe i-Kfz-Anbindungen bleiben bis zur offiziellen Dokumentation im Mock-Modus.
- `src/Views/layout/`: gemeinsamer Header, Footer und Metadaten.
- `src/Views/pages/`: geroutete Seiten.
- `database/migrations/`: versionierbare Datenbankschema-Änderungen.
- `storage/documents/`: späterer privater Dokumentenspeicher; direkter Apache-Zugriff ist gesperrt.
- `storage/logs/`: Anwendungsprotokolle ohne Passwörter oder Dokumentinhalte.

## Ablauf einer Fahrzeugabmeldung

1. Angemeldeter Nutzer öffnet `/vorgang-starten/`.
2. Formular prüft CSRF-Token, Fahrzeugbesitz und Eingaben serverseitig.
3. Ein Entwurf wird gemeinsam mit dem ersten Statusverlauf in einer Datenbanktransaktion gespeichert.
4. Die Detailansicht unter `/vorgaenge/?id=…` prüft erneut die Benutzerzuordnung.
5. Eine spätere Übermittlung wird ausschließlich über einen Mock-Adapter vorbereitet, bis offizielle i-Kfz-/GKS-Unterlagen vorliegen.
