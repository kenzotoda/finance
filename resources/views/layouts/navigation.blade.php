<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-blue-100/90 bg-white/85 backdrop-blur-xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-finance-brand size="xs" variant="dark" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="finance-nav-links hidden items-center sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('despesas.index')" :active="request()->routeIs('despesas.*')">
                        Despesas do cartao
                    </x-nav-link>
                    <x-nav-link :href="route('despesas-fixas.index')" :active="request()->routeIs('despesas-fixas.*')">
                        Despesas Fixas
                    </x-nav-link>
                    <x-nav-link :href="route('lucros-fixos.index')" :active="request()->routeIs('lucros-fixos.*')">
                        Lucros Fixos
                    </x-nav-link>
                    <x-nav-link :href="route('pagar-receber.index')" :active="request()->routeIs('pagar-receber.*')">
                        Pagar ou Receber
                    </x-nav-link>
                    <x-nav-link :href="route('impostos.index')" :active="request()->routeIs('impostos.*')">
                        Impostos
                    </x-nav-link>
                    <x-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                        Categorias
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-xl border border-blue-100 bg-blue-50/70 px-3 py-2 text-sm leading-4 font-medium text-slate-600 transition hover:text-blue-700 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Sair
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 transition hover:bg-blue-50 hover:text-blue-600 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-blue-100 bg-white sm:hidden">
        <div class="space-y-1 px-3 py-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('despesas.index')" :active="request()->routeIs('despesas.*')">
                Despesas do cartao
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('despesas-fixas.index')" :active="request()->routeIs('despesas-fixas.*')">
                Despesas Fixas
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('lucros-fixos.index')" :active="request()->routeIs('lucros-fixos.*')">
                Lucros Fixos
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pagar-receber.index')" :active="request()->routeIs('pagar-receber.*')">
                Pagar ou Receber
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('impostos.index')" :active="request()->routeIs('impostos.*')">
                Impostos
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                Categorias
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-blue-100 pb-2 pt-3">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-3">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Perfil
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Sair
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
