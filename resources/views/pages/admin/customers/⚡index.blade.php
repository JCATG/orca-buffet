<?php

use App\Models\Customer;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Clientes')] class extends Component
{
    #[Computed]
    public function customers()
    {
        return Customer::withCount('orders')
            ->orderBy('name')
            ->get();
    }
};
?>

<div class="space-y-6">
    <flux:heading size="xl">Clientes</flux:heading>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left">
                <th class="py-2">Nome</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Cidade</th>
                <th>Orçamentos</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->customers as $customer)
                <tr class="border-b" wire:key="{{ $customer->id }}">
                    <td class="py-2">{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->email ?? '—' }}</td>
                    <td>{{ $customer->city }}</td>
                    <td>{{ $customer->orders_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-zinc-500">Nenhum cliente cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
