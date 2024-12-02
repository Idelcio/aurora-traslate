import './bootstrap';

import { PDFDocument } from 'pdf-lib';

import Alpine from 'alpinejs';

import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

window.Alpine = Alpine;

Alpine.start();


