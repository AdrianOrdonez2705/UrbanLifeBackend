<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Create a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:usuario',
            'contrasenia' => 'required|string|min:8|confirmed',
            'rol_id_rol' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $user = Usuario::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'contrasenia' => Hash::make($request->contrasenia),
                'rol_id_rol' => $request->rol_id_rol,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create user', 'message' => $e->getMessage()], 500);
        }
    }
    
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

        $key = Str::lower($request->correo) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)){
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Demasiados intentos fallidos. Intente de nuevo en {$seconds} segundos."
            ], 429);
        }

        // 2. Find the user by their 'correo'
        $user = Usuario::where('correo', $request->correo)->first();

        // 3. Check if user exists and if the password is correct
        if (!$user || !Hash::check($request->contrasenia, $user->contrasenia)) {
            RateLimiter::hit($key, 900);
            return response()->json(['error' => 'Unauthorized: Invalid credentials'], 401);
        }
        RateLimiter::clear($key);
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

