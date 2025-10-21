<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-slate-900">
            <h2 class="text-3xl font-semibold tracking-tight">{{ __('guest.login.title') }}</h2>
            <p class="text-sm text-slate-500">
                {{ __('guest.login.subtitle') }}
            </p>
        </div>

        @if (session('status'))
            <x-auth-session-status class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700" :status="session('status')" />
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <x-input-label for="email" :value="__('messages.email')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="email"
                              type="email"
                              name="email"
                              :value="old('email')"
                              required
                              autofocus
                              autocomplete="username"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('email')" class="text-sm text-rose-500" />
            </div>

            <div class="space-y-2">
                <x-input-label for="password" :value="__('messages.password')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="password"
                              type="password"
                              name="password"
                              required
                              autocomplete="current-password"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('password')" class="text-sm text-rose-500" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-500">
                    <input id="remember_me"
                           type="checkbox"
                           name="remember"
                           class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span>{{ __('messages.remember_me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">
                        {{ __('messages.forgot_password') }}
                    </a>
                @endif
            </div>

            <div class="space-y-3">
                <button type="submit"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:-translate-y-0.5 hover:bg-indigo-500">
                    <span class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-blue-500 to-cyan-500 opacity-0 transition group-hover:opacity-100"></span>
                    <span class="relative">{{ __('messages.login') }}</span>
                </button>

                @if (Route::has('register'))
                    <p class="text-center text-sm text-slate-500">
                        {{ __('guest.login.no_account') }}
                        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">
                            {{ __('messages.register') }}
                        </a>
                    </p>
                @endif
            </div>
        </form>
    </div>
</x-guest-layout>
