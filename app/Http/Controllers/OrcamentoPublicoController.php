<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class OrcamentoPublicoController extends Controller
{
    public function show(Order $order): View
    {
        $order->load('customer', 'items.product.category');

        return view('orcamento.documento', ['order' => $order, 'paraPdf' => false]);
    }

    public function pdf(Order $order): Response
    {
        $order->load('customer', 'items.product.category');

        return Pdf::loadView('orcamento.documento', ['order' => $order, 'paraPdf' => true])
            ->download('orcamento-'.ltrim($order->numero(), '#').'.pdf');
    }
}
