# Konzept: Digitales Berichtsheft

**Projekt:** Webanwendung zur digitalen Führung des Ausbildungs- und Praktikumsberichtshefts  
**Erstellt von:** Fatih Ayyildiz, Akhmad Gazimagomedov  
**Datum:** 11.08.2026

---

## 1. Einleitung

Das Projekt ist eine Webanwendung zur digitalen Führung des Berichtshefts während der Ausbildung beziehungsweise des Praktikums.

Auszubildende können ihre Tätigkeiten täglich dokumentieren und ihre Berichte übersichtlich verwalten. Am Ende einer Woche kann der Wochenbericht vom Auszubildenden und anschließend vom Ausbilder digital unterschrieben werden.

Ein besonderer Bestandteil des Projekts ist die Speicherung der Berichte in strukturierten Dateien und deren Versionsverwaltung mit Git. Dadurch bleiben die Berichte nicht nur für die Anwendung nutzbar, sondern sind auch außerhalb der Anwendung als nachvollziehbare Dateien verfügbar.

---

## 2. Ausgangslage

Das Berichtsheft wird häufig noch auf Papier oder mit Textverarbeitungsprogrammen wie Microsoft Word geführt. Dadurch entstehen verschiedene Nachteile:

- Papierberichte können verloren gehen oder beschädigt werden.
- Unterschriften sind häufig nur persönlich vor Ort möglich.
- Berichte müssen ausgedruckt, abgeheftet und verwaltet werden.
- Änderungen und die Suche nach älteren Berichten sind umständlich.
- Die Verwaltung mehrerer Auszubildender verursacht zusätzlichen organisatorischen Aufwand.

Eine digitale Lösung kann diesen Aufwand reduzieren. Die Berichte sind zentral verfügbar, können strukturiert verwaltet und versioniert werden.

---

## 3. Zielsetzung

Ziel des Projekts ist die Entwicklung einer schlanken Webanwendung, mit der das Berichtsheft vollständig digital geführt werden kann.

Die Anwendung soll insbesondere ermöglichen:

- tägliche Tätigkeiten zu dokumentieren,
- bestehende Einträge zu bearbeiten und zu löschen,
- Berichte nach Datum und Ausbildungswoche zu organisieren,
- Tagesberichte als strukturierte JSON- und Markdown-Dateien zu speichern,
- Wochenberichte aus den Tagesberichten zu erstellen,
- die Berichte verpflichtend in einem Git-Repository zu speichern,
- Änderungen der Berichte mit Git zu versionieren,
- Wochenberichte digital zu unterschreiben,
- einen Wochenbericht zuerst vom Auszubildenden und anschließend vom Ausbilder unterschreiben zu lassen,
- die Berichte über einen visuellen Kalender zu verwalten.

---

# 4. Nutzer und Rollen

Die Anwendung kennt zwei Rollen:

- **Auszubildender**
- **Ausbilder**

Für die Benutzerverwaltung sind zwei unterschiedliche Konzepte möglich.

## 4.1 Variante A – Benutzer wird vom Ausbilder angelegt

Der Ausbilder erstellt ein Benutzerkonto für einen neuen Auszubildenden.

Dabei werden die benötigten Stammdaten eingegeben und ein eigenes Git-Repository für den Auszubildenden eingerichtet.

Der Auszubildende erhält anschließend seine Zugangsdaten und kann sich anmelden.

### Vorteile

- Der Ausbilder kontrolliert, welche Benutzer Zugriff auf das System erhalten.
- Die Zuordnung zwischen Auszubildendem, Berichtsheft und Git-Repository ist von Anfang an festgelegt.
- Geeignet für eine betriebliche Anwendung.

---

## 4.2 Variante B – Auszubildender registriert sich selbst

Alternativ kann sich ein Auszubildender selbst registrieren.

Nach der Registrierung werden sein Benutzerkonto und sein persönliches Berichtsheft eingerichtet. Dazu gehört auch das entsprechende Git-Repository.

Der Ausbilder erhält anschließend Zugriff auf die Berichte des Auszubildenden.

### Vorteile

- Weniger Verwaltungsaufwand für den Ausbilder.
- Benutzer können ihr Konto selbst erstellen.
- Die Anwendung kann auch außerhalb eines einzelnen Betriebs leichter verwendet werden.

---

## 4.3 Entscheidung

Beide Varianten sind technisch möglich.

Welche Variante tatsächlich umgesetzt wird, soll nach der Präsentation des Konzepts gemeinsam mit dem Ausbilder entschieden werden.

---

# 5. Funktionen

## 5.1 Login und Benutzerverwaltung

Die Anwendung bietet einen Login für Auszubildende und Ausbilder.

Die Benutzerrolle bestimmt die verfügbaren Funktionen und Zugriffsrechte.

---

## 5.2 Tagesberichte

Der Auszubildende kann für einen bestimmten Tag einen Tagesbericht erstellen.

Ein Tagesbericht enthält beispielsweise:

- Datum
- Wochentag
- Ausbildungsjahr
- Ausbildungswoche
- Tätigkeiten
- neu erlernte Inhalte
- Probleme oder offene Fragen

Das Datum wird standardmäßig auf den aktuellen Tag gesetzt, kann aber geändert werden.

Der Wochentag, das Ausbildungsjahr und die Ausbildungswoche werden automatisch aus den vorhandenen Daten berechnet.

---

## 5.3 Erstellung des Wochenberichts

Der Wochenbericht wird automatisch aus den vorhandenen Tagesberichten einer Kalenderwoche erstellt.

Dafür werden die Tagesberichte der betreffenden Woche gesammelt. Die Inhalte der Felder **Tätigkeiten**, **Gelernt** und **Probleme** werden den jeweiligen Wochentagen zugeordnet und in einer gemeinsamen Tabelle dargestellt.

Der Wochenbericht enthält für jeden Arbeitstag:

- Datum,
- Wochentag,
- ausgeführte Tätigkeiten,
- Unterweisungen beziehungsweise Gelerntes,
- Probleme oder offene Fragen.

Die Darstellung orientiert sich an einem klassischen Wochennachweis: Die fünf Arbeitstage von Montag bis Freitag werden als einzelne Zeilen der Tabelle dargestellt.

Zusätzlich enthält der Wochenbericht:

- Name des Auszubildenden,
- Ausbildungsberuf,
- Betrieb,
- Abteilung,
- Kalenderwoche und Jahr,
- Berichtszeitraum,
- Bereich für die digitale Unterschrift des Auszubildenden,
- Datum der Unterschrift des Auszubildenden,
- Bereich für die digitale Unterschrift des Ausbilders,
- Datum der Unterschrift des Ausbilders.

Der Wochenbericht wird zunächst als strukturierte Daten erzeugt und anschließend als **Markdown-Datei und JSON-Datei** im Git-Repository des Auszubildenden gespeichert.

Die Markdown-Datei kann als Grundlage für die Darstellung im Browser und für den späteren PDF-Export verwendet werden.

---

## 5.4 Visueller Kalender

Die Anwendung soll einen visuellen Kalender enthalten.

Der Kalender dient als zentrale Übersicht über die Tagesberichte.

Er kann beispielsweise anzeigen:

- welche Tage bereits einen Tagesbericht enthalten,
- an welchen Tagen noch ein Bericht fehlt,
- zu welcher Ausbildungswoche ein Tag gehört,
- welche Wochen bereits abgeschlossen wurden.

Durch Auswahl eines Tages kann der entsprechende Tagesbericht geöffnet oder neu erstellt werden.

Der Kalender soll zunächst mit **HTML, CSS und JavaScript** umgesetzt werden. Eine zusätzliche Kalenderbibliothek ist für die erste Version nicht vorgesehen.

---

## 5.5 Abschluss und Unterschriften

Nach der Erstellung des Wochenberichts überprüft der Auszubildende die zusammengefassten Angaben.

Anschließend wird der Wochenbericht vom Auszubildenden digital unterschrieben und mit dem Datum der Unterschrift versehen.

Danach überprüft der Ausbilder den Wochenbericht und unterschreibt ihn ebenfalls digital mit Datum.

Erst nachdem beide Unterschriften vorhanden sind, gilt der Wochenbericht als abgeschlossen.

---

## 5.6 Digitale Unterschrift

Die digitale Unterschrift wird ohne zusätzliche Bibliothek umgesetzt.

Dafür werden verwendet:

- HTML `<canvas>`
- JavaScript
- Maus-, Touch- und Pointer-Eingaben

Die gezeichnete Unterschrift wird anschließend zusammen mit dem Wochenbericht gespeichert.

---

# 6. Datenmodell

Die Anwendung verwendet zwei unterschiedliche Bereiche für die Datenspeicherung.

### MariaDB

MariaDB enthält ausschließlich die für die Anwendung benötigten Benutzer- und Verwaltungsdaten.

### Git Repository

Die eigentlichen Tages- und Wochenberichte werden als Dateien gespeichert und **müssen in Git versioniert werden**.

Damit ist Git ein zentraler Bestandteil der Datenhaltung des Projekts und nicht nur eine optionale Zusatzfunktion.

---

## 6.1 Benutzerdaten in MariaDB

Für einen Benutzer werden beispielsweise folgende Informationen gespeichert:

|Feld|Beschreibung|
|---|---|
|`name`|Name des Benutzers|
|`password`|Passwort bzw. Passwort-Hash|
|`role`|Rolle des Benutzers|
|`ausbildungsberuf`|Ausbildungsberuf|
|`betrieb`|Ausbildungsbetrieb|
|`abteilung`|Abteilung|
|`ausbildungsbeginn`|Beginn der Ausbildung|
|`repository_path`|Zuordnung zum Git-Repository|

Die Benutzerdaten werden in MariaDB gespeichert.

---

# 7. Speicherung der Berichte

Die Berichte werden **nicht in MariaDB gespeichert**.

Stattdessen werden Tages- und Wochenberichte als Dateien gespeichert.

Für jeden Auszubildenden gibt es ein eigenes Git-Repository.

Eine mögliche Struktur ist:

```text
repository/
├── profil.json
│
├── tagesberichte/
│   ├── 2026-08-10.json
│   ├── 2026-08-10.md
│   ├── 2026-08-11.json
│   ├── 2026-08-11.md
│   └── ...
│
└── wochenberichte/
    ├── 2026-KW32.json
    ├── 2026-KW32.md
    └── ...
```

---

## 7.1 profil.json

Für jedes Benutzer-Repository wird eine `profil.json` gespeichert. Sie enthält die aktuell gültigen Stammdaten des jeweiligen Auszubildenden.

Beispiel:

```json
{
  "name": "Fatih Ayyildiz",
  "ausbildungsberuf": "Fachinformatiker Anwendungsentwicklung",
  "betrieb": "artif GmbH & Co. KG",
  "abteilung": "Entwicklung",
  "ausbildungsbeginn": "2026-09-01"
}
```

Die `profil.json` dient als zentrale Quelle für die aktuellen Stammdaten innerhalb des Git-Repositories.

Wenn sich beispielsweise die Abteilung des Auszubildenden ändert, wird die `profil.json` entsprechend aktualisiert.

Bereits erstellte Tages- und Wochenberichte werden dadurch jedoch nicht verändert. Die zum Zeitpunkt der Erstellung gültigen Stammdaten werden zusätzlich direkt im jeweiligen Bericht gespeichert.

---

## 7.2 JSON

JSON ist der strukturierte Datensatz eines Berichts.

Er kann von Laravel problemlos gelesen und verarbeitet werden.

Beispiel:

```json
{
  "type": "tagesbericht",
  "datum": "2026-08-11",
  "wochentag": "Dienstag",
  "ausbildungsjahr": 1,
  "ausbildungswoche": 3,
  "taetigkeiten": "...",
  "gelernt": "...",
  "probleme": "..."
}
```

Sowohl Tagesberichte als auch Wochenberichte werden als JSON gespeichert. 

Bei einem Wochenbericht enthält die JSON-Datei zusätzlich die Zuordnung der Tagesberichte zur jeweiligen Kalenderwoche sowie die Informationen zum Abschluss und zu den Unterschriften.

---

## 7.3 Markdown

Für jeden Bericht wird zusätzlich eine Markdown-Datei erzeugt.

Markdown bietet eine einfache, menschenlesbare Darstellung der Daten.

Beispielsweise:

```markdown
# Tagesbericht – 11.08.2026

## Tätigkeiten

...

## Gelernt

...

## Probleme

...
```

Dadurch können die Berichte direkt in Git-Plattformen oder Markdown-Editoren gelesen werden.

Die Markdown-Datei enthält ein YAML Frontmatter mit den zum Zeitpunkt der Erstellung gültigen Stammdaten. 

Dadurch bleiben historische Angaben, beispielsweise eine frühere Abteilung, auch nach einer Änderung des Benutzerprofils erhalten.

---

## 7.4 Metadaten im Markdown Frontmatter

Jede Markdown-Datei enthält zusätzlich ein YAML Frontmatter mit den Stammdaten, die zum Zeitpunkt der Erstellung des Berichts gültig waren.

Beispiel:

```yaml
---
name: Akhmad Gazimagomedov
ausbildungsberuf: Fachinformatiker Anwendungsentwicklung
betrieb: artif GmbH & Co. KG
abteilung: Entwicklung
woche: KW 33 / 2026
---
```

Diese Informationen werden bewusst direkt im jeweiligen Bericht gespeichert.

Das ist besonders für die **Abteilung** wichtig. Die Abteilung eines Auszubildenden kann sich im Laufe der Ausbildung ändern. Eine spätere Änderung des Benutzerprofils darf jedoch keine bereits gespeicherten Berichte verändern.

Deshalb werden die Stammdaten beim Erstellen eines Berichts aus dem aktuellen Benutzerprofil übernommen und als unveränderliche Momentaufnahme im Bericht gespeichert.

Dadurch enthält jeder Bericht die Informationen, die zum Zeitpunkt seiner Erstellung gültig waren.

---

## 7.5 PDF-Export

Die Anwendung soll abgeschlossene Wochenberichte zusätzlich als PDF exportieren können.

Das PDF orientiert sich an der Darstellung des digitalen Wochenberichts und enthält insbesondere:

- Name des Auszubildenden,
- Ausbildungsberuf,
- Betrieb,
- Abteilung,
- Kalenderwoche und Berichtszeitraum,
- die Tagesberichte von Montag bis Freitag in einer gemeinsamen Tabelle,
- Tätigkeiten und gelernte Inhalte,
- gegebenenfalls Probleme oder offene Fragen,
- die Unterschrift des Auszubildenden mit Datum,
- die Unterschrift des Ausbilders mit Datum.

Der PDF-Export wird aus den bereits vorhandenen Daten des Wochenberichts erzeugt. Dadurch muss der Bericht nicht ein zweites Mal eingegeben oder manuell formatiert werden.

Das PDF dient insbesondere als druckbare beziehungsweise weitergebbare Version des digitalen Berichts.

---
# 8. Git als zentraler Bestandteil

Git ist ein zentraler Bestandteil des Projekts.

Nach dem Speichern oder Ändern eines Berichts werden die entsprechenden JSON- und Markdown-Dateien im Repository aktualisiert.

Anschließend werden die Änderungen mit Git versioniert.

Der grundlegende Ablauf ist:

```text
Benutzer speichert Bericht
        ↓
Laravel verarbeitet die Daten
        ↓
JSON wird erstellt/aktualisiert
        ↓
Markdown wird erstellt/aktualisiert
        ↓
Dateien werden im Benutzer-Repository gespeichert
        ↓
git add
        ↓
git commit
        ↓
git push
```

Dadurch entsteht eine nachvollziehbare Versionshistorie der Berichte.

Git kann beispielsweise über `Symfony\Component\Process\Process` aus Laravel heraus aufgerufen werden.

Für Commit und Push kann später Laravel Queue verwendet werden, damit der Benutzer nicht auf die vollständige Git-Operation warten muss.

---

# 9. Technischer Stack

## Backend

- **PHP**
- **Laravel**
- **Laravel Breeze** für Authentifizierung
- **PDF-Export** – Generierung druckbarer Wochenberichte

## Datenbank

- **MariaDB**
- Speicherung ausschließlich der Benutzer- und Verwaltungsdaten

## Berichtsformat

- **JSON** für strukturierte Daten
- **Markdown** für die menschenlesbare Darstellung
- YAML Frontmatter – historische Metadaten jedes Berichts

## Versionsverwaltung

- **Git**
- Git-Repository pro Auszubildendem
- optional ein Remote-Repository wie GitLab oder GitHub

## Frontend

- **Laravel Blade**
- **HTML**
- **CSS**
- **JavaScript**

JavaScript wird unter anderem für den visuellen Kalender und die digitale Unterschrift verwendet.

## Entwicklung

- **DDEV**
- **Docker**

---

# 10. Technische Architektur

```text
                         Browser
                            │
                            ▼
                    Laravel Webanwendung
                            │
             ┌──────────────┼──────────────┐
             │              │              │
             ▼              ▼              ▼
        Benutzer       Tagesberichte   Wochenberichte
             │              │              │
             ▼              └──────┬───────┘
          MariaDB                  │
                                   ▼
                            JSON + Markdown
                                   │
                                   ▼
                             Git Repository
                                   │
                                   ▼
                             Remote Git
```

MariaDB übernimmt die Benutzerverwaltung.

Die Berichtsdateien werden als JSON und Markdown im jeweiligen Git-Repository gespeichert.

---

# 11. Typischer Ablauf

```text
                    Benutzer
                       │
                       ▼
                  Anmeldung
                       │
                       ▼
             Visueller Kalender
                       │
             ┌─────────┴─────────┐
             ▼                   ▼
        Tagesbericht        Wochenbericht
             │                   │
             ▼                   ▼
       JSON + Markdown       Unterschrift
             │                   │
             └─────────┬─────────┘
                       ▼
                 Git Repository
                       │
                       ▼
                  Git Commit
                       │
                       ▼
                   Git Push
```

---

# 12. Betrieb

Die Anwendung läuft intern auf einem Server des Unternehmens und ist nicht öffentlich aus dem Internet erreichbar.

Die Berichte werden in den jeweiligen Git-Repositories gespeichert und können zusätzlich auf einem internen oder externen Git-Server versioniert werden.

---

# 13. Zusammenfassung

Das Projekt soll eine digitale Alternative zum klassischen Berichtsheft schaffen.

Die Anwendung basiert auf **Laravel, MariaDB, JSON, Markdown und Git**.

Dabei haben die einzelnen Technologien klar getrennte Aufgaben:

- **MariaDB** verwaltet Benutzer und Anwendungsdaten.
- **JSON** speichert die strukturierten Daten der Berichte.
- **Markdown** stellt die Berichte menschenlesbar dar.
- **Git** speichert und versioniert die Berichtsdateien und ist ein zentraler Bestandteil des Projekts.
- **JavaScript** übernimmt unter anderem den visuellen Kalender und die digitale Unterschrift.
- **HTML Canvas** wird für die Unterschrift verwendet, ohne zusätzliche Signature-Bibliothek.

Die Frage, ob Benutzer durch den Ausbilder angelegt werden oder sich selbst registrieren, bleibt zunächst offen und soll gemeinsam mit dem Ausbilder entschieden werden.

```
MariaDB
└── User
    ├── id
    ├── login
    ├── password
    └── repository_path
             │
             ▼
      Git Repository
      ├── profil.json          ← aktuelles Profil
      │
      ├── tagesberichte/
      │   ├── 2026-08-11.json
      │   └── 2026-08-11.md   ← Profil-Snapshot in YAML
      │
      └── wochenberichte/
          ├── KW33.json
          └── KW33.md          ← Profil-Snapshot in YAML
```

---
