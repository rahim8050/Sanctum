<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
       $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        // Logic to create a new user
        $user = User::create($fields);
        $token = $user->createToken($request->name);
        return [
            'user' => $user,
            'token' => $token,
        ];

    }
    public function login(Request $request){
        //  $request->validate([

        //     'email' => 'required|email|exists:users',
        //     'password' => 'required',
        // ]);
        // $user = User::where('email', $request->email)->first();
        // if (!$user || Hash::check($request->password, $user->password)) {

        //     return [ 'message' => 'Invalid credentials'];
        // }
        // $token = $user->createToken($user->name);
        // return [
        //     'user' => $user,
        //     'token' => $token,
        // ];
           $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ];
        }
          $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];

    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
       return response()->json([
            'message' => 'Logged out successfully'
        ]);

    }
}
