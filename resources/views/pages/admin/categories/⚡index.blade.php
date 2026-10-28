<?php

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Categorias')] class extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public ?int $ordem = 0;

    #[Computed]
    public function categories()
    {
        return Category::withCount('products')
            ->orderBy('ordem')
            ->get();
    }

    public function create(): void
    {
        $this->reset('editingId', 'name', 'ordem');
        $this->resetValidation();

        Flux::modal('category-form')->show();
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->resetValidation();
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->ordem = $category->ordem;

        Flux::modal('category-form')->show();
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'ordem' => ['required', 'integer', 'min:0'],
        ]);

        Category::updateOrCreate(['id' => $this->editingId], $data);

        Flux::modal('category-form')->close();
        Flux::toast(variant: 'success', text: $this->editingId ? 'Categoria atualizada.' : 'Categoria criada.');

        $this->reset('editingId', 'name', 'ordem');
        unset($this->categories);
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            Flux::toast(variant: 'danger', text: 'Não é possível excluir: existem itens nesta categoria.');

            return;
        }

        $category->delete();

        Flux::toast(variant: 'success', text: 'Categoria excluída.');
        unset($this->categories);
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-serif text-3xl text-brand-900">Categorias</h1>
            <p class="mt-1 text-sm text-zinc-500">Organize o cardápio na ordem em que aparece no site.</p>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">Nova categoria</flux:button>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b text-left">
                <th class="py-2">Ordem</th>
                <th>Nome</th>
                <th>Itens</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($this->categories as $category)
                <tr class="border-b" wire:key="{{ $category->id }}">
                    <td class="py-2">{{ $category->ordem }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td class="space-x-1 py-2 text-right">
                        <flux:button size="sm" icon="pencil-square" wire:click="edit({{ $category->id }})">Editar</flux:button>
                        <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $category->id }})" wire:confirm="Excluir a categoria {{ $category->name }}?">Excluir</flux:button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-zinc-500">Nenhuma categoria cadastrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <flux:modal name="category-form" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">{{ $editingId ? 'Editar categoria' : 'Nova categoria' }}</flux:heading>

            <flux:input wire:model="name" label="Nome" placeholder="Ex.: Entradas" />

            <flux:input wire:model="ordem" label="Ordem" type="number" min="0" description="Define a posição da categoria no cardápio." />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">Salvar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
