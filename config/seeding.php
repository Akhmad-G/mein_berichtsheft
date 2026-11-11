<?php

return [
  'ausbilder' => [
    'firstname' => env('SEED_AUSBILDER_FIRSTNAME', 'Alex'),
    'lastname' => env('SEED_AUSBILDER_LASTNAME', 'Trainer'),
    'email' => env('SEED_AUSBILDER_EMAIL', 'ausbilder@example.com'),
    'password' => env('SEED_AUSBILDER_PASSWORD', 'password'),
  ],

  'azubi' => [
    'firstname' => env('SEED_AZUBI_FIRSTNAME', 'Max'),
    'lastname' => env('SEED_AZUBI_LASTNAME', 'Mustermann'),
    'email' => env('SEED_AZUBI_EMAIL', 'azubi@example.com'),
    'password' => env('SEED_AZUBI_PASSWORD', 'password'),
    'beruf' => env('SEED_AZUBI_BERUF', 'Fachinformatiker für Anwendungsentwicklung'),
    'betrieb' => env('SEED_AZUBI_BETRIEB', 'Musterfirma GmbH'),
    'abteilung' => env('SEED_AZUBI_ABTEILUNG', 'Softwareentwicklung'),
    'beginn' => env('SEED_AZUBI_BEGINN', '2026-09-01 00:00:00'),
    'gitlab_path' => env('SEED_AZUBI_GITLAB_PATH'),
  ],

  'create_secondary_azubi' => env('SEED_CREATE_SECONDARY_AZUBI', true),

  'azubi2' => [
    'firstname' => env('SEED_AZUBI2_FIRSTNAME', 'Erika'),
    'lastname' => env('SEED_AZUBI2_LASTNAME', 'Musterfrau'),
    'email' => env('SEED_AZUBI2_EMAIL', 'erika.musterfrau@example.com'),
    'password' => env('SEED_AZUBI2_PASSWORD', 'password'),
    'beruf' => env('SEED_AZUBI2_BERUF', 'Fachinformatiker für Anwendungsentwicklung'),
    'betrieb' => env('SEED_AZUBI2_BETRIEB', 'Musterfirma GmbH'),
    'abteilung' => env('SEED_AZUBI2_ABTEILUNG', 'Frontend-Entwicklung'),
    'beginn' => env('SEED_AZUBI2_BEGINN', '2026-09-01 00:00:00'),
    'gitlab_path' => env('SEED_AZUBI2_GITLAB_PATH'),
  ],
];