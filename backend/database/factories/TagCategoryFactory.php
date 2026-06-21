<?php

namespace Database\Factories;

use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagCategoryFactory extends Factory
{
    protected $model = TagCategory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'description' => fake()->sentence(),
        ];
    }
}
