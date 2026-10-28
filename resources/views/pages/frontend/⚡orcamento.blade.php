<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public'), Title('Monte seu orçamento')] class extends Component
{
    public string $etapa = 'itens';

    public array $selecionados = [];

    public string $nome = '';

    public string $telefone = '';

    public string $email = '';

    public string $cidade = '';

    public string $data_evento = '';

    public string $local_evento = '';

    public string $tipo_evento = '';

    public string $convidados = '';

    public string $observacoes = '';

    public ?string $numeroPedido = null;

    #[Computed]
    public function categorias()
    {
        return Category::query()
            ->whereHas('products', fn ($query) => $query->where('status', true))
            ->with(['products' => fn ($query) => $query->where('status', true)->orderBy('name')])
            ->orderBy('ordem')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function itensEscolhidos()
    {
        return Product::with('category')
            ->whereIn('id', $this->selecionados)
            ->where('status', true)
            ->get()
            ->sortBy(fn (Product $product) => sprintf('%05d-%s', $product->category->ordem, $product->name))
            ->groupBy(fn (Product $product) => $product->category->name);
    }

    public function etapas(): array
    {
        return [
            'itens' => 'Itens',
            'resumo' => 'Resumo',
            'finalizar' => 'Finalizar',
        ];
    }

    public function tiposDeEvento(): array
    {
        return ['Casamento', 'Aniversário', '15 anos', 'Formatura', 'Batizado', 'Chá de bebê', 'Corporativo', 'Outro'];
    }

    public function iconeDaCategoria(string $nome): string
    {
        $nome = Str::lower(Str::ascii($nome));

        return match (true) {
            Str::contains($nome, ['entrada', 'salgad', 'petisco']) => 'sparkles',
            Str::contains($nome, ['salada']) => 'sun',
            Str::contains($nome, ['prato', 'principal', 'carne', 'massa']) => 'fire',
            Str::contains($nome, ['acompanhamento', 'guarni']) => 'squares-plus',
            Str::contains($nome, ['bebida', 'drink', 'suco']) => 'beaker',
            Str::contains($nome, ['sobremesa', 'doce', 'bolo']) => 'cake',
            default => 'star',
        };
    }

    public function irParaResumo(): void
    {
        $this->validarItens();
        $this->irPara('resumo');
    }

    public function irParaFinalizar(): void
    {
        $this->validarItens();
        $this->irPara('finalizar');
    }

    public function voltar(): void
    {
        $destino = match ($this->etapa) {
            'finalizar' => 'resumo',
            'resumo' => 'itens',
            default => 'itens',
        };

        $this->irPara($destino);
    }

    public function editar(string $etapa): void
    {
        if (in_array($etapa, ['itens', 'resumo', 'finalizar'], true)) {
            $this->irPara($etapa);
        }
    }

    public function enviar(): void
    {
        $this->validarItens();
        $this->validarDados();

        $chave = 'orcamento-publico:'.request()->ip();

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            $this->addError('envio', 'Você já enviou vários pedidos agora há pouco. Tente novamente mais tarde ou fale com a gente.');

            return;
        }

        RateLimiter::hit($chave, 3600);

        $order = DB::transaction(function () {
            $email = Str::lower(trim($this->email));

            $customer = $email !== ''
                ? Customer::firstOrNew(['email' => $email])
                : Customer::firstOrNew(['phone' => $this->telefone]);

            $customer->fill([
                'name' => $this->nome,
                'phone' => $this->telefone,
                'city' => $this->cidade,
            ])->save();

            $order = $customer->orders()->create([
                'data_evento' => $this->data_evento,
                'local_evento' => $this->local_evento,
                'tipo_evento' => $this->tipo_evento,
                'convidados' => (int) $this->convidados,
                'status' => OrderStatus::Pendente,
                'observacoes' => filled($this->observacoes) ? $this->observacoes : null,
            ]);

            Product::whereIn('id', $this->selecionados)
                ->where('status', true)
                ->get()
                ->each(fn (Product $product) => $order->items()->create([
                    'product_id' => $product->id,
                    'valor_unitario' => $product->price,
                ]));

            $order->recalcularTotal();

            return $order;
        });

        $this->numeroPedido = $order->numero();
        $this->irPara('enviado');
    }

    protected function irPara(string $etapa): void
    {
        $this->etapa = $etapa;
        $this->resetValidation();
        $this->js('window.scrollTo({ top: 0, behavior: "smooth" })');
    }

    protected function validarItens(): void
    {
        $this->validate([
            'selecionados' => ['required', 'array', 'min:1'],
            'selecionados.*' => ['integer', Rule::exists('products', 'id')->where('status', true)],
        ], $this->mensagens());
    }

    protected function validarDados(): void
    {
        $this->validate([
            'nome' => ['required', 'string', 'max:255'],
            'telefone' => ['required', 'string', function ($attribute, $value, $fail) {
                $digitos = strlen(preg_replace('/\D/', '', (string) $value));

                if ($digitos < 10 || $digitos > 11) {
                    $fail('Informe um telefone válido com DDD.');
                }
            }],
            'email' => ['nullable', 'email', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'data_evento' => ['required', 'date', 'after:today'],
            'local_evento' => ['required', 'string', 'max:255'],
            'tipo_evento' => ['required', Rule::in($this->tiposDeEvento())],
            'convidados' => ['required', 'integer', 'min:1', 'max:5000'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
        ], $this->mensagens(), [
            'nome' => 'seu nome',
            'telefone' => 'seu telefone',
            'email' => 'o e-mail',
            'cidade' => 'sua cidade',
            'data_evento' => 'a data do evento',
            'local_evento' => 'o local do evento',
            'tipo_evento' => 'o tipo de evento',
            'convidados' => 'o número de convidados',
            'observacoes' => 'as observações',
        ]);
    }

    protected function mensagens(): array
    {
        return [
            'required' => 'Preencha :attribute.',
            'max' => ':Attribute está muito longo.',
            'email' => 'Informe um e-mail válido.',
            'date' => 'Informe uma data válida.',
            'integer' => 'Informe um número inteiro.',
            'selecionados.required' => 'Escolha pelo menos um item do cardápio.',
            'selecionados.min' => 'Escolha pelo menos um item do cardápio.',
            'selecionados.*.exists' => 'Um dos itens escolhidos não está mais disponível. Revise sua seleção.',
            'data_evento.after' => 'A data do evento precisa ser a partir de amanhã.',
            'tipo_evento.in' => 'Escolha um tipo de evento da lista.',
            'convidados.min' => 'Informe pelo menos :min convidado.',
            'convidados.max' => 'Para mais de :max convidados, fale diretamente com a gente.',
        ];
    }
};
?>

<div>
    @if ($etapa === 'enviado')
        <div class="mx-auto flex min-h-screen max-w-lg flex-col items-center justify-center px-6 py-16 text-center">
            <x-publico.marca class="mb-10" />

            <span class="flex size-24 items-center justify-center rounded-full bg-brand-100 text-brand-800">
                <flux:icon.check class="size-12!" />
            </span>

            <h1 class="mt-8 font-serif text-3xl text-brand-900">Pedido salvo com sucesso!</h1>

            <p class="mt-4 text-zinc-600">
                Recebemos o pedido.
                Em breve o buffet envia o orçamento completo no WhatsApp <strong class="text-zinc-800">{{ $telefone }}</strong>.
            </p>

            <p class="mt-8 font-serif text-lg italic text-brand-700">
                Obrigada por fazer parte do nosso trabalho!
            </p>

            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-brand-800 px-6 text-sm font-medium text-white hover:bg-brand-900">
                    Voltar ao início
                </a>
                <a href="{{ route('orcamento.montar') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-brand-200 px-6 text-sm font-medium text-brand-800 hover:bg-brand-50">
                    Fazer um novo orçamento
                </a>
            </div>
        </div>
    @else
        @php
            $indiceAtual = array_search($etapa, array_keys($this->etapas()), true);
            if ($indiceAtual === false) {
                $indiceAtual = 0;
            }
        @endphp

        <header class="border-b border-brand-100 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-2xl px-5 pt-5 pb-4">
                <div class="grid grid-cols-[2.5rem_1fr_2.5rem] items-center">
                    @if ($etapa === 'itens')
                        <a href="{{ route('home') }}" class="flex size-10 items-center justify-center rounded-full text-brand-800 hover:bg-brand-50" aria-label="Voltar">
                            <flux:icon.arrow-left variant="mini" />
                        </a>
                    @else
                        <button type="button" wire:click="voltar" class="flex size-10 items-center justify-center rounded-full text-brand-800 hover:bg-brand-50" aria-label="Voltar">
                            <flux:icon.arrow-left variant="mini" />
                        </button>
                    @endif

                    <a href="{{ route('home') }}" class="justify-self-center text-center">
                        <x-publico.marca />
                    </a>
                </div>

                <ol class="mx-auto mt-6 flex max-w-sm items-start">
                    @foreach ($this->etapas() as $chave => $rotulo)
                        <li @class(['flex items-start', 'flex-1' => ! $loop->last])>
                            <div class="flex w-[4.5rem] flex-col items-center gap-1.5">
                                <span @class([
                                    'flex size-8 items-center justify-center rounded-full text-sm font-semibold',
                                    'bg-brand-800 text-white' => $loop->index <= $indiceAtual,
                                    'bg-zinc-200 text-zinc-500' => $loop->index > $indiceAtual,
                                ])>
                                    @if ($loop->index < $indiceAtual)
                                        <flux:icon.check variant="micro" />
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </span>
                                <span @class([
                                    'text-xs whitespace-nowrap',
                                    'font-medium text-brand-800' => $loop->index === $indiceAtual,
                                    'text-zinc-400' => $loop->index !== $indiceAtual,
                                ])>{{ $rotulo }}</span>
                            </div>

                            @unless ($loop->last)
                                <div @class([
                                    'mt-4 h-0.5 flex-1 rounded-full',
                                    'bg-brand-800' => $loop->index < $indiceAtual,
                                    'bg-zinc-200' => $loop->index >= $indiceAtual,
                                ])></div>
                            @endunless
                        </li>
                    @endforeach
                </ol>
            </div>
        </header>

        <main class="mx-auto max-w-2xl px-5 pt-8 pb-36">
            @if ($etapa === 'itens')
                <h1 class="font-serif text-2xl text-brand-900 sm:text-3xl">Selecione os itens do seu buffet</h1>
                <p class="mt-2 text-zinc-600">Marque o que deseja servir. Os valores vêm no orçamento.</p>

                <div class="mt-8 space-y-4">
                    @forelse ($this->categorias as $categoria)
                        <section
                            wire:key="categoria-{{ $categoria->id }}"
                            x-data="{ aberto: {{ $loop->first ? 'true' : 'false' }}, ids: @js($categoria->products->pluck('id')->map(fn ($id) => (string) $id)) }"
                            class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm"
                        >
                            <button type="button" x-on:click="aberto = ! aberto" class="flex w-full items-center gap-4 p-4 text-left">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-800">
                                    <flux:icon :icon="$this->iconeDaCategoria($categoria->name)" variant="mini" />
                                </span>

                                <span class="flex-1">
                                    <span class="block font-medium text-zinc-800">{{ $categoria->name }}</span>
                                    <span class="block text-sm text-zinc-500">
                                        <span x-text="$wire.selecionados.filter(id => ids.includes(String(id))).length">0</span>
                                        de {{ $categoria->products->count() }} selecionados
                                    </span>
                                </span>

                                <flux:icon.chevron-down variant="mini" class="text-zinc-400 transition" x-bind:class="aberto && 'rotate-180'" />
                            </button>

                            <div x-show="aberto" x-collapse>
                                <ul class="divide-y divide-zinc-100 border-t border-zinc-100">
                                    @foreach ($categoria->products as $produto)
                                        <li wire:key="produto-{{ $produto->id }}">
                                            <label class="flex cursor-pointer items-center gap-4 px-4 py-3.5 transition hover:bg-brand-50/50 has-checked:bg-brand-50">
                                                @if ($produto->fotoUrl())
                                                    <img src="{{ $produto->fotoUrl() }}" alt="" class="size-14 shrink-0 rounded-xl object-cover">
                                                @else
                                                    <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                                                        <flux:icon.photo variant="mini" />
                                                    </span>
                                                @endif

                                                <span class="min-w-0 flex-1">
                                                    <span class="block font-medium text-zinc-800">{{ $produto->name }}</span>
                                                    @if ($produto->descricao)
                                                        <span class="mt-0.5 block text-sm text-zinc-500">{{ $produto->descricao }}</span>
                                                    @endif
                                                </span>

                                                <input
                                                    type="checkbox"
                                                    wire:model="selecionados"
                                                    value="{{ $produto->id }}"
                                                    class="size-5 shrink-0 cursor-pointer accent-brand-800"
                                                >
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </section>
                    @empty
                        <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-10 text-center text-zinc-500">
                            Nosso cardápio está sendo atualizado. Volte em breve!
                        </div>
                    @endforelse
                </div>
            @elseif ($etapa === 'resumo')
                <h1 class="font-serif text-2xl text-brand-900 sm:text-3xl">Resumo do pedido</h1>
                <p class="mt-2 text-zinc-600">Confira os itens escolhidos antes de finalizar.</p>

                <div class="mt-8 space-y-3">
                    @foreach ($this->itensEscolhidos as $categoria => $produtos)
                        <div wire:key="resumo-{{ \Illuminate\Support\Str::slug($categoria) }}" x-data="{ aberto: false }" class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
                            <div class="flex items-center gap-4 p-4">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-800">
                                    <flux:icon :icon="$this->iconeDaCategoria($categoria)" variant="mini" />
                                </span>

                                <div class="flex-1">
                                    <p class="font-medium text-zinc-800">{{ $categoria }}</p>
                                    <p class="text-sm text-zinc-500">{{ $produtos->count() }} {{ $produtos->count() === 1 ? 'item selecionado' : 'itens selecionados' }}</p>
                                </div>

                                <button type="button" x-on:click="aberto = ! aberto" class="text-sm font-medium text-brand-800 hover:underline">
                                    <span x-text="aberto ? 'Ocultar' : 'Ver detalhes'">Ver detalhes</span>
                                </button>
                            </div>

                            <ul x-show="aberto" x-collapse class="border-t border-zinc-100 bg-brand-50/40">
                                @foreach ($produtos as $produto)
                                    <li class="flex items-center gap-3 px-4 py-2.5 text-sm text-zinc-700">
                                        @if ($produto->fotoUrl())
                                            <img src="{{ $produto->fotoUrl() }}" alt="" class="size-8 rounded-lg object-cover">
                                        @endif
                                        <flux:icon.check variant="micro" class="text-brand-700" />
                                        {{ $produto->name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    <button type="button" wire:click="editar('itens')" class="mt-2 text-sm font-medium text-brand-800 hover:underline">
                        Alterar itens
                    </button>
                </div>
            @else
                <h1 class="font-serif text-2xl text-brand-900 sm:text-3xl">Finalize seu pedido</h1>
                <p class="mt-2 text-zinc-600">Conte um pouco sobre você e o evento para recebermos o orçamento.</p>

                <form wire:submit="enviar" id="form-finalizar" class="mt-8 space-y-6">
                    <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 font-medium text-zinc-800">
                            <flux:icon.user variant="mini" class="text-brand-800" /> Seus dados
                        </h2>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <flux:input wire:model="nome" label="Nome completo" autocomplete="name" />
                            </div>
                            <flux:input
                                wire:model="telefone"
                                label="WhatsApp"
                                type="tel"
                                inputmode="tel"
                                autocomplete="tel"
                                placeholder="(00) 00000-0000"
                                x-mask:dynamic="$input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-99999'"
                            />
                            <flux:input wire:model="email" label="E-mail (opcional)" type="email" autocomplete="email" />
                            <div class="sm:col-span-2">
                                <flux:input wire:model="cidade" label="Cidade" autocomplete="address-level2" />
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 font-medium text-zinc-800">
                            <flux:icon.calendar-days variant="mini" class="text-brand-800" /> Sobre o evento
                        </h2>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <flux:select wire:model="tipo_evento" label="Tipo de evento" placeholder="Selecione...">
                                @foreach ($this->tiposDeEvento() as $tipo)
                                    <flux:select.option :value="$tipo">{{ $tipo }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:input wire:model="data_evento" label="Data do evento" type="date" :min="now()->addDay()->toDateString()" />
                            <flux:input wire:model="convidados" label="Número de convidados" type="number" min="1" inputmode="numeric" />
                            <flux:input wire:model="local_evento" label="Local do evento" placeholder="Salão, endereço ou bairro" />
                            <div class="sm:col-span-2">
                                <flux:textarea wire:model="observacoes" label="Observações (opcional)" rows="3" placeholder="Horário, tema da festa, restrições alimentares..." />
                            </div>
                        </div>
                    </section>
                </form>
            @endif
        </main>

        <div class="fixed inset-x-0 bottom-0 border-t border-zinc-200 bg-white/95 backdrop-blur">
            <div class="mx-auto max-w-2xl px-5 py-4">
                @error('selecionados')
                    <p class="mb-3 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
                @error('selecionados.*')
                    <p class="mb-3 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
                @error('envio')
                    <p class="mb-3 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-between gap-4">
                    @if ($etapa === 'itens')
                        <p class="text-sm text-zinc-600">
                            <strong class="text-zinc-800" x-text="$wire.selecionados.length">{{ count($selecionados) }}</strong>
                            <span x-text="$wire.selecionados.length === 1 ? 'item selecionado' : 'itens selecionados'">itens selecionados</span>
                        </p>
                        <button type="button" wire:click="irParaResumo" class="inline-flex h-11 items-center gap-2 rounded-xl bg-brand-800 px-6 text-sm font-medium text-white hover:bg-brand-900">
                            Próximo
                            <flux:icon.arrow-right variant="mini" />
                        </button>
                    @elseif ($etapa === 'resumo')
                        <button type="button" wire:click="voltar" class="inline-flex h-11 items-center rounded-xl border border-zinc-200 px-5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                            Voltar
                        </button>
                        <button type="button" wire:click="irParaFinalizar" class="inline-flex h-11 items-center gap-2 rounded-xl bg-brand-800 px-6 text-sm font-medium text-white hover:bg-brand-900">
                            Continuar
                            <flux:icon.arrow-right variant="mini" />
                        </button>
                    @else
                        <button type="button" wire:click="voltar" class="inline-flex h-11 items-center rounded-xl border border-zinc-200 px-5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                            Voltar
                        </button>
                        <button type="submit" form="form-finalizar" class="inline-flex h-11 items-center gap-2 rounded-xl bg-brand-800 px-6 text-sm font-medium text-white hover:bg-brand-900">
                            Enviar pedido
                            <flux:icon.paper-airplane variant="mini" />
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
