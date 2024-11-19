<x-app-layout>
    <div class="container mx-auto mt-5 ml-10">
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
        <h2 class="mt-8 font-semibold"></h2>
        <div id="pdf-container" style="max-width: 95%; max-height: 600px; overflow: auto; border: 1px solid #4B4B4B; padding: 5px; border-radius: 4px; position: relative;">
            <canvas id="pdf-canvas"></canvas>
        </div>
        <!-- Botões de Desfazer e Salvar -->
        <div class="mt-4">
            <button id="saveButton" class="bg-green-500 text-white rounded p-2">Salvar Anotações</button>
        </div>
        @endif
    </div>

    <!-- Contêiner para botões de zoom no canto inferior direito -->
    <div id="zoom-buttons-container" class="fixed bottom-5 right-5 z-50 bg-gray-200 rounded-lg p-2 border-2 border-gray-400">
        <div class="flex space-x-2">
            <button id="zoom-in" class="bg-blue-500 text-white rounded-full p-2 text-sm">+</button>
            <button id="zoom-out" class="bg-blue-500 text-white rounded-full p-2 text-sm">-</button>
        </div>
    </div>

    <!-- Menu contextual para opções de exclusão -->
    <div id="context-menu" class="hidden absolute bg-white border border-gray-300 rounded shadow-lg z-50">
        <ul>
            <li id="remove-and-refactor" class="p-2 hover:bg-gray-200 cursor-pointer">Excluir e Refatorar</li>
            <li id="remove-keep-numbering" class="p-2 hover:bg-gray-200 cursor-pointer">Excluir e Manter Numeração</li>
        </ul>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>

    <script>
        let pdfDoc = null;
        let currentPage = 1;
        let scale = 0.3;
        let circleScale = 1; // Variável para o tamanho dos círculos e números
        let circles = [];
        let counter = 1;
        let targetCircle = null;

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
                    scale
                });

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({
                    canvasContext: context,
                    viewport
                }).promise.then(() => {
                    updateCirclePositions();
                });
            });
        }

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

        let pdfContainer = document.getElementById('pdf-container');
        pdfContainer.addEventListener('click', function(event) {
            addCircle(event, pdfContainer);
        });

        pdfContainer.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            let target = e.target;
            if (target.classList.contains('circle')) {
                targetCircle = target;
                showContextMenu(e);
            }
        });

        pdfContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            if (e.deltaY < 0) circleScale += 0.1; // Aumenta o tamanho
            if (e.deltaY > 0 && circleScale > 0.5) circleScale -= 0.1; // Diminui o tamanho (limite mínimo)
            updateCircleSizes();
        });

        function showContextMenu(e) {
            const contextMenu = document.getElementById('context-menu');
            contextMenu.style.left = `${e.pageX}px`;
            contextMenu.style.top = `${e.pageY}px`;
            contextMenu.classList.remove('hidden');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('#context-menu')) {
                document.getElementById('context-menu').classList.add('hidden');
            }
        });

        document.getElementById('remove-and-refactor').addEventListener('click', function() {
            if (targetCircle) {
                removeCircle(targetCircle, true);
                document.getElementById('context-menu').classList.add('hidden');
            }
        });

        document.getElementById('remove-keep-numbering').addEventListener('click', function() {
            if (targetCircle) {
                removeCircle(targetCircle, false);
                document.getElementById('context-menu').classList.add('hidden');
            }
        });

        function addCircle(event, container) {
            let rect = document.getElementById('pdf-canvas').getBoundingClientRect();
            let x = (event.clientX - rect.left) / scale;
            let y = (event.clientY - rect.top) / scale;

            let circle = document.createElement('div');
            circle.className = 'circle';
            circle.textContent = counter++;
            circle.dataset.x = x;
            circle.dataset.y = y;

            updateCircleStyles(circle);
            container.appendChild(circle);
            circles.push(circle);
        }

        function updateCircleSizes() {
            circles.forEach(circle => updateCircleStyles(circle));
        }

        function updateCircleStyles(circle) {
            let x = parseFloat(circle.dataset.x);
            let y = parseFloat(circle.dataset.y);
            let size = adjustCircleSizeForScale(scale * circleScale);

            circle.style.left = `${(x * scale) - size}px`;
            circle.style.top = `${(y * scale) - size}px`;
            circle.style.transform = `scale(${scale * circleScale})`;
        }

        function removeCircle(circle, refactorNumbers) {
            let index = circles.indexOf(circle);
            if (index !== -1) circles.splice(index, 1);
            circle.remove();

            if (refactorNumbers) {
                circles.forEach((circle, i) => {
                    circle.textContent = i + 1;
                });
                counter = circles.length + 1;
            }
        }

        function updateCirclePositions() {
            circles.forEach(circle => updateCircleStyles(circle));
        }

        document.getElementById('saveButton').addEventListener('click', async function() {
            const existingPdfBytes = await fetch("{{ route('pdf.show', ['filename' => $pdf_filename]) }}").then(res => res.arrayBuffer());
            const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
            const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
            const pages = pdfDoc.getPages();
            const firstPage = pages[0];

            circles.forEach(circle => {
                const x = parseFloat(circle.dataset.x);
                const y = parseFloat(circle.dataset.y);
                const text = circle.textContent;

                firstPage.drawEllipse({
                    x,
                    y: firstPage.getHeight() - y,
                    xScale: 15 * circleScale, // Tamanho do círculo no eixo X
                    yScale: 15 * circleScale, // Tamanho do círculo no eixo Y
                    color: PDFLib.rgb(209 / 255, 6 / 255, 6 / 255), // Cor de preenchimento (vermelho)
                });

                firstPage.drawText(text, {
                    x: x - 3 * circleScale, // Ajusta para centralizar o texto no círculo
                    y: firstPage.getHeight() - y - 3 * circleScale, // Ajusta para centralizar o texto no círculo
                    size: 12 * circleScale, // Tamanho do texto
                    font,
                    color: PDFLib.rgb(1, 1, 1), // Cor do texto (branco)
                });
            });

            // Salva o PDF
            const pdfBytes = await pdfDoc.save();
            const blob = new Blob([pdfBytes], {
                type: 'application/pdf'
            });
            const url = URL.createObjectURL(blob);

            // Salva o PDF com o nome original
            const downloadLink = document.createElement('a');
            downloadLink.href = url;
            downloadLink.download = "{{ $pdf_filename }}" + "_boleado.pdf";
            downloadLink.click();

            URL.revokeObjectURL(url);
        });


        function adjustCircleSizeForScale(scale) {
            return 10 * scale;
        }
        @endif
    </script>

    <style>
        #pdf-container {
            position: relative;
        }

        .circle {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: red;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            transform-origin: center;
        }
    </style>
</x-app-layout>