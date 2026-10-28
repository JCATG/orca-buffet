<?php

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Itens do cardápio')] class extends Component
{
    use WithFileUploads;

    public ?int $editingItemId = null;

    public string $name = '';

    public ?int $category_id = null;

    public ?string $descricao = '';

    public ?string $price = null;

    public bool $status = true;

    public $foto = null;

    public ?string $fotoAtual = null;

    #[Computed]
    public function products()
    {
        return Product::with('category')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('ordem')->get();
    }

    public function create(): void
    {
        $this->reset('editingItemId', 'name', 'category_id', 'descricao', 'price', 'status', 'foto', 'fotoAtual');
        $this->status = true;
        $this->resetValidation();

        Flux::modal('product-item-form')->show();
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->resetValidation();
        $this->editingItemId = $product->id;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->descricao = $product->descricao;
        $this->price = $product->price;
        $this->status = (bool) $product->status;
        $this->foto = null;
        $this->fotoAtual = $product->fotoUrl();

        Flux::modal('product-item-form')->show();
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'descricao' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['boolean'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($data['foto']);

        $product = $this->editingItemId
            ? Product::findOrFail($this->editingItemId)
            : new Product;

        $product->fill($data);

        if ($this->foto) {
            if ($product->foto) {
                Storage::disk(config('filesystems.media'))->delete($product->foto);
            }

            $product->foto = $this->foto->store('produtos', config('filesystems.media'));
        }

        $product->save();

        Flux::modal('product-item-form')->close();
        Flux::toast(variant: 'success', text: $this->editingItemId ? 'Item atualizado.' : 'Item criado.');

        $this->reset('editingItemId', 'name', 'category_id', 'descricao', 'price', 'status', 'foto', 'fotoAtual');
        unset($this->products);
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);

        if (OrderItem::where('product_id', $product->id)->exists()) {
            Flux::toast(variant: 'danger', text: 'Este item já está em orçamentos. Desative-o em vez de excluir.');

            return;
        }

        if ($product->foto) {
            Storage::disk(config('filesystems.media'))->delete($product->foto);
        }

        $product->delete();

        Flux::toast(variant: 'success', text: 'Item excluído.');
        unset($this->products);
    }
};
?>

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-serif text-3xl text-brand-900">Itens do cardápio</h1>
            <p class="mt-1 text-sm text-zinc-500">Cadastre os pratos com foto para aparecerem no pedido online.</p>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">Novo item</flux:button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-creme-dark/60 text-left text-zinc-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Item</th>
                    <th class="px-4 py-3 font-medium">Categoria</th>
                    <th class="px-4 py-3 font-medium">Preço / pessoa</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->products as $product)
                    <tr class="border-t border-brand-50" wire:key="{{ $product->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($product->fotoUrl())
                                    <img src="{{ $product->fotoUrl() }}" alt="" class="size-12 rounded-lg object-cover">
                                @else
                                    <span class="flex size-12 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                                        <flux:icon.photo variant="mini" />
                                    </span>
                                @endif
                                <div>
                                    <p class="font-medium text-zinc-800">{{ $product->name }}</p>
                                    @if ($product->descricao)
                                        <p class="line-clamp-1 text-xs text-zinc-500">{{ $product->descricao }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-600">{{ $product->category->name }}</td>
                        <td class="px-4 py-3">R$ {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <flux:badge size="sm" :color="$product->status ? 'green' : 'zinc'">
                                {{ $product->status ? 'Ativo' : 'Inativo' }}
                            </flux:badge>
                        </td>
                        <td class="space-x-1 px-4 py-3 text-right whitespace-nowrap">
                            <flux:button size="sm" icon="pencil-square" wire:click="edit({{ $product->id }})">Editar</flux:button>
                            <flux:button size="sm" variant="danger" icon="trash" wire:click="delete({{ $product->id }})" wire:confirm="Excluir o item {{ $product->name }}?">Excluir</flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-500">Nenhum item cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal name="product-item-form" class="md:w-[28rem]">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">{{ $editingItemId ? 'Editar item' : 'Novo item' }}</flux:heading>

            <flux:input wire:model="name" label="Nome do item" placeholder="Ex.: Canapé de salmão" />

            <flux:select wire:model="category_id" label="Categoria" placeholder="Selecione...">
                @foreach ($this->categories as $category)
                    <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="descricao" label="Descrição" placeholder="Ex.: Arroz com legumes picados" rows="2" />

            <div>
                <flux:label>Foto</flux:label>
                <div class="mt-2 flex items-center gap-4">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" alt="" class="size-16 rounded-lg object-cover">
                    @elseif ($fotoAtual)
                        <img src="{{ $fotoAtual }}" alt="" class="size-16 rounded-lg object-cover">
                    @else
                        <span class="flex size-16 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            <flux:icon.photo />
                        </span>
                    @endif
                    <input type="file" wire:model="foto" accept="image/*" class="block w-full text-sm text-zinc-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-800 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-brand-900">
                </div>
                <div wire:loading wire:target="foto" class="mt-1 text-xs text-zinc-500">Enviando foto...</div>
                @error('foto') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <flux:input wire:model="price" label="Valor por pessoa (R$)" type="number" step="0.01" min="0" placeholder="Ex.: 5.00" />

            <flux:switch wire:model="status" label="Ativo no cardápio" />

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">Salvar</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
