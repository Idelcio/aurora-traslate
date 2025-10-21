<!DOCTYPE html>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Aurora Translate</title>

        <link rel="icon" href="{{ asset('branding/aurora-favicon.svg') }}" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
        <style>
            [x-cloak] { display: none !important; }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 font-['Poppins',sans-serif] text-slate-50 antialiased">
                @php
            $flagMap = [
                'pt_BR' => ['img' => 'flags/br.png', 'label' => 'BR', 'title' => 'Português'],
                'en' => ['img' => 'flags/en.png', 'label' => 'EN', 'title' => 'English'],
                'es' => ['img' => 'flags/es.png', 'label' => 'ES', 'title' => 'Español'],
            ];
            $currentLocale = app()->getLocale();
            $currentFlag = $flagMap[$currentLocale]['img'] ?? 'flags/br.png';
            $currentLabel = $flagMap[$currentLocale]['label'] ?? 'BR';
            $isRegister = request()->routeIs('register');
            $headline = $isRegister
                ? __('guest.layout.headline.register')
                : __('guest.layout.headline.login');
            $subhead = $isRegister
                ? __('guest.layout.subhead.register')
                : __('guest.layout.subhead.login');
        @endphp


        <div class="relative flex min-h-screen flex-col overflow-hidden">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900"></div>
                <div class="absolute -top-32 -left-24 h-80 w-80 rounded-full bg-sky-500/25 blur-3xl"></div>
                <div class="absolute top-40 -right-16 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute bottom-12 left-1/3 h-64 w-64 rounded-full bg-cyan-400/10 blur-3xl"></div>
            </div>

            <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-8 sm:px-10">
                <a href="{{ url('/') }}" class="flex items-center gap-3 text-lg font-semibold tracking-tight text-white">
                    <img src="{{ asset('branding/aurora-mark.svg') }}" alt="Aurora Translate" class="h-9 w-9">
                    <span class="text-xl">{{ __('guest.layout.tagline') }}</span>
                </a>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1 rounded-full bg-white/10 px-2 py-1 text-xs font-semibold tracking-wide">
                        @foreach ($flagMap as $locale => $flag)
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}"
                               title="{{ $flag['title'] }}"
                               class="flex items-center gap-1 rounded-full px-2 py-1 transition {{ $locale === $currentLocale ? 'bg-white text-slate-900 shadow' : 'text-white/80 hover:bg-white/15' }}">
                                <img src="{{ asset($flag['img']) }}" alt="{{ $flag['label'] }}" class="h-4 w-4 rounded-full object-cover">
                                <span>{{ $flag['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                    @if (Route::has('login'))
                        <div class="hidden text-sm font-medium text-slate-200 sm:flex sm:items-center sm:gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="transition hover:text-white/80">
                                    {{ __('guest.layout.nav.dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="transition hover:text-white/80">
                                    {{ __('guest.layout.nav.login') }}
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="transition hover:text-white/80">
                                        {{ __('guest.layout.nav.register') }}
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </header>

            <main class="flex flex-1 items-center justify-center px-4 pb-12 pt-4 sm:px-8">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-10 rounded-[2.75rem] border border-white/5 bg-white/5 p-6 shadow-2xl shadow-indigo-950/40 backdrop-blur-2xl sm:p-10 lg:grid lg:grid-cols-[1.1fr_0.9fr] lg:gap-16">
                    <section class="flex flex-col justify-between gap-12">
                        <div class="space-y-6">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.4em] text-white/80">
                                {{ __('guest.layout.tagline') }}
                            </span>
                            <h1 class="text-4xl font-semibold leading-tight text-white sm:text-5xl">
                                {{ $headline }}
                            </h1>
                            <p class="max-w-xl text-base text-slate-200">
                                {{ $subhead }}
                            </p>
                        </div>

                        <div class="grid gap-4 text-sm text-slate-200/90 sm:text-base">
                            <div class="rounded-3xl border border-white/10 bg-slate-900/50 px-5 py-4 backdrop-blur">
                                <p class="font-semibold text-white">{{ __('guest.layout.features.upload_title') }}</p>
                                <p class="mt-1 text-slate-300/80">{{ __('guest.layout.features.upload_body') }}</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-900/50 px-5 py-4 backdrop-blur">
                                <p class="font-semibold text-white">{{ __('guest.layout.features.cloud_title') }}</p>
                                <p class="mt-1 text-slate-300/80">{{ __('guest.layout.features.cloud_body') }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="relative">
                        <div class="absolute -top-16 right-8 hidden h-32 w-32 rounded-full bg-indigo-400/30 blur-3xl lg:block"></div>
                        <div class="absolute -bottom-20 left-8 hidden h-28 w-28 rounded-full bg-cyan-400/25 blur-3xl lg:block"></div>

                        <div class="relative rounded-[2.25rem] border border-white/15 bg-white/95 p-6 shadow-2xl shadow-slate-900/30 sm:p-10">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </main>

            <footer class="mx-auto w-full max-w-6xl px-6 pb-10 pt-6 text-center text-sm text-slate-400 sm:px-10">
                @php
                    $termsMap = [
                        'pt_BR' => 1,
                        'en' => 3,
                        'es' => 4,
                    ];
                    $locale = app()->getLocale();
                    $termId = $termsMap[$locale] ?? 1;
                @endphp
                <p class="mb-3 text-xs uppercase tracking-[0.35em] text-white/60">
                    {{ __('guest.layout.footer') }}
                </p>
                <a href="{{ route('terms.show', ['id' => $termId]) }}" class="text-sm font-medium text-white/70 underline-offset-4 transition hover:text-white">
                    {{ __('guest.layout.terms_link') }}
                </a>
            </footer>
        </div>
    </body>
</html>



















