import './bootstrap';

import { PDFDocument } from 'pdf-lib';

import Alpine from 'alpinejs';

import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

// Exemplo de como usar o PDF.js
pdfjsLib.getDocument('caminho/para/o/arquivo.pdf').promise.then(function(pdf) {
    console.log('PDF carregado');
    // Renderizar as páginas do PDF
});


window.Alpine = Alpine;

Alpine.start();
