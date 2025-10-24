<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'mensaje' => 'required|string',
        ]);

        $mensaje = Mensaje::create([
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'mensaje' => $request->input('mensaje'),
        ]);

        return response()->json([
            'message' => 'Data inserted successfully!',
            'data' => $mensaje,
        ], 201);
    }
}
