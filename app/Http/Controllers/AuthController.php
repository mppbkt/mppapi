<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request){
        $this->validate($request, [
            'email' => 'required|email|unique:admins',
            'name' => 'required|unique:admins',
            'password' => 'required',
            'cPassword' => 'required'
        ]);
        $name = $request->input('name');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));
        $cPassword = $request->input('cPassword');
        
        if(Hash::check($cPassword, $password)){
            $register = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            if($register){
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Daftar',
                    'data' => $register
                ],201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal Daftar',
                    'data' => ''
                ],400);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Password Tidak sama',
                'data' => ''
            ], 403);
        }

        
    }
    public function login(Request $request){
        $user = User::where('name', $request->input('username'))->first();
        if(Hash::check($request->input('password'), $user->password)){
            $apiToken = base64_encode(Str::random(40));
            // dd($apiToken);
            $user->update([
                'remember_token' => $apiToken
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Login',
                'data' => [
                    'user' => $user,
                    'remember_token' => $apiToken
                ]
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal Login',
                'data' => [
                    'user' => '',
                    'remember_token' => ''
                ]
            ]);
        }

    }
}
