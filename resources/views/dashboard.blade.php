<x-app-layout>
    @php
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription?->plan;
        $books = $user->books()->latest()->paginate(10);
        $languageOptions = trans('dashboard.form.language_options');
        $targetLanguageOptions = trans('dashboard.form.target_language_options');
    @endphp

    {{-- Hero Section com Boas-vindas --}}
    <div class="relative overflow-hidden rounded-b-[3rem] bg-gradient-to-br from-indigo-600 via-blue-500 to-slate-700 text-white shadow-xl">
        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=80"
            alt="Dashboard" class="absolute inset-0 h-full w-full object-cover opacity-20">
        <div class="relative mx-auto max-w-7xl px-6 py-16 md:px-10">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="space-y-3">
                    <h1 class="text-3xl font-bold md:text-4xl">
                        Olá, {{ $user->name }}! 👋
                    </h1>
                    <p class="text-lg text-white/90">
                        Bem-vindo ao seu painel de traduções
                    </p>
                </div>

                {{-- Informações do Plano --}}
                @if($subscription && $subscription->isActive())
                    <div class="rounded-2xl border border-white/20 bg-white/10 p-6 backdrop-blur-lg">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-white/20 p-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white/80">Plano Atual</p>
                                <p class="text-xl font-bold">{{ $plan->name }}</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/20">
                            <p class="text-sm text-white/80">Limite de Páginas</p>
                            <p class="text-lg font-semibold">
                                @if($plan->max_pages == 0)
                                    Ilimitado ∞
                                @else
                                    até {{ number_format($plan->max_pages, 0, ',', '.') }} páginas/livro
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-yellow-300/30 bg-yellow-500/20 p-6 backdrop-blur-lg">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-semibold">Nenhum plano ativo</p>
                                <p class="text-sm text-white/80">Contrate um plano para começar</p>
                            </div>
                        </div>
                        <a href="#" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-white/90">
                            Ver Planos
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Mensagens de Feedback --}}
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-6 pt-6 md:px-10">
            <div class="rounded-2xl border border-green-200 bg-green-50 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-auto max-w-7xl px-6 pt-6 md:px-10">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Estatísticas Rápidas --}}
    <div class="mx-auto max-w-7xl px-6 py-12 md:px-10">
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Total de Livros --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-indigo-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Total de Livros</p>
                        <p class="text-2xl font-bold text-slate-900">{{ $books->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- Páginas Traduzidas --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-green-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Páginas Traduzidas</p>
                        <p class="text-2xl font-bold text-slate-900">{{ number_format($books->sum('total_pages'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Idiomas Usados --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-blue-100 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Idiomas Usados</p>
                        <p class="text-2xl font-bold text-slate-900">{{ $books->unique('target_language')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload e Histórico --}}
    <div class="mx-auto max-w-7xl px-6 pb-16 md:px-10">
        <div class="grid gap-10 lg:grid-cols-2">
            {{-- Formulário de Upload --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
                <div class="mb-6 flex items-center gap-3">
                    <div class="rounded-full bg-indigo-100 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Novo Livro</h2>
                </div>

                <form id="pdf-translate-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Upload de PDF --}}
                    <div>
                        <label for="pdf-file" class="mb-2 block text-sm font-semibold text-slate-700">Arquivo PDF</label>
                        <div id="drop-zone"
                            class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-indigo-400 hover:bg-indigo-50/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-slate-700">
                                Arraste seu PDF aqui ou <span class="text-indigo-600 underline">clique para selecionar</span>
                            </p>
                            <p id="selected-file" class="mt-2 text-xs font-medium text-slate-400">
                                Nenhum arquivo selecionado
                            </p>
                        </div>
                        <input id="pdf-file" type="file" name="pdf" accept="application/pdf" class="hidden" required>
                    </div>

                    {{-- Idiomas --}}
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="source_language" class="mb-2 block text-sm font-semibold text-slate-700">Idioma Original</label>
                            <select id="source_language" name="source_language"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                                <option value="">Auto-detectar</option>
                                @foreach ($languageOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="target_language" class="mb-2 block text-sm font-semibold text-slate-700">Traduzir Para</label>
                            <select id="target_language" name="target_language" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                                @foreach ($targetLanguageOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Botão de Envio --}}
                    <button type="submit" @if(!$subscription || !$subscription->isActive()) disabled @endif
                        class="group relative inline-flex w-full items-center justify-center gap-3 rounded-xl bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:-translate-y-0.5 hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Iniciar Tradução</span>
                    </button>

                    @if(!$subscription || !$subscription->isActive())
                        <p class="text-center text-sm text-red-600">
                            Você precisa de um plano ativo para traduzir livros
                        </p>
                    @endif
                </form>
            </div>

            {{-- Histórico de Livros --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="rounded-full bg-blue-100 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900">Seus Livros</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ $books->count() }}</span>
                </div>

                <div class="max-h-[600px] space-y-3 overflow-y-auto pr-2">
                    @forelse($books as $book)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-indigo-300 hover:shadow-md">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-slate-900 truncate">{{ $book->title }}</h3>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            {{ $book->total_pages }} páginas
                                        </span>
                                        <span>•</span>
                                        <span>{{ strtoupper($book->source_language) }} → {{ strtoupper($book->target_language) }}</span>
                                        <span>•</span>
                                        <span>{{ $book->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="mt-3 flex items-center gap-3">
                                        @if($book->status === 'translated')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Concluído
                                            </span>

                                            @if($book->translated_pdf_path)
                                                <a href="{{ route('books.download', $book->id) }}"
                                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Baixar
                                                </a>
                                            @endif
                                        @elseif($book->status === 'processing')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                                <svg class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Processando
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                Erro
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border-2 border-dashed border-slate-300 px-6 py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-slate-500">Nenhum livro traduzido ainda</p>
                            <p class="mt-1 text-xs text-slate-400">Faça upload do seu primeiro PDF para começar</p>
                        </div>
                    @endforelse
                </div>

                {{-- Paginação --}}
                @if($books->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $books->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div id="loading-overlay" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="flex flex-col items-center gap-4 rounded-3xl bg-white px-8 py-10 shadow-2xl">
            <svg class="h-10 w-10 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-medium text-slate-700">Processando seu livro...</p>
            <p class="text-xs text-slate-500">Isso pode levar alguns minutos</p>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('pdf-file');
        const dropZone = document.getElementById('drop-zone');
        const selectedFile = document.getElementById('selected-file');
        const form = document.getElementById('pdf-translate-form');
        const overlay = document.getElementById('loading-overlay');

        const updateFileName = file => {
            if (!file) {
                selectedFile.textContent = 'Nenhum arquivo selecionado';
                return;
            }
            selectedFile.textContent = file.name;
            selectedFile.classList.remove('text-slate-400');
            selectedFile.classList.add('text-indigo-600', 'font-semibold');
        };

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', event => {
            event.preventDefault();
            dropZone.classList.add('!border-indigo-600', '!bg-indigo-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('!border-indigo-600', '!bg-indigo-50');
        });

        dropZone.addEventListener('drop', event => {
            event.preventDefault();
            dropZone.classList.remove('!border-indigo-600', '!bg-indigo-50');
            if (event.dataTransfer.files.length) {
                const file = event.dataTransfer.files[0];
                if (file.type === 'application/pdf') {
                    fileInput.files = event.dataTransfer.files;
                    updateFileName(file);
                } else {
                    alert('Por favor, selecione apenas arquivos PDF.');
                }
            }
        });

        fileInput.addEventListener('change', event => {
            updateFileName(event.target.files[0]);
        });

        form.addEventListener('submit', (e) => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton.disabled) {
                e.preventDefault();
                return;
            }
            overlay.classList.remove('hidden');
        });

        window.addEventListener('load', () => overlay.classList.add('hidden'));
    </script>
</x-app-layout>
