<?php

namespace Database\Seeders;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder {
  public function run(): void {
    $gitLabService = app(GitLabServiceInterface::class);

    User::withoutEvents(function () use ($gitLabService) {
      // Ein Ausbilder
      $ausbilder = User::factory()->ausbilder()->create([
        'vorname' => 'Andreas',
        'nachname' => 'Brus',
        'email' => 'andreas.brus@artif.com',
        'password' => '5@3$afg6$JyWsk4F',
      ]);

      // Ein „benannter“ Azubi für einen vorhersehbaren Login
      $akhmed = User::factory()->create([
        'vorname' => 'Akhmed',
        'nachname' => 'Gazimagomedov',
        'email' => 'akhmed.gazimagomedov@artif.com',
        'email_verified_at' => now(),
        'password' => 'GG+|47>9W2uCYeyJ',
        'remember_token' => Str::random(10),
        'ausbildungsberuf' => 'Fachinformatiker für Anwendungsentwicklung',
        'ausbildungsbetrieb' => 'artif GmbH & Co. KG',
        'abteilung' => 'Backend',
        'ausbildungsbeginn' => '2026-09-01 00:00:00',
        'ausbilder_id' => $ausbilder->id,
      ]);

      $akhmed->assignGitLabPath($gitLabService);

      $fatih = User::factory()->create([
        'vorname' => 'Fatih',
        'nachname' => 'Ayyildiz',
        'email' => 'fatih.ayyildiz@artif.com',
        'email_verified_at' => now(),
        'password' => 'GK76a^7Rs9\f',
        'remember_token' => Str::random(10),
        'ausbildungsberuf' => 'Fachinformatiker für Anwendungsentwicklung',
        'ausbildungsbetrieb' => 'artif GmbH & Co. KG',
        'abteilung' => 'Backend',
        'ausbildungsbeginn' => '2026-09-01 00:00:00',
        'ausbilder_id' => $ausbilder->id,
      ]);

      $fatih->assignGitLabPath($gitLabService);


    });
  }
}
