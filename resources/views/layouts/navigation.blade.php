<nav x-data="{ open: false, langOpen: false }" class="border-b border-slate-200 bg-white shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <!-- Logo -->
            <div class="flex shrink-0 items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('branding/aurora-mark.svg') }}" alt="Aurora Translate" class="h-8 w-8">
                    <span class="hidden text-lg font-bold text-slate-900 sm:block">Aurora Translate</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                @can('level')
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium transition duration-150 ease-in-out">
                    {{ __('Home') }}
                </a>

                <a href="{{ route('users.index') }}"
                    class="{{ request()->routeIs('users.index') ? 'border-indigo-500 text-slate-900' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium transition duration-150 ease-in-out">
                    {{ __('Lista de usuários') }}
                </a>
                @endcan
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <!-- Language Switcher -->
                <div class="relative z-50 mr-4">
                    <button @click="langOpen = !langOpen" class="flex items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-slate-100">
                        <div class="h-5 w-5 overflow-hidden rounded-full">
                            @php
                            $flagMap = [
                                'pt_BR' => ['img' => 'flags/br.png', 'label' => 'BR'],
                                'en' => ['img' => 'flags/en.png', 'label' => 'EN'],
                                'es' => ['img' => 'flags/es.png', 'label' => 'ES'],
                            ];
                            $currentLocale = app()->getLocale();
                            $currentFlag = $flagMap[$currentLocale]['img'] ?? 'flags/br.png';
                            $currentLabel = $flagMap[$currentLocale]['label'] ?? 'BR';
                            @endphp
                            <img src="{{ asset($currentFlag) }}" alt="Idioma Atual" class="h-full w-full object-cover">
                        </div>
                        <span class="text-sm font-medium text-slate-700">{{ $currentLabel }}</span>
                        <svg class="h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown de Idiomas -->
                    <div x-show="langOpen" @click.away="langOpen = false" x-transition class="absolute right-0 mt-2 w-32 rounded-lg border border-slate-200 bg-white shadow-lg z-50">
                        <div class="flex flex-col gap-1 p-2">
                            @foreach ($flagMap as $locale => $flag)
                            @if ($locale !== $currentLocale)
                            <form method="GET" action="{{ url()->current() }}" class="block">
                                <button type="submit" name="lang" value="{{ $locale }}" class="flex w-full items-center gap-2 rounded-lg p-2 text-sm transition hover:bg-slate-100">
                                    <div class="h-5 w-5 overflow-hidden rounded-full">
                                        <img src="{{ asset($flag['img']) }}" alt="{{ $flag['label'] }}" class="h-full w-full object-cover">
                                    </div>
                                    <span>{{ $flag['label'] }}</span>
                                </button>
                            </form>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative z-50">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600">
                            <span class="text-sm font-semibold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="hidden md:block">{{ Auth::user()->name }}</span>
                        <svg class="h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown de Usuário -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 bg-white shadow-lg z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-100">
                            @if (app()->getLocale() == 'pt_BR') Perfil
                            @elseif (app()->getLocale() == 'en') Profile
                            @else Perfil
                            @endif
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full px-4 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                @if (app()->getLocale() == 'pt_BR') Sair
                                @elseif (app()->getLocale() == 'en') Logout
                                @else Salir
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            @can('level')
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800' }} block border-l-4 py-2 pl-3 pr-4 text-base font-medium transition">
                {{ __('Home') }}
            </a>

            <a href="{{ route('users.index') }}"
                class="{{ request()->routeIs('users.index') ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-transparent text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800' }} block border-l-4 py-2 pl-3 pr-4 text-base font-medium transition">
                {{ __('Lista de usuários') }}
            </a>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-slate-200 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">
                    @if (app()->getLocale() == 'pt_BR') Perfil
                    @elseif (app()->getLocale() == 'en') Profile
                    @else Perfil
                    @endif
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full px-4 py-2 text-left text-base font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        @if (app()->getLocale() == 'pt_BR') Sair
                        @elseif (app()->getLocale() == 'en') Logout
                        @else Salir
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
