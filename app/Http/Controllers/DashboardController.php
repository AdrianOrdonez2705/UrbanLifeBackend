<?php

namespace App\Http\Controllers;

use App\Models\MaterialAlmacen;
use App\Models\Pedido;
use App\Models\Proyecto;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overview() : JsonResponse 
    {
        $totalUsuarios = Usuario::count();
        $totalProyectos = Proyecto::count();
        $totalRecibidos = Pedido::where('estado', 'recibido')->count();
        $totalTransito = Pedido::where('estado', 'transito')->count();
        $totalRechazados = Pedido::where('estado', 'rechazado')->count();
        $materiales = MaterialAlmacen::select('nombre', 'cantidad')->get();

        return response()->json([
            'success' => true,
            'totalUsuarios' => $totalUsuarios,
            'totalProyectos' => $totalProyectos,
            'totalRecibidos' => $totalRecibidos,
            'totalTransito' => $totalTransito,
            'totalRechazados' => $totalRechazados,
            'materiales' => $materiales
        ], 200);
    }
}
