<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * The login endpoint.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validate the incoming data
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'contrasenia' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Find the user by their 'correo'
        $user = Usuario::where('correo', $request->correo)->first();

        // 3. Check if user exists and if the password is correct
        if (!$user || !Hash::check($request->contrasenia, $user->contrasenia)) {
            return response()->json(['error' => 'Unauthorized: Invalid credentials'], 401);
        }

        // 4. User is valid, so create the token
        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // 5. Return the token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60 // Get expiration from config
        ]);
    }

    /**
     * A simple endpoint to test if your token works.
     */
    public function profile()
    {
        // 'auth:api' uses the config from 'config/auth.php'
        // to find the user via the provided token.
        return response()->json(auth('api')->user());
    }
}

