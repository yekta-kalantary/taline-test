<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! auth()->attempt($data)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $user = auth()->user();

        $token = $user->createToken('app')->plainTextToken;

        return response()->json([
            'message' => 'Login successfully',
            'token' => $token,
        ]);
    }
}
