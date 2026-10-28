<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Funcionários')] class extends Component
{
    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }
};
?>

<div class="space-y-6">
    <flux:heading size="xl">Funcionários</flux:heading>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left">
                <th class="py-2">Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->users as $user)
                <tr class="border-b" wire:key="{{ $user->id }}">
                    <td class="py-2">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role === 'admin' ? 'Administrador' : 'Funcionário' }}</td>
                    <td>{{ $user->ativo ? 'Ativo' : 'Inativo' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-zinc-500">Nenhum funcionário cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
