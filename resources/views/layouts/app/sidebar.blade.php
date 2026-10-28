<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="color-scheme: light">
    <head>
        {{-- Keep the admin in light mode without watching the <html> class. A MutationObserver here fights Flux on every navigation and freezes the tab. --}}
        <script data-navigate-once>
            try { localStorage.setItem('flux.appearance', 'light'); } catch (e) {}
            document.documentElement.classList.remove('dark');
        </script>
        @include('partials.head')
    </head>
    <body class="admin-buffet min-h-screen bg-creme font-sans text-zinc-800 antialiased">
        <flux:sidebar sticky collapsible="mobile" class="admin-sidebar border-e-0! bg-brand-800! text-white! dark:bg-brand-800!">
            <flux:sidebar.header>
                <a href="{{ route('admin.orcamento.index') }}" wire:navigate class="block px-1 py-1 leading-none">
                    <p class="font-serif text-lg text-white">{{ config('app.name') }}</p>
                    <p class="mt-1 text-[10px] tracking-[0.22em] text-white/50 uppercase">Buffet e eventos</p>
                </a>
                <flux:sidebar.collapse class="lg:hidden text-white!" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="text-white/85!">
                <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.orcamento.index')" :current="request()->routeIs('admin.orcamento.*')" wire:navigate class="text-white/85! hover:bg-white/10! hover:text-white!">
                    Pedidos
                </flux:sidebar.item>

                <flux:sidebar.item icon="cake" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate class="text-white/85! hover:bg-white/10! hover:text-white!">
                    Itens do cardápio
                </flux:sidebar.item>

                <flux:sidebar.item icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate class="text-white/85! hover:bg-white/10! hover:text-white!">
                    Categorias
                </flux:sidebar.item>

                <flux:sidebar.item icon="users" :href="route('admin.customers.index')" :current="request()->routeIs('admin.customers.*')" wire:navigate class="text-white/85! hover:bg-white/10! hover:text-white!">
                    Clientes
                </flux:sidebar.item>

                <flux:sidebar.item icon="user-group" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate class="text-white/85! hover:bg-white/10! hover:text-white!">
                    Funcionários
                </flux:sidebar.item>

                <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.configs.index')" :current="request()->routeIs('admin.configs.*')" wire:navigate class="text-white/85! hover:bg-white/10! hover:text-white!">
                    Configurações
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="hidden border-t border-white/10 p-4 lg:block">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-white/50">{{ auth()->user()->email }}</p>
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('profile.edit') }}" wire:navigate class="text-xs text-white/70 underline-offset-2 hover:text-white hover:underline">Conta</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-white/70 underline-offset-2 hover:text-white hover:underline">Sair</button>
                    </form>
                </div>
            </div>
        </flux:sidebar>

        <flux:header class="border-b border-brand-100 bg-creme lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Conta</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">Sair</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
