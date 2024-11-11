<x-app-layout>
    <div class="container mx-auto mt-5">
        <h2 class="font-semibold">Carregar e Visualizar PDF</h2>

        <!-- Exibe erros de validação -->
        @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-3 rounded mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Formulário de upload do PDF -->
        <form id="pdf-upload-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div>
                <label for="pdf" class="block mb-2"></label>
                <input type="file" name="pdf" id="pdf-input" accept="application/pdf" required class="hidden">
                <button type="button" id="choose-file-btn" class="bg-gray-700 text-white rounded p-2">Escolher arquivo</button>
            </div>
        </form>

        <!-- Exibição do PDF -->
        @if (isset($pdf_filename))
        <h2 class="mt-8 font-semibold">Visualizar PDF:</h2>
        <div id="pdf-container" style="max-width: 95%; max-height: 600px; overflow: auto; border: 1px solid #4B4B4B; padding: 5px; border-radius: 4px; position: relative;">
            <canvas id="pdf-canvas"></canvas>
        </div>
        <!-- Botões de Desfazer e Salvar -->
        <div class="mt-4">
            <button id="undoButton" class="bg-red-500 text-white rounded p-2">Desfazer</button>
            <button id="saveButton" class="bg-green-500 text-white rounded p-2">Salvar Anotações</button>
        </div>
        @endif
    </div>

    <!-- Contêiner para botões de zoom -->
    <div id="zoom-buttons-container" class="fixed bottom-5 right-5 z-50 cursor-move bg-gray-200 rounded-lg p-2 border-2 border-gray-400">
        <div class="flex space-x-2">
            <button id="zoom-in" class="bg-blue-500 text-white rounded-full p-2 text-sm">+</button>
            <button id="zoom-out" class="bg-blue-500 text-white rounded-full p-2 text-sm">-</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

    <script>
        let pdfDoc = null;
        let currentPage = 1;
        let scale = 0.3;
        let circles = [];
        let counter = 1;

        document.getElementById('choose-file-btn').addEventListener('click', function() {
            document.getElementById('pdf-input').click();
        });

        document.getElementById('pdf-input').addEventListener('change', function() {
            document.getElementById('pdf-upload-form').submit();
        });

        @if(isset($pdf_filename))
        const pdfUrl = "{{ route('pdf.show', ['filename' => $pdf_filename]) }}";

        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            renderPage(currentPage);
        }).catch(function(error) {
            console.error('Erro ao carregar o PDF:', error);
        });

        function renderPage(pageNum) {
            pdfDoc.getPage(pageNum).then(function(page) {
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');
                const viewport = page.getViewport({
                    scale: scale
                });

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise.then(() => {
                    // Atualiza a posição dos círculos ao renderizar a página
                    updateCirclePositions();
                });
            });
        }

        // Função de zoom
        document.getElementById('zoom-in').addEventListener('click', function() {
            scale += 0.1;
            renderPage(currentPage);
        });

        document.getElementById('zoom-out').addEventListener('click', function() {
            if (scale > 0.2) {
                scale -= 0.1;
                renderPage(currentPage);
            }
        });

        // Funções de marcação de círculos dentro do pdf-container
        let pdfContainer = document.getElementById('pdf-container');
        pdfContainer.addEventListener('click', function(event) {
            addCircle(event, pdfContainer);
        });
        pdfContainer.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            removeCircle(e);
        });

        function addCircle(event, container) {
            // Obtenha a posição do canvas dentro do container PDF
            let rect = document.getElementById('pdf-canvas').getBoundingClientRect();

            // Calcule a posição clicada relativa ao canvas do PDF, levando em conta o zoom (escala)
            let x = (event.clientX - rect.left) / scale; // posição X relativa ao PDF sem escala
            let y = (event.clientY - rect.top) / scale; // posição Y relativa ao PDF sem escala

            let circle = document.createElement('div');
            circle.className = 'circle';
            circle.textContent = counter++;
            circle.dataset.x = x; // armazena a posição X sem escala
            circle.dataset.y = y; // armazena a posição Y sem escala

            // Corrigir o cálculo da posição do círculo para centralizá-lo no ponto de clique
            let circleSize = 25; // Tamanho do círculo (metade da largura e altura)
            circle.style.left = `${(x * scale) - circleSize}px`; // Subtrai a metade do tamanho do círculo
            circle.style.top = `${(y * scale) - circleSize}px`; // Subtrai a metade do tamanho do círculo
            circle.style.transform = `scale(${scale})`; // ajusta o tamanho do círculo conforme a escala

            container.appendChild(circle);
            circles.push(circle);
        }

        function removeCircle(event) {
            let target = event.target;
            if (target.classList.contains('circle')) {
                let index = circles.indexOf(target);
                if (index !== -1) circles.splice(index, 1);
                target.remove();

                circles.forEach((circle, i) => {
                    circle.textContent = i + 1;
                });
                counter = circles.length + 1;
            }
        }

        // Função para atualizar as posições dos círculos após o zoom
        function updateCirclePositions() {
            circles.forEach(circle => {
                let x = parseFloat(circle.dataset.x); // posição original X sem escala
                let y = parseFloat(circle.dataset.y); // posição original Y sem escala

                // Recalcula a posição e o tamanho conforme a nova escala
                let circleSize = 25; // Tamanho do círculo (metade da largura e altura)
                circle.style.left = `${(x * scale) - circleSize}px`;
                circle.style.top = `${(y * scale) - circleSize}px`;
                circle.style.transform = `scale(${scale})`;
            });
        }

        // Função de desfazer última anotação
        document.getElementById('undoButton').addEventListener('click', function() {
            if (circles.length > 0) {
                let lastCircle = circles.pop();
                lastCircle.remove();
                circles.forEach((circle, i) => circle.textContent = i + 1);
                counter = circles.length + 1;
            }
        });

        // Função de salvar anotações
        document.getElementById('saveButton').addEventListener('click', function() {
            console.log("Salvando anotações...");
            // Lógica de salvar anotações aqui
        });
        @endif
    </script>

    <!-- CSS para os círculos -->
    <style>
        .circle {
            width: 50px;
            height: 50px;
            background-color: red;
            color: white;
            border-radius: 50%;
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            cursor: pointer;
            user-select: none;
        }

        /* Cursor de mira */
        #pdf-container {

            cursor: crosshair;
        }
    </style>
</x-app-layout>
