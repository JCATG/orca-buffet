@props(['tamanho' => 'md', 'clara' => false])

<div {{ $attributes->class('leading-none') }}>
    <p @class([
        'font-serif',
        'text-white' => $clara,
        'text-brand-800' => ! $clara,
        'text-xl' => $tamanho === 'md',
        'text-3xl' => $tamanho === 'lg',
        'text-2xl' => $tamanho === 'xl',
    ])>{{ config('app.name') }}</p>
    <p @class([
        'mt-1 text-[10px] tracking-[0.28em] uppercase',
        'text-white/80' => $clara,
        'text-zinc-500' => ! $clara,
    ])>Buffet e eventos</p>
</div>
