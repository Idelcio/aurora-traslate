#!/usr/bin/env node

/**
 * Script para extrair texto de PDFs mantendo informações de formatação
 * Preserva: posição, fonte, tamanho, cor, e estrutura
 */

const fs = require('fs').promises;

// Usa a API do pdfjs através de dynamic import
let pdfjsLib;

async function initPdfJs() {
    if (!pdfjsLib) {
        pdfjsLib = await import('pdfjs-dist/legacy/build/pdf.mjs');
        pdfjsLib.GlobalWorkerOptions.workerSrc = false;
    }
    return pdfjsLib;
}

/**
 * Extrai texto e metadados de um PDF
 * @param {string} pdfPath - Caminho para o arquivo PDF
 * @returns {Promise<Object>} Dados extraídos do PDF
 */
async function extractPdfData(pdfPath) {
    try {
        await initPdfJs();
        const data = await fs.readFile(pdfPath);
        const loadingTask = pdfjsLib.getDocument({ data });
        const pdf = await loadingTask.promise;

        const pdfData = {
            numPages: pdf.numPages,
            pages: []
        };

        // Processa cada página
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            const page = await pdf.getPage(pageNum);
            const viewport = page.getViewport({ scale: 1.0 });
            const textContent = await page.getTextContent();

            const pageData = {
                pageNumber: pageNum,
                width: viewport.width,
                height: viewport.height,
                textItems: []
            };

            // Extrai cada item de texto com suas propriedades
            for (const item of textContent.items) {
                if (item.str.trim()) {
                    const textItem = {
                        text: item.str,
                        x: item.transform[4],
                        y: item.transform[5],
                        width: item.width,
                        height: item.height,
                        fontName: item.fontName,
                        fontSize: Math.round(item.transform[0] * 100) / 100,
                        // Preserva transformações para itálico, negrito, etc
                        transform: item.transform
                    };
                    pageData.textItems.push(textItem);
                }
            }

            pdfData.pages.push(pageData);
        }

        return pdfData;
    } catch (error) {
        console.error('Erro ao extrair dados do PDF:', error);
        throw error;
    }
}

/**
 * Salva os dados extraídos em JSON
 */
async function main() {
    const args = process.argv.slice(2);

    if (args.length < 2) {
        console.error('Uso: node extractPdfText.js <caminho-pdf> <caminho-saida-json>');
        process.exit(1);
    }

    const [pdfPath, outputPath] = args;

    try {
        console.log(`Extraindo texto de: ${pdfPath}`);
        const pdfData = await extractPdfData(pdfPath);

        await fs.writeFile(outputPath, JSON.stringify(pdfData, null, 2));

        console.log(`✓ Dados extraídos com sucesso!`);
        console.log(`  Páginas: ${pdfData.numPages}`);
        console.log(`  Total de itens de texto: ${pdfData.pages.reduce((sum, p) => sum + p.textItems.length, 0)}`);
        console.log(`  Salvo em: ${outputPath}`);

        // Retorna sucesso via código de saída
        process.exit(0);
    } catch (error) {
        console.error('Erro:', error.message);
        process.exit(1);
    }
}

// Executa se chamado diretamente
if (require.main === module) {
    main();
}

module.exports = { extractPdfData };
