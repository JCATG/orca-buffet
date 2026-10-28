<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pedido')] class extends Component
{
    public Order $order;

    /** @var array<int, string> */
    public array $valores = [];

    public ?int $novoProdutoId = null;

    public string $mensagem = '';

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->carregarItens();
    }

    #[Computed]
    public function valorPorPessoa(): float
    {
        return collect($this->valores)->sum(fn ($valor) => is_numeric($valor) ? (float) $valor : 0);
    }

    #[Computed]
    public function total(): float
    {
        return $this->valorPorPessoa * $this->order->convidados;
    }

    #[Computed]
    public function produtosDisponiveis()
    {
        return Product::with('category')
            ->where('status', true)
            ->whereNotIn('id', $this->order->items->pluck('product_id'))
            ->orderBy('name')
            ->get();
    }

    public function salvar(): void
    {
        $this->persistir();

        Flux::toast(variant: 'success', text: 'Orçamento salvo.');
    }

    public function adicionarItem(): void
    {
        $this->validate(['novoProdutoId' => ['required', 'exists:products,id']], [], ['novoProdutoId' => 'item']);

        $this->persistir();

        $produto = Product::findOrFail($this->novoProdutoId);

        $this->order->items()->create([
            'product_id' => $produto->id,
            'valor_unitario' => $produto->price,
        ]);

        $this->novoProdutoId = null;
        $this->order->recalcularTotal();
        $this->carregarItens();

        Flux::toast(variant: 'success', text: "{$produto->name} adicionado.");
    }

    public function removerItem(int $itemId): void
    {
        $this->order->items()->whereKey($itemId)->delete();
        unset($this->valores[$itemId]);

        $this->persistir();

        Flux::toast(text: 'Item removido.');
    }

    public function abrirEnvio(): void
    {
        $this->persistir();

        $primeiroNome = strtok($this->order->customer->name, ' ');
        $link = route('orcamento.publico', $this->order->token);

        $this->mensagem = "Olá, {$primeiroNome}! Segue o orçamento do seu evento ({$this->order->tipo_evento} em {$this->order->data_evento->format('d/m/Y')}):\n\n{$link}\n\nQualquer dúvida, estamos à disposição!";

        Flux::modal('enviar-orcamento')->show();
    }

    public function marcarComoEnviado(): void
    {
        $this->order->update([
            'status' => OrderStatus::Enviado,
            'enviado_em' => now(),
        ]);

        Flux::modal('enviar-orcamento')->close();
        Flux::toast(variant: 'success', text: 'Orçamento marcado como enviado.');
    }

    protected function persistir(): void
    {
        $this->validate([
            'valores' => ['array'],
            'valores.*' => ['required', 'numeric', 'min:0'],
        ], [], ['valores.*' => 'valor']);

        $this->order->load('items');

        foreach ($this->order->items as $item) {
            if (isset($this->valores[$item->id])) {
                $item->update(['valor_unitario' => $this->valores[$item->id]]);
            }
        }

        $this->order->recalcularTotal();
        $this->carregarItens();
    }

    protected function carregarItens(): void
    {
        $this->order->load('customer', 'items.product.category');

        $this->valores = $this->order->items
            ->mapWithKeys(fn ($item) => [$item->id => number_format((float) $item->valor_unitario, 2, '.', '')])
            ->all();

        unset($this->produtosDisponiveis);
    }
};
?>

<div class="mx-auto max-w-4xl space-y-6">
    <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('admin.orcamento.index')" wire:navigate>Voltar para pedidos</flux:button>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="font-serif text-3xl text-brand-900">Pedido {{ $order->numero() }}</h1>
            <flux:badge :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
        </div>

        @if ($order->enviado_em)
            <p class="text-sm text-zinc-500">Enviado em {{ $order->enviado_em->format('d/m/Y \à\s H:i') }}</p>
        @endif
    </div>

    <div class="grid gap-6 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm sm:grid-cols-2">
        <div class="space-y-1 text-sm">
            <p class="text-xs font-semibold tracking-wide text-zinc-400 uppercase">Cliente</p>
            <p class="font-medium text-zinc-800">{{ $order->customer->name }}</p>
            <p class="text-zinc-500">{{ $order->customer->phone }}</p>
            @if ($order->customer->email)
                <p class="text-zinc-500">{{ $order->customer->email }}</p>
            @endif
            <p class="text-zinc-500">{{ $order->customer->city }}</p>
        </div>

        <div class="space-y-1 text-sm">
            <p class="text-xs font-semibold tracking-wide text-zinc-400 uppercase">Evento</p>
            <p class="font-medium text-zinc-800">{{ $order->tipo_evento }} — {{ $order->data_evento->format('d/m/Y') }}</p>
            <p class="text-zinc-500">{{ $order->local_evento }}</p>
            <p class="text-zinc-500">{{ $order->convidados }} convidados</p>
        </div>

        @if ($order->observacoes)
            <div class="space-y-1 text-sm sm:col-span-2">
                <p class="text-xs font-semibold tracking-wide text-zinc-400 uppercase">Observações</p>
                <p class="text-zinc-600">{{ $order->observacoes }}</p>
            </div>
        @endif
    </div>

    <div class="space-y-4 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
        <h2 class="font-serif text-xl text-brand-900">Itens selecionados</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-brand-100 text-left text-zinc-500">
                    <th class="py-2 font-medium">Item</th>
                    <th class="w-44 py-2 font-medium">Valor por pessoa (R$)</th>
                    <th class="w-12 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->itensPorCategoria() as $categoria => $itens)
                    <tr wire:key="categoria-{{ $categoria }}">
                        <td colspan="3" class="pt-5 pb-1 text-xs font-semibold tracking-wide text-brand-700 uppercase">{{ $categoria }}</td>
                    </tr>
                    @foreach ($itens as $item)
                        <tr class="border-b border-brand-50" wire:key="item-{{ $item->id }}">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    @if ($item->product->fotoUrl())
                                        <img src="{{ $item->product->fotoUrl() }}" alt="" class="size-10 rounded-lg object-cover">
                                    @endif
                                    <span class="font-medium text-zinc-800">{{ $item->product->name }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <flux:input size="sm" type="number" step="0.01" min="0" wire:model.blur="valores.{{ $item->id }}" />
                            </td>
                            <td class="py-3 text-right">
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="removerItem({{ $item->id }})" wire:confirm="Remover {{ $item->product->name }} do orçamento?" />
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-zinc-500">Nenhum item neste orçamento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <form wire:submit="adicionarItem" class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-end">
            <div class="flex-1">
                <flux:select wire:model="novoProdutoId" label="+ Adicionar item" placeholder="Selecione um item do cardápio...">
                    @foreach ($this->produtosDisponiveis as $produto)
                        <flux:select.option :value="$produto->id">
                            {{ $produto->category->name }} — {{ $produto->name }} (R$ {{ number_format($produto->price, 2, ',', '.') }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <flux:button type="submit" icon="plus">Adicionar</flux:button>
        </form>
    </div>

    <div class="space-y-2 rounded-2xl border border-brand-100 bg-white p-6 text-sm shadow-sm">
        <div class="flex justify-between text-zinc-600">
            <span>Valor por pessoa</span>
            <span>R$ {{ number_format($this->valorPorPessoa, 2, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-zinc-600">
            <span>Convidados</span>
            <span>× {{ $order->convidados }}</span>
        </div>
        <div class="flex justify-between border-t border-brand-100 pt-3 font-serif text-xl text-brand-900">
            <span>Total</span>
            <span>R$ {{ number_format($this->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-2">
        <flux:button icon="document-text" :href="route('orcamento.publico', $order->token)" target="_blank">Ver PDF</flux:button>
        <flux:button wire:click="salvar">Salvar</flux:button>
        <flux:button variant="primary" icon="paper-airplane" wire:click="abrirEnvio">
            {{ $order->status === \App\Enums\OrderStatus::Enviado ? 'Reenviar pelo WhatsApp' : 'Gerar PDF e enviar WhatsApp' }}
        </flux:button>
    </div>

    <flux:modal name="enviar-orcamento" class="md:w-[32rem]">
        <div class="space-y-6">
            <div>
                <h2 class="font-serif text-2xl text-brand-900">Enviar orçamento</h2>
                <div class="mt-3 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-800">
                    A mensagem abaixo abre o WhatsApp de <strong>{{ $order->customer->name }}</strong> ({{ $order->customer->phone }}) com o link do orçamento. Você pode editar antes de enviar.
                </div>
            </div>

            <flux:textarea wire:model="mensagem" label="Mensagem (opcional)" rows="7" />

            <div class="flex flex-col gap-2">
                <flux:button
                    variant="primary"
                    icon="chat-bubble-left-right"
                    href="#"
                    target="_blank"
                    x-bind:href="'https://wa.me/{{ $order->customer->whatsappNumber() }}?text=' + encodeURIComponent($wire.mensagem)"
                    wire:click="marcarComoEnviado"
                >
                    Enviar pelo WhatsApp
                </flux:button>

                <flux:button icon="arrow-down-tray" :href="route('orcamento.pdf', $order->token)">Baixar PDF</flux:button>

                <flux:modal.close>
                    <flux:button variant="ghost" class="w-full">Voltar para o pedido</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
