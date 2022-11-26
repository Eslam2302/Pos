<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    
    public function index(Request $request)
    {

        $categories = Category::all();

        $products = Product::when($request->search, function ($q) use ($request) {

            return $q->whereTranslationLike('name', '%' . $request->search . '%');

        })->when($request->category_id, function ($q) use ($request) {

            return $q->where('category_id', $request->category_id);

        })->latest()->paginate(5);

        return view('dashboard.products.index', compact('categories' ,'products'));
        
    } // end of index

   
    public function create()
    {
        
        $categories = Category::all();
        return view('dashboard.products.create', compact('categories'));

    } // end of create 

   
    public function store(Request $request)
    {
        
        $rules = [
            'category_id' => 'required'
        ];

        foreach (config('translatable.locales') as $locale ) {

            $rules += [$locale . '.name' => 'required|unique:product_translations,name'];
            $rules += [$locale . '.description' => 'required'];

        } // end of foreach

        $rules += [
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'stock' => 'required',
        ];

        $request->validate($rules);

        $requests_data = $request->all();

        if($request->image) {

            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/product_images/' . $request->image->hashName()));

            $requests_data['image'] = $request->image->hashName(); 

        }

        Product::create($requests_data);
        session()->flash('success',__('site.added_successfully'));
        return redirect()->route('dashboard.products.index');


    } // end of store

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.edit', compact('categories' ,'product'));

    } // end of edit

    public function update(Request $request, Product $product)
    {
        
        $rules = [
            'category_id' => 'required'
        ];

        foreach (config('translatable.locales') as $locale ) {

            $rules += [$locale . '.name' => ['required', Rule::unique('product_translations', 'name')->ignore($product->id, 'product_id')]];
            $rules += [$locale . '.description' => 'required'];

        } // end of foreach

        $rules += [
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'stock' => 'required',
        ];

        $request->validate($rules);

        $requests_data = $request->all();

        if($request->image) {

            if($product->image != 'default.png') {

                storage::disk('public_uploads')->delete('/product_images/' . $product->image);
               
            }

            Image::make($request->image)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(public_path('uploads/product_images/' . $request->image->hashName()));

            $requests_data['image'] = $request->image->hashName(); 

        }

        $product->update($requests_data);
        session()->flash('success',__('site.updated_successfully'));
        return redirect()->route('dashboard.products.index');

    }

    public function destroy(Product $product)
    {

        if($product->image != 'default.png') {

            storage::disk('public_uploads')->delete('/product_images/' . $product->image);
           
        }
        
        $product->delete();
        session()->flash('success',__('site.deleted_successfully'));
        return redirect()->route('dashboard.products.index');

    } // end of destroy
}
