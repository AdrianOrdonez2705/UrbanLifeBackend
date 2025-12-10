<?php

namespace App\Http\Controllers;

use App\Models\MaterialAlmacen;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class MaterialAlmacenController extends Controller
{
    public function index() : JsonResponse 
    {
        $materiales = MaterialAlmacen::all(['id_material_almacen', 'nombre', 'cantidad']);
        
        if (!$materiales) {
            return response()->json(['message' => 'No hay materiales en almacen'], 404);
        }

        return response()->json($materiales, 200);
    }
}
