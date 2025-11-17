<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmpleadoController extends Controller
{
    public function index(){
        try {

            $empleados = Empleado::get();

            $empleadosMapeados = $empleados->map(function ($empleado) {

                $usuario = Usuario::where('empleado_id_empleado', $empleado->id_empleado)->exists();

                return [
                    'id_empleado' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre ?? null,
                    'puesto' => $empleado->puesto ?? null,
                    'contrato' => $empleado->contrato ?? null,
                    'usuario' => $usuario
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $empleadosMapeados
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener empleados: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener los empleados.'
            ], 500);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'puesto' => 'required|string',
            'contrato' => 'required|string',
            'activo' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $empleado = Empleado::create($validator->validated());

            if (!$empleado) {
                throw new \Exception("Hubo un error al crear el empleado");
            }

            return response()->json([
                'message' => 'Empleado creado correctamente',
                'data' => $empleado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo crear el empleado',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id_empleado)
    {
        try {
            $empleado = Empleado::find($id_empleado);

            if (!$empleado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado.'
                ], 404);
            }

            $empleadoMapeado = [
                    'id_empleado' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre ?? null,
                    'puesto' => $empleado->puesto ?? null,
                    'contrato' => $empleado->contrato ?? null,
            ];

            return response()->json([
                'success' => true,
                'data' => $empleadoMapeado
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener el empleado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el empleado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id_empleado)
    {
        try {
            $empleado = Empleado::find($id_empleado);
            if (!$empleado) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Empleado no encontrado.'
                ], 404);
            }

            $validatedData = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'puesto' => 'sometimes|string|max:255',
            'contrato' => 'sometimes|string|max:255',
            'activo' => 'sometimes|boolean',
        ]);

            $dataToUpdate = $request->only(['nombre', 'puesto', 'contrato','empleado_id_empleado']);

            
            if (!empty($dataToUpdate)) {
                $empleado->update($dataToUpdate);
            }

            $empleadoMapeado = [
                    'id_empleado' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre ?? null,
                    'puesto' => $empleado->puesto ?? null,
                    'contrato' => $empleado->contrato ?? null,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado correctamente.',
                'data' => $empleadoMapeado
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación inválidos.',
                'errors' => $e->errors()
            ], 422); 
        } catch (\Exception $e) {
            Log::error('Error al actualizar empleado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el empleado.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function borradoLogico($id_empleado) {
        try {
            $empleado = Empleado::find($id_empleado);
            $usuario = Usuario::where('empleado_id_empleado', $id_empleado)->first();

            if (!$empleado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado.'
                ], 404);
            }
            
            if (!$usuario) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Usuario asociado al empleado no encontrado.'
                ], 404);
            }

            if ($empleado->activo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya se encuentra desactivado.'
                ], 400);
            }

            DB::transaction(function () use ($usuario, $empleado) {
                $empleado->activo = false;
                $empleado->save();

                $usuario->activo = false;
                $usuario->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Usuario y empleado desactivados correctamente.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en borrado lógico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
