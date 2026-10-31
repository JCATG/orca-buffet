@props(['solido' => false])

<header @class([
    'z-50',
    'absolute inset-x-0 top-0' => ! $solido,
    'relative bg-[#10291f]' => $solido,
])>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-10">
        <a href="{{ route('home') }}" class="min-w-0">
            <x-publico.marca tamanho="md" clara class="sm:hidden" />
            <x-publico.marca tamanho="xl" clara class="hidden sm:block" />
        </a>

        <nav class="hidden items-center gap-8 lg:flex">
            <a href="{{ route('home') }}#inicio" class="text-lg font-medium text-white transition hover:text-[#e9c17d]">Início</a>
            <a href="{{ route('historia') }}" @class(['text-lg font-medium transition hover:text-[#e9c17d]', 'text-[#e9c17d]' => request()->routeIs('historia'), 'text-white' => ! request()->routeIs('historia')])>Sobre Nós</a>
            <a href="{{ route('home') }}#servicos" class="text-lg font-medium text-white transition hover:text-[#e9c17d]">Serviços</a>
            <a href="{{ route('home') }}#eventos" class="text-lg font-medium text-white transition hover:text-[#e9c17d]">Eventos</a>
            <a href="{{ route('home') }}#depoimentos" class="text-lg font-medium text-white transition hover:text-[#e9c17d]">Depoimentos</a>
            <a href="{{ route('home') }}#contato" class="text-lg font-medium text-white transition hover:text-[#e9c17d]">Contato</a>
        </nav>

        <a
            href="{{ route('orcamento.montar') }}"
            class="inline-flex h-10 shrink-0 items-center gap-1.5 whitespace-nowrap rounded-full bg-white px-3.5 text-sm font-semibold text-[#19392e] shadow-lg transition hover:bg-white/90 sm:h-11 sm:gap-2 sm:px-5 sm:text-base"
        >
            <flux:icon.chat-bubble-left-right variant="mini" class="hidden sm:block" />
            <span class="sm:hidden">Orçamento</span>
            <span class="hidden sm:inline">Fazer orçamento</span>
            <flux:icon.arrow-right variant="mini" />
        </a>
    </div>
</header>
