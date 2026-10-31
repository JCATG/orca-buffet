
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public'), Title('Mônica Buffet | Sabores para celebrar')] class extends Component
{
    //
};
?>

<div class="min-h-screen overflow-hidden bg-[#faf6ef] text-[#183c30]">

    <x-publico.header />


    {{-- HERO --}}
    <section id="inicio" class="relative flex min-h-[680px] items-center bg-[#10291f] lg:min-h-[760px]">

        {{-- Imagem de fundo --}}
        <div class="absolute inset-0">
            <img
                src="{{ asset('images/home/hero-buffet.jpg') }}"
                alt="Mesa sofisticada preparada para um evento"
                class="h-full w-full object-cover object-center"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-[#091b14]/95 via-[#10291f]/75 to-transparent"></div>
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-6 pb-20 pt-36 lg:px-10 lg:pt-32">

            <div class="max-w-2xl">

                <div class="mb-7 h-px w-16 bg-[#e9bd73]"></div>

                <h1 class="font-serif text-5xl leading-[1.12] tracking-tight text-white sm:text-6xl lg:text-7xl">
                    Transforme seus momentos em
                    <span class="italic text-[#e9bd73]">
                        experiências inesquecíveis.
                    </span>
                </h1>

                <p class="mt-7 max-w-lg text-base leading-relaxed text-white/85 sm:text-lg">
                    Buffet completo, com sabor, qualidade e muito carinho em cada detalhe.
                    Seu evento merece uma experiência especial.
                </p>

                <a
                    href="{{ route('orcamento.montar') }}"
                    class="mt-9 inline-flex h-14 items-center gap-3 rounded-full bg-white px-8 text-base font-semibold text-[#17382c] shadow-xl transition duration-300 hover:-translate-y-1 hover:bg-white/90"
                >
                    <flux:icon.chat-bubble-left-right variant="mini" />
                    Fazer meu orçamento
                    <flux:icon.arrow-right variant="mini" />
                </a>

                <p class="mt-5 text-xs tracking-wide text-white/65">
                    Monte seu orçamento de forma simples e personalizada.
                </p>

            </div>
        </div>

        {{-- Detalhe inferior --}}
        <div class="absolute bottom-0 left-0 h-1.5 w-full bg-gradient-to-r from-[#b58a49] via-[#e9bd73] to-transparent"></div>

    </section>


    {{-- DIFERENCIAIS --}}
    <section class="relative bg-[#17382c] py-9">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-6 md:grid-cols-4 lg:px-10">

            <div class="flex flex-col items-center text-center">
                <flux:icon.sparkles class="size-8 text-[#e9bd73]" />
                <p class="mt-3 text-sm font-medium text-white">
                    Qualidade<br>em cada detalhe
                </p>
            </div>

            <div class="flex flex-col items-center text-center">
                <flux:icon.shopping-bag class="size-8 text-[#e9bd73]" />
                <p class="mt-3 text-sm font-medium text-white">
                    Cardápio variado<br>e personalizado
                </p>
            </div>

            <div class="flex flex-col items-center text-center">
                <flux:icon.heart class="size-8 text-[#e9bd73]" />
                <p class="mt-3 text-sm font-medium text-white">
                    Atendimento<br>com carinho
                </p>
            </div>

            <div class="flex flex-col items-center text-center">
                <flux:icon.calendar-days class="size-8 text-[#e9bd73]" />
                <p class="mt-3 text-sm font-medium text-white">
                    Compromisso<br>com seu evento
                </p>
            </div>

        </div>
    </section>


    {{-- SOBRE NÓS --}}
    <section id="sobre" class="bg-[#faf6ef] py-20 lg:py-28">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2 lg:gap-20 lg:px-10">

            <div class="relative">
                <div class="absolute -left-5 -top-5 h-40 w-40 rounded-full border border-[#d8ad6a]"></div>

                <img
                    src="{{ asset('images/home/sobre-buffet.png') }}"
                    alt="Detalhes da gastronomia Mônica Buffet"
                    class="relative aspect-[5/4] w-full rounded-2xl object-cover shadow-lg"
                >
            </div>

            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.3em] text-[#ad7835]">
                    Sobre nós
                </span>

                <h2 class="mt-5 max-w-xl font-serif text-4xl leading-tight text-[#19382d] sm:text-5xl">
                    Uma história de dedicação e amor pela boa comida.
                </h2>

                <p class="mt-7 text-base leading-8 text-[#665f55]">
                    Há mais de 15 anos, a Mônica Buffet transforma celebrações
                    em momentos únicos, com uma gastronomia especial e atenção
                    em cada detalhe.
                </p>

                <p class="mt-4 text-base leading-8 text-[#665f55]">
                    Nossa equipe é apaixonada por receber bem, preparar pratos
                    especiais e proporcionar experiências que ficam na memória.
                </p>

                <a
                    href="{{ route('historia') }}"
                    class="mt-8 inline-flex items-center gap-3 rounded-full border border-[#9b713d] px-7 py-3 text-sm font-semibold text-[#19382d] transition hover:bg-[#19382d] hover:text-white"
                >
                    Conheça nossa história
                    <flux:icon.arrow-right variant="mini" />
                </a>
            </div>

        </div>
    </section>


    {{-- SERVIÇOS --}}
    <section id="servicos" class="bg-[#f1e8d9] py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">

            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-semibold uppercase tracking-[0.3em] text-[#ad7835]">
                    Nossas experiências
                </span>

                <h2 class="mt-5 font-serif text-4xl text-[#19382d] sm:text-5xl">
                    Momentos especiais merecem sabores especiais.
                </h2>

                <p class="mt-5 leading-7 text-[#74695b]">
                    Cada celebração é única. Por isso, criamos experiências
                    gastronômicas personalizadas para diferentes ocasiões.
                </p>
            </div>

            <div id="eventos" class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Casamentos --}}
                <div class="group relative overflow-hidden rounded-xl">
                    <img
                        src="{{ asset('images/home/casamentos.jpg') }}"
                        alt="Buffet para casamentos"
                        class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 p-7">
                        <h3 class="font-serif text-2xl text-white">Casamentos</h3>
                        <p class="mt-2 text-sm text-white/80">Celebre o amor com uma experiência inesquecível.</p>
                    </div>
                </div>

                {{-- Aniversários --}}
                <div class="group relative overflow-hidden rounded-xl">
                    <img
                        src="{{ asset('images/home/aniversarios.jpg') }}"
                        alt="Buffet para aniversários"
                        class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 p-7">
                        <h3 class="font-serif text-2xl text-white">Aniversários</h3>
                        <p class="mt-2 text-sm text-white/80">Sabores que tornam cada comemoração especial.</p>
                    </div>
                </div>

                {{-- Corporativos --}}
                <div class="group relative overflow-hidden rounded-xl sm:col-span-2 lg:col-span-1">
                    <img
                        src="{{ asset('images/home/corporativos.png') }}"
                        alt="Evento corporativo"
                        class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 p-7">
                        <h3 class="font-serif text-2xl text-white">Eventos especiais</h3>
                        <p class="mt-2 text-sm text-white/80">Soluções completas para celebrar grandes momentos.</p>
                    </div>
                </div>

            </div>

            <div class="mt-12 text-center">
                <a
                    href="{{ route('orcamento.montar') }}"
                    class="inline-flex h-14 items-center gap-3 rounded-full bg-[#19382d] px-9 font-semibold text-white transition hover:bg-[#285341]"
                >
                    Solicitar orçamento
                    <flux:icon.arrow-right variant="mini" />
                </a>
            </div>

        </div>
    </section>


    {{-- GASTRONOMIA --}}
    <section class="bg-[#faf6ef] py-20 lg:py-24">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2 lg:gap-20 lg:px-10">

            <div class="order-2 lg:order-1">
                <span class="text-xs font-semibold uppercase tracking-[0.3em] text-[#ad7835]">
                    Nossa gastronomia
                </span>

                <h2 class="mt-5 font-serif text-4xl leading-tight text-[#19382d] sm:text-5xl">
                    Uma experiência gastronômica completa.
                </h2>

                <p class="mt-6 leading-8 text-[#665f55]">
                    Do primeiro aperitivo à sobremesa, cada detalhe é pensado
                    para surpreender. Trabalhamos com opções variadas,
                    ingredientes selecionados e combinações especiais.
                </p>

                <p class="mt-4 leading-8 text-[#665f55]">
                    Monte uma combinação que tenha a sua personalidade e
                    faça do seu evento uma ocasião ainda mais especial.
                </p>

                <a
                    href="{{ route('orcamento.montar') }}"
                    class="mt-8 inline-flex items-center gap-3 rounded-full border border-[#9b713d] px-7 py-3 text-sm font-semibold text-[#19382d] transition hover:bg-[#19382d] hover:text-white"
                >
                    Conhecer opções e fazer orçamento
                    <flux:icon.arrow-right variant="mini" />
                </a>
            </div>

            <div class="order-1 lg:order-2">
                <img
                    src="{{ asset('images/home/gastronomia.jpg') }}"
                    alt="Prato preparado pela Mônica Buffet"
                    class="aspect-[5/4] w-full rounded-2xl object-cover shadow-lg"
                >
            </div>

        </div>
    </section>


    {{-- DEPOIMENTOS --}}
    <section id="depoimentos" class="bg-[#f1e8d9] py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-10">

            <div class="text-center">
                <span class="text-xs font-semibold uppercase tracking-[0.3em] text-[#ad7835]">
                    Depoimentos
                </span>

                <h2 class="mt-4 font-serif text-4xl text-[#19382d] sm:text-5xl">
                    Quem já viveu, recomenda!
                </h2>
            </div>

            {{-- Substituir pelos depoimentos reais dos clientes --}}
            <div class="mt-12 grid gap-6 md:grid-cols-3">

                <div class="rounded-xl border border-[#e3d2b9] bg-[#faf6ef] p-8">
                    <span class="font-serif text-5xl text-[#d9ac65]">“</span>
                    <p class="font-serif text-lg italic leading-relaxed text-[#51483e]">
                        Seu depoimento de cliente aparecerá aqui.
                    </p>
                    <p class="mt-5 text-sm font-semibold text-[#19382d]">
                        Cliente Mônica Buffet
                    </p>
                </div>

                <div class="rounded-xl border border-[#e3d2b9] bg-[#faf6ef] p-8">
                    <span class="font-serif text-5xl text-[#d9ac65]">“</span>
                    <p class="font-serif text-lg italic leading-relaxed text-[#51483e]">
                        Seu depoimento de cliente aparecerá aqui.
                    </p>
                    <p class="mt-5 text-sm font-semibold text-[#19382d]">
                        Cliente Mônica Buffet
                    </p>
                </div>

                <div class="rounded-xl border border-[#e3d2b9] bg-[#faf6ef] p-8">
                    <span class="font-serif text-5xl text-[#d9ac65]">“</span>
                    <p class="font-serif text-lg italic leading-relaxed text-[#51483e]">
                        Seu depoimento de cliente aparecerá aqui.
                    </p>
                    <p class="mt-5 text-sm font-semibold text-[#19382d]">
                        Cliente Mônica Buffet
                    </p>
                </div>

            </div>
        </div>
    </section>


    {{-- CHAMADA FINAL --}}
    <section id="contato" class="bg-[#faf6ef] px-6 py-16 lg:px-10">
        <div class="relative mx-auto flex max-w-7xl flex-col items-center justify-between gap-8 overflow-hidden rounded-2xl bg-[#153528] px-8 py-12 shadow-xl md:flex-row md:px-16">

            <div class="relative z-10">
                <h2 class="font-serif text-3xl italic text-white sm:text-4xl">
                    Pronto para celebrar?
                </h2>

                <p class="mt-3 text-sm leading-relaxed text-white sm:text-base">
                    Solicite agora seu orçamento e receba um atendimento personalizado.
                </p>
            </div>

            <a
                href="{{ route('orcamento.montar') }}"
                class="relative z-10 inline-flex h-14 shrink-0 items-center gap-3 whitespace-nowrap rounded-full bg-white px-8 font-semibold text-[#19382d] transition hover:-translate-y-1 hover:bg-white/90"
            >
                <flux:icon.chat-bubble-left-right variant="mini" />
                Fazer orçamento
                <flux:icon.arrow-right variant="mini" />
            </a>

        </div>
    </section>


    <x-publico.footer />

</div>
