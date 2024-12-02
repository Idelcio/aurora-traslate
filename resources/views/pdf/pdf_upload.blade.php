<x-app-layout>
    <div class="container mx-auto mt-5 px-4 sm:px-6 lg:px-8">
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
                <button type="button" id="choose-file-btn" class="bg-gray-700 text-white rounded p-2 w-full sm:w-auto">Upload de Arquivo</button>
                <button id="saveButton" class="bg-blue-500 text-white rounded p-2 w-full sm:w-auto">Salvar Anotações</button>
                <button id="refactorButton" class="bg-red-500 text-white rounded p-2 w-full sm:w-auto">Refatorar</button>
                <button id="remove-all-button" class="bg-purple-500 text-white rounded p-2 w-full sm:w-auto">Limpar</button>
            </div>
        </form>

        <!-- Exibição do PDF -->
        @if (isset($pdf_filename))
        <div class="mt-8">
            <div id="pdf-container" class="max-w-full h-auto overflow-auto border-2 border-gray-700 p-4 rounded relative">
                <canvas id="pdf-canvas"></canvas>
            </div>
            @endif
        </div>

        <!-- Contêiner para botões de zoom no canto inferior direito
        oculto
        -->
        <div id="zoom-buttons-container" hidden class="fixed bottom-5 right-5 z-50 bg-gray-200 rounded-lg p-2 border-2 border-gray-400 sm:bottom-3 sm:right-10">
            <div class="flex space-x-2">
                <button id="zoom-in" class="bg-blue-500 text-white rounded-full p-2 text-sm">+</button>
                <button id="zoom-out" class="bg-blue-500 text-white rounded-full p-2 text-sm">-</button>
            </div>
        </div>

        <!-- Menu contextual para opções de exclusão -->
        <div id="context-menu" class="hidden absolute bg-white border border-gray-300 rounded shadow-lg z-50 text-sm w-40 sm:w-48">
            <ul>
                <li id="remove-keep-numbering" class="p-2 hover:bg-gray-200 cursor-pointer">Excluir</li>
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


            document.getElementById('remove-all-button').addEventListener('click', function() {
                // Remove todos os círculos
                circles.forEach(circle => circle.remove());
                circles = []; // Limpa a lista de círculos
                counter = 1; // Reinicia o contador para os números
            });


            document.getElementById('choose-file-btn').addEventListener('click', function() {
                document.getElementById('pdf-input').click();
            });

            document.getElementById('pdf-input').addEventListener('change', function() {
                document.getElementById('pdf-upload-form').submit();
            });

            const pdfFilename = "{{ $pdf_filename ?? '' }}";

            if (pdfFilename) {
                const pdfUrl = "{{ route('pdf.show', ['filename' => ':filename']) }}".replace(':filename', pdfFilename);

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
                    let target = e.target;

                    // Verifica se o evento foi disparado sobre um círculo
                    if (target.classList.contains('circle')) {
                        if (e.deltaY < 0) circleScale += 0.1; // Aumenta o tamanho
                        if (e.deltaY > 0 && circleScale > 0.5) circleScale -= 0.1; // Diminui o tamanho (limite mínimo)
                        updateCircleSizes();
                    }
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

                document.getElementById('refactorButton').addEventListener('click', function() {
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

                    // Verifica se a nova posição não sobrepõe nenhuma bolinha existente
                    if (isOverlapping(x, y)) {
                        return; // Não adiciona o círculo se estiver sobrepondo outro
                    }

                    let circle = document.createElement('div');
                    circle.className = 'circle';
                    circle.textContent = counter++;
                    circle.dataset.x = x;
                    circle.dataset.y = y;

                    updateCircleStyles(circle);
                    container.appendChild(circle);
                    circles.push(circle);

                    // Iniciar o drag
                    makeDraggable(circle);
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

                function adjustCircleSizeForScale(scale) {
                    return 10 * scale;
                }

                // Função para verificar se a nova posição do círculo sobrepõe outro
                function isOverlapping(x, y) {
                    let overlapThreshold = 20; // Distância mínima entre círculos para evitar sobreposição
                    for (let circle of circles) {
                        let circleX = parseFloat(circle.dataset.x);
                        let circleY = parseFloat(circle.dataset.y);
                        let distance = Math.sqrt(Math.pow(x - circleX, 2) + Math.pow(y - circleY, 2));

                        // Se a distância entre os centros dos círculos for menor que o limite de sobreposição, retorna true
                        if (distance < overlapThreshold) {
                            return true; // O círculo vai se sobrepor
                        }
                    }
                    return false; // Não há sobreposição
                }

                // Função para tornar os círculos arrastáveis
                function makeDraggable(circle) {
                    let isDragging = false;
                    let offsetX, offsetY;

                    // Quando o usuário começa a arrastar
                    circle.addEventListener('mousedown', function(e) {
                        isDragging = true;
                        offsetX = e.clientX - parseFloat(circle.style.left);
                        offsetY = e.clientY - parseFloat(circle.style.top);

                        // Adiciona a classe para indicar que está sendo arrastado (opcional)
                        circle.classList.add('dragging');
                    });

                    // Quando o mouse move enquanto o círculo está sendo arrastado
                    document.addEventListener('mousemove', function(e) {
                        if (isDragging) {
                            let x = (e.clientX - offsetX) / scale;
                            let y = (e.clientY - offsetY) / scale;

                            circle.style.left = `${(x * scale)}px`;
                            circle.style.top = `${(y * scale)}px`;

                            // Atualiza as coordenadas do círculo
                            circle.dataset.x = x;
                            circle.dataset.y = y;

                            updateCircleStyles(circle); // Atualiza o estilo do círculo
                        }
                    });

                    // Quando o mouse é solto e o arrasto termina
                    document.addEventListener('mouseup', function() {
                        if (isDragging) {
                            isDragging = false;
                            circle.classList.remove('dragging');
                        }
                    });
                }


                document.getElementById('saveButton').addEventListener('click', async function() {
                    const existingPdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());
                    const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
                    const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
                    const pages = pdfDoc.getPages();
                    const firstPage = pages[0];

                    circles.forEach(circle => {
                        const x = parseFloat(circle.dataset.x);
                        const y = parseFloat(circle.dataset.y);
                        const text = circle.textContent;

                        // Desenha o círculo no PDF
                        firstPage.drawEllipse({
                            x,
                            y: firstPage.getHeight() - y,
                            xScale: 15 * circleScale,
                            yScale: 15 * circleScale,
                            color: PDFLib.rgb(6 / 255, 9 / 255, 209 / 255),
                        });

                        // Adiciona o texto ao círculo
                        firstPage.drawText(text, {
                            x: x - (text.length === 1 ? 3 * circleScale : (text.length === 2 ? 7 * circleScale : 10 * circleScale)),
                            y: firstPage.getHeight() - y - 3 * circleScale,
                            size: 12 * circleScale,
                            font,
                            color: PDFLib.rgb(1, 1, 1),
                        });
                    });

                    const pdfBytes = await pdfDoc.save();
                    const blob = new Blob([pdfBytes], {
                        type: 'application/pdf'
                    });
                    const url = URL.createObjectURL(blob);

                    const downloadLink = document.createElement('a');
                    downloadLink.href = url;
                    downloadLink.download = pdfFilename + "_boleado.pdf";
                    downloadLink.click();

                    URL.revokeObjectURL(url);
                });
            } else {
                console.error("Nenhum arquivo PDF foi carregado.");
            }

            document.getElementById('pdf-container').addEventListener('wheel', function(e) {
                e.preventDefault(); // Impede que o scroll afete a rolagem da página

                // Verifica se o evento foi disparado sobre um círculo
                let target = e.target;

                if (target.classList.contains('circle')) {
                    // Ajusta o zoom nos círculos (aumentar ou diminuir o tamanho)
                    if (e.deltaY < 0) {
                        circleScale += 0.1; // Aumenta o tamanho do círculo
                    } else if (e.deltaY > 0 && circleScale > 0.5) {
                        circleScale -= 0.1; // Diminui o tamanho do círculo, com limite mínimo
                    }
                    updateCircleSizes(); // Atualiza os tamanhos dos círculos

                } else {
                    // Aplica o zoom no PDF fora dos círculos
                    if (e.deltaY < 0) {
                        scale += 0.05; // Aumenta o zoom do PDF
                    } else if (e.deltaY > 0 && scale > 0.2) {
                        scale -= 0.05; // Diminui o zoom do PDF, com limite mínimo
                    }
                    renderPage(currentPage); // Renderiza a página com a nova escala
                }

                // Aplica o scroll normal no contêiner do PDF
                pdfContainer.scrollTop += e.deltaY;
            });
        </script>

</x-app-layout>