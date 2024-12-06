<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            <x-nav-link
                :href="route('pdf.upload.post')"
                :active="request()->routeIs('pdf.upload.post')"
                class="inline-block bg-[#004BAD] text-white px-4 py-2 rounded-md ease-in-out leading-[2rem]">
                {{ __('Upload de PDF') }}
            </x-nav-link>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Conteúdo adicional -->
            </div>
        </div>
    </div>

</x-app-layout>