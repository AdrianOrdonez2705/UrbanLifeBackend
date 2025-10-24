<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(): JsonResponse {
        $proyectos = Proyecto::select('nombre','imagen')->orderBy('id_proyecto', 'desc')->take(3)->get();
        return response()->json($proyectos);
    }
}
