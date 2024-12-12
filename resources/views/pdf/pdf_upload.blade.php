<x-app-layout>
    <!-- Contêiner de Erros de Validação -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-0">
        @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-3 rounded mt-2">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Contêiner Principal Alinhado ao Cabeçalho -->
    <div class="w-full px-4 flex items-center justify-between">

        <!-- Formulário de Upload -->
        <form id="pdf-upload-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4 py-0">

            <!-- Logo à Esquerda -->
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('icones/logo/tagpdf_icone.png') }}" alt="User Logo" class="w-[40px]">
            </a>

            @csrf
            <input type="file" name="pdf" id="pdf-input" accept="application/pdf" required class="hidden">

            <!-- Botões de Ação -->
            <button type="button" id="choose-file-btn" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD]">
                Upload de Arquivo
            </button>

            <button id="saveButton" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD]">
                Salvar Anotações
            </button>

            <button id="refactorButton" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD]">
                Refatorar
            </button>

            <button id="remove-all-button" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD]">
                Limpar
            </button>


            <!-- Navegação de Página -->
            <div class="flex items-center justify-center space-x-4">
                <h3 class="text-lg font-medium text-gray-800">Página</h3>
                <button id="prev-page-btn" class="px-2 py-1 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400">
                    &lt;
                </button>
                <span id="current-page" class="px-4 py-2 bg-[#004BAD] text-white text-5xl font-bold rounded-md">
                    1
                </span>
                <button id="next-page-btn" class="px-2 py-1 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400">
                    &gt;
                </button>
            </div>
        </form>

        <!-- Terceira Div: Botão Comandos no Canto Direito -->
        <div class="ml-auto">
            <button id="toggle-comandos" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-[#004BAD] hidden">
                Comandos
            </button>
        </div>
    </div>


    <!-- Exibição do PDF -->
    @if (isset($pdf_filename))
    <div class="flex mt-0">
        <div id="pdf-container" class="flex-grow w-full h-[calc(100vh-120px)] overflow-auto border-2 border-gray-700 p-4 rounded relative">
            <canvas id="pdf-canvas"></canvas>
        </div>
        @endif


        <!-- Área de Comandos -->
        <div id="comandos-container" class="w-[320px] h-auto ml-auto bg-white shadow-md border-l-2 border-gray-700 rounded-lg">
            <div class="bg-[#333333] text-white font-bold text-[18px] p-2 flex items-center justify-between rounded-t-lg">
                <span>Comandos:</span>
                <button id="close-comandos" class="text-white text-xl rounded-full w-8 h-8 flex items-center justify-center hover:bg-white hover:text-black transition">
                    &times;
                </button>
            </div>


            <!-- Conteúdo dos Comandos (Sem rolagem) -->
            <div class="ml-auto space-y-2 p-2">
                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_tamanho_tag_(1).png') }}" alt="Definir Tamanho" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">Definir tamanho da tag:</h3>
                        <p class="text-gray-600 text-[12px]">Roda do mouse para cima ou para baixo sobre a tag.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_zom.png') }}" alt="Zoom" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">Zoom:</h3>
                        <p class="text-gray-600 text-[12px]">Roda do mouse para cima ou para baixo.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_mover.png') }}" alt="Mover PDF" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">Mover PDF:</h3>
                        <p class="text-gray-600 text-[12px]">Clique na roda do mouse.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_mouse.png') }}" alt="Adicionar Tag" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">Adicionar tag:</h3>
                        <p class="text-gray-600 text-[12px]">Clique com botão esquerdo na cota que deseja marcar.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_direito_mouse.png') }}" alt="Excluir Tag" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">Excluir tag:</h3>
                        <p class="text-gray-600 text-[12px]">Clique com botão direito sobre a tag que deseja excluir.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_e_segure_mouse_(1).png') }}" alt="Mover Tag" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">Mover tag:</h3>
                        <p class="text-gray-600 text-[12px]">Clique e segure com botão direito sobre a tag.</p>
                    </div>
                </div>
            </div>
        </div>

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

    <body data-pdf-uploaded="{{ session('pdf_uploaded') ? 'true' : 'false' }}">


        <!-- Scripts -->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

        <script>
            let pdfDoc = null;
            let currentPage = 1;
            let scale = 0.3;
            let circleScale = 1; // Variável para o tamanho dos círculos e números
            let circles = [];
            let counter = 1;
            let targetCircle = null;
            let pageCircles = {}; // Armazena círculos para cada página
            // Elementos
            const openModalBtn = document.getElementById('open-modal-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const modal = document.getElementById('page-selector-modal');
            const loadPageBtn = document.getElementById('load-page-btn');
            window.pdfDoc = null;
            window.currentPage = 1;

            const prevPageBtn = document.getElementById('prev-page-btn');
            const nextPageBtn = document.getElementById('next-page-btn');
            const currentPageDisplay = document.getElementById('current-page');
            // Gerenciamento da área de comandos
            document.addEventListener('DOMContentLoaded', () => {
                const comandosContainer = document.getElementById('comandos-container');
                const toggleComandosBtn = document.getElementById('toggle-comandos');
                const closeComandosBtn = document.getElementById('close-comandos');

                // Alternar visibilidade dos comandos
                toggleComandosBtn.addEventListener('click', () => {
                    comandosContainer.classList.toggle('hidden');
                });

                // Fechar a área de comandos
                closeComandosBtn.addEventListener('click', () => {
                    comandosContainer.classList.add('hidden');
                });
            });


            // atualiza o comandos-container
            document.addEventListener("DOMContentLoaded", () => {
                const comandosContainer = document.getElementById("comandos-container");
                const toggleComandosBtn = document.getElementById("toggle-comandos");
                const closeComandosBtn = document.getElementById("close-comandos");

                // Verifica no localStorage se o painel foi fechado manualmente
                let fechadoNoX = localStorage.getItem("fechadoNoX");

                // Função para abrir comandos
                function abrirComandos() {
                    comandosContainer.style.display = "block"; // Mantém aberto
                    toggleComandosBtn.style.display = "none";
                    localStorage.setItem("fechadoNoX", "false"); // Define como não fechado
                }

                // Função para fechar comandos ao clicar no botão "X"
                closeComandosBtn.addEventListener("click", () => {
                    comandosContainer.style.display = "none"; // Fecha a área de comandos
                    toggleComandosBtn.style.display = "inline-block"; // Mostra o botão "Comandos"
                    localStorage.setItem("fechadoNoX", "true"); // Salva o estado como fechado
                });

                // Reabrir comandos ao clicar no botão "Comandos"
                toggleComandosBtn.addEventListener("click", () => {
                    abrirComandos(); // Reabre comandos
                });

                // Lógica de carregamento ao entrar na página
                if (fechadoNoX === "false" || fechadoNoX === null) {
                    // Se não foi fechado manualmente, mantém aberto
                    abrirComandos();
                } else {
                    // Se foi fechado manualmente, exibe o botão
                    comandosContainer.style.display = "none";
                    toggleComandosBtn.style.display = "inline-block";
                }
            });



            // Função para atualizar exibição de página
            function updatePageDisplay() {
                currentPageDisplay.textContent = currentPage;
                renderPage(currentPage);
            }

            // Lógica para ir à página anterior
            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    updatePageDisplay();
                } else {
                    alert("Você já está na primeira página.");
                }
            });

            // Lógica para ir à próxima página
            nextPageBtn.addEventListener('click', () => {
                if (currentPage < pdfDoc.numPages) {
                    currentPage++;
                    updatePageDisplay();
                } else {
                    alert("Você já está na última página.");
                }
            });

            // Função global para renderizar a página
            window.renderPage = function(pageNum) {
                pdfDoc.getPage(pageNum).then((page) => {
                    const canvas = document.getElementById('pdf-canvas');
                    const context = canvas.getContext('2d');
                    const viewport = page.getViewport({
                        scale
                    });

                    // Redimensiona o canvas
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    // Renderiza a página no canvas
                    page.render({
                        canvasContext: context,
                        viewport: viewport,
                    }).promise.then(() => {
                        console.log(`Página ${pageNum} renderizada`);
                    });
                });
            };
            document.addEventListener('DOMContentLoaded', () => {
                const isPdfUploaded = document.body.dataset.pdfUploaded === 'true';

                if (isPdfUploaded) {
                    const modalComandos = document.getElementById('upload-modal');
                    if (modalComandos) {
                        modalComandos.classList.remove('hidden'); // Mostra o modal
                        console.log("Modal de comandos aberto automaticamente após upload.");
                    }
                }
            });


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
                // Limpa os círculos ao carregar um novo arquivo
                circles.forEach(circle => circle.remove());
                circles = []; // Esvazia o array de círculos
                counter = 1; // Reinicia o contador de círculos

                document.getElementById('pdf-upload-form').submit();
            });


            const pdfFilename = "{{ $pdf_filename ?? '' }}";
            let isPageChanging = false; // Controla se estamos trocando de página

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
                    try {
                        const existingPdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());
                        const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
                        const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);

                        const pages = pdfDoc.getPages();
                        const selectedPage = pages[currentPage - 1]; // Obtenha a página selecionada

                        // Adicione círculos e textos na página selecionada
                        circles.forEach(circle => {
                            const x = parseFloat(circle.dataset.x);
                            const y = parseFloat(circle.dataset.y);
                            const text = circle.textContent;

                            // Desenha o círculo
                            selectedPage.drawEllipse({
                                x,
                                y: selectedPage.getHeight() - y,
                                xScale: 15 * circleScale,
                                yScale: 15 * circleScale,
                                color: PDFLib.rgb(1, 1, 1), // Branco (equivalente a #FFFFFF)
                                borderColor: PDFLib.rgb(0 / 255, 75 / 255, 173 / 255), // Azul (hexadecimal #004BAD)
                                borderWidth: 2,
                            });

                            // Adicione o número no círculo
                            const fontSize = 16 * circleScale;
                            const textOffsetX = text.length === 1 ? fontSize * 0.3 : fontSize * 0.6;
                            const textOffsetY = fontSize * 0.35;

                            selectedPage.drawText(text, {
                                x: x - textOffsetX,
                                y: selectedPage.getHeight() - y - textOffsetY,
                                size: fontSize,
                                font,
                                color: PDFLib.rgb(0 / 255, 75 / 255, 173 / 255), // Azul (hexadecimal #004BAD)
                            });
                        });

                        // Adiciona o link no canto inferior direito
                        const linkFontSize = 12; // Tamanho da fonte do link
                        const linkText = "WWW.TAGPDF.COM.BR"; // Texto do link
                        const linkWidth = font.widthOfTextAtSize(linkText, linkFontSize); // Calcula a largura do texto
                        const pageWidth = selectedPage.getWidth();
                        const margin = 10; // Margem do texto para as bordas da página

                        selectedPage.drawText(linkText, {
                            x: pageWidth - linkWidth - margin, // Posição à direita com margem
                            y: margin, // Posição inferior com margem
                            size: linkFontSize,
                            font,
                            color: PDFLib.rgb(0 / 255, 75 / 255, 173 / 255), // Azul (hexadecimal #004BAD)
                        });

                        // Salva o PDF com as alterações
                        const pdfBytes = await pdfDoc.save();
                        const blob = new Blob([pdfBytes], {
                            type: 'application/pdf'
                        });
                        const url = URL.createObjectURL(blob);

                        // Baixa o arquivo
                        const downloadLink = document.createElement('a');
                        downloadLink.href = url;
                        downloadLink.download = pdfFilename + "_boleado.pdf";
                        downloadLink.click();

                        URL.revokeObjectURL(url);
                    } catch (error) {
                        console.error("Erro ao salvar o PDF:", error);
                        alert("Ocorreu um erro ao salvar o PDF. Verifique o console para mais detalhes.");
                    }
                });

            } else {
                console.error("Nenhum arquivo PDF foi carregado.");
            }

            function renderPage(pageNum) {
                pdfDoc.getPage(pageNum).then(function(page) {
                    const canvas = document.getElementById('pdf-canvas');
                    const context = canvas.getContext('2d');
                    const viewport = page.getViewport({
                        scale
                    });

                    // Limpa os círculos visíveis antes de renderizar a nova página
                    circles.forEach(circle => circle.remove());
                    circles = []; // Esvazia o array de círculos da página anterior
                    counter = 1; // Reinicia o contador de círculos

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    page.render({
                        canvasContext: context,
                        viewport,
                    }).promise.then(() => {
                        console.log(`Página ${pageNum} renderizada`);
                    });
                });
            }
        </script>
        </div>
    </body>
</x-app-layout>
