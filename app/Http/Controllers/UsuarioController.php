<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            $usuarios = Usuario::with('rol')->get();
            $usuariosMapeados = $usuarios->map(function ($usuario) {
                return [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol->rol ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $usuariosMapeados
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
            $usuario = Usuario::with('rol')->find($id_usuario);

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ], 404);
            }

            $usuarioMapeado = [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol->rol ?? null,
            ];

            return response()->json([
                'success' => true,
                'data' => $usuarioMapeado
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener el usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function findByNombre($nombre)
    {
        try {
            $usuario = Usuario::with('rol')->where('nombre', $nombre)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ], 404);
            }

            $usuarioMapeado = [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol->rol ?? null,
            ];

            return response()->json([
                'success' => true,
                'data' => $usuarioMapeado
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al buscar usuario por nombre: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al buscar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function findByRol($rolNombre)
    {
        try {
            $usuarios = Usuario::with('rol')
                ->whereHas('rol', function ($query) use ($rolNombre) {
                    $query->where('rol', $rolNombre);
                })
                ->get();

            $usuariosMapeados = $usuarios->map(function ($usuario) {
                return [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol->rol ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $usuariosMapeados
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al buscar usuarios por rol: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al buscar usuarios por rol.',
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

            $validatedData = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'correo' => 'sometimes|string|email|max:255|unique:usuario,correo,' . $id_usuario . ',id_usuario',
                'rol'    => 'sometimes|string|exists:rol,rol' 
            ]);

            $dataToUpdate = $request->only(['nombre', 'correo']);

            if ($request->has('rol')) {
                $rol = Rol::where('rol', $validatedData['rol'])->first();
                if ($rol) {
                    $dataToUpdate['rol_id_rol'] = $rol->id_rol;
                }
            }
            
            if (!empty($dataToUpdate)) {
                $usuario->update($dataToUpdate);
            }

            $usuario->load('rol');

            $usuarioMapeado = [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol->rol ?? null,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.',
                'data' => $usuarioMapeado
            ], 200);

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
}