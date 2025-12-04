<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProyectoController extends Controller
{
    public function index(): JsonResponse {
        $proyectos = Proyecto::join('documento', 'proyecto.id_proyecto', '=', 'documento.proyecto_id_proyecto')
                            ->select('proyecto.nombre', 'documento.ruta')
                            ->orderBy('proyecto.id_proyecto', 'desc')
                            ->take(3)
                            ->get();
        return response()->json($proyectos);
    }

    public function getProyectosActivos() {
        $now = Carbon::now();

        $proyectosActivos = Proyecto::whereDate('fecha_fin', '>', $now)
            ->select('id_proyecto', 'nombre as nombre_proyecto', 'fecha_fin')
            ->get();

        if (!$proyectosActivos) {
            return response()->json(['message' => 'No se encontraron proyectos activos'], 204);
        }

        return response()->json($proyectosActivos);
    }

    public function getAllProjectsData(): JsonResponse
    {
        $proyectos = Proyecto::with(['documentos', 'actividades', 'empleado'])->get();

        $data = $proyectos->map(function ($proyecto) {
            return [
                'id_proyecto' => $proyecto->id_proyecto,
                'nombre' => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
                'fecha_inicio' => $proyecto->fecha_inicio ? $proyecto->fecha_inicio->format('Y-m-d') : null,
                'fecha_fin' => $proyecto->fecha_fin ? $proyecto->fecha_fin->format('Y-m-d') : null,
                'estado' => $proyecto->estado,
                'presupuesto' => $proyecto->presupuesto,
                'departamento' => $proyecto->departamento,
                'nombre_empleado' => $proyecto->empleado ? $proyecto->empleado->nombre : 'Sin Empleado Asignado',

                'documentos' => $proyecto->documentos->map(function ($documento) {
                    return [
                        'nombre_documento' => $documento->nombre,
                        'tipo' => $documento->tipo,
                        'ruta' => $documento->ruta,
                    ];
                })->toArray(),

                'actividades' => $proyecto->actividades->map(function ($actividad) {
                    return [
                        'nombre_actividad' => $actividad->nombre,
                        'descripcion' => $actividad->descripcion,
                        'fecha' => $actividad->fecha ? $actividad->fecha->format('Y-m-d') : null,
                        'estado' => $actividad->estado,
                    ];
                })->toArray(),
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string',
                'descripcion' => 'required|string',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'estado' => 'required|string',
                'presupuesto' => 'required|numeric|min:0',
                'departamento' => 'required|string',
                'id_usuario' => 'nullable|integer|exists:usuario,id_usuario',
                'id_empleado' => 'nullable|integer|exists:empleado,id_empleado',
            ]);

            $proyecto = Proyecto::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Proyecto registrado correctamente.',
                'data' => $proyecto
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación al registrar el proyecto.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al registrar proyecto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al intentar registrar el proyecto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}