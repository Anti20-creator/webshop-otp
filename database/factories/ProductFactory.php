<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker\Factory::create();

        $special_labels = [ 'new', null ];

        return [
            'name' => $faker->unique()->word(),
            'description' => $faker->text(),
            'images' => null,
            'thumbnails' => null,
            'quantity' => $faker->numberBetween(0, 35),
            'price' => floor($faker->numberBetween(0, 15)) * 1000 + 995,
            'discount' => null,
            'special-label' => $faker->randomElement($special_labels)
        ];
    }
}
