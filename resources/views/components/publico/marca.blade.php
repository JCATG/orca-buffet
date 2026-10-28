@props(['tamanho' => 'md'])

<div {{ $attributes->class('leading-none') }}>
    <p @class([
        'font-serif text-brand-800',
        'text-xl' => $tamanho === 'md',
        'text-3xl' => $tamanho === 'lg',
        'text-2xl' => $tamanho === 'xl',
    ])>{{ config('app.name') }}</p>
    <p class="mt-1 text-[10px] tracking-[0.28em] text-zinc-500 uppercase">Buffet e eventos</p>
</div>
