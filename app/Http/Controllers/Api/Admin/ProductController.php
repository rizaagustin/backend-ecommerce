<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Hamcrest\Description;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;

class ProductController extends Controller
{
    public function index(){
        $product =  Product::with('category')->when(request()->q, function($product){
            $product = $product->where('title','like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        return new ProductResource(true,'List Data Product',$product);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:products',
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/products',$image->hashName());

        $product = Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'slug'          => Str::slug($request->title,'-'),
            'category_id'   => $request->category_id,
            'user_id'       => auth()->guard('api_admin')->user()->id,
            'description'   => $request->description,
            'weight'        => $request->weight,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'discount'      => $request->discount
        ]);

        if ($product) {
            return new ProductResource(true,'Data Product Berhasil Disimpan!',$product);
        }

        return new ProductResource(false,'Data Produt Gagal Disimpan!',null);
    }

    public function show($id){
        // $product = Product::whereId($id)->first();
        $product = Product::with('category')->whereId($id)->first();
        if ($product) {
            return new ProductResource('true','Detail Data Product!',$product);
        }

        return new ProductResource('false','Detail Data Product Tidak ditemukan',null);
    }

    public function destroy(Product $product){
        
        //remove image
        Storage::disk('local')->delete('public/products/'.basename($product->image));

        if ($product->delete()) {
            return new ProductResource(true,'Data Product Berhasil Dihapus!',null);
        }

        return new ProductResource(true,'Data Product Gagal Dihapus!',null);

    }

    public function update(Request $request, Product $product){

        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:products,title,'.$product->id,
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            
            Storage::disk('local')->delete('public/products/'.basename($product->image));
            
            $image = $request->file('image');
            $image->storeAs('public/products',$image->hashName());
            
            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'slug' => Str::slug($request->title,'-'),
                'category_id' => $request->category_id,
                'user_id' => auth()->guard('api_admin')->user()->id,
                'description' => $request->description,
                'weight' => $request->weight,
                'price' => $request->price,
                'stock' => $request->stock,
                'discount' => $request->discount,
            ]);

        }

        $product->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title,'-'),
            'category_id' => $request->category_id,
            'user_id' => auth()->guard('api_admin')->user()->id,
            'description' => $request->description,
            'weight' => $request->weight,
            'price' => $request->price,
            'stock' => $request->stock,
            'discount' => $request->discount,
        ]);

        if ($product) {
            return new ProductResource(true,'Data Product Berhasil Diupdate!',$product);
        }

        return new ProductResource(false,'Data Product Gagal Diupdate!',null);

    }
}
