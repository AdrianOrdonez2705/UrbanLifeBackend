<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Usuario;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActividadSospechosa;
use App\Models\Auditoria;

class AuthController extends Controller
{

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
    
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'contrasenia' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $key = Str::lower($request->correo) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)){

            $usuario = Usuario::where('correo', $request->correo)->first();

            /*if($usuario){
                $actividad = [
                    'fecha' => now()->format('d/m/Y H:i'),
                    'ip' => $request->ip(),
                    'ubicacion' => 'Desconocida'
                    'user_agent' => $request->userAgent(),
                ];

                Mail::to($usuario->correo)->send(new ActividadSospechosa($usuario->toArray(), $actividad));
            }*/

            Auditoria::create([
                'hora' => now(),
                'fecha' => now(),
                'id_usuario' => $usuario ? $usuario->id_usuario : null,
                'status' => 'failed'
            ]);

            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Demasiados intentos fallidos. Intente de nuevo en {$seconds} segundos."
            ], 429);
        }

        $user = Usuario::where('correo', $request->correo)->first();

        if (!$user || !Hash::check($request->contrasenia, $user->contrasenia)) {
            RateLimiter::hit($key, 900);

            Auditoria::create([
                'hora' => now(),
                'fecha' => now(),
                'id_usuario' => $user ? $user->id_usuario : null, 
                'status' => 'failed'
            ]);
            return response()->json(['error' => 'Unauthorized: Invalid credentials'], 401);
        }
        RateLimiter::clear($key);

        $codigo = random_int(100000,999999);

        Cache::put('2fa_' . $user->correo, $codigo, now()->addMinutes(5));

        //Mail::to($user->correo)->send(new Verificacion2Pasos($user->toArray(), $codigo));

        Auditoria::create([
            'hora' => now(),
            'fecha' => now(),
            'id_usuario' => $user->id_usuario,
            'status' => 'success'
        ]);

        return response()->json([
            'message' => 'Código de verificacion enviado',
            'correo' => $user->correo
        ]);
        
    }

    public function verify2FA(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|mail',
            'codigo' => 'required|digits:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $cachedCode = Cache::get('2fa_' . $request->correo);

        if(!$cachedCode || $cachedCode != $request->correo){
            return response()->json(['error' => 'Código invaido o expirado'], 401);
        }

        $user = Usuario::where('correo', $request->correo)->first();
        Cache::forget('2fa_' . $request->correo);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);

    }
    
    public function profile()
    {
        return response()->json(auth('api')->user());
    }
}

