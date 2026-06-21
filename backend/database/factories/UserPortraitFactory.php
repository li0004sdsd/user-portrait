<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPortrait;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPortraitFactory extends Factory
{
    protected $model = UserPortrait::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'age' => fake()->numberBetween(18, 60),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'occupation' => fake()->jobTitle(),
            'location' => fake()->city(),
            'income_level' => fake()->randomFloat(2, 1000, 100000),
            'interests' => fake()->sentence(),
            'pain_points' => fake()->sentence(),
            'goals' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
