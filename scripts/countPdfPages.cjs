#!/usr/bin/env node

/**
 * Script rápido para contar páginas de um PDF
 */

const fs = require('fs').promises;
const { PDFParse } = require('pdf-parse');

/**
 * Conta o número de páginas de um PDF
 * @param {string} pdfPath - Caminho para o arquivo PDF
 * @returns {Promise<number>} Número de páginas
 */
async function countPages(pdfPath) {
    try {
        const dataBuffer = await fs.readFile(pdfPath);

        // pdf-parse v2: instancia e passa o buffer
        const parser = new PDFParse({ data: dataBuffer });

        // Obtém as informações do PDF
        const info = await parser.getInfo();

        // Retorna o número de páginas (campo 'total' na v2)
        return info.total || 0;
    } catch (error) {
        throw new Error(`Erro ao contar páginas: ${error.message}`);
    }
}

// Execução via linha de comando
async function main() {
    const args = process.argv.slice(2);

    if (args.length < 1) {
        console.error('Uso: node countPdfPages.js <caminho-pdf>');
        process.exit(1);
    }

    const pdfPath = args[0];

    try {
        const numPages = await countPages(pdfPath);

        // Retorna apenas o número (fácil de parsear no PHP)
        console.log(numPages);
        process.exit(0);
    } catch (error) {
        console.error('ERRO:', error.message);
        process.exit(1);
    }
}

if (require.main === module) {
    main();
}

module.exports = { countPages };
