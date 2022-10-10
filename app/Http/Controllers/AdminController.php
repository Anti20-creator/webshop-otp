<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\Order;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct()
    {
        Image::configure(['driver' => 'imagick']);
    }

    public function index() 
    {
        return view('admin');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        
        $credentials = array(
            'email' => $email,
            'password' => $password
        );

        if(!Auth::validate($credentials)) {
            return redirect()->to('admin')
                ->withErrors(['error' => 'Hibás email vagy jelszó!', 'email' => $credentials['email']]);
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    public function categoriesAdminPage()
    {
        $categories = Category::all();
        $counts = ProductCategory::all()->countBy('category_id');
        return view('admin.categories', compact('categories', 'counts'));
    }

    public function productsAdminPage()
    {
        $products = Product::all();
        //$counts = ProductCategory::all()->countBy('category_id');
        return view('admin.products', compact('products'));
    }

    public function ordersAdminPage()
    {
        $orders = Order::all();
        return view('admin.orders', compact('orders'));
    }

    public function productsAddAdminPage()
    {
        $categories = Category::all();
        return view('admin.products-add', compact('categories'));
    }

    public function productsEditAdminPage($id)
    {
        $product = Product::where('id', $id)->first();
        if(!$product) return abort(404);

        $categories = Category::all();
        return view('admin.products-edit', compact('product', 'categories'));
    }

    public function editProduct(Request $request)
    {
        $name = $request->get('product');
        $description = $request->get('description');
        $price = $request->get('price');
        $images = $request->file('image');
        $categories = $request->get('categories');
        $variantNames = $request->get('variantname');
        $variantQuantity = $request->get('variantquantity');
        $slug = $request->get('slug');
        $removedImages = $request->get('removedimages');

        if($name == null || $price == null || $slug == null || count($variantQuantity) != count($variantNames) || $categories == null || count($categories) == 0) {
            return back()
                ->withErrors(['error' => 'A termék frissítéséhez több adatra van szükség!']);
        }

        try {
            $product = Product::where('id', $request->id)->first();
            $product->name = $name;
            $product->description = $description;
            $product->price = $price;
            $product->slug = $slug;

            $product->save();
        }catch(Exception $e){
            return back()
                ->withErrors(['error' => 'A termék frissítése során hiba lépett fel!']);
        }
        
        $oldImages = json_decode($product->images);
        $data = [];

        $removedImages = explode(';', $removedImages);

        foreach ($oldImages as $key => $oldImage) {
            if(!in_array($key, $removedImages)) {
                $data[] = $oldImage;
            }else{
                if (File::exists(public_path('/uploads/'.$oldImage))) {
                    File::delete(public_path('/uploads/'.$oldImage));
                }
                if (File::exists(public_path('/uploads/thumb_'.$oldImage))) {
                    File::delete(public_path('/uploads/thumb_'.$oldImage));
                }
            }
        }

        if($request->hasfile('image')) {

            foreach($images as $file)

            {

                $filename = Str::uuid().'.'.$file->extension();

                $image = Image::make($file);
                if($image->width() > 1200) {
                    $image->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $image->save(public_path().'/uploads/'.$filename);

                $image->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/uploads/thumb_'.$filename);

                $data[] = $filename;

            }

        }

        try {
            $product = Product::where('id', $request->id)->first();
            $product->images = json_encode($data);

            $product->save();

            DB::transaction(function() use ($categories, $product, $variantQuantity, $variantNames) {

                $oldCategories = $product->categories()->pluck('category_id')->toArray();

                foreach($oldCategories as $oldCategory) {
                    if (!in_array($oldCategory, $categories)) {
                        ProductCategory::where('category_id', $oldCategory)->where('product_id', $product['id'])->delete();
                    }
                }

                foreach($categories as $category) {
                    if(!in_array($category, $oldCategories)) {
                        ProductCategory::insert(array(
                            'product_id' => $product['id'],
                            'category_id' => $category
                        ));
                    }
                }

                $oldVariants = $product->sizes()->pluck('name')->toArray();

                foreach($oldVariants as $oldVariant) {
                    if(!in_array($oldVariant, $variantNames)) {
                        Size::where('name', $oldVariant)->where('product_id', $product['id'])->first()->delete();
                    }
                }

                for ($i = 0; $i < count($variantNames); ++$i) {
                    if(in_array($variantNames[$i], $oldVariants)) {
                        $element = Size::where('name', $variantNames[$i])->where('product_id', $product['id'])->first();
                        $element->quantity += $variantQuantity[$i];
                        $element->timestamps = false;
                        $element->save();
                    }else{
                        Size::insert(array(
                            'product_id' => $product['id'],
                            'quantity' => $variantQuantity[$i],
                            'name' => $variantNames[$i]
                        ));
                    }
                }

            });
        }catch(Exception $e){
            return back()
                ->withErrors(['error' => 'A termék frissítése során hiba lépett fel! #3']);
        }

        return back();
    }

    public function createCategory(Request $request)
    {

        $category = $request->get('category');
        $conflict = Category::where('name', $category)->count();

        if(!$category || $conflict > 0) {
            return back()
                ->withErrors(['error' => 'A megadott kategória létrehozása sikertelen volt!', 'category' => $category]);
        }

        Category::insert(array(
            'name' => $category,
            'slug' => implode('-', explode(' ', $category))
        ));

        return back();
    }

    public function createProduct(Request $request)
    {

        $name = $request->get('product');
        $description = $request->get('description');
        $price = $request->get('price');
        $images = $request->file('image');
        $categories = $request->get('categories');
        $variantNames = $request->get('variantname');
        $variantQuantity = $request->get('variantquantity');
        $slug = $request->get('slug');

        if($name == null || $price == null || $slug == null || count($variantQuantity) != count($variantNames) || $categories == null || count($categories) == 0) {
            return back()
                ->withErrors(['error' => 'A termék létrehozásához több adatra van szükség!']);
        }

        if($request->hasfile('image')) {

            foreach($images as $file)

            {

                $filename = Str::uuid().'.'.$file->extension();

                $image = Image::make($file);
                if($image->width() > 1200) {
                    $image->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $image->save(public_path().'/uploads/'.$filename);

                $image->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path().'/uploads/thumb_'.$filename);

                $data[] = $filename;

            }

        }

        $result = Product::insert(array(
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'slug' => $slug,
            'images' => json_encode($data),
        ));

        if(!$result) {
            foreach ($data as $oldImage) {
                if (File::exists(public_path('/uploads/'.$oldImage))) {
                    File::delete(public_path('/uploads/'.$oldImage));
                }
            }

            return back()
                ->withErrors(['error' => 'A termék létrehozásához több adatra van szükség!']);
        }

        $productId = Product::where('name', $name)->first()->id;

        DB::transaction(function() use ($categories, $variantNames, $variantQuantity, $productId) {

            foreach($categories as $category) {
                ProductCategory::insert(array(
                    'product_id' => $productId,
                    'category_id' => $category
                ));
            }

            for ($i = 0; $i < count($variantNames); ++$i) {
                Size::insert(array(
                    'product_id' => $productId,
                    'quantity' => $variantQuantity[$i],
                    'name' => $variantNames[$i]
                ));
            }

        });

        return redirect()->to('/admin/products')->with('msg', 'success');
    }

    protected function authenticated(Request $request, $user) 
    {
        return redirect('/admin');
    }
}
