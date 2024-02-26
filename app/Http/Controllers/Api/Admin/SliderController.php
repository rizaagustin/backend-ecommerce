<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()   {
        //get sliders
        $sliders = Slider::latest()->paginate(5);        
        //return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2000', 
            'link' => 'required', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        //create slider
        $slider= Slider::create([
            'image' => $image->hashName(),
            'link' => $request->link,
        ]);

        if ($slider) {
            return new SliderResource(true, 'Data Slider Berhasil Disimpan!', $slider);
        }

        return new SliderResource(false, 'Data Slider Gagal Disimpan!',null);
    }

    public function destroy(Slider $slider){
        Storage::disk('local')->delete('public/sliders/'.basename($slider->image));

        if ($slider->delete()) {
            return new SliderResource(true, 'Data Slider Berhasil Dihapus!', null);
        }        

        return new SliderResource(false, 'Data Slider Gagal Dihapus!', null);
    }
    
}
