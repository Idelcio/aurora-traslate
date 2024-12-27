import './bootstrap';
import { PDFDocument } from 'pdf-lib';
import Alpine from 'alpinejs';
import * as pdfjsLib from 'pdfjs-dist';
import * as fontkit from 'fontkit';

// Configura o worker para o pdfjs
pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

window.Alpine = Alpine;
Alpine.start();

// Exporta o fontkit globalmente
window.fontkit = fontkit; // Define no escopo global
export { fontkit }; // Exporta explicitamente



// zoom do PDF





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


});


// modal comandos

// Gerenciamento do Modal de Comandos
document.addEventListener('DOMContentLoaded', () => {
    const modalComandos = document.getElementById('upload-modal');
    const openModalBtnComandos = document.getElementById('open-upload-modal');
    const closeModalBtnComandos = document.getElementById('close-upload-modal');

    if (openModalBtnComandos && closeModalBtnComandos && modalComandos) {
        // Abrir o modal de comandos
        openModalBtnComandos.addEventListener('click', () => {
            modalComandos.classList.remove('hidden'); // Mostra o modal
        });

        // Fechar o modal de comandos
        closeModalBtnComandos.addEventListener('click', () => {
            modalComandos.classList.add('hidden'); // Esconde o modal
        });

        // Fechar o modal de comandos ao clicar fora do conteúdo
        modalComandos.addEventListener('click', (event) => {
            if (event.target === modalComandos) {
                modalComandos.classList.add('hidden');
            }
        });
    }
});

// modal selecionar pagina





// Fechar ao clicar fora do modal
window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});





// remover círculos

// Função para remover todos os círculos e reiniciar contador
export function removeAllCircles() {
    const circles = document.querySelectorAll('.circle');
    circles.forEach(circle => circle.remove());

    // Reinicia o contador global
    window.counter = 1;
    window.circles = []; // Limpa a lista de círculos
    console.log("Círculos removidos e contador reiniciado.");
}

// Função para excluir um círculo
export function removeCircle(targetCircle, refactorNumbers = false) {
    const index = window.circles.indexOf(targetCircle);

    if (index !== -1) {
        window.circles.splice(index, 1); // Remove da lista global
        targetCircle.remove(); // Remove do DOM

        // Refatora números se necessário
        if (refactorNumbers) {
            window.circles.forEach((circle, i) => {
                circle.textContent = i + 1;
            });
            window.counter = window.circles.length + 1;
        }
    }
}

// Configura Eventos Após Carregar o DOM
document.addEventListener('DOMContentLoaded', () => {
    // Limpar Todos os Círculos
    const removeAllButton = document.getElementById('remove-all-button');
    if (removeAllButton) {
        removeAllButton.addEventListener('click', removeAllCircles);
    }

    // Excluir Círculo Com ou Sem Refatoração
    const refactorButton = document.getElementById('refactorButton');
    const removeKeepButton = document.getElementById('remove-keep-numbering');

    if (refactorButton) {
        refactorButton.addEventListener('click', () => {
            if (window.targetCircle) {
                removeCircle(window.targetCircle, true);
                document.getElementById('context-menu').classList.add('hidden');
            }
        });
    }

    if (removeKeepButton) {
        removeKeepButton.addEventListener('click', () => {
            if (window.targetCircle) {
                removeCircle(window.targetCircle, false);
                document.getElementById('context-menu').classList.add('hidden');
            }
        });
    }
});

// carregar página

// Função para carregar uma página específica
export function loadPage(pdfDoc, currentPage, renderPage) {
    const loadPageBtn = document.getElementById('load-page-btn');
    const pageInput = document.getElementById('page-selector');

    if (loadPageBtn && pageInput) {
        loadPageBtn.addEventListener('click', () => {
            const selectedPage = parseInt(pageInput.value, 10);

            if (selectedPage >= 1 && selectedPage <= pdfDoc.numPages) {
                currentPage.value = selectedPage; // Atualiza o número da página
                renderPage(selectedPage); // Renderiza a página
            } else {
                alert(`Por favor, insira um número de página válido (1 a ${pdfDoc.numPages}).`);
            }
        });
    }
}

// Configuração após carregar o DOM
document.addEventListener('DOMContentLoaded', () => {
    const pdfDoc = window.pdfDoc;
    const currentPage = { value: 1 };

    if (pdfDoc) {
        loadPage(pdfDoc, currentPage, window.renderPage);
    }
});

