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
}
