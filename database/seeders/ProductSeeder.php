<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = ['basic', 'comfy', 'sexy', 'tops'];
        foreach($categories as $category) { 
            DB::table('categories')->insert([
                'name' => $category,
                'slug' => implode('-', explode(' ', $category))
            ]);
        }

        $images = json_decode(file_get_contents(__DIR__.'/data/images.json', true));
        $names = json_decode(file_get_contents(__DIR__.'/data/names.txt', true));
        $prices = json_decode(file_get_contents(__DIR__.'/data/prices.txt', true));
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];

        for ($i = 0; $i < count($images); $i++) { 

            $faker = Faker\Factory::create();

            $imgset = $faker->randomElements($images, 3);

            $special_labels = [ 'basic', 'comfy', null ];

            print_r($names[$i]);
            DB::table('products')->insert([
                'name'          => $names[$i],
                'slug'          => implode('-', explode(' ', trim(strtolower($names[$i])))),
                'description'   => $faker->text(),
                'price'         => intval($prices[$i]),
                'images'        => json_encode($imgset),
                'thumbnails'    => null,
                'discount'      => null,
                'special-label' => $faker->randomElement($special_labels)
            ]);


            DB::table('product_categories')->insert([
                'product_id' => $i + 1,
                'category_id' => $faker->numberBetween(1, 4)
            ]);
            
            $selected_sizes = $faker->randomElements($sizes, $faker->numberBetween(1, 4));
            
            foreach ($selected_sizes as $size) {
                DB::table('sizes')->insert([
                    'name'       => $size,
                    'quantity'   => $faker->numberBetween(0, 10),
                    'product_id' => $i+1
                ]);
            }

        }
    }
}