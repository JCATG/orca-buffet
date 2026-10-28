<!DOCTYPE html>
<html lang="pt-BR" style="color-scheme: light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>{{ filled($title ?? null) ? $title.' - '.config('app.name') : config('app.name') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- The public pages follow the light visual identity even when the visitor's device is in dark mode. --}}
        <script>
            new MutationObserver(() => document.documentElement.classList.remove('dark'))
                .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        </script>
    </head>
    <body class="publico min-h-screen bg-creme font-sans text-zinc-800 antialiased">
        {{ $slot }}

        @fluxScripts
    </body>
</html>
