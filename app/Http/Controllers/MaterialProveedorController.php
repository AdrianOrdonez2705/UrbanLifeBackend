<?php

namespace App\Http\Controllers;

use App\Models\MaterialProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialProveedorController extends Controller
{
    public function index() {
        $material_proveedor = MaterialProveedor::all();
        return response()->json($material_proveedor);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'proveedor_id_proveedor' => 'required|integer',
            'material' => 'required|string',
            'descripcion' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $material_proveedor = MaterialProveedor::create($validator->validated());

        return response()->json($material_proveedor, 201);
    }

    public function update(Request $request, $id_material) {
        $material_proveedor = MaterialProveedor::find($id_material);

        if (!$material_proveedor) {
            return response()->json(['message' => 'Material del proveedor no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'material' => 'sometimes|required|string',
            'descripcion' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $material_proveedor->update($validator->validated());

        return response()->json($material_proveedor);
    }
}
