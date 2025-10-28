<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome.meta_title') }}</title>
    <link rel="icon" href="{{ asset('branding/aurora-favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-gradient-to-b from-slate-900 via-gray-900 to-gray-800 font-['Poppins',sans-serif] flex flex-col">
    @php
        $cardHighlights = trans('welcome.card.highlights');
        $infoItems = trans('welcome.info.items');
        $workflowSteps = trans('welcome.workflow.steps');
        $integrationCards = trans('welcome.integration.cards');
        $flagMap = [
            'pt_BR' => ['img' => 'flags/br.png', 'label' => 'BR', 'title' => 'Português'],
            'en' => ['img' => 'flags/en.png', 'label' => 'EN', 'title' => 'English'],
            'es' => ['img' => 'flags/es.png', 'label' => 'ES', 'title' => 'Español'],
        ];
        $currentLocale = app()->getLocale();
        $plans = \App\Models\Plan::query()->where('active', true)->orderBy('price')->get();
        $planTexts = trans('welcome.plans');
        $decimalSeparator = $currentLocale === 'en' ? '.' : ',';
        $thousandSeparator = $currentLocale === 'en' ? ',' : '.';
        $currencySymbol = $planTexts['currency'] ?? 'R$';
    @endphp

    <!-- Hero Section -->
    <header
        class="relative overflow-hidden rounded-b-[3rem] bg-gradient-to-br from-blue-400 via-indigo-500 to-slate-800 text-white">
        <!-- Background Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-slate-900/15 via-gray-800/60 to-gray-900 mix-blend-multiply">
        </div>

        <!-- Navigation -->
        <nav
            class="absolute inset-x-0 top-0 z-10 mx-auto flex w-full max-w-screen-xl items-center justify-between px-12 py-9 pb-10">
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-3 text-lg font-bold tracking-tight text-slate-50">
                <img src="{{ asset('branding/aurora-mark.svg') }}" alt="Aurora Translate" class="h-8 w-8">
                <span class="text-xl">{{ __('guest.layout.tagline') }}</span>
            </a>

            <div class="flex items-center gap-4">
                <!-- Language Selector -->
                <div
                    class="flex items-center gap-1 rounded-full bg-white/10 px-2 py-1 text-xs font-semibold tracking-wide">
                    @foreach ($flagMap as $locale => $flag)
                        <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}" title="{{ $flag['title'] }}"
                            class="flex items-center gap-1 rounded-full px-2 py-1 transition {{ $locale === $currentLocale ? 'bg-white text-slate-900 shadow' : 'text-white/80 hover:bg-white/15' }}">
                            <img src="{{ asset($flag['img']) }}" alt="{{ $flag['label'] }}"
                                class="h-4 w-4 rounded-full object-cover">
                            <span>{{ $flag['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Auth Links -->
                @if (Route::has('login'))
                    <div class="inline-flex gap-2 text-sm font-semibold">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full px-5 py-2 transition hover:bg-white/10">
                                {{ __('guest.layout.nav.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full px-5 py-2 transition hover:bg-white/10">
                                {{ __('guest.layout.nav.login') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="rounded-full px-5 py-2 transition hover:bg-white/10">
                                    {{ __('guest.layout.nav.register') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </nav>

        <!-- Hero Content -->
        <div class="relative z-[1] mx-auto grid max-w-screen-xl items-center gap-12 px-6 pb-28 pt-32 lg:grid-cols-2">
            <div>
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-slate-50/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.08em]">
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                    {{ __('welcome.hero.badge') }}
                </div>
                <h1
                    class="mt-4 text-4xl font-semibold leading-tight tracking-tight text-white drop-shadow-lg lg:text-5xl xl:text-6xl">
                    {{ __('welcome.hero.title') }}
                </h1>
                {{-- <p class="mt-6 text-base leading-relaxed text-white/90">
                    {{ __('welcome.hero.description') }}
                </p> --}}
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-8 py-4 text-sm font-semibold text-gray-900 shadow-xl shadow-slate-900/25 transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-900/35">
                        {{ __('welcome.hero.primary_cta') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                            stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18m0 0l-6-6m6 6l-6 6" />
                        </svg>
                    </a>
                    <a href="#integrations"
                        class="inline-flex items-center gap-2 rounded-full border border-white/55 px-8 py-4 text-sm font-semibold text-slate-50 transition hover:-translate-y-1 hover:opacity-80">
                        {{ __('welcome.hero.secondary_cta') }}
                    </a>
                </div>
            </div>

            <!-- Feature Card -->
            <div
                class="group relative rounded-[1.75rem] border border-white/20 bg-white/10 p-10  shadow-2xl shadow-slate-900/35 backdrop-blur-2xl transition hover:border-teal-300/30">
                <div
                    class="absolute inset-0 rounded-[1.75rem] border border-teal-200/0 opacity-0 transition group-hover:border-teal-200/30 group-hover:opacity-100">
                </div>
                <h3 class="text-2xl font-semibold text-white drop-shadow-lg">{{ __('welcome.card.title') }}</h3>
                <div class="mt-4 space-y-2">
                    @foreach ($cardHighlights as $highlight)
                        <p class="text-sm text-white/90 drop-shadow">- {{ $highlight }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Info Cards Section -->
        <section class="mx-auto -mt-10 mb-20 grid max-w-screen-lg gap-8 px-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($infoItems as $item)
                <article class="grid gap-5 rounded-[1.75rem] bg-white/95 p-10 shadow-2xl shadow-slate-900/15">
                    <span
                        class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600/10 text-lg font-semibold text-indigo-700">
                        {{ sprintf('%02d', $loop->iteration) }}
                    </span>
                    <h2 class="text-xl font-semibold text-slate-900">{{ $item['title'] }}</h2>
                    <p class="text-sm leading-relaxed text-slate-600">{{ $item['body'] }}</p>
                </article>
            @endforeach
        </section>

        <!-- Workflow Section -->
        <section class="mx-auto mb-20 mt-16 max-w-screen-lg px-6">
            <div class="mb-10">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-slate-700/50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-200">
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                    {{ __('welcome.workflow.badge') }}
                </div>
                <h2 class="mt-4 text-3xl font-semibold text-white">{{ __('welcome.workflow.title') }}</h2>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($workflowSteps as $step)
                    <div class="rounded-3xl border border-white/10 bg-slate-900/65 p-8 text-slate-200">
                        <strong class="mb-3 block text-lg font-semibold text-slate-50">{{ $step['title'] }}</strong>
                        <p class="text-sm leading-relaxed">{{ $step['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Integrations Section -->
        <section id="integrations" class="mx-auto mb-20 max-w-screen-lg px-6">
            <div class="mb-10">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-slate-700/50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-200">
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                    {{ __('welcome.integration.badge') }}
                </div>
                <h2 class="mt-4 text-3xl font-semibold text-white">{{ __('welcome.integration.title') }}</h2>
                <p class="mt-3 text-slate-400">{{ __('welcome.integration.description') }}</p>
            </div>
            <div class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($integrationCards as $card)
                    <div class="rounded-3xl border border-slate-900/10 bg-white/95 p-8 shadow-2xl shadow-slate-900/10">
                        <strong class="block font-semibold text-slate-900">{{ $card['title'] }}</strong>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $card['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        @if ($plans->isNotEmpty())
            <!-- Plans Section -->
            <section class="mx-auto mb-20 max-w-screen-xl px-6">
                <div
                    class="rounded-[2.5rem] border border-white/10 bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-950 px-10 py-16 shadow-2xl shadow-indigo-900/30">
                    <div class="mx-auto max-w-3xl text-center">
                        <h2 class="text-3xl font-semibold text-indigo-100 sm:text-4xl">
                            {{ $planTexts['title'] ?? '' }}
                        </h2>
                        <p class="mt-4 text-base leading-relaxed text-indigo-200">
                            {{ $planTexts['description'] ?? '' }}
                        </p>
                    </div>

                    <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($plans as $plan)
                            @php
                                $formattedPrice = number_format($plan->price, 2, $decimalSeparator, $thousandSeparator);
                                $formattedPages = number_format(
                                    max($plan->max_pages, 0),
                                    0,
                                    $decimalSeparator,
                                    $thousandSeparator,
                                );
                                $formattedBooks = number_format(
                                    max($plan->max_books_per_month, 0),
                                    0,
                                    $decimalSeparator,
                                    $thousandSeparator,
                                );
                                $planLink = Route::has('register')
                                    ? route('register', ['plan' => $plan->slug])
                                    : (Route::has('login')
                                        ? route('login', ['plan' => $plan->slug])
                                        : route('dashboard'));
                                $bookLimitText =
                                    $plan->max_books_per_month === 0
                                        ? $planTexts['book_limit_unlimited'] ?? ($planTexts['unlimited'] ?? '')
                                        : trans_choice('welcome.plans.book_limit', $plan->max_books_per_month, [
                                            'count' => $formattedBooks,
                                        ]);
                            @endphp
                            <article
                                class="group flex flex-col justify-between rounded-[1.75rem] bg-white/95 p-8 text-left shadow-xl shadow-indigo-900/10 ring-1 ring-transparent transition hover:-translate-y-1 hover:shadow-indigo-900/20 hover:ring-indigo-200/70">
                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-xl font-semibold text-slate-900">{{ $plan->name }}</h3>
                                        @if ($plan->max_pages === 0)
                                            <span
                                                class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                {{ $planTexts['unlimited_badge'] ?? ($planTexts['unlimited'] ?? 'Unlimited') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $plan->description }}</p>
                                </div>

                                <div class="mt-8 flex flex-col gap-4">
                                    <div class="flex items-baseline gap-2 text-slate-900">
                                        <span class="text-2xl font-semibold">{{ $currencySymbol }}</span>
                                        <span class="text-4xl font-bold tracking-tight">{{ $formattedPrice }}</span>
                                        <span class="text-xs uppercase text-slate-500">
                                            {{ $planTexts['per_month'] ?? '' }}
                                        </span>
                                    </div>
                                    {{-- <ul class="space-y-2 text-sm text-slate-600">
                                        <li class="flex items-center gap-2">
                                            <span
                                                class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600/10 text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            <span>
                                                @if ($plan->max_pages === 0)
                                                    {{ $planTexts['unlimited_label'] ?? $planTexts['unlimited'] ?? '' }}
                                                @else
                                                    {{ trans_choice('welcome.plans.page_limit', $plan->max_pages, ['count' => $formattedPages]) }}
                                                @endif
                                            </span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span
                                                class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600/10 text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>
                                            </span>
                                            <span>
                                                {{ $bookLimitText }}
                                            </span>
                                        </li>
                                    </ul> --}}
                                </div>

                                <div class="mt-8">
                                    <a href="{{ $planLink }}"
                                        class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/30 transition hover:-translate-y-0.5 hover:bg-indigo-500">
                                        {{ $planTexts['button'] ?? __('welcome.quick_start.button') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 12h18m0 0l-6-6m6 6l-6 6" />
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @else
            <!-- Quick Start CTA (Fallback) -->
            <section class="mx-auto mb-20 max-w-screen-lg px-6">
                <div
                    class="rounded-[2.5rem] border border-white/10 bg-gradient-to-br from-indigo-800 via-indigo-900 to-slate-950 px-10 py-16 text-center shadow-2xl shadow-indigo-900/30">
                    <h2 class="text-3xl font-semibold text-indigo-100 sm:text-4xl">
                        {{ __('welcome.quick_start.title') }}
                    </h2>
                    <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-indigo-200">
                        {{ __('welcome.quick_start.description') }}
                    </p>
                    @php
                        $quickStartUrl = Route::has('register')
                            ? route('register')
                            : (Route::has('login')
                                ? route('login')
                                : route('dashboard'));
                    @endphp
                    <a href="{{ $quickStartUrl }}"
                        class="group mt-8 inline-flex items-center gap-2 rounded-full bg-white/10 px-8 py-3 text-sm font-semibold text-indigo-50 transition hover:bg-white/20 hover:text-white">
                        {{ __('welcome.quick_start.button') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                            stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"
                            class="transition group-hover:translate-x-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18m0 0l-6-6m6 6l-6 6" />
                        </svg>
                    </a>
                </div>
            </section>
        @endif

        <!-- Final CTA Section -->
        <section
            class="mx-auto mb-20 max-w-screen-lg rounded-[1.75rem] border border-slate-500/20 bg-gradient-to-br from-indigo-900/15 via-indigo-800/30 to-indigo-900/50 px-12 py-16 text-center backdrop-blur-sm">
            <h2 class="text-3xl font-semibold text-indigo-100">{{ __('welcome.cta.title') }}</h2>
            <p class="mx-auto mt-4 max-w-2xl text-indigo-200">{{ __('welcome.cta.description') }}</p>
            <a href="{{ route('dashboard') }}"
                class="mt-8 inline-flex items-center gap-2 rounded-full bg-indigo-900 px-8 py-4 text-sm font-semibold text-indigo-100 shadow-none transition hover:-translate-y-1">
                {{ __('welcome.cta.button') }}
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18m0 0l-6-6m6 6l-6 6" />
                </svg>
            </a>
        </section>
    </main>

    <!-- Footer -->
    <footer class="pb-12 pt-6 text-center text-xs uppercase tracking-[0.08em] text-slate-400">
        {{ __('welcome.footer') }}
    </footer>
</body>

</html>
