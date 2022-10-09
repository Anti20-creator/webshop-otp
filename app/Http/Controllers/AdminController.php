<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
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

    public function productsAddAdminPage()
    {
        $categories = Category::all();
        return view('admin.products-add', compact('categories'));
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

                $file->move(public_path().'/uploads/', $filename);  

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
