<footer class="bg-[#10291f] py-12 text-white">
    <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-8 px-6 md:flex-row lg:px-10">
        <div class="text-center md:text-left">
            <x-publico.marca tamanho="xl" clara />
            <p class="mt-3 font-serif text-sm italic text-white">
                Sabores para celebrar.
            </p>
        </div>

        <div class="flex items-center gap-7">
            <a
                href="https://www.instagram.com/monicabuffet"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Instagram"
                class="text-white transition hover:text-white/70"
            >
                <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="5" />
                    <circle cx="12" cy="12" r="4" />
                    <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" stroke="none" />
                </svg>
            </a>

            <a
                href="{{ route('orcamento.montar') }}"
                aria-label="Fazer orçamento"
                class="text-white transition hover:text-white/70"
            >
                <flux:icon.chat-bubble-left-right class="size-7" />
            </a>
        </div>

        <div class="text-center md:text-right">
            <p class="text-sm text-white">
                Qualidade, sabor e carinho em cada detalhe.
            </p>
            <p class="mt-2 text-xs text-white">
                © {{ date('Y') }} Mônica Buffet. Todos os direitos reservados.
            </p>
        </div>
    </div>
</footer>
