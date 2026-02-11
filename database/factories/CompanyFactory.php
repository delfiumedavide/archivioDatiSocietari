<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'denominazione' => fake()->company(),
            'codice_fiscale' => fake()->unique()->numerify('###########'),
            'partita_iva' => fake()->unique()->numerify('###########'),
            'pec' => fake()->companyEmail(),
            'forma_giuridica' => fake()->randomElement(['SRL', 'SPA', 'SAS', 'SNC']),
            'sede_legale_indirizzo' => fake()->streetAddress(),
            'sede_legale_citta' => fake()->city(),
            'sede_legale_provincia' => fake()->stateAbbr(),
            'sede_legale_cap' => fake()->postcode(),
            'capitale_sociale' => fake()->randomFloat(2, 10000, 1000000),
            'data_costituzione' => fake()->dateTimeBetween('-20 years', '-1 year'),
            'is_active' => true,
        ];
    }
}
