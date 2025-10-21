<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-slate-900">
            <h2 class="text-3xl font-semibold tracking-tight">{{ __('guest.register.title') }}</h2>
            <p class="text-sm text-slate-500">
                {{ __('guest.register.subtitle') }}
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <x-input-label for="name" :value="__('forms.name')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="name"
                              type="text"
                              name="name"
                              :value="old('name')"
                              required
                              autofocus
                              autocomplete="name"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('name')" class="text-sm text-rose-500" />
            </div>

            <div class="space-y-2">
                <x-input-label for="company_name" :value="__('forms.company_name')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="company_name"
                              type="text"
                              name="company_name"
                              :value="old('company_name')"
                              required
                              autocomplete="organization"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('company_name')" class="text-sm text-rose-500" />
            </div>

            <div class="space-y-2">
                <x-input-label for="phone" :value="__('forms.phone')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="phone"
                              type="tel"
                              name="phone"
                              :value="old('phone')"
                              required
                              autocomplete="tel"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('phone')" class="text-sm text-rose-500" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" :value="__('forms.email')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="email"
                              type="email"
                              name="email"
                              :value="old('email')"
                              required
                              autocomplete="username"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('email')" class="text-sm text-rose-500" />
            </div>

            <div class="space-y-2">
                <x-input-label for="cpf" :value="__('forms.cpf')" class="text-sm font-medium text-slate-700" />
                <x-text-input id="cpf"
                              type="text"
                              name="cpf"
                              :value="old('cpf')"
                              required
                              placeholder="000.000.000-00"
                              maxlength="14"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                <x-input-error :messages="$errors->get('cpf')" class="text-sm text-rose-500" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('forms.password')" class="text-sm font-medium text-slate-700" />
                    <x-text-input id="password"
                                  type="password"
                                  name="password"
                                  required
                                  autocomplete="new-password"
                                  class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                    <x-input-error :messages="$errors->get('password')" class="text-sm text-rose-500" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('forms.password_confirmation')" class="text-sm font-medium text-slate-700" />
                    <x-text-input id="password_confirmation"
                                  type="password"
                                  name="password_confirmation"
                                  required
                                  autocomplete="new-password"
                                  class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="text-sm text-rose-500" />
                </div>
            </div>

            <div class="space-y-3">
                <button type="submit"
                        class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-full bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:-translate-y-0.5 hover:bg-indigo-500">
                    <span class="absolute inset-0 bg-gradient-to-r from-indigo-500 via-blue-500 to-cyan-500 opacity-0 transition group-hover:opacity-100"></span>
                    <span class="relative">{{ __('forms.register') }}</span>
                </button>

                <p class="text-center text-sm text-slate-500">
                    {{ __('guest.register.has_account') }}
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">
                        {{ __('messages.login') }}
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script>
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }

            e.target.value = value;
        });
    </script>
</x-guest-layout>
