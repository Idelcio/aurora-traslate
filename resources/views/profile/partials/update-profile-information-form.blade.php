<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('profile.profile_info') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('profile.update_your_profile') }}
        </p>
    </header>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        <!-- Campo para Nome -->
        <div>
            <x-input-label for="name" :value="__('profile.name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                value="{{ old('name', $user->name ?? '') }}"
                required
                autofocus
                autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Campo para E-mail -->
        <div>
            <x-input-label for="email" :value="__('profile.email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                value="{{ old('email', $user->email ?? '') }}"
                required
                autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('profile.unverified_email') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('profile.resend_verification') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600">
                    {{ __('profile.verification_link_sent') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <!-- Campo para Telefone -->
        <div>
            <x-input-label for="phone" :value="__('profile.phone')" />
            <x-text-input
                id="phone"
                name="phone"
                type="text"
                class="mt-1 block w-full"
                value="{{ old('phone', $user->phone ?? '') }}" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Campo para Nome da Empresa -->
        <div>
            <x-input-label for="company_name" :value="__('profile.company_name')" />
            <x-text-input
                id="company_name"
                name="company_name"
                type="text"
                class="mt-1 block w-full"
                value="{{ old('company_name', $user->company_name ?? '') }}" />
            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
        </div>

        <!-- Botão de Salvar -->
        <div class="flex items-center gap-4">
            <x-primary-button class="font-normal">
                {{ __('profile.save') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600">
                {{ __('profile.saved') }}
            </p>
            @endif
        </div>

    </form>
</section>