<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    public function index(): JsonResponse {
        $proveedores = Proveedor::select(
            'id_proveedor',
            'nombre',
            'contacto',
            'telefono',
            'correo',
            'direccion', 
            'visibilidad', 
            'logo', 
            'web',
        )->where('visibilidad', true)->get();
        return response()->json($proveedores);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'contacto' => 'required|string',
            'telefono' => 'required|string',
            'correo' => 'required|string',
            'direccion' => 'required|string',
            'logo' => 'nullable|string',
            'web' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $proveedor = Proveedor::create($validator->validated());
        return response()->json($proveedor, 201);
    }

    public function update(Request $request, $id_proveedor) {
        $proveedor = Proveedor::find($id_proveedor);

        if (!$proveedor) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string',
            'contacto' => 'sometimes|required|string',
            'telefono' => 'sometimes|required|string',
            'correo' => 'sometimes|required|string',
            'direccion' => 'sometimes|required|string',
            'visibilidad' => 'sometimes|boolean',
            'logo' => 'sometimes|nullable|string',
            'web' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $proveedor->update($validator->validated());

        return response()->json($proveedor);
    }

    public function getProveedoresWithMateriales() {
        $proveedores = Proveedor::with('materiales')->get();

        if (!$proveedores) {
            return response()->json(['message' => 'No se encontraron proveedores ni materiales'], 204);
        }

        $data = $proveedores->map(function ($proveedor) {
            return [
                'id_proveedor' => $proveedor->id_proveedor,
                'nombre' => $proveedor->nombre,
                'correo' => $proveedor->correo,
                'materiales' => $proveedor->materiales->map(function ($material) {
                    return [
                        'id_material_proveedor' => $material->id_material,
                        'material' => $material->material,
                    ];
                })
            ];
        });

        return response()->json($data, 200);
    }
}
