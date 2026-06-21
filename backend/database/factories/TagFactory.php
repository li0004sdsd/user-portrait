<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'tag_category_id' => TagCategory::factory(),
            'user_id' => User::factory(),
            'name' => fake()->word(),
            'value' => fake()->word(),
            'weight' => fake()->numberBetween(1, 10),
        ];
    }
}
