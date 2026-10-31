<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Funcionários')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'funcionario';

    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    public function create(): void
    {
        $this->reset('name', 'email', 'password', 'password_confirmation');
        $this->role = 'funcionario';
        $this->resetValidation();

        Flux::modal('user-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['funcionario', 'admin'])],
        ], [
            'name.required' => 'Informe o nome.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'Informe uma senha.',
            'password.min' => 'A senha precisa ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'role.required' => 'Escolha o perfil.',
            'role.in' => 'Escolha um perfil da lista.',
        ]);

        $user = new User;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->role = $this->role;
        $user->ativo = true;
        $user->email_verified_at = now();
        $user->save();

        Flux::modal('user-form')->close();
        Flux::toast(variant: 'success', text: 'Funcionário cadastrado.');

        $this->reset('name', 'email', 'password', 'password_confirmation');
        $this->role = 'funcionario';
        unset($this->users);
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-serif text-3xl text-brand-900">Funcionários</h1>
            <p class="mt-1 text-sm text-zinc-500">Quem pode entrar no painel do buffet.</p>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">Novo funcionário</flux:button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-creme-dark/60 text-left text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Nome</th>
                    <th class="px-4 py-3 font-medium">E-mail</th>
                    <th class="px-4 py-3 font-medium">Perfil</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->users as $user)
                    <tr class="border-t border-brand-50" wire:key="{{ $user->id }}">
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role === 'admin' ? 'Administrador' : 'Funcionário' }}</td>
                        <td class="px-4 py-3">
                            <flux:badge size="sm" :color="$user->ativo ? 'green' : 'zinc'">
                                {{ $user->ativo ? 'Ativo' : 'Inativo' }}
                            </flux:badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-500">Nenhum funcionário cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal name="user-form" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">Novo funcionário</flux:heading>

            <flux:input wire:model="name" label="Nome" autocomplete="off" />

            <flux:input wire:model="email" label="E-mail" type="email" autocomplete="off" />

            <flux:input wire:model="password" label="Senha" type="password" viewable autocomplete="new-password" />

            <flux:input wire:model="password_confirmation" label="Confirmar senha" type="password" viewable autocomplete="new-password" />

            <flux:select wire:model="role" label="Perfil">
                <flux:select.option value="funcionario">Funcionário</flux:select.option>
                <flux:select.option value="admin">Administrador</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">Salvar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
