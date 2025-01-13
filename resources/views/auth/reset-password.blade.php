<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password">
                @if (app()->getLocale() == 'pt_BR')
                Senha
                @elseif (app()->getLocale() == 'es')
                Contraseña
                @else
                Password
                @endif
            </x-input-label>
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation">
                @if (app()->getLocale() == 'pt_BR')
                Confirmar senha
                @elseif (app()->getLocale() == 'es')
                Confirmar contraseña
                @else
                Confirm Password
                @endif
            </x-input-label>

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                @if (app()->getLocale() == 'pt_BR')
                Redefinir senha
                @elseif (app()->getLocale() == 'es')
                Restablecer contraseña
                @else
                Reset Password
                @endif
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>