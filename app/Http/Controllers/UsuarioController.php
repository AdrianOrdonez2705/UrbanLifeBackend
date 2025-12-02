<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Rol;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            $usuarios = Usuario::with(['rol', 'empleado'])
            ->get()
            ->map(function ($usuario) {
                return [
                    'id_usuario' => $usuario->id_usuario,
                    'correo'     => $usuario->correo,
                    'contrasenia'=> $usuario->contrasenia,
                    'rol'        => $usuario->rol->rol ?? null,
                    'nombre'     => $usuario->empleado->nombre ?? null,
                     'empleadoId'   => $usuario->empleado->id_empleado ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $usuarios
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener usuarios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener los usuarios.'
            ], 500);
        }
    }

    public function show($id_usuario)
    {
        try {
            $usuario = Usuario::with(['rol', 'empleado'])->find($id_usuario);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_usuario' => $usuario->id_usuario,
                    'correo'     => $usuario->correo,
                    'contrasenia'=> $usuario->contrasenia,
                    'rol'        => $usuario->rol->nombre ?? null,
                    'nombre'     => $usuario->empleado->nombre ?? null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener el usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id_usuario)
    {
        try {
            $usuario = Usuario::find($id_usuario);
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ], 404);
            }

            $request->validate([
                'correo'        => 'sometimes|email',
                'rol_id_rol'    => 'sometimes|integer|exists:rol,id_rol',
                'contrasenia'   => 'sometimes|string|min:4',
            ]);

            if ($request->has('correo')) {
                $usuario->correo = $request->correo;
            }

            if ($request->has('contrasenia')) {
                $usuario->contrasenia = Hash::make($request->contrasenia);
            }

            if ($request->has('rol_id_rol')) {
                $usuario->rol_id_rol = $request->rol_id_rol;
            }

            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación inválidos.',
                'errors' => $e->errors()
            ], 422); 
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function borradoLogico($id_usuario)
    {
        try {
            // Buscar el usuario asociado al empleado
            $usuario = Usuario::find($id_usuario);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ], 404);
            }

            if ($usuario->activo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya se encuentra desactivado.'
                ], 400);
            }

            // Desactivar usuario
            $usuario->activo = false;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario desactivado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al desactivar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function asignar_usuario(Request $request) {
        $validator = Validator::make($request->all(), [
            'rol_id_rol' => 'required|integer',
            'correo' => 'required|string|email|unique:usuario',
            'contrasenia' => 'required|string',
            'activo' => 'nullable|boolean',
            'empleado_id_empleado' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $validatedData['contrasenia'] = Hash::make($validatedData['contrasenia']);
 
        try {
            $user = Usuario::create($validatedData);
            $token = JWTAuth::fromUser($user);

            if (!$user) {
                throw new \Exception("Ocurrió un error en la asignación del usuario");
            }

            return response()->json([
                'message' => 'Usuario asignado correctamente',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo asignar un usuario al empleado',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}