<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\RajaOngkirResource;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RajaOngkirController extends Controller
{
    public function getProvinces(){
        $provincies = Province::all();
        return new RajaOngkirResource(true,'List Data Provinces',$provincies);
    }

    public function getCities(Request $request){

        //validate        
        $validator = validator::make($request->all(), [
            'province_id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        //get province name
        $province = Province::where('province_id',$request->province_id)->first();
            
        //get cities by province
        $cities = City::where('province_id',$request->province_id)->get();

        //return with Api Resource
        return new RajaOngkirResource(true, 'List Data City By Province : '.$province->name.'', $cities);
    }

    public function checkOngkir(Request $request)
    {
        //Fetch Rest API
        $response = Http::withHeaders([
            //api key rajaongkir
            'key'          => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost', [

            //send data
            'origin'      => 113, // ID kota Demak
            'destination' => $request->destination,
            'weight'      => $request->weight,
            'courier'     => $request->courier    
        ]);

        //return with Api Resource
        return new RajaOngkirResource(true, 'List Data Biaya Ongkos Kirim : '.$request->courier.'', $response['rajaongkir']['results'][0]['costs']);
    }
    
}
