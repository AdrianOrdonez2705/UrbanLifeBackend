<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
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
}
