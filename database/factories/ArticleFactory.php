<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'image' => '',//$this->faker->imageUrl(),
            'slug' => $this->faker->slug(),
            'content' => $this->faker->paragraph(2),
            'category_id' => Category::all()->random()->id,
            'user_id' => User::factory()
        ];
    }
}
