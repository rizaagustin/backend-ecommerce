<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(){
        
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name','like','%'.Request()->q.'%');            
        })->latest()->paginate(5);
        
        return new UserResource(true,'List Data Users', $users);
    
    }

    public function store(Request $request){
        $validator = Validator::make(request()->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',            
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'passwod' => bcrypt($request->password)
        ]);

        if ($user) {
            return new UserResource(true,'Data User Berhasil Disimpan!',$user);
        }

        return new UserResource(false,'Data User Gagal Disimpan!',null);
    }

    public function show($id){
        $user = User::whereId($id)->first();
        if ($user) {
            return new UserResource(true,'Detail Data User ditemukan!',$user);
        }

        return new UserResource('false','Detail Data User Tidak ditemukan',null);

    }

    public function update(Request $request, User $user){

        $validator = validator::make($request->all(),[
            'name'      =>   'required',
            'email'    => 'required|unique:users,email,'.$user->id,
            'password'  =>   'confirmed' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->password == '') {
            //update user without password
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);
        }
        //update user with new password
        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Diupdate!', null);
    }

    public function destroy(User $user){

        if ($user->delete()) {
            return new UserResource(true, 'Data User Berhasil Dihapus!', null);
        }

        return new UserResource(true, 'Data User Gagal Dihapus!', null);

    }

}
