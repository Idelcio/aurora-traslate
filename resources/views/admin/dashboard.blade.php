<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-800">
            {{ __('Painel Administrativo') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 sm:px-8">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Total de usuários</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalUsers) }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Livros cadastrados</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalBooks) }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Páginas processadas</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalPages) }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Assinaturas ativas</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($activeSubscriptions) }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Receita mensal estimada</p>
                    <p class="mt-3 text-3xl font-semibold text-emerald-600">
                        R$ {{ number_format($totalRevenue, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Resumo por usuário</h3>
                        <p class="text-sm text-slate-500">
                            Quantidade de livros e páginas processadas por cada membro da plataforma.
                        </p>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Usuário
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    E-mail
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Livros
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Páginas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            @forelse ($usersSummary as $user)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-800">
                                        {{ number_format($user->books_count) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-600">
                                        {{ number_format($user->books_pages_sum ?? 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                                        Nenhum dado disponível no momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
