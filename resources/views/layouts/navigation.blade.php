<nav x-data="{ open: false, langOpen: false }" class="bg-white border-b border-gray-100 pb-0">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="absolute left-0 flex items-center h-full">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('icones/logo/tagpdf_logo.png') }}" alt="User Logo" class="w-[130px] ml-0">
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-auto sm:flex">
                @can('level')
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'border-b-4 border-[#004BAD] text-gray-900' : 'border-b-4 border-transparent text-gray-500 hover:border-[#004BAD] hover:text-gray-700' }} inline-flex items-center px-3 pt-1 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                    {{ __('Home') }}
                </a>

                <a href="{{ route('users.index') }}"
                    class="{{ request()->routeIs('users.index') ? 'border-b-4 border-[#004BAD] text-gray-900' : 'border-b-4 border-transparent text-gray-500 hover:border-[#004BAD] hover:text-gray-700' }} inline-flex items-center px-3 pt-1 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                    {{ __('Lista de usuários') }}
                </a>
                @endcan
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="relative">
                    <button @click="open = !open" class="flex items-center px-3 py-2 text-sm leading-4 font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <img src="{{ asset('icones/usuario/user.png') }}" alt="User Logo" class="h-6 w-6 mr-2">
                        <div>{{ Auth::user()->name }}</div>
                        <svg class="ml-1 w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown de Usuário -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded-md">
                        <!-- Link Perfil -->
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                            @if (app()->getLocale() == 'pt_BR') Perfil
                            @elseif (app()->getLocale() == 'en') Profile
                            @else Perfil
                            @endif
                        </a>

                        <!-- Botão Sair -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                @if (app()->getLocale() == 'pt_BR') Sair
                                @elseif (app()->getLocale() == 'en') Logout
                                @else Salir
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Language Switcher Manual -->
                <div class="ml-4 relative">
                    <button @click="langOpen = !langOpen" class="flex items-center px-3 py-2">
                        <div class="w-6 h-6 rounded-full overflow-hidden">
                            @php
                            $flagMap = [
                            'pt_BR' => ['img' => 'flags/br.png', 'label' => 'BR'],
                            'en' => ['img' => 'flags/en.png', 'label' => 'EN'],
                            'es' => ['img' => 'flags/es.png', 'label' => 'ES'],
                            ];
                            $currentLocale = app()->getLocale();
                            $currentFlag = $flagMap[$currentLocale]['img'] ?? 'flags/br.png';
                            @endphp
                            <img src="{{ asset($currentFlag) }}" alt="Idioma Atual">
                        </div>
                    </button>

                    <!-- Dropdown Manual de Idiomas -->
                    <div x-show="langOpen" @click.away="langOpen = false" class="absolute right-0 mt-2">
                        <div class="flex flex-col gap-1">
                            @foreach ($flagMap as $locale => $flag)
                            @if ($locale !== $currentLocale)
                            <form method="GET" action="{{ url()->current() }}" class="block">
                                <button type="submit" name="lang" value="{{ $locale }}" class="flex items-center space-x-2">
                                    <div class="w-5 h-5 rounded-full overflow-hidden">
                                        <img src="{{ asset($flag['img']) }}" alt="{{ $flag['label'] }}">
                                    </div>
                                    <span class="text-sm">{{ $flag['label'] }}</span>
                                </button>
                            </form>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
</nav>