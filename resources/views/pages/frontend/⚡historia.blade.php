<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public'), Title('Nossa história')] class extends Component
{
    //
};
?>

<div class="min-h-screen bg-[#faf6ef] text-[#183c30]">
    <x-publico.header solido />

    <section class="bg-[#10291f] px-6 pb-16 pt-6 lg:px-10 lg:pb-24">
        <div class="mx-auto max-w-7xl">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#e9bd73]">Sobre nós</p>
            <h1 class="mt-4 max-w-3xl font-serif text-4xl leading-tight text-white sm:text-5xl lg:text-6xl">
                Uma história de dedicação e amor pelo que faz.
            </h1>
            <p class="mt-6 max-w-2xl text-base leading-relaxed text-white/80 sm:text-lg">
                Onde cada prato é feito com carinho. Da cozinha de casa ao buffet, a Mônica carrega o sabor de quem cresceu entre panelas.
            </p>
        </div>
    </section>

    <section class="px-6 py-16 lg:px-10 lg:py-24">
        <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div class="relative">
                <div class="absolute -left-5 -top-5 hidden h-40 w-40 rounded-full border border-[#d8ad6a] sm:block"></div>
                <img
                    src="{{ asset('images/home/sobre-buffet.png') }}"
                    alt="Mesa preparada pela Mônica Buffet"
                    class="relative aspect-[5/4] w-full rounded-2xl object-cover shadow-lg"
                >
            </div>

            <div class="space-y-5 text-base leading-8 text-[#665f55]">
                <p>
                    Mônica Buffet traz consigo uma história de dedicação e amor pelo que faz. Filha de cozinheira, dona Sebastiana Ribeiro, conhecida pelo talento no Hotel de Lourdes, em Passa Quatro, Mônica cresceu cercada pelos aromas e sabores da boa comida.
                </p>
                <p>
                    Foram 25 anos à frente de um açougue popular, conquistando a confiança da comunidade. Há 15 anos, essa mesma dedicação passou a transformar celebrações em experiências inesquecíveis através do buffet.
                </p>
                <p>
                    A equipe é apaixonada por receber bem e preparar pratos que encantam pelo sabor, pela apresentação e pelo cuidado em cada detalhe. Do planejamento ao último brinde, o compromisso é fazer parte dos seus melhores momentos.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-[#f1e8d9] px-6 py-16 lg:px-10 lg:py-20">
        <div class="mx-auto grid max-w-7xl gap-6 md:grid-cols-3">
            <article class="rounded-2xl bg-[#faf6ef] p-8">
                <p class="font-serif text-3xl text-[#ad7835]">A origem</p>
                <p class="mt-4 leading-7 text-[#665f55]">
                    Dona Sebastiana Ribeiro, cozinheira do Hotel de Lourdes, em Passa Quatro, ensinou o ofício que Mônica leva até hoje.
                </p>
            </article>
            <article class="rounded-2xl bg-[#faf6ef] p-8">
                <p class="font-serif text-3xl text-[#ad7835]">25 anos</p>
                <p class="mt-4 leading-7 text-[#665f55]">
                    À frente de um açougue popular, construiu a confiança da comunidade com trabalho diário e presença.
                </p>
            </article>
            <article class="rounded-2xl bg-[#faf6ef] p-8">
                <p class="font-serif text-3xl text-[#ad7835]">15 anos</p>
                <p class="mt-4 leading-7 text-[#665f55]">
                    No buffet, essa história virou celebração: sabor, apresentação e cuidado em cada festa.
                </p>
            </article>
        </div>
    </section>

    <section class="px-6 py-16 lg:px-10 lg:py-24">
        <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#ad7835]">O compromisso</p>
                <h2 class="mt-4 font-serif text-4xl leading-tight text-[#19382d] sm:text-5xl">
                    Cada celebração merece ser lembrada com carinho.
                </h2>
                <p class="mt-6 leading-8 text-[#665f55]">
                    Ficamos felizes em fazer parte da sua história. Conte a data, o lugar e o que você imagina para a festa. A gente monta o orçamento com o mesmo cuidado de quem cresceu na cozinha.
                </p>
                <div class="mt-8 flex flex-col gap-3 text-[#19382d] sm:flex-row sm:gap-8">
                    <a href="tel:+5535997432230" class="font-semibold hover:text-[#ad7835]">(35) 99743-2230</a>
                    <a href="https://www.instagram.com/monicabuffet" target="_blank" rel="noopener noreferrer" class="font-semibold hover:text-[#ad7835]">@monicabuffet</a>
                </div>
                <a
                    href="{{ route('orcamento.montar') }}"
                    class="mt-8 inline-flex h-12 items-center gap-2 rounded-full bg-[#19382d] px-7 text-sm font-semibold text-white transition hover:bg-[#285341]"
                >
                    Fazer orçamento
                    <flux:icon.arrow-right variant="mini" />
                </a>
            </div>

            <img
                src="{{ asset('images/home/gastronomia.jpg') }}"
                alt="Prato preparado pela Mônica Buffet"
                class="aspect-[5/4] w-full rounded-2xl object-cover shadow-lg"
            >
        </div>
    </section>

    <x-publico.footer />
</div>
