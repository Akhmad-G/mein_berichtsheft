<?php

namespace Database\Factories;

use App\Models\Tagesbericht;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tagesbericht>
 */
class TagesberichtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
          'ausbildungsnachweis_nummer' => fake()->randomNumber(2,false),
          'datum' => fake()->date,
          'wochentag' => fake()->dayOfWeek,
          'name' => fake()->name,
          'ausbildungsberuf' => 'Fachinformatiker – Anwendungsentwicklung',
          'betrieb' => fake()->company,
          'backend' => 'Backend', //abteilung!!!
          'ausbildungsjahr' => fake()->randomDigit(),
          'ausbildungswoche' => fake()->randomDigit(),
          'tätigkeiten' => fake()->paragraph, //taetigkeiten!
          'gelernt' => fake()->paragraph,
          'probleme' => fake()->paragraph,
        ];
    }
}
