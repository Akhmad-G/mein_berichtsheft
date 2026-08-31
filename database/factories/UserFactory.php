<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vorname' => fake('de_DE')->firstName(),
            'nachname' => fake('de_DE')->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Azubi,
            'ausbilder_id' => null,
            'ausbildungsberuf' => fake('de_DE')->jobTitle(),
            'ausbildungsbetrieb' => fake('de_DE')->company(),
            'ausbildungsbeginn' => fake()->dateTimeBetween('now', '+10 years'),
            'ausbildung_info_completed_at' => \Symfony\Component\Clock\now(),
        ];
    }
  
    public function ausbilder(): static
    {
      return $this->state(fn () => [
        'role' => UserRole::Ausbilder,
        'ausbilder_id' => null,
      ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
