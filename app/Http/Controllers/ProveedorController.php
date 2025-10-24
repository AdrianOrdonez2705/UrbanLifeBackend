<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(): JsonResponse {
        $proveedores = Proveedor::select('nombre', 'visibilidad', 'logo', 'web')->where('visibilidad', true)->get();
        return response()->json($proveedores);
    }
}
