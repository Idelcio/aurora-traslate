<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Termos de Uso') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-4">Termos de Uso</h2>

                    @forelse($terms as $term)
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2 text-blue-800">{{ $term->title }}</h3>
                        <div class="text-gray-700 leading-relaxed">
                            {!! nl2br(e($term->content)) !!}
                        </div>

                    </div>
                    @empty
                    <p class="text-gray-600 italic">Nenhum termo de uso encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>