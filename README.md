# Mein Berichtsheft 📓✨

> **Digitales Ausbildungs- und Praktikumsberichtsheft mit Git-basierter Datenhaltung im realistischen Notizbuch-Design.**

---

## 📖 Übersicht

**Mein Berichtsheft** ist eine moderne Webanwendung zur vollständigen digitalen Führung von Ausbildungsnachweisen (Berichtsheft) gemäß den Anforderungen der IHK / HWK. 

Die Anwendung verbindet ein haptisch wirkendes, von traditionellen Berichtsheften und Heftern inspiriertes UI-Design („Paper & Ink“) mit einer innovativen und manipulationssicheren Speicherarchitektur: **Die eigentlichen Berichte werden nicht in einer SQL-Datenbank abgelegt, sondern strukturiert als JSON-Dateien direkt in einem GitLab-Repository versioniert**.

### ✨ Kernmerkmale

- 🎨 **Authentisches Notizbuch-Design:** Skewmorphe, klare Papierästhetik (`Paper & Ink`), Register-Tabs, dynamische Stempel und Status-Pins mit nahtlosem Light-/Dark-Mode (Tailwind CSS v4).
- 📅 **Interaktiver Kalender:** Monatsübersicht mit visueller Statusanzeige jedes Tages (Erfasst, Offen, Berufsschule, Urlaub, Krank, Feiertag).
- 📝 **Tagesberichte & Zeitraum-Erfassung:** Erfassung von Tätigkeiten, Dauer, Abteilung, Lernschritten oder Abwesenheitsgründen. Unterstützung für Sammelerfassung über Datumsbereiche.
- 🗂️ **Wochenberichte & Arbeitsnachweise:** Automatische Aggregation von Montag bis Freitag in übersichtlichen Wochenblättern.
- ✍️ **Digitale Unterschriften (Canvas):** Zeichnen von Unterschriften direkt im Browser (für Azubi und Ausbilder) ohne externe Bibliotheken.
- 🔒 **GitLab als Datenspeicher (Git-backed):** Automatische Versionierung, transparente Historie und Datensouveränität. SQL wird ausschließlich für Benutzer- und Authentifizierungsdaten verwendet.
- 🖨️ **Druck- & PDF-Export:** Perfekt auf DIN A4 optimierte Druckansichten für Einzelwochen sowie das gesamte Ausbildungsnachweis-Heft inklusive Deckblatt.
- 👥 **Rollenbasiertes Rechtesystem:** Maßgeschneiderte Oberflächen und Workflows für **Auszubildende (Azubi)** und **Ausbilder**.

---

## 🏗️ Architektur & Technologie-Stack

### Backend & Framework
- **PHP 8.3+**
- **Laravel 12 / 13**
- **Laravel Breeze** (Authentifizierung)
- **Barryvdh Laravel DomPDF** (Ergänzender PDF-Support)

### Frontend & Styling
- **Laravel Blade Components** (Strenge Komponentenstruktur)
- **Tailwind CSS v4** (CSS-Tokens via `@theme`, Native CSS-Variablen)
- **Vanilla JavaScript** (Theme-Toggle, HTML5 Canvas für Unterschriften, dynamische Formularfelder)

### Datenhaltung & Storage-Konzept
- **Relational DB (SQLite / MySQL / MariaDB):** Speichert ausschließlich `users`-Stammdaten, Login-Informationen und den zugeordneten `gitlab_path`.
- **GitLab REST API (`GitLabService` & `ReportRepository`):** Speichert Tages- und Wochenberichte als JSON-Dateien direkt im Ziel-Repository.

---

## 👥 Rollen & Workflow

```
[Azubi]
  │
  ├─► Tagesberichte ausfüllen / Tage kategorisieren (Arbeit, Schule, Urlaub, Krank, Feiertag)
  ├─► Wochenbericht prüfen & digital unterschreiben
  └─► Wochenbericht einreichen (Status: Draft ➔ Submitted)
        │
        ▼
[Ausbilder]
  │
  ├─► Übersicht „Meine Azubis“ & eingereichte Wochen prüfen
  ├─► Wochenbericht digital gegenzeichnen (Status: Submitted ➔ Signed)
  └─► Gesamten Ausbildungsnachweis als PDF/Druck exportieren
```

### 1. Auszubildender (Azubi)
- **Kalenderansicht (`/calendar`):** Direkte Übersicht aller Tage des Monats mit Farbkennzeichnung.
- **Tageserfassung:** 
  - *Arbeitstag:* Tätigkeiten, Zeitaufwand, Abteilung, Lernschritte.
  - *Besondere Tage:* Berufsschule, Urlaub, Krankheit (inkl. AU-Notiz), Feiertag.
  - *Zeitraumfunktion:* Z. B. 2 Wochen Urlaub oder Berufsschulblock mit einem Klick erfassen.
- **Wochenabschluss:** Zusammenfassung prüfen, digital unterschreiben und zur Freigabe an den Ausbilder übermitteln.

### 2. Ausbilder
- **Azubi-Verwaltung (`/azubis`):** Übersicht aller zugewiesenen Auszubildenden mit Zählern für offene, eingereichte und signierte Wochen.
- **Prüfung & Freigabe (`/weekly-reports`):** Einsehen der Tageseinträge und digitale Freigabe per Unterschrift.
- **Ausbildungsnachweis-Druck (`/training-record/{user}/print`):** Kompletter Ausbildungsnachweis inklusive Deckblatt für Zwischen- und Abschlussprüfungen.

---

## 📁 Repository- & JSON-Dateistruktur (GitLab)

Jeder Auszubildende besitzt einen eigenen Ordnerpfad (`gitlab_path`, z. B. `azubis/mustermann-max`). Die Berichte werden nach ISO-Kalenderwochen strukturiert:

```text
{gitlab_path}/
└── 2026/
    ├── KW-40/
    │   ├── 2026-09-28.json              # Tagesbericht Montag
    │   ├── 2026-09-29.json              # Tagesbericht Dienstag
    │   ├── ...
    │   └── KW-40 Wochenbericht.json     # Wochenbericht-Status, Metadaten & Signaturen
    └── KW-41/
        └── ...
```

### Beispiel: Tagesbericht (`2026-09-28.json`)
```json
{
  "datum": "2026-09-28",
  "typ": "work",
  "taetigkeiten": "Entwicklung der GitLab-API-Anbindung für das Berichtsheft.",
  "dauer": "8 h",
  "abteilung": "Softwareentwicklung",
  "lernziele": [
    "REST-API-Integration",
    "Repository Pattern in Laravel"
  ]
}
```

### Beispiel: Abwesenheit / Schule (`2026-09-29.json`)
```json
{
  "datum": "2026-09-29",
  "typ": "sick",
  "notiz": "AU bis 02.10. eingereicht"
}
```

### Beispiel: Wochenbericht (`KW-40 Wochenbericht.json`)
```json
{
  "jahr": 2026,
  "kalenderwoche": 40,
  "azubi": "Max Mustermann",
  "berichtsnummer": 38,
  "status": "signed",
  "eingereicht_am": "2026-10-02T16:30:00+02:00",
  "unterschrieben_am": "2026-10-05T09:15:00+02:00",
  "unterschrieben_von": {
    "id": 1,
    "name": "Alex Trainer"
  },
  "azubi_unterschrift": {
    "lines": [...]
  },
  "ausbilder_unterschrift": {
    "lines": [...]
  }
}
```

---

## 🧩 Wichtige Blade-Komponenten

Die Benutzeroberfläche basiert auf wiederverwendbaren Blade-Komponenten:

| Komponente | Tag | Beschreibung |
|---|---|---|
| **Notebook Shell** | `<x-notebook :tabs="...">` | Simuliert das aufgeschlagene Heft mit Slots für linke/rechte Seite |
| **Page Header** | `<x-notebook.page-header>` | Einheitliche Kopfzeile mit exakter Faltlinien-Ausrichtung |
| **Register Tabs** | `<x-notebook.tabs>` | Haptische Reiter zur Navigation zwischen Bereichen |
| **Stempel** | `<x-stamp status="..." />` | Visueller Statusstempel (`recorded`, `open`, `school`, `vacation`, `sick`, `holiday`, `pending`, `signed`) |
| **Tagestyp-Auswahl** | `<x-calendar.day-type>` | Umschalter zwischen Arbeitszeiten und Fehl-/Schulzeiten |
| **Wochenblatt** | `<x-weekly-report.sheet>` | DIN A4-konforme Darstellung der gesamten Kalenderwoche |
| **Signatur** | `<x-weekly-report.signature>` | Canvas-basierte Anzeige und Erfassung digitaler Unterschriften |
| **Ausbildungsnachweis**| `<x-training-record.cover>` | IHK-konformes Deckblatt des gesamten Berichtshefts |

---

## ⚙️ Installation & Einrichtung

### Voraussetzungen
- PHP >= 8.3 (mit Extensions: `curl`, `json`, `mbstring`, `pdo_sqlite` bzw. `pdo_mysql`)
- Composer
- Node.js & npm
- GitLab-Account & Access Token (mit `api` bzw. `read_repository` / `write_repository` Rechten)
- DDEV / Docker (optional, aber empfohlen)

### 1. Repository klonen & Abhängigkeiten installieren
```bash
git clone <repository-url> mein-berichtsheft
cd mein-berichtsheft

composer install
npm install
```

### 2. Umgebungskonfiguration (`.env`)
Kopiere die Vorlage und generiere den App-Key:
```bash
cp .env.example .env
php artisan key:generate
```

Konfiguriere die GitLab-Anbindung in deiner `.env`:
```dotenv
DB_CONNECTION=sqlite

# GitLab API Konfiguration
GITLAB_URL=https://gitlab.example.com
GITLAB_TOKEN=dein-personal-access-token
GITLAB_PROJECT_ID=12345
GITLAB_BRANCH=main
```

### 3. Datenbank migrieren und Testdaten anlegen
```bash
php artisan migrate --seed
```

Der Seeder (`DatabaseSeeder`) richtet standardmäßig Test-Accounts ein (konfigurierbar über `.env`):
- **Ausbilder:** `ausbilder@example.com` (Passwort: `password`)
- **Azubi 1:** `azubi@example.com` (Passwort: `password`)
- **Azubi 2:** `erika.musterfrau@example.com` (Passwort: `password`)

### 4. Assets kompilieren & Server starten
```bash
# Im Entwicklungsmodus:
npm run dev

# In einem separaten Terminal:
php artisan serve
```

---

## 🛠️ Nützliche Artisan-Befehle

- **Tests ausführen:**
  ```bash
  php artisan test
  ```
- **Code-Formatting (Pint):**
  ```bash
  ./vendor/bin/pint
  ```

---

## 📄 Lizenz

Dieses Projekt steht unter der [MIT-Lizenz](LICENSE).
