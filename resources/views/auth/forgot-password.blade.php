<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('auth.forgot_password_intro') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('auth.email_label')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-center mt-4">
            <x-primary-button>
                {{ __('auth.send_password_reset_link') }}
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>