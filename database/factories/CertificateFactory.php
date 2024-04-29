<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(rand(2, 4)),
            'university' => $this->faker->sentence(rand(2, 4)),
            'graduation_date' => $this->faker->date(),
            'user_id' => User::factory(),
        ];
    }
}
