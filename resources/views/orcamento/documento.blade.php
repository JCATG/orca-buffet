<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orçamento {{ $order->numero() }} — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #27272a; background: {{ $paraPdf ? '#ffffff' : '#f4f4f5' }}; }
        .pagina { max-width: 760px; margin: 0 auto; padding: {{ $paraPdf ? '0' : '24px 16px' }}; }
        .documento { background: #ffffff; padding: {{ $paraPdf ? '0' : '32px' }}; border-radius: 8px; }
        .acoes { text-align: right; margin-bottom: 16px; }
        .botao { display: inline-block; background: #166534; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-size: 14px; }
        .marca { font-size: 22px; color: #166534; margin: 0; }
        .marca-sub { font-size: 10px; letter-spacing: 2px; color: #71717a; text-transform: uppercase; margin: 2px 0 0; }
        h1 { font-size: 18px; margin: 24px 0 12px; }
        .dados { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .dados td { padding: 3px 0; vertical-align: top; }
        .dados .rotulo { color: #71717a; width: 140px; }
        .itens { width: 100%; border-collapse: collapse; }
        .itens th { text-align: left; font-size: 11px; color: #52525b; border-bottom: 2px solid #166534; padding: 8px 6px; }
        .itens td { padding: 7px 6px; border-bottom: 1px solid #e4e4e7; }
        .itens .categoria { font-weight: bold; color: #166534; width: 150px; }
        .itens .valor { text-align: right; white-space: nowrap; width: 130px; }
        .resumo { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .resumo td { padding: 4px 6px; }
        .resumo .valor { text-align: right; width: 160px; }
        .resumo .total td { font-size: 15px; font-weight: bold; border-top: 2px solid #166534; padding-top: 8px; }
        .observacoes { margin-top: 20px; padding: 10px 12px; background: #f4f4f5; border-radius: 6px; }
        .rodape { margin-top: 28px; font-size: 10px; color: #a1a1aa; text-align: center; }
    </style>
</head>
<body>
    <div class="pagina">
        @unless ($paraPdf)
            <div class="acoes">
                <a class="botao" href="{{ route('orcamento.pdf', $order->token) }}">Baixar PDF</a>
            </div>
        @endunless

        <div class="documento">
            <p class="marca">{{ config('app.name') }}</p>
            <p class="marca-sub">Buffet e eventos</p>

            <h1>Orçamento de Buffet {{ $order->numero() }}</h1>

            <table class="dados">
                <tr><td class="rotulo">Cliente</td><td>{{ $order->customer->name }}</td></tr>
                <tr><td class="rotulo">Tipo de evento</td><td>{{ $order->tipo_evento }}</td></tr>
                <tr><td class="rotulo">Data do evento</td><td>{{ $order->data_evento->format('d/m/Y') }}</td></tr>
                <tr><td class="rotulo">Local</td><td>{{ $order->local_evento }}</td></tr>
                <tr><td class="rotulo">Convidados</td><td>{{ $order->convidados }}</td></tr>
            </table>

            <table class="itens">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Item</th>
                        <th class="valor">Valor por pessoa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->itensPorCategoria() as $categoria => $itens)
                        @foreach ($itens as $item)
                            <tr>
                                <td class="categoria">{{ $loop->first ? $categoria : '' }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="valor">R$ {{ number_format((float) $item->valor_unitario, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <table class="resumo">
                <tr>
                    <td>Valor por pessoa</td>
                    <td class="valor">R$ {{ number_format($order->valorPorPessoa(), 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Convidados</td>
                    <td class="valor">× {{ $order->convidados }}</td>
                </tr>
                <tr class="total">
                    <td>Total do orçamento</td>
                    <td class="valor">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                </tr>
            </table>

            @if ($order->observacoes)
                <div class="observacoes"><strong>Observações:</strong> {{ $order->observacoes }}</div>
            @endif

            <p class="rodape">Orçamento gerado em {{ now()->format('d/m/Y') }} · {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
