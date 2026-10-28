<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Pedidos')] class extends Component
{
    use WithPagination;

    public string $status = '';

    public string $busca = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function orders()
    {
        return Order::query()
            ->with('customer:id,name')
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->busca !== '', function ($query) {
                $termo = '%'.$this->busca.'%';

                $query->where(function ($inner) use ($termo) {
                    $inner->whereHas('customer', fn ($customer) => $customer->where('name', 'like', $termo))
                        ->orWhere('tipo_evento', 'like', $termo)
                        ->orWhere('local_evento', 'like', $termo);

                    if (ctype_digit($this->busca)) {
                        $inner->orWhere('id', (int) $this->busca);
                    }
                });
            })
            ->latest()
            ->paginate(20);
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl text-brand-900">Pedidos</h1>
            <p class="mt-1 text-sm text-zinc-500">Pedidos recebidos pelo site e orçamentos em andamento.</p>
        </div>

        <form wire:submit="$refresh" class="w-full max-w-xs">
            <flux:input wire:model="busca" placeholder="Buscar cliente, evento... Enter" icon="magnifying-glass" />
        </form>
    </div>

    <div class="flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="$set('status', '')"
            @class([
                'rounded-full px-4 py-1.5 text-sm font-medium transition',
                'bg-brand-800 text-white' => $status === '',
                'bg-white text-zinc-600 ring-1 ring-brand-100 hover:bg-brand-50' => $status !== '',
            ])
        >Todos</button>

        @foreach (OrderStatus::cases() as $case)
            <button
                type="button"
                wire:click="$set('status', '{{ $case->value }}')"
                @class([
                    'rounded-full px-4 py-1.5 text-sm font-medium transition',
                    'bg-brand-800 text-white' => $status === $case->value,
                    'bg-white text-zinc-600 ring-1 ring-brand-100 hover:bg-brand-50' => $status !== $case->value,
                ])
            >{{ $case->label() }}</button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-creme-dark/60 text-left text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Nº</th>
                    <th class="px-4 py-3 font-medium">Cliente</th>
                    <th class="px-4 py-3 font-medium">Evento</th>
                    <th class="px-4 py-3 font-medium">Data</th>
                    <th class="px-4 py-3 font-medium">Convidados</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->orders as $order)
                    <tr class="border-t border-brand-50 hover:bg-brand-50/40" wire:key="{{ $order->id }}">
                        <td class="px-4 py-3 font-medium text-brand-900">{{ $order->numero() }}</td>
                        <td class="px-4 py-3">{{ $order->customer->name }}</td>
                        <td class="px-4 py-3">{{ $order->tipo_evento }}</td>
                        <td class="px-4 py-3">{{ $order->data_evento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $order->convidados }}</td>
                        <td class="px-4 py-3">R$ {{ number_format((float) $order->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <flux:badge size="sm" :color="$order->status->color()">{{ $order->status->label() }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orcamento.show', $order) }}" wire:navigate class="inline-flex items-center rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-800 hover:bg-zinc-50">
                                Abrir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-zinc-500">Nenhum pedido encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->orders->links() }}
    </div>
</div>
