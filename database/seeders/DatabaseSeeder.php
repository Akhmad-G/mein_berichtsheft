<?php

namespace Database\Seeders;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $gitLabService = app(GitLabServiceInterface::class);
    
    User::withoutEvents(function () use ($gitLabService) {
      // Ein Ausbilder
      $ausbilder = User::factory()->ausbilder()->create([
        'vorname' => 'Andreas',
        'nachname' => 'Brus',
        'email' => 'andreas.brus@example.test',
      ]);
      
      // Ein „benannter“ Azubi für einen vorhersehbaren Login
      $akhmad = User::factory()->create([
        'vorname' => 'Akhmad',
        'nachname' => 'Gazimagomedov',
        'email' => 'akhmad@artif.com',
        'email_verified_at' => now(),
        'password' => 'artifartif',
        'remember_token' => Str::random(10),
        'ausbildungsberuf' => 'Fachinformatiker',
        'ausbildungsbetrieb' => 'artif GmbH',
        'ausbildungsbeginn' => '2026-09-01 00:00:00',
        'ausbildung_info_completed_at' => \Symfony\Component\Clock\now(),
//        'gitlab_path' => "gazimagomedov-akhmad-1",
        'ausbilder_id' => $ausbilder->id,
      ]);
      
      // 10 weitere zufällige Azubi
      $azubis = User::factory(10)->create([
        'ausbilder_id' => $ausbilder->id,
      ]);
      
      // gitlab_path + test Tagesbericht für alle Azubis
      $azubis->push($akhmad)->each(function (User $user) use ($gitLabService) {
        $user->assignGitlabPathIfMissing();
        
        $filename = now()->format('Y-m-d') . ' Tagesbericht.json';
        
        $gitLabService->saveReport($user, $filename, [
          'date' => now()->format('Y-m-d'),
          'wochentag' => now()->translatedFormat('l'),
          'ausbildungsjahr' => 1,
          'ausbildungswoche' => '1',
          'taetigkeiten' => 'Seed-Beispiel für ' . $user->vorname,
          'gelernt' => '',
          'probleme' => '',
        ]);
      });
    });
  }
}
