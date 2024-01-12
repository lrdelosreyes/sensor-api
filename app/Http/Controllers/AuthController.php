<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'errors' => json_decode($validated->messages())
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->roles()->attach(2);

        return response()->json([
            'data' => $user
        ]);
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'errors' => json_decode($validated->messages())
            ], 422);
        }

        $user = User::where('email', $request->username)
            ->orWhere('username', $request->username)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'credential' => 'Invalid credentials.'
                ]
            ], 422);
        }

        $authToken = $user
            ->createToken('auth-token')
            ->plainTextToken;

        return response()->json([
            'access_token' => $authToken,
        ]);
    }

    public function unauthenticated()
    {
        return response()->json([
            'unauthenticated' => 1,
        ], 402);
    }
}
