<?php

use App\Models\Config;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Configurações')] class extends Component
{
    #[Computed]
    public function configs()
    {
        return Config::orderBy('chave')->get();
    }
};
?>

<div class="space-y-6">
    <flux:heading size="xl">Configurações</flux:heading>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left">
                <th class="py-2">Chave</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->configs as $config)
                <tr class="border-b" wire:key="{{ $config->id }}">
                    <td class="py-2">{{ $config->chave }}</td>
                    <td>{{ $config->valor }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="py-6 text-center text-zinc-500">Nenhuma configuração cadastrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
