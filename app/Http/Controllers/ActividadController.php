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
    public function index() : JsonResponse 
    {
        $actividades = Actividad::with([
            'imagenes' => function ($query) {
                $query->select('actividad_id_actividad', 'ruta');
            },
            'empleados' => function ($query) {
                $query->select('id_empleado', 'nombre');
            }
        ])->get();

        $data = $actividades->map(function ($actividad) {
            $rutasImagenes = $actividad->imagenes->pluck('ruta')->map(function ($ruta) {
                return ['ruta' => $ruta];
            });
            $nombresEmpleados = $actividad->empleados->pluck('nombre')->map(function ($nombre) {
                return ['nombre' => $nombre];
            });

            return [
                'id_actividad' => $actividad->id_actividad,
                //'contrato_id_contrato' => $actividad->contrato_id_contrato,
                'proyecto_id_proyecto' => $actividad->proyecto_id_proyecto,
                'nombre' => $actividad->nombre,
                'descripcion' => $actividad->descripcion,
                'fecha' => $actividad->fecha,
                'estado' => $actividad->estado,
                'imagenes' => $rutasImagenes,
                'empleados' => $nombresEmpleados
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            //'contrato_id_contrato' => 'required|integer',
            'nombre' => 'required|string',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'estado' => 'required|string',
            'proyecto_id_proyecto' => 'required|integer',
            'empleados' => 'array',
            'empleados.*.id_empleado' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $actividadData = $validator->validated();

        try {
            DB::beginTransaction();

            $actividad = Actividad::create([
                //'contrato_id_contrato' => $actividadData['contrato_id_contrato'],
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

    public function cambiarEnProgreso(Request $request) : JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'id_actividad' => 'required|integer',
            'estado' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $idActividad = $data['id_actividad'];

        $actividad = Actividad::find($idActividad);

        if (!$actividad) {
            return response()->json(['message' => 'Actividad no encontrada'], 404);
        }

        $actividad->estado = 'en progreso';
        $actividad->save();

        return response()->json([
            'message' => 'Actividad actualizada correctamente',
            'actividad' => $actividad
        ], 200);
    }

    public function cambiarFinalizado(Request $request) : JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'id_actividad' => 'required|integer',
            'estado' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        $idActividad = $data['id_actividad'];

        $actividad = Actividad::find($idActividad);

        if (!$actividad) {
            return response()->json(['message' => 'Actividad no encontrada'], 404);
        }

        $actividad->estado = 'finalizado';
        $actividad->save();

        return response()->json([
            'message' => 'Actividad finalizada correctamente',
            'actividad' => $actividad
        ], 200);
    }
}
