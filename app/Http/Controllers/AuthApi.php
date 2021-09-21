<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApi extends Controller
{
    public function register(Request $request){
        $v = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' =>'required',
            'image' => 'required|mimes:png,jpg'
        ]);
        if($v->fails()){
            return response()->json([
                'success'=> false,
                'status' => 500,
                'data' => $v->errors()
            ]);
        }

        $file = $request->file('image');
        $image_name = uniqid().$file->getClientOriginalName();
        $image_path = "/image/".$image_name;
        $file->move(public_path('/image'),$image_name);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $image_path
        ]);

        $token = $user->createToken('usertoken')->accessToken;
        return response()->json([
            'success'=>true,
            'status' => 200,
            'data' => ['token'=>$token, 'user'=>$user]
        ]);
    }
}
