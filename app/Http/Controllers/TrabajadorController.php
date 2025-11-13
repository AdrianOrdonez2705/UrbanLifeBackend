<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrabajadorController extends Controller
{
    public function index() {
        $trabajadores = Trabajador::all();
        return response()->json($trabajadores);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'fecha_nac' => 'required|date',
            'experiencia' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $trabajador = Trabajador::create($validator->validated());
        return response()->json($trabajador, 201);
    }

    public function update(Request $request, $id_trabajador) {
        
        $trabajador = Trabajador::find($id_trabajador);

        if (!$trabajador) {
            return response()->json(['message' => 'Trabajador no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'fecha_nac' => 'sometimes|required|date',
            'experiencia' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $trabajador->update($validator->validated());

        return response()->json($trabajador);
    }
}
