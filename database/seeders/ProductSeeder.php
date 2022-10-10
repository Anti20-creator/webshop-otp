<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'name' => 'Kristof',
            'email' => 'amtmannkristof@gmail.com',
            'password' => Hash::make('123456')
        ]);

        Image::configure(['driver' => 'imagick']);

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

        $products = scandir(__DIR__.'/data/product-images');

        array_shift($products);
        array_shift($products);

        for ($i = 0; $i < count($products); $i++) { 

            $faker = Faker\Factory::create();

            $images_count = count(scandir(__DIR__.'/data/product-images/'.$products[$i])) - 2;
            $img_names = [];

            for($j = 0; $j < $images_count; ++$j) {
                $filename = Str::uuid().'.jpg';

                print_r(__DIR__.'/data/product-images/'.$products[$i].'/'.($j+1).'.jpg');
                $image = Image::make(__DIR__.'/data/product-images/'.$products[$i].'/'.($j+1).'.jpg');
                if($image->width() > 1200) {
                    $image->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $image->save(public_path().'/uploads/'.$filename);

                $image->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/uploads/'.'thumb_'.$filename);

                $img_names[] = $filename;
            }            
            
            $special_labels = [ 'basic', 'comfy', null ];

            print_r($names[$i]);
            DB::table('products')->insert([
                'name'          => ucwords(implode(' ', explode('-', $products[$i]))),
                'slug'          => $products[$i],
                'description'   => $faker->text(),
                'price'         => intval($prices[$i]),
                'images'        => json_encode($img_names),
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