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
                {{ __('pdf_upload.upload_pdf') }}
            </button>

            <button id="saveButton" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD] font-normal">
                {{ __('pdf_upload.download') }}
            </button>

            <button id="refactorButton" class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-[#004BAD] font-normal">
                {{ __('pdf_upload.refactor') }}
            </button>

            <!-- Navegação de Página -->
            <div id="page-selector" class="flex items-center justify-center space-x-4">
                <h3 class="text-lg font-normal text-gray-800">{{ __('pdf_upload.page') }}</h3>
                <button id="prev-page-btn" class="px-2 py-1 bg-gray-300 text-gray-800 text-sm rounded-md hover:bg-gray-400 font-normal">&lt;</button>

                <div id="page-numbers" class="flex space-x-2"></div>

                <button id="next-page-btn" class="px-2 py-1 bg-gray-300 text-gray-800 text-sm rounded-md hover:bg-gray-400 font-normal">&gt;</button>
            </div>

        </form>


        <!-- Terceira Div: Botão Comandos no Canto Direito -->
        <div class="ml-auto">
            <button id="toggle-comandos" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-[#004BAD] hidden font-normal">
                {{ __('pdf_upload.commands') }}
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
        <div id="comandos-container" class="w-[320px] h-auto ml-auto bg-white shadow-md border-l-2 rounded-lg">
            <div class="bg-[#333333] text-white font-bold text-[18px] p-2 flex items-center justify-between rounded-t-lg">
                <span>{{ __('pdf_upload.commands') }}</span>
                <button id="close-comandos" class="text-white text-xl rounded-full w-8 h-8 flex items-center justify-center hover:bg-white hover:text-black transition">
                    &times;
                </button>
            </div>

            <!-- Conteúdo dos Comandos -->
            <div class="ml-auto space-y-2 p-2">
                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_tamanho_tag_(1).png') }}" alt="Definir Tamanho" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">{{ __('pdf_upload.tag_size') }}</h3>
                        <p class="text-gray-600 text-[12px]">{{ __('pdf_upload.scroll_tag_size') }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_zom.png') }}" alt="Zoom" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">{{ __('pdf_upload.zoom') }}</h3>
                        <p class="text-gray-600 text-[12px]">{{ __('pdf_upload.scroll_zoom') }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_mover1.png') }}" alt="Mover PDF" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">{{ __('pdf_upload.move_pdf') }}</h3>
                        <p class="text-gray-600 text-[12px]">{{ __('pdf_upload.right_click_drag') }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_mouse.png') }}" alt="Adicionar Tag" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">{{ __('pdf_upload.add_tag') }}</h3>
                        <p class="text-gray-600 text-[12px]">{{ __('pdf_upload.left_click_tag') }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_direito_mouse.png') }}" alt="Excluir Tag" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">{{ __('pdf_upload.delete_tag') }}</h3>
                        <p class="text-gray-600 text-[12px]">{{ __('pdf_upload.right_click_delete') }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-2">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_e_segure_mouse_(1).png') }}" alt="Mover Tag" class="w-8 h-8">
                    <div>
                        <h3 class="font-bold text-[14px] text-gray-800">{{ __('pdf_upload.move_tag') }}</h3>
                        <p class="text-gray-600 text-[12px]">{{ __('pdf_upload.right_click_hold_tag') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Overlay de carregamento -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
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
            window.pdfDoc = null; // Documento PDF carregado
            window.currentPage = 1; // Página atual exibida
            let scale = 1.4; // Nível de zoom inicial
            let circleScale = 1; // Escala inicial para círculos
            let circles = []; // Array para círculos criados
            let counter = 1; // Contador de círculos
            let targetCircle = null; // Círculo atualmente selecionado
            let pageCircles = {}; // Marcações de círculos por página

            const openModalBtn = document.getElementById('open-modal-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const modal = document.getElementById('page-selector-modal');
            const loadPageBtn = document.getElementById('load-page-btn');

            let currentBlockStart = 1;
            const pagesPerBlock = 5;
            const pageNumbersContainer = document.getElementById("page-numbers");
            const currentPageDisplay = document.getElementById('current-page');

            const minZoom = 2.5; // Zoom mínimo permitido
            const maxZoom = 5.0; // Zoom máximo permitido

            const locale = "{{ app()->getLocale() }}";

            function showLoadingOverlay() {
                const overlay = document.getElementById('loading-overlay');
                overlay.style.display = 'flex'; // Mostra o overlay
            }

            function hideLoadingOverlay() {
                const overlay = document.getElementById('loading-overlay');
                overlay.style.display = 'none'; // Oculta o overlay
            }


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


            document.addEventListener("DOMContentLoaded", () => {
                const pdfContainer = document.getElementById("pdf-container");
                let isMoving = false;
                let startX, startY, scrollLeft, scrollTop;

                // Apenas Botão Esquerdo: Criar Círculo
                pdfContainer.addEventListener("mousedown", (e) => {
                    if (e.button === 0) { // Verifica se é o botão esquerdo do mouse
                        if (!e.target.classList.contains("circle")) {
                            addCircle(e, pdfContainer); // Adiciona um círculo
                        } else {
                            startDraggingCircle(e.target, e); // Inicia arrasto do círculo
                        }
                    } else if (e.button === 2) { // Verifica se é o botão direito do mouse
                        e.preventDefault(); // Impede o menu de contexto padrão
                        isMoving = true;

                        // Salva posição inicial do mouse e do scroll
                        startX = e.clientX;
                        startY = e.clientY;
                        scrollLeft = pdfContainer.scrollLeft;
                        scrollTop = pdfContainer.scrollTop;

                        pdfContainer.classList.add("grabbing");
                    }
                });

                // Move o PDF ao arrastar com botão direito
                pdfContainer.addEventListener("mousemove", (e) => {
                    if (!isMoving) return;

                    // Calcula deslocamento
                    const xDiff = e.clientX - startX;
                    const yDiff = e.clientY - startY;

                    // Atualiza o scroll
                    pdfContainer.scrollLeft = scrollLeft - xDiff;
                    pdfContainer.scrollTop = scrollTop - yDiff;
                });

                // Para movimento ao soltar o botão direito
                document.addEventListener("mouseup", () => {
                    if (isMoving) {
                        isMoving = false;
                        pdfContainer.classList.remove("grabbing");
                    }
                });

                // Impede o menu de contexto padrão
                pdfContainer.addEventListener("contextmenu", (e) => e.preventDefault());
            });


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

            let isDownloaded = false; // Variável para controlar se o PDF foi baixado

            // Inicialização com placeholder temporário
            const translations = {
                unsavedChanges: "Carregando mensagem..." // Mensagem temporária
            };

            // Faz o fetch das traduções
            fetch('/translations/pdf_upload')
                .then(response => response.json())
                .then(data => {
                    translations.unsavedChanges = data.unsaved_changes; // Substitui pelo texto traduzido
                })
                .catch(() => {
                    translations.unsavedChanges = "Erro ao carregar a tradução. Verifique sua conexão.";
                });

            document.getElementById('choose-file-btn').addEventListener('click', function() {
                if (!isDownloaded && Object.keys(pageCircles).length > 0) {
                    const confirmUpload = confirm(translations.unsavedChanges); // Exibe a mensagem traduzida
                    if (!confirmUpload) {
                        return; // Cancela se o usuário não confirmar
                    }
                }
                document.getElementById('pdf-input').click(); // Simula clique no input de arquivo
            });

            document.getElementById('pdf-input').addEventListener('change', function() {
                // Mostra o overlay de carregamento
                showLoadingOverlay();

                // Limpa os círculos ao carregar um novo arquivo
                circles.forEach(circle => circle.remove());
                circles = []; // Esvazia o array de círculos
                pageCircles = {}; // Reinicia as marcações por página
                counter = 1; // Reinicia o contador de círculos
                isDownloaded = false; // Reinicia o estado de download para o novo arquivo

                // Submete o formulário de upload
                setTimeout(() => {
                    document.getElementById('pdf-upload-form').submit();
                }, 500); // Adiciona um pequeno delay para garantir que o overlay seja exibido
            });


            function updatePageDisplay() {
                // Reinicia o contador ao trocar de página
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
                let currentRenderTask = null;

                function renderPage(pageNum) {
                    pdfDoc.getPage(pageNum).then(function(page) {
                        const canvas = document.getElementById('pdf-canvas');
                        const context = canvas.getContext('2d');
                        const viewport = page.getViewport({
                            scale
                        });

                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        // Cancela a renderização anterior, se existir
                        if (currentRenderTask) {
                            currentRenderTask.cancel();
                        }

                        // Inicia a renderização da página
                        currentRenderTask = page.render({
                            canvasContext: context,
                            viewport: viewport,
                        });

                        currentRenderTask.promise
                            .then(() => {
                                console.log(`Página ${pageNum} renderizada com sucesso.`);

                                // Remove círculos antigos da página atual
                                document.querySelectorAll('.circle').forEach(circle => circle.remove());
                                circles = []; // Limpa a lista de círculos

                                // Restaura os círculos salvos para a página atual
                                if (pageCircles[pageNum]) {
                                    pageCircles[pageNum].forEach(circleData => {
                                        const circle = createCircleElement(
                                            circleData.text,
                                            parseFloat(circleData.x),
                                            parseFloat(circleData.y),
                                            pageNum
                                        );
                                        document.getElementById('pdf-container').appendChild(circle);
                                        circles.push(circle);
                                        updateCircleStyles(circle); // Atualiza estilos
                                        makeDraggable(circle); // Torna arrastável
                                    });
                                }
                            })
                            .catch(error => {
                                if (error.name !== 'RenderingCancelledException') {
                                    console.error('Erro ao renderizar página:', error);
                                }
                            });
                    }).catch(error => {
                        console.error('Erro ao obter página:', error);
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
                let isLongPress = false;
                let pressTimer;

                // Inicia o temporizador ao pressionar o botão esquerdo
                pdfContainer.addEventListener('mousedown', function(event) {
                    if (event.button !== 0) return; // Ignora qualquer botão que não seja o esquerdo

                    // Configura o temporizador para clique longo
                    pressTimer = setTimeout(function() {
                        isLongPress = true; // Clique longo detectado
                    }, 500);
                });

                // Adiciona círculo apenas com o botão esquerdo
                pdfContainer.addEventListener('mouseup', function(event) {
                    if (event.button !== 0) return; // Ignora se não for botão esquerdo

                    clearTimeout(pressTimer); // Limpa o temporizador

                    if (!isLongPress) {
                        // Se não for um clique longo, adiciona o círculo
                        addCircle(event, pdfContainer);
                    }

                    isLongPress = false; // Reseta o status para o próximo clique
                });

                // Cancela temporizador ao sair do contêiner
                pdfContainer.addEventListener('mouseleave', function() {
                    clearTimeout(pressTimer); // Cancela o temporizador
                    isLongPress = false; // Reseta o status
                });



                pdfContainer.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    let target = e.target;
                    if (target.classList.contains('circle')) {
                        targetCircle = target;
                        showContextMenu(e);
                    }
                });
                let zoomScale = 1.5; // Zoom inicial
                let zoomTimeout = null; // Controlador de timeout

                pdfContainer.addEventListener('wheel', function(e) {
                    e.preventDefault(); // Evita o comportamento padrão de rolagem

                    const rect = pdfContainer.getBoundingClientRect();

                    // Posição do mouse em relação ao container
                    const mouseX = e.clientX - rect.left;
                    const mouseY = e.clientY - rect.top;

                    // Posição de scroll atual
                    const scrollLeftBefore = pdfContainer.scrollLeft;
                    const scrollTopBefore = pdfContainer.scrollTop;

                    // Proporção de deslocamento com base no zoom atual
                    const xRatio = (mouseX + scrollLeftBefore) / pdfContainer.scrollWidth;
                    const yRatio = (mouseY + scrollTopBefore) / pdfContainer.scrollHeight;

                    // Calcula o novo zoom
                    const zoomDelta = e.deltaY < 0 ? 0.1 : -0.1;
                    const newZoomScale = Math.min(maxZoom, Math.max(minZoom, zoomScale + zoomDelta));

                    if (newZoomScale === zoomScale) return; // Se o zoom não mudou, não atualiza

                    // Atualiza o zoom
                    zoomScale = newZoomScale;

                    // Renderiza a página com o novo zoom
                    renderPage(currentPage);

                    // Aguarda renderização para aplicar scroll ajustado
                    setTimeout(() => {
                        const newScrollWidth = pdfContainer.scrollWidth;
                        const newScrollHeight = pdfContainer.scrollHeight;

                        // Recalcula o scroll para manter a centralização
                        pdfContainer.scrollLeft = xRatio * newScrollWidth - mouseX;
                        pdfContainer.scrollTop = yRatio * newScrollHeight - mouseY;
                    }, 0); // Tempo mínimo para garantir a atualização do layout
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

                // Exclusão simples ao clicar na opção
                document.getElementById('remove-keep-numbering').addEventListener('click', function() {
                    if (targetCircle) {
                        removeCircle(targetCircle); // Remove sem reordenar
                        document.getElementById('context-menu').classList.add('hidden');
                    }
                });

                document.getElementById('refactorButton').addEventListener('click', function() {
                    refactorCircles(); // Refatora todas as páginas em sequência contínua
                });


                function addCircle(event, container) {
                    let rect = document.getElementById('pdf-canvas').getBoundingClientRect();

                    // Calcula a posição relativa do clique
                    let x = (event.clientX - rect.left) / scale;
                    let y = (event.clientY - rect.top) / scale;

                    const margin = 20;

                    // Verifica se o clique está dentro dos limites
                    if (x < margin / scale || y < margin / scale || x > rect.width / scale - margin || y > rect.height / scale - margin) {
                        console.log("Fora dos limites permitidos. Círculo não adicionado.");
                        return;
                    }

                    // Verifica o próximo número na sequência
                    const lastNumber = Math.max(
                        ...Object.values(pageCircles).flat().map(circle => parseInt(circle.text)),
                        0
                    );
                    const nextNumber = lastNumber + 1;

                    // Corrige a posição do círculo para centralizar
                    const circleSize = adjustCircleSizeForScale(scale * circleScale);
                    x = x - circleSize / 2 / scale;
                    y = y - circleSize / 2 / scale;

                    // Verifica se está sobrepondo outros círculos
                    if (isOverlapping(x, y, margin)) {
                        console.log("Sobreposição detectada. Círculo não adicionado.");
                        return;
                    }

                    const timestamp = Date.now();
                    const circle = createCircleElement(nextNumber, x, y, currentPage, timestamp);
                    container.appendChild(circle);

                    // Adiciona o círculo ao array de marcações
                    if (!pageCircles[currentPage]) {
                        pageCircles[currentPage] = [];
                    }

                    pageCircles[currentPage].push({
                        text: circle.textContent,
                        x: parseFloat(circle.dataset.x).toFixed(6),
                        y: parseFloat(circle.dataset.y).toFixed(6),
                        createdAt: timestamp
                    });

                    makeDraggable(circle); // Torna o círculo arrastável
                }


                function createCircleElement(text, x, y, page, timestamp) {
                    const circle = document.createElement('div');
                    circle.className = 'circle';
                    circle.textContent = text;

                    // Armazena as coordenadas com precisão
                    circle.dataset.x = x.toFixed(6); // 6 casas decimais para mais precisão
                    circle.dataset.y = y.toFixed(6);
                    circle.dataset.page = page;
                    circle.dataset.timestamp = timestamp; // Adiciona o timestamp como identificador único

                    updateCircleStyles(circle); // Aplica os estilos para posicionar
                    return circle; // Retorna o elemento criado
                }




                function updateCircleSizes() {
                    circles.forEach(circle => updateCircleStyles(circle));
                }

                function updateCircleStyles(circle) {
                    let x = parseFloat(circle.dataset.x); // Posição original X
                    let y = parseFloat(circle.dataset.y); // Posição original Y

                    // Multiplica o tamanho pelo zoom atual
                    let size = adjustCircleSizeForScale(circleScale) * scale; // Ajusta o tamanho com o zoom atual

                    // Tamanho base para o círculo
                    const baseSize = 40; // Tamanho base do círculo em pixels
                    const zoomedSize = baseSize * scale * circleScale; // Tamanho do círculo ajustado pelo zoom

                    // Ajusta o tamanho do círculo
                    circle.style.width = `${zoomedSize}px`;
                    circle.style.height = `${zoomedSize}px`;

                    // Ajusta a posição para manter a proporção no zoom
                    circle.style.left = `${(x * scale - zoomedSize / 2).toFixed(2)}px`; // Ajusta X
                    circle.style.top = `${(y * scale - zoomedSize / 2).toFixed(2)}px`; // Ajusta Y

                    // Ajusta a fonte proporcionalmente ao tamanho do círculo
                    let fontSize = 20 * scale * circleScale; // Tamanho proporcional ao zoom
                    circle.style.fontSize = `${fontSize}px`; // Tamanho da fonte
                    circle.style.lineHeight = `${zoomedSize}px`; // Centraliza verticalmente o número
                    circle.style.borderRadius = "50%"; // Garante que o círculo permaneça circular
                }




                function removeCircle(circle) {
                    const pageNum = parseInt(circle.dataset.page);
                    const x = parseFloat(circle.dataset.x);
                    const y = parseFloat(circle.dataset.y);
                    const text = circle.textContent;

                    // Remove o círculo do DOM
                    circle.remove();

                    // Remove do array `pageCircles`
                    pageCircles[pageNum] = pageCircles[pageNum].filter(
                        c => !(c.x == x && c.y == y && c.text === text)
                    );

                    console.log(`Círculo removido na página ${pageNum}: X=${x}, Y=${y}`);
                }


                function refactorCircles() {
                    if (Object.keys(pageCircles).length === 0) {
                        console.log("Nenhum círculo para refatorar.");
                        return;
                    }

                    let globalCounter = 1; // Sempre começa a partir de 1

                    // Cria um array de todas as marcações em todas as páginas
                    let allCircles = [];

                    // Itera pelas páginas em ordem crescente e adiciona os círculos ao array
                    for (const pageNum of Object.keys(pageCircles).sort((a, b) => a - b)) {
                        pageCircles[pageNum].forEach(circleData => {
                            allCircles.push({
                                ...circleData,
                                pageNum: parseInt(pageNum)
                            });
                        });
                    }

                    // Ordena todas as marcações por página e depois por `createdAt`
                    allCircles.sort((a, b) => {
                        if (a.pageNum !== b.pageNum) {
                            return a.pageNum - b.pageNum;
                        }
                        return a.createdAt - b.createdAt;
                    });

                    // Reordena as marcações para manter a sequência contínua a partir de 1
                    allCircles.forEach(circle => {
                        circle.text = globalCounter.toString(); // Atualiza o número do círculo
                        globalCounter++; // Incrementa o contador global
                    });

                    // Atualiza a estrutura `pageCircles` para refletir as novas ordens
                    pageCircles = allCircles.reduce((acc, circle) => {
                        const {
                            pageNum,
                            ...data
                        } = circle;
                        if (!acc[pageNum]) {
                            acc[pageNum] = [];
                        }
                        acc[pageNum].push(data);
                        return acc;
                    }, {});

                    console.log("Refatoração concluída. Sequência numérica reiniciada a partir de 1.");

                    // Atualiza a página atual
                    renderPage(currentPage);
                }



                // Função para atualizar posições dos círculos
                function updateCirclePositions() {
                    circles.forEach(circle => updateCircleStyles(circle));
                }

                function adjustCircleSizeForScale(scale) {
                    return 10 * scale;
                }


                // Função para verificar se a nova posição do círculo sobrepõe outro
                function isOverlapping(x, y, margin = 10) {
                    if (pageCircles[currentPage]) {
                        for (let circle of pageCircles[currentPage]) {
                            const distance = Math.sqrt(Math.pow(x - circle.x, 2) + Math.pow(y - circle.y, 2));
                            if (distance < margin / scale) {
                                return true; // Sobreposição detectada
                            }
                        }
                    }
                    return false; // Não há sobreposição
                }



                // Função para tornar os círculos arrastáveis
                function makeDraggable(circle) {
                    let isDragging = false;
                    let offsetX, offsetY;

                    circle.addEventListener('mousedown', function(e) {
                        if (e.button !== 0) return; // Certifica-se de que apenas o botão esquerdo ativa o drag
                        isDragging = true;
                        offsetX = e.clientX - parseFloat(circle.style.left); // Captura o deslocamento X
                        offsetY = e.clientY - parseFloat(circle.style.top); // Captura o deslocamento Y
                        circle.classList.add('dragging');
                    });

                    document.addEventListener('mousemove', function(e) {
                        if (isDragging) {
                            // Calcula as novas coordenadas
                            let x = (e.clientX - offsetX) / scale;
                            let y = (e.clientY - offsetY) / scale;

                            // Atualiza a posição do círculo visualmente
                            circle.style.left = `${x * scale}px`;
                            circle.style.top = `${y * scale}px`;

                            // Atualiza coordenadas reais no dataset do círculo
                            circle.dataset.x = x.toFixed(6); // Armazena com alta precisão
                            circle.dataset.y = y.toFixed(6);

                            // Atualiza a posição na estrutura de dados (pageCircles)
                            const pageNum = parseInt(circle.dataset.page, 10);
                            const index = pageCircles[pageNum].findIndex(c => c.text === circle.textContent);
                            if (index !== -1) {
                                pageCircles[pageNum][index].x = x.toFixed(6); // Alta precisão
                                pageCircles[pageNum][index].y = y.toFixed(6); // Alta precisão
                            }
                        }
                    });

                    document.addEventListener('mouseup', function() {
                        if (isDragging) {
                            isDragging = false;
                            circle.classList.remove('dragging');

                            // Após o soltar do mouse, certifique-se de que as coordenadas finais estão salvas
                            const pageNum = parseInt(circle.dataset.page, 10);
                            const x = parseFloat(circle.dataset.x).toFixed(6);
                            const y = parseFloat(circle.dataset.y).toFixed(6);

                            const index = pageCircles[pageNum].findIndex(c => c.text === circle.textContent);
                            if (index !== -1) {
                                pageCircles[pageNum][index].x = x;
                                pageCircles[pageNum][index].y = y;
                            }

                            console.log(`Círculo solto na página ${pageNum}: X=${x}, Y=${y}`);
                        }
                    });
                }


                document.getElementById('saveButton').addEventListener('click', async function() {
                    try {
                        // Mostra o overlay de carregamento
                        showLoadingOverlay();

                        console.log('Iniciando processo de salvamento do PDF...');

                        // Certifica-se de que o fontkit está disponível
                        if (typeof fontkit === 'undefined') {
                            console.error('Fontkit não está definido. Verifique a configuração no app.js.');
                            alert('Erro ao salvar o PDF: Fontkit não carregado.');
                            hideLoadingOverlay(); // Esconde o overlay em caso de erro
                            return;
                        }

                        // Carrega o PDF existente
                        const existingPdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());
                        const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes);

                        // Registra o Fontkit
                        pdfDoc.registerFontkit(fontkit);

                        // Carrega a fonte Montserrat-Regular
                        const montserratFontBytes = await fetch('/fonts/Montserrat-Regular.ttf').then(res => res.arrayBuffer());
                        const montserratFont = await pdfDoc.embedFont(montserratFontBytes);

                        const pages = pdfDoc.getPages();
                        console.log('PDF carregado para edição.');

                        // Define o idioma e o texto da marca d'água
                        const locale = "{{ app()->getLocale() }}"; // O idioma atual vindo do backend
                        let watermarkText;

                        if (locale === 'pt_BR') {
                            watermarkText = 'Criado por TagPDF';
                        } else if (locale === 'es') {
                            watermarkText = 'Creado por TagPDF';
                        } else {
                            watermarkText = 'Marked with TagPDF'; // Inglês como padrão
                        }

                        // Itera sobre todas as páginas e aplica as marcações
                        for (const [pageNum, circles] of Object.entries(pageCircles)) {
                            const selectedPage = pages[pageNum - 1];

                            circles.forEach(circle => {
                                // Calcula as posições com precisão definida (6 casas decimais, por exemplo)
                                const x = parseFloat(circle.x).toFixed(6);
                                const y = parseFloat(circle.y).toFixed(6);
                                const text = circle.text;

                                // Ajusta tamanho da fonte com base no número de caracteres
                                let fontSize = 16 * circleScale;
                                let textOffsetX = fontSize * 0.3;
                                let textOffsetY = fontSize * 0.35;

                                if (text.length === 2) {
                                    fontSize -= 1;
                                    textOffsetX += 3 * scale;
                                } else if (text.length === 3) {
                                    fontSize -= 3;
                                    textOffsetX += 4 * scale;
                                } else if (text.length >= 4) {
                                    fontSize -= 8;
                                    textOffsetX += 7 * scale;
                                    textOffsetY -= 2 * scale;
                                }

                                // Desenha o círculo
                                selectedPage.drawEllipse({
                                    x: parseFloat(x),
                                    y: selectedPage.getHeight() - parseFloat(y),
                                    xScale: 20 * circleScale,
                                    yScale: 20 * circleScale,
                                    color: PDFLib.rgb(1, 1, 1),
                                    borderColor: PDFLib.rgb(0, 75 / 255, 173 / 255),
                                    borderWidth: 2,
                                });

                                // Desenha o número dentro do círculo
                                selectedPage.drawText(text.toString(), {
                                    x: parseFloat(x) - textOffsetX,
                                    y: selectedPage.getHeight() - parseFloat(y) - textOffsetY,
                                    size: fontSize,
                                    font: montserratFont,
                                    color: PDFLib.rgb(0, 75 / 255, 173 / 255),
                                });
                            });

                            // Adiciona a marca d'água
                            const transparentLinkText = 'www.tagpdf.com.br'; // Texto "invisível" com o link
                            const padding = 10; // Espaçamento das bordas
                            const fontSizeWatermark = 12; // Tamanho da fonte da marca d'água

                            // Calcula largura e altura do texto
                            const textWidth = montserratFont.widthOfTextAtSize(watermarkText, fontSizeWatermark);

                            // Posição no canto inferior direito
                            const xPosition = selectedPage.getWidth() - textWidth - padding; // Direita
                            const yPosition = padding; // Inferior

                            // Desenha o texto visível
                            selectedPage.drawText(watermarkText, {
                                x: xPosition,
                                y: yPosition,
                                size: fontSizeWatermark,
                                font: montserratFont,
                                color: PDFLib.rgb(0, 75 / 255, 173 / 255), // Azul padrão
                                opacity: 0.8, // Transparência sutil
                            });

                            // Desenha o texto com o link invisível
                            selectedPage.drawText(transparentLinkText, {
                                x: xPosition,
                                y: yPosition,
                                size: fontSizeWatermark,
                                font: montserratFont,
                                color: PDFLib.rgb(1, 1, 1), // Cor branca (invisível em fundo branco)
                                opacity: 0.01, // Quase totalmente transparente
                            });
                        }

                        // Salva o documento
                        const pdfBytes = await pdfDoc.save();
                        const blob = new Blob([pdfBytes], {
                            type: 'application/pdf'
                        });
                        const url = URL.createObjectURL(blob);

                        // Gera o link de download e baixa o arquivo
                        const downloadLink = document.createElement('a');
                        downloadLink.href = url;
                        downloadLink.download = `${pdfFilename.replace('.pdf', '_boleado.pdf')}`;
                        downloadLink.click();

                        // Libera o URL gerado
                        URL.revokeObjectURL(url);
                        console.log('PDF salvo e baixado com sucesso.');

                        // Marca o PDF como baixado
                        isDownloaded = true;

                        // Esconde o overlay após o download
                        hideLoadingOverlay();
                    } catch (error) {
                        console.error('Erro ao salvar o PDF:', error);
                        alert('Erro ao salvar o PDF. Veja o console para mais detalhes.');
                        hideLoadingOverlay(); // Esconde o overlay em caso de erro
                    }
                });


            } else {
                console.error("Nenhum arquivo PDF foi carregado.");
            }



            // Requisição para obter as traduções do backend
            fetch('/translations/pdf_navigation')
                .then(response => response.json())
                .then(data => {
                    translations.firstPageAlert = data.first_page_alert; // Carrega a tradução correta
                    translations.lastPageAlert = data.last_page_alert; // Carrega a tradução correta
                })
                .catch(() => {
                    translations.firstPageAlert = "Erro ao carregar tradução para a primeira página.";
                    translations.lastPageAlert = "Erro ao carregar tradução para a última página.";
                });

            // Botão para página anterior
            document.getElementById("prev-page-btn").addEventListener("click", () => {
                if (currentBlockStart > 1) {
                    currentBlockStart -= pagesPerBlock;
                    renderPageNumbers();
                } else {
                    alert(translations.firstPageAlert); // Mostra a tradução da primeira página
                }
            });

            // Botão para próxima página
            document.getElementById("next-page-btn").addEventListener("click", () => {
                if (currentBlockStart + pagesPerBlock <= pdfDoc.numPages) {
                    currentBlockStart += pagesPerBlock;
                    renderPageNumbers();
                } else {
                    alert(translations.lastPageAlert); // Mostra a tradução da última página
                }
            });
        </script>

    </body>
</x-app-layout>