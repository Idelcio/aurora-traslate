<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                Gerenciar Planos
            </h2>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 rounded-full border border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">
                ← Voltar ao painel
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-5xl flex-col gap-8 px-6 sm:px-8">
            @if (session('success'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-medium text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-4 text-sm text-rose-700 shadow-sm">
                    <p class="font-semibold">Não foi possível salvar o plano.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-sm text-slate-500">
                Ajuste preços, limites de páginas e limites mensais de livros diretamente por aqui. Valores com zero indicam ilimitado para o campo correspondente.
            </p>

            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($plans as $plan)
                    <form action="{{ route('admin.plans.update', $plan) }}"
                          method="POST"
                          class="flex flex-col gap-4 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        @csrf
                        @method('PATCH')

                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-700" for="name-{{ $plan->id }}">Nome do plano</label>
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                <input type="checkbox" name="active" value="1" @checked(old('active', $plan->active))
                                       class="h-4 w-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                Ativo
                            </label>
                        </div>
                        <input id="name-{{ $plan->id }}"
                               type="text"
                               name="name"
                               value="{{ old('name', $plan->name) }}"
                               class="rounded-2xl border border-slate-300 px-4 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                               required>

                        <div>
                            <label for="price-{{ $plan->id }}" class="text-sm font-semibold text-slate-700">Preço mensal</label>
                            <div class="mt-1 flex items-center gap-2 rounded-2xl border border-slate-300 bg-slate-50 px-3 py-2">
                                <span class="text-slate-500">R$</span>
                                <input id="price-{{ $plan->id }}"
                                       type="number"
                                       step="0.01"
                                       min="0"
                                       name="price"
                                       value="{{ old('price', $plan->price) }}"
                                       class="w-full border-0 bg-transparent text-sm text-slate-800 focus:outline-none"
                                       required>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="max-pages-{{ $plan->id }}" class="text-sm font-semibold text-slate-700">Páginas por livro</label>
                                <input id="max-pages-{{ $plan->id }}"
                                       type="number"
                                       min="0"
                                       name="max_pages"
                                       value="{{ old('max_pages', $plan->max_pages) }}"
                                       class="mt-1 w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                       required>
                                <p class="mt-1 text-xs text-slate-500">Use 0 para ilimitado.</p>
                            </div>
                            <div>
                                <label for="max-books-{{ $plan->id }}" class="text-sm font-semibold text-slate-700">Livros por mês</label>
                                <input id="max-books-{{ $plan->id }}"
                                       type="number"
                                       min="0"
                                       name="max_books_per_month"
                                       value="{{ old('max_books_per_month', $plan->max_books_per_month) }}"
                                       class="mt-1 w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                       required>
                                <p class="mt-1 text-xs text-slate-500">Use 0 para ilimitado.</p>
                            </div>
                        </div>

                        <div>
                            <label for="description-{{ $plan->id }}" class="text-sm font-semibold text-slate-700">Descrição</label>
                            <textarea id="description-{{ $plan->id }}"
                                      name="description"
                                      rows="3"
                                      class="mt-1 w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                      placeholder="Resumo do plano">{{ old('description', $plan->description) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-indigo-500">
                                Salvar alterações
                            </button>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
