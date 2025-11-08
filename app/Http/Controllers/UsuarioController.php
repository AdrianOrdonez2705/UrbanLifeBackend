<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}