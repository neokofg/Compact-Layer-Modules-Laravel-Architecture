<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'salary' => rand(1000,40000),
            'is_some' => false,
            'description' => $this->faker->text,
            'contact' => $this->faker->url(),
            'is_active' => true,
            'company_id' => Company::factory(),
            'city_id' => City::factory()
        ];
    }
}
