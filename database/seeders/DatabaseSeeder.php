<?php

namespace Database\Seeders;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Main database seeder populating initial trainers and apprentices.
 */
class DatabaseSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void {
    $gitLabService = app(GitLabServiceInterface::class);

    User::withoutEvents(function () use ($gitLabService) {
      // 1. Trainer
      $ausbilder = User::factory()->ausbilder()->create([
        'vorname' => config('seeding.ausbilder.firstname'),
        'nachname' => config('seeding.ausbilder.lastname'),
        'email' => config('seeding.ausbilder.email'),
        'password' => config('seeding.ausbilder.password'),
      ]);

      // 2. Primary apprentice
      $azubi1Data = [
        'vorname' => config('seeding.azubi.firstname'),
        'nachname' => config('seeding.azubi.lastname'),
        'email' => config('seeding.azubi.email'),
        'email_verified_at' => now(),
        'password' => config('seeding.azubi.password'),
        'remember_token' => Str::random(10),
        'ausbildungsberuf' => config('seeding.azubi.beruf'),
        'ausbildungsbetrieb' => config('seeding.azubi.betrieb'),
        'abteilung' => config('seeding.azubi.abteilung'),
        'ausbildungsbeginn' => config('seeding.azubi.beginn'),
        'ausbilder_id' => $ausbilder->id,
      ];

      if ($customGitLabPath = config('seeding.azubi.gitlab_path')) {
        $azubi1Data['gitlab_path'] = $customGitLabPath;
      }

      $azubi1 = User::factory()->create($azubi1Data);

      if (!$azubi1->gitlab_path) {
        $azubi1->assignGitLabPath($gitLabService);
      }

      // 3. Optional secondary demo apprentice
      if (config('seeding.create_secondary_azubi')) {
        $azubi2Data = [
          'vorname' => config('seeding.azubi2.firstname'),
          'nachname' => config('seeding.azubi2.lastname'),
          'email' => config('seeding.azubi2.email'),
          'email_verified_at' => now(),
          'password' => config('seeding.azubi2.password'),
          'remember_token' => Str::random(10),
          'ausbildungsberuf' => config('seeding.azubi2.beruf'),
          'ausbildungsbetrieb' => config('seeding.azubi2.betrieb'),
          'abteilung' => config('seeding.azubi2.abteilung'),
          'ausbildungsbeginn' => config('seeding.azubi2.beginn'),
          'ausbilder_id' => $ausbilder->id,
        ];

        if ($customGitLabPath2 = config('seeding.azubi2.gitlab_path')) {
          $azubi2Data['gitlab_path'] = $customGitLabPath2;
        }

        $azubi2 = User::factory()->create($azubi2Data);

        if (!$azubi2->gitlab_path) {
          $azubi2->assignGitLabPath($gitLabService);
        }
      }
    });
  }
}