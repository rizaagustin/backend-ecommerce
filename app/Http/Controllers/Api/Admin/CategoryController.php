<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
    public function index(){

        $categories = Category::when(request()->q, function($categories){
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(1);

        return new CategoryResource(true,'List Data Category',$categories);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'     => 'required|unique:categories',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/categories',$image->hashName());

        $category = Category::create([
            'image' => $image->hashName(),
            'name' =>  $request->name,
            'slug' => Str::slug($request->name,'-')
        ]);

        if ($category) {
            return new CategoryResource(true,'Data Category Berhasil Disimpan!',$category);
        }

        return new CategoryResource(false,'Data Category Gagal Disimpan!',null);

    }

    public function show($id){
        
        $category = Category::whereId($id)->first();

        if ($category) {
            return new CategoryResource(true,'Detail Data Category!',$category);
        }

        return new CategoryResource(false,'Detail Data Category Tidak ditemukan',$category);
    }

    public function destroy(Category $category){

        Storage::disk('local')->delete('public/categories/'.basename($category->name));

        if ($category->delete()) {
            return new CategoryResource(true,'Data Category Berhasil Di hapus!',null);
        }

        return new CategoryResource(true,'Data Category Gagal Di hapus!',null);

    }

    public function update(Request $request, Category $category){

        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories,name,'.$category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/categories'.basename($category->image));

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/categories',$image->hashName());

            //update category with new image
            $category->update([
                'image'=> $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);
        }

        //update category without new image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name,'-')
        ]);

        if ($category) {
            return new CategoryResource(true,'Data Category Berhasil diupdate!',$category);
        }
        
        return new CategoryResource(false,'Data ategory Gagal Diupdate!',null);

    }

}
