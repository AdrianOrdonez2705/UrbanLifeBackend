<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
