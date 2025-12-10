<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\EmpleadoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActividadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contrato_id_contrato' => 'required|integer',
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'estado' => 'required|string',
            'proyecto_id_proyecto' => 'required|integer',
            'empleados' => 'required|array|min:1',
            'empleados.*.id_empleado' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $actividadData = $validator->validated();

        try {
            DB::beginTransaction();

            $actividad = Actividad::create([
                'contrato_id_contrato' => $actividadData['contrato_id_contrato'],
                'nombre' => $actividadData['nombre'],
                'descripcion' => $actividadData['descripcion'],
                'fecha' => $actividadData['fecha'],
                'estado' => $actividadData['estado'],
                'proyecto_id_proyecto' => $actividadData['proyecto_id_proyecto'],
            ]);

            $idActividad = $actividad->id_actividad;

            foreach ($actividadData['empleados'] as $empleado) {
                EmpleadoActividad::create([
                    'empleado_id_empleado' => $empleado['id_empleado'],
                    'actividad_id_actividad' => $idActividad
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al hacer el registro',
                'errores' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Registro realizado correctamente',
            'data' => $actividad
        ], 201);
    }
}
