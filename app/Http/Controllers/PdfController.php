<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialProveedor;
use App\Models\Contabilidad;
use App\Models\MaterialPedido;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generarPDF()
    {
        $materiales = MaterialProveedor::get();
        $date = date('m/d/Y');
        $data = [
            'title' => 'Pedido',
            'date' => $date,
            'materiales' => $materiales
        ];
        $pdf = Pdf::loadView('pdf.generarPedidoPDF', $data);
        return $pdf->download('PedidoMaterial.pdf');
    }
}
