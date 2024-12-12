<x-app-layout>

    <!-- Link da fonte -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Botão Alinhado -->
    <div class="container mx-auto px-0 mb-0"> <!-- Removido preenchimento interno -->
        <h2 class="text-2xl text-gray-800 leading-tight">
            <x-nav-link
                :href="route('pdf.upload.post')"
                :active="request()->routeIs('pdf.upload.post')"
                class="inline-flex items-center justify-center bg-[#004BAD] text-white px-2 py-1.5 rounded-md leading-none text-lg font-semibold hover:bg-[#333333] hover:text-white text-center h-10 font-[Montserrat] ml-10">
                {{ __('Upload de PDF') }}
            </x-nav-link>
        </h2>

    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Conteúdo adicional -->
            </div>
        </div>
    </div>

</x-app-layout>
