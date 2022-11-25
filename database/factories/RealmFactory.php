<?php

namespace Database\Factories;

use App\Models\wow\Realm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RealmFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Realm::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => fake()->randomNumber(3, true),
            'name' => fake()->name(),
            'slug' => fake()->name(),
            'region' => 'US',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

