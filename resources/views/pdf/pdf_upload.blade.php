<x-app-layout>
    <div class="container mx-auto mt-5">
        <h2 class="font-semibold">Carregar e Visualizar PDF</h2>

        @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-3 rounded mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Formulário para upload do PDF -->
        <form id="pdf-upload-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div>
                <!-- Rótulo vazio para o botão -->
                <label for="pdf" class="block mb-2"></label>

                <!-- Input do tipo file (botão oculto e estilizado) -->
                <input type="file" name="pdf" id="pdf-input" accept="application/pdf" required class="hidden">

                <!-- Botão personalizado -->
                <button type="button" id="choose-file-btn" class="bg-gray-700 text-white rounded p-2">
                    Escolher arquivo
                </button>
            </div>
        </form>

        <!-- Exibição do PDF -->
        @if (session('pdf_filename'))
        <h2 class="mt-8 font-semibold">Visualizar PDF:</h2>

        <div id="pdf-container" style="max-width: 95%; max-height: 600px; overflow: auto; border: 2px solid #4B4B4B; padding: 5px; border-radius: 4px;">
            <canvas id="pdf-canvas"></canvas>
        </div>

        @endif
    </div>

    <!-- Botões de Zoom Fixos -->
    <div class="fixed bottom-5 right-5 flex space-x-2 z-50">
        <button id="zoom-in" class="bg-blue-500 text-white rounded-full p-2 text-sm">
            +
        </button>
        <button id="zoom-out" class="bg-blue-500 text-white rounded-full p-2 text-sm">
            -
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

    <script>
        // Variáveis globais para controlar o PDF e o zoom
        let pdfDoc = null;
        let currentPage = 1;
        let scale = 0.5; // Escala inicial do PDF

        // Quando o botão personalizado for clicado, o input file será acionado
        document.getElementById('choose-file-btn').addEventListener('click', function() {
            document.getElementById('pdf-input').click();
        });

        // Submete automaticamente o formulário quando o arquivo for selecionado
        document.getElementById('pdf-input').addEventListener('change', function() {
            // Submete o formulário após o arquivo ser selecionado
            document.getElementById('pdf-upload-form').submit();
        });

        // Carregar e renderizar o PDF após o upload
        @if(session('pdf_filename'))
        const pdfUrl = "{{ route('pdf.show', ['filename' => session('pdf_filename')]) }}";

        // Carregar o PDF com PDF.js
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            renderPage(currentPage); // Renderiza a primeira página
        }).catch(function(error) {
            console.error('Erro ao carregar o PDF: ', error);
        });


        // Função para renderizar a página no canvas
        function renderPage(pageNum) {
            pdfDoc.getPage(pageNum).then(function(page) {
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');
                const viewport = page.getViewport({
                    scale: scale
                });

                // Ajusta o tamanho do canvas mantendo a proporção
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Renderiza a página no canvas
                page.render({
                    canvasContext: context,
                    viewport: viewport
                });
            });
        }

        // Zoom In - Aumenta a escala
        document.getElementById('zoom-in').addEventListener('click', function() {
            scale += 0.1; // Aumenta a escala em 0.1
            renderPage(currentPage); // Re-renderiza o PDF com a nova escala
        });

        // Zoom Out - Diminui a escala
        document.getElementById('zoom-out').addEventListener('click', function() {
            if (scale > 0.2) { // Limita o zoom para não ficar muito pequeno
                scale -= 0.1; // Diminui a escala em 0.1
                renderPage(currentPage); // Re-renderiza o PDF com a nova escala
            }
        });
        @endif
    </script>
</x-app-layout>