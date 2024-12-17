<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
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
        <form id="pdf-upload-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-4 py-0 font-normal" style="font-family: 'Montserrat', sans-serif;">

            <!-- Logo à Esquerda -->
            <a href="#" class="flex items-center">
                <img src="{{ asset('icones/logo/tagpdf_icone.png') }}" alt="User Logo" class="w-[40px]">
            </a>


            @csrf
            <input type="file" name="pdf" id="pdf-input" accept="application/pdf" required class="hidden">

            <!-- Botões de Ação -->
            <button type="button" id="choose-file-btn" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD] font-normal">
                Upload de PDF
            </button>

            <button id="saveButton" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD] font-normal">
                Salvar anotações
            </button>

            <button id="refactorButton" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD] font-normal">
                Reordenar
            </button>

            <button id="remove-all-button" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD] font-normal">
                Limpar
            </button>

            <!-- Navegação de Página -->
            <div id="page-selector" class="flex items-center justify-center space-x-4">
                <h3 class="text-lg font-normal text-gray-800">Página</h3>
                <button id="prev-page-btn" class="px-2 py-1 bg-gray-300 text-gray-800 text-sm rounded-md hover:bg-gray-400 font-normal">&lt;</button>

                <div id="page-numbers" class="flex space-x-2"></div>

                <button id="next-page-btn" class="px-2 py-1 bg-gray-300 text-gray-800 text-sm rounded-md hover:bg-gray-400 font-normal">&gt;</button>
            </div>

        </form>

        <!-- Terceira Div: Botão Comandos no Canto Direito -->
        <div class="ml-auto">
            <button id="toggle-comandos" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-[#004BAD] hidden font-normal">
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
        <div id="comandos-container" class="w-[320px] h-auto ml-auto bg-white shadow-md border-l-2  rounded-lg">
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
            // Armazena a referência ao documento PDF carregado. Inicialmente, não há PDF, portanto é nulo.

            let currentPage = 1;
            // Define o número da página atualmente exibida no PDF. Começa na página 1.

            let scale = 0.3;
            // Define o nível de zoom base para a renderização do PDF. O viewport da página será escalado por esse valor.

            let circleScale = 1;
            // Define a escala dos círculos (marcações) desenhados sobre o PDF. Ajusta o tamanho relativo deles.

            let circles = [];
            // Array para armazenar as referências aos elementos HTML (círculos) criados sobre o PDF, para manipulação futura.

            let counter = 1;
            // Contador usado para numerar os círculos em ordem crescente conforme eles são adicionados.

            let targetCircle = null;
            // Armazena a referência ao círculo atualmente selecionado (por exemplo, para remoção ou edição).

            let pageCircles = {};
            // Um objeto para armazenar os círculos por página. Cada chave seria um número de página, e o valor um array de círculos.
            // (Pode não estar sendo usado agora, mas a intenção é agrupar círculos por página.)

            window.pdfDoc = null;
            // Define também a variável pdfDoc no escopo global (window), permitindo acessá-la de fora do escopo atual se necessário.

            window.currentPage = 1;
            // Define a variável currentPage também no escopo global, possibilitando acesso global ao número da página atual.

            const openModalBtn = document.getElementById('open-modal-btn');
            // Obtém a referência ao botão que abre um modal (por exemplo, um seletor de páginas). Pode não estar em uso imediato.

            const closeModalBtn = document.getElementById('close-modal-btn');
            // Referência ao botão que fecha o modal de seleção de página.

            const modal = document.getElementById('page-selector-modal');
            // Referência ao elemento modal que permite selecionar uma página específica do PDF.

            const loadPageBtn = document.getElementById('load-page-btn');
            // Referência ao botão que confirma o carregamento de uma página selecionada no modal.

            let currentBlockStart = 1;
            const pagesPerBlock = 5;
            const pageNumbersContainer = document.getElementById("page-numbers");

            const currentPageDisplay = document.getElementById('current-page');
            // Referência ao elemento HTML que mostra o número da página atual para o usuário.



            // comandos

            document.addEventListener("DOMContentLoaded", () => {
                const comandosContainer = document.getElementById("comandos-container");
                const toggleComandosBtn = document.getElementById("toggle-comandos");
                const closeComandosBtn = document.getElementById("close-comandos");

                // Função para fechar comandos ao clicar no botão "X"
                closeComandosBtn.addEventListener("click", () => {
                    comandosContainer.style.display = "none"; // Fecha a área de comandos
                    toggleComandosBtn.style.display = "inline-block"; // Mostra o botão "Comandos"
                });

                // Reabrir comandos ao clicar no botão "Comandos"
                toggleComandosBtn.addEventListener("click", () => {
                    comandosContainer.style.display = "block"; // Reabre comandos
                    toggleComandosBtn.style.display = "none"; // Esconde o botão "Comandos"
                });

                // Mantém o container sempre aberto ao carregar a página
                comandosContainer.style.display = "block";
                toggleComandosBtn.style.display = "none";
            });



            // Função para atualizar exibição de página
            function updatePageDisplay() {
                currentPageDisplay.textContent = currentPage;
                counter = 1; // Zera o contador ao trocar de página
                renderPage(currentPage);
            }




            document.addEventListener('DOMContentLoaded', () => {
                // Quando o DOM estiver totalmente carregado, executa este código
                const isPdfUploaded = document.body.dataset.pdfUploaded === 'true';
                // Verifica se o atributo data-pdf-uploaded no body é 'true', indicando que um PDF foi enviado

                if (isPdfUploaded) {
                    const modalComandos = document.getElementById('upload-modal');
                    // Obtém o elemento com id 'upload-modal', que representa o modal de comandos ou instruções

                    if (modalComandos) {
                        modalComandos.classList.remove('hidden');
                        // Remove a classe 'hidden' do modal, tornando-o visível ao usuário

                        console.log("Modal de comandos aberto automaticamente após upload.");
                        // Exibe uma mensagem no console indicando que o modal foi aberto automaticamente após o upload do PDF
                    }
                }
            });


            document.getElementById('remove-all-button').addEventListener('click', function() {
                if (pageCircles[currentPage]) {
                    // Remove todos os círculos do DOM
                    document.querySelectorAll(`.circle[data-page="${currentPage}"]`).forEach(circle => circle.remove());

                    // Limpa a lista de círculos da página atual
                    pageCircles[currentPage] = [];
                    counter = 1; // Reseta o contador apenas para a página atual
                }
            });



            document.getElementById('choose-file-btn').addEventListener('click', function() {
                // Quando o botão com id 'choose-file-btn' é clicado,
                // simula o clique no input de arquivo 'pdf-input'.
                // Isso abre a janela para o usuário selecionar um arquivo PDF.
                document.getElementById('pdf-input').click();
            });


            document.getElementById('pdf-input').addEventListener('change', function() {
                // Limpa os círculos ao carregar um novo arquivo
                circles.forEach(circle => circle.remove());
                circles = []; // Esvazia o array de círculos
                counter = 1; // Reinicia o contador de círculos

                document.getElementById('pdf-upload-form').submit();
            });

            function updatePageDisplay() {
                counter = 1; // Reinicia o contador ao trocar de página
                renderPage(currentPage); // Renderiza a nova página
                renderPageNumbers(); // Atualiza os números de página
            }


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


                pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                    pdfDoc = pdf;
                    renderPage(currentPage); // Renderiza a primeira página
                    renderPageNumbers(); // Renderiza os botões de página
                }).catch(function(error) {
                    console.error('Erro ao carregar o PDF:', error);
                });

                // Função para renderizar a página do PDF
                function renderPage(pageNum) {
                    pdfDoc.getPage(pageNum).then(function(page) {
                        const canvas = document.getElementById('pdf-canvas');
                        const context = canvas.getContext('2d');
                        const viewport = page.getViewport({
                            scale
                        });

                        // Ajusta o tamanho do canvas
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        // Renderiza a página no canvas
                        page.render({
                            canvasContext: context,
                            viewport,
                        }).promise.then(() => {
                            // Remove círculos antigos da página
                            circles.forEach(circle => circle.remove());
                            circles = [];

                            // Restaura círculos salvos
                            if (pageCircles[pageNum]) {
                                pageCircles[pageNum].forEach(circleData => {
                                    const circle = createCircleElement(circleData.text, circleData.x, circleData.y, pageNum);
                                    document.getElementById('pdf-container').appendChild(circle);
                                    circles.push(circle);
                                });
                            }
                        });
                    });
                }

                // Renderiza os números das páginas
                function renderPageNumbers() {
                    pageNumbersContainer.innerHTML = ""; // Limpa botões anteriores

                    for (let i = currentBlockStart; i < currentBlockStart + pagesPerBlock && i <= pdfDoc.numPages; i++) {
                        const pageBtn = document.createElement("button");

                        // Aplicando classes do Tailwind
                        pageBtn.textContent = i;
                        pageBtn.className = `
            page-btn px-4 py-2 rounded-md text-sm font-medium transition
            ${i === currentPage ? "bg-[#004BAD] text-white" : "bg-gray-200 text-gray-800 hover:bg-gray-300"}
        `;

                        // Evento de clique para renderizar a página
                        pageBtn.addEventListener("click", () => {
                            currentPage = i; // Atualiza a página atual
                            updatePageDisplay(); // Renderiza a nova página
                        });

                        pageNumbersContainer.appendChild(pageBtn); // Adiciona botão ao seletor
                    }
                }



                // Destaca o botão atual
                function highlightActiveButton() {
                    document.querySelectorAll('.page-btn').forEach(btn => {
                        const pageNumber = parseInt(btn.textContent, 10);

                        // Adiciona ou remove classes com Tailwind
                        if (pageNumber === currentPage) {
                            btn.classList.add("bg-[#004BAD]", "text-white");
                            btn.classList.remove("bg-gray-200", "text-gray-800", "hover:bg-gray-300");
                        } else {
                            btn.classList.remove("bg-[#004BAD]", "text-white");
                            btn.classList.add("bg-gray-200", "text-gray-800", "hover:bg-gray-300");
                        }
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
                let zoomScale = 1.0; // Zoom inicial
                let zoomTimeout = null; // Controlador de timeout

                pdfContainer.addEventListener('wheel', function(e) {
                    // Verifica se o alvo do evento é um círculo
                    if (e.target.classList.contains('circle')) {
                        return; // Ignora a rolagem sobre os círculos
                    }

                    e.preventDefault(); // Evita a rolagem padrão

                    // Se já houver um timeout definido, cancela o anterior
                    if (zoomTimeout) clearTimeout(zoomTimeout);

                    // Controla o zoom após parar de rolar
                    zoomTimeout = setTimeout(() => {
                        if (e.deltaY < 0 && zoomScale < 5.0) {
                            zoomScale = Math.min(zoomScale + 0.1, 5.0); // Aumenta o zoom
                        } else if (e.deltaY > 0 && zoomScale > 0.5) {
                            zoomScale = Math.max(zoomScale - 0.5, 1.5); // Diminui o zoom
                        }

                        console.log(`Zoom Atual: ${zoomScale.toFixed(1)}`);
                        renderPage(currentPage); // Renderiza novamente com o novo zoom
                    }, 200); // Define um atraso de 200 ms
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

                document.getElementById('remove-keep-numbering').addEventListener('click', function() {
                    if (targetCircle) {
                        removeCircle(targetCircle, false); // Remove e mantém numeração
                        document.getElementById('context-menu').classList.add('hidden');
                    }
                });

                document.getElementById('refactorButton').addEventListener('click', function() {
                    if (targetCircle) {
                        removeCircle(targetCircle, true); // Remove e reordena números
                        document.getElementById('context-menu').classList.add('hidden');
                    }
                });


                function addCircle(event, container) {
                    let rect = document.getElementById('pdf-canvas').getBoundingClientRect();
                    let x = (event.clientX - rect.left) / scale;
                    let y = (event.clientY - rect.top) / scale;

                    // Verifica se a nova posição sobrepõe algum círculo
                    if (isOverlapping(x, y)) return;

                    // Cria e configura o círculo
                    const circle = createCircleElement(counter++, x, y, currentPage);
                    container.appendChild(circle);
                    circles.push(circle);

                    // Salva o círculo na estrutura por página
                    if (!pageCircles[currentPage]) {
                        pageCircles[currentPage] = [];
                    }
                    pageCircles[currentPage].push({
                        text: circle.textContent,
                        x: circle.dataset.x,
                        y: circle.dataset.y
                    });

                    makeDraggable(circle);
                }

                function createCircleElement(text, x, y, page) {
                    const circle = document.createElement('div');
                    circle.className = 'circle';
                    circle.textContent = text;
                    circle.dataset.x = x;
                    circle.dataset.y = y;
                    circle.dataset.page = page;

                    updateCircleStyles(circle);
                    return circle;
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
                    const pageNum = parseInt(circle.dataset.page);
                    const x = parseFloat(circle.dataset.x);
                    const y = parseFloat(circle.dataset.y);

                    // Remove do array de círculos da página
                    pageCircles[pageNum] = pageCircles[pageNum].filter(
                        c => !(c.x == x && c.y == y && c.text === circle.textContent)
                    );

                    // Remove do DOM
                    circle.remove();

                    if (refactorNumbers) {
                        // Reordena números restantes
                        pageCircles[pageNum].forEach((circleData, i) => {
                            const correspondingCircle = document.querySelector(
                                `.circle[data-page="${pageNum}"][data-x="${circleData.x}"][data-y="${circleData.y}"]`
                            );
                            if (correspondingCircle) {
                                correspondingCircle.textContent = i + 1;
                                circleData.text = (i + 1).toString();
                            }
                        });

                        // Atualiza o contador
                        counter = pageCircles[pageNum].length + 1;
                    }
                }



                // Função para atualizar posições dos círculos
                function updateCirclePositions() {
                    circles.forEach(circle => updateCircleStyles(circle));
                }

                function adjustCircleSizeForScale(scale) {
                    return 10 * scale;
                }


                // Função para verificar se a nova posição do círculo sobrepõe outro
                function isOverlapping(x, y) {
                    const overlapThreshold = 20; // Distância mínima para considerar sobreposição
                    if (pageCircles[currentPage]) {
                        for (let circle of pageCircles[currentPage]) {
                            const distance = Math.sqrt(Math.pow(x - circle.x, 2) + Math.pow(y - circle.y, 2));
                            if (distance < overlapThreshold) {
                                return true; // Indica sobreposição
                            }
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

                        // Itera sobre todas as páginas e adiciona as marcações
                        for (const [pageNum, circles] of Object.entries(pageCircles)) {
                            const selectedPage = pages[pageNum - 1];

                            circles.forEach(circle => {
                                const x = parseFloat(circle.x);
                                const y = parseFloat(circle.y);
                                const text = circle.text;

                                // Desenha o círculo
                                selectedPage.drawEllipse({
                                    x,
                                    y: selectedPage.getHeight() - y,
                                    xScale: 15 * circleScale,
                                    yScale: 15 * circleScale,
                                    color: PDFLib.rgb(1, 1, 1),
                                    borderColor: PDFLib.rgb(0 / 255, 75 / 255, 173 / 255),
                                    borderWidth: 2,
                                });

                                // Adiciona o número no círculo
                                const fontSize = 16 * circleScale;
                                const textOffsetX = text.length === 1 ? fontSize * 0.3 : fontSize * 0.6;
                                const textOffsetY = fontSize * 0.35;

                                selectedPage.drawText(text, {
                                    x: x - textOffsetX,
                                    y: selectedPage.getHeight() - y - textOffsetY,
                                    size: fontSize,
                                    font,
                                    color: PDFLib.rgb(0 / 255, 75 / 255, 173 / 255),
                                });
                            });

                            // Adiciona o link no canto inferior direito
                            const linkFontSize = 12;
                            const linkText = "WWW.TAGPDF.COM.BR";
                            const linkWidth = font.widthOfTextAtSize(linkText, linkFontSize);
                            const pageWidth = selectedPage.getWidth();
                            const margin = 10;

                            selectedPage.drawText(linkText, {
                                x: pageWidth - linkWidth - margin,
                                y: margin,
                                size: linkFontSize,
                                font,
                                color: PDFLib.rgb(0 / 255, 75 / 255, 173 / 255),
                            });
                        }

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



            document.getElementById("prev-page-btn").addEventListener("click", () => {
                if (currentBlockStart > 1) {
                    currentBlockStart -= pagesPerBlock;
                    renderPageNumbers();
                } else {
                    alert("Você já está na primeira página.");
                }
            });

            document.getElementById("next-page-btn").addEventListener("click", () => {
                if (currentBlockStart + pagesPerBlock <= pdfDoc.numPages) {
                    currentBlockStart += pagesPerBlock;
                    renderPageNumbers();
                } else {
                    alert("Você já está na última página.");
                }
            });
        </script>

    </body>
</x-app-layout>