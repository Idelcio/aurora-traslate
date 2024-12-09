import './bootstrap';

import { PDFDocument } from 'pdf-lib';

import Alpine from 'alpinejs';

import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

window.Alpine = Alpine;

Alpine.start();


// zoom do PDF


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


// modais

