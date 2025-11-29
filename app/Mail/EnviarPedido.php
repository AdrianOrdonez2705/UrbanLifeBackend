<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarPedido extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $proveedor;
    public $total;
    public $date;
    public $pdf;

    public function __construct($pedido, $proveedor, $total, $date, $pdf)
    {
        $this->pedido = $pedido;
        $this->proveedor = $proveedor;
        $this->total = $total;
        $this->date = $date;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Pedido de materiales')
                    ->view('mail.enviar-pedido') // Vista del correo
                    ->attachData(
                        $this->pdf->output(),
                        "PedidoMaterial_{$this->date}.pdf",
                        ['mime' => 'application/pdf']
                    );
    }
}