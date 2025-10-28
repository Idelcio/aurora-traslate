<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                Painel Administrativo
            </h2>
            <a href="{{ route('admin.plans.index') }}"
               class="inline-flex items-center gap-2 rounded-full border border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">
                Gerenciar planos
            </a>
        </div>
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

            @if (session('success'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-medium text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Resumo por usuário</h3>
                        <p class="text-sm text-slate-500">
                            Acompanhe traduções, páginas processadas e planos atribuídos.
                        </p>
                    </div>
                </div>

                @if($plans->isNotEmpty())
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($plans as $plan)
                            @php
                                $activeCount = $activePlanCounts[$plan->id] ?? 0;
                            @endphp
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
                                <p class="font-semibold text-slate-800">{{ $plan->name }}</p>
                                <p class="mt-1 text-slate-500">Usuários com este plano: {{ number_format($activeCount) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

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
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Plano atual
                                </th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Livros no mês
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Gerenciar
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-sm">
                            @forelse ($usersSummary as $user)
                                @php
                                    $activeSubscription = $user->activeSubscription;
                                    $plan = optional($activeSubscription)->plan;
                                    $booksThisMonth = $user->books_this_month ?? 0;
                                    $limitPerMonth = $plan?->max_books_per_month ?? 0;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ $user->name }}
                                        @if($user->is_admin)
                                            <span class="ml-2 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">Admin</span>
                                        @endif
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
                                    <td class="px-4 py-3 text-slate-600">
                                        @if ($plan)
                                            <div class="font-medium text-slate-800">{{ $plan->name }}</div>
                                            <p class="text-xs text-slate-500">
                                                {{ $plan->max_books_per_month > 0
                                                    ? trans_choice('welcome.plans.book_limit', $plan->max_books_per_month, ['count' => $plan->max_books_per_month])
                                                    : __('welcome.plans.book_limit_unlimited') }}
                                            </p>
                                        @else
                                            <span class="text-slate-400">Sem plano</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-600">
                                        @if ($plan && $plan->max_books_per_month > 0)
                                            <span class="font-semibold text-slate-800">{{ number_format($booksThisMonth) }}</span>
                                            <span class="text-xs text-slate-500">/ {{ number_format($plan->max_books_per_month) }}</span>
                                        @else
                                            <span class="font-semibold text-slate-800">{{ number_format($booksThisMonth) }}</span>
                                            <span class="text-xs text-slate-500">/ ∞</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <form action="{{ route('admin.users.plan.update', $user) }}" method="POST" class="flex items-center gap-3">
                                            @csrf
                                            @method('PATCH')
                                            <select name="plan_id"
                                                    class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                                <option value="">Sem plano</option>
                                                @foreach ($plans as $planOption)
                                                    <option value="{{ $planOption->id }}" @selected(optional($plan)->id === $planOption->id)>
                                                        {{ $planOption->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                    class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                                Atualizar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">
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
