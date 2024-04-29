<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'type' => ['offline', 'online'][rand(0, 1)],
            'price' => rand(1000, 10000),
            'student_count' => rand(20, 40),
            'status' => ['closed', 'opened'][rand(0, 1)],
            'location' => $this->faker->city(),
            'user_id' => User::factory(null, ['category' => 1]),
        ];
    }
}
