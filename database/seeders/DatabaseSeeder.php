<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      User::factory()->create([
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
      ]);
      
      User::factory(0)->create();
    }
}
