<?php

namespace App\Http\Controllers;

use App\Models\Prueba;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $prueba = Prueba::create([
            'nombre' => $request->input('nombre'),
        ]);

        return response()->json([
            'message' => 'Data inserted successfully!',
            'data' => $prueba,
        ], 201);
    }
}
