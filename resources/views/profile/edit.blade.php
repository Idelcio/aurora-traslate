<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('profile.profile_info') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <!-- Formulário de atualização do perfil -->
            <div class="rounded-3xl border border-slate-200/60 bg-white/95 p-4 shadow-xl shadow-slate-900/5 backdrop-blur-xl sm:p-8">
                <div class="max-w-xl">
                    @includeWhen(isset($user) && is_array((array) $user), 'profile.partials.update-profile-information-form', ['user' => $user])
                </div>
            </div>

            <!-- Formulário de alteração de senha -->
            <div class="rounded-3xl border border-slate-200/60 bg-white/95 p-4 shadow-xl shadow-slate-900/5 backdrop-blur-xl sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Formulário de exclusão de usuário -->
            <div class="rounded-3xl border border-slate-200/60 bg-white/95 p-4 shadow-xl shadow-slate-900/5 backdrop-blur-xl sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
