<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // [POST] /auth/signup
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:80',
            'email' => 'required|string|email|max:80|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => $this->generateToken($user),
        ], 201);
    }

    // [POST] /auth/signin
    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'TOken inválido'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => $this->generateToken($user),
        ]);
    }

    // Token
    private function generateToken($user)
    {
        return $user->createToken('Session')->plainTextToken;
    }
}
