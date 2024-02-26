<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;


class RegisterController extends Controller
{
        public function store(Request $request){
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers',
                'password' => 'required|string|min:8|confirmed'
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(),400);
            }
    
            //create customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
    
            if ($customer) {
                return new CustomerResource(true, 'Register Customer Berhasil', $customer);
            }
    
            return new CustomerResource(false, 'Register Customer Gagal', null);
        }
        
}
