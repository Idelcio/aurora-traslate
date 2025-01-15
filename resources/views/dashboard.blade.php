<x-app-layout>

    <!-- Link da fonte -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Botão Alinhado -->
    <div class="container mx-auto px-0 mb-0"> <!-- Removido preenchimento interno -->
        <h2 class="text-2xl text-gray-800 leading-tight">
            <x-nav-link
                href="#"
                onclick="document.getElementById('pdf-input').click(); return false;"
                class="inline-flex items-center justify-center bg-[#004BAD] text-white px-2 py-1.5 rounded-md leading-none text-lg font-semibold hover:bg-[#333333] hover:text-white text-center h-10 font-[Montserrat] ml-10">
                {{ __('pdf_upload.upload_pdf') }}
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

    <!-- Formulário oculto para upload -->
    <form id="pdf-upload-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" id="pdf-input" name="pdf" accept="application/pdf" onchange="showLoadingOverlay(); document.getElementById('pdf-upload-form').submit();">
    </form>

    <!-- Overlay de carregamento -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>

    <!-- Script -->
    <script>
        function showLoadingOverlay() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }

        function hideLoadingOverlay() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }

        document.getElementById('pdf-upload-form').addEventListener('submit', function() {
            showLoadingOverlay(); // Mostra o overlay no envio do formulário
        });

        window.addEventListener('load', function() {
            hideLoadingOverlay(); // Oculta após o carregamento completo
        });
    </script>

</x-app-layout>