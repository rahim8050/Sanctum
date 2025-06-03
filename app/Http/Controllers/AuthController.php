<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function changePassword(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ], [
            'new_password.regex' => 'Password must contain at least: 1 uppercase, 1 lowercase, 1 number, and 1 special character'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get authenticated user
        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ], 401);
        }

        // Prevent password reuse
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'New password cannot be the same as current password'
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Invalidate other tokens (optional)
        // $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully'
        ], 200);
    }

}
