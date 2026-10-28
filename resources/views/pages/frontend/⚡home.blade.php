<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public'), Title('Monte seu orçamento')] class extends Component
{
    //
};
?>

<div class="min-h-screen lg:grid lg:grid-cols-2">
    {{-- Foto no mobile no topo --}}
    <div class="relative order-1 h-52 sm:h-64 lg:order-2 lg:h-auto lg:min-h-screen">
       
            <div class="absolute inset-0 bg-linear-to-br from-brand-700 via-brand-800 to-brand-950">
                
            </div>
    </div>

    <div class="order-2 flex flex-col px-6 py-6 sm:px-10 lg:order-1 lg:px-14 lg:py-8">
        <header class="flex items-center justify-between">
            <x-publico.marca tamanho="xl" />
        </header>

        <main class="flex flex-1 flex-col justify-center py-10 lg:py-0">
            <h1 class="font-serif text-4xl leading-[1.15] text-brand-900 sm:text-5xl lg:text-[3.4rem]">
                Seu evento<br>do seu jeito
            </h1>

            <p class="mt-5 max-w-md text-base leading-relaxed text-zinc-600 sm:text-lg">
                Selecione os itens do nosso cardápio e monte o orçamento do seu buffet em poucos minutos.
            </p>

            <div class="mt-8">
                <a
                    href="{{ route('orcamento.montar') }}"
                    class="inline-flex h-12 items-center gap-2 rounded-xl bg-brand-800 px-8 text-base font-medium text-white shadow-sm transition hover:bg-brand-900"
                >
                    Começar agora
                    <flux:icon.arrow-right variant="mini" />
                </a>
            </div>

            <ul class="mt-14 grid max-w-lg gap-6 sm:grid-cols-3">
                <li class="flex flex-col gap-2">
                    <span class="flex size-10 items-center justify-center rounded-full bg-brand-100 text-brand-800">
                        <flux:icon.squares-2x2 variant="mini" />
                    </span>
                    <span class="text-sm leading-snug text-zinc-600">Escolha fácil dos itens</span>
                </li>
                <li class="flex flex-col gap-2">
                    <span class="flex size-10 items-center justify-center rounded-full bg-brand-100 text-brand-800">
                        <flux:icon.document-text variant="mini" />
                    </span>
                    <span class="text-sm leading-snug text-zinc-600">Orçamento em PDF</span>
                </li>
                <li class="flex flex-col gap-2">
                    <span class="flex size-10 items-center justify-center rounded-full bg-brand-100 text-brand-800">
                        <flux:icon.chat-bubble-left-right variant="mini" />
                    </span>
                    <span class="text-sm leading-snug text-zinc-600">Envio pelo WhatsApp</span>
                </li>
            </ul>
        </main>

        <footer class="pt-6 text-sm text-zinc-400">
            Qualquer dúvida, entre em contato conosco!
        </footer>
    </div>
</div>
