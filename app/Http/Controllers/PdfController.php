<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialProveedor;
use App\Models\Contabilidad;
use App\Models\MaterialPedido;
use App\Models\Proveedor;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generarPDF($idPedido)
    {
        $pedido = Pedido::with(['proveedor', 'materiales_pedido.materialProveedor'])->findOrFail($idPedido);
        $proveedor = $pedido->proveedor->nombre;
        $materiales = $pedido->materiales_pedido;
        $total = $materiales->sum(function ($item) {
            return $item->cantidad * $item->precio_unitario;
        });


        //$proveedor = Proveedor::with(['pedidos.materialesPedido.materialProveedor'])->find("id_proveedor");
        //$cantidad = MaterialPedido::get();
        //$contabilidad = Contabilidad::get();
        $date = date('d-m-Y');
        $data = [
            'date' => $date,
            'materiales' => $materiales,
            'proveedor' => $proveedor,
            'pedido' => $pedido,
            'total' => $total
        ];
        $pdf = Pdf::loadView('pdf.generarPedidoPDF', $data);
        return $pdf->download("PedidoMaterial{$idPedido}{$date}.pdf");
    }
}
