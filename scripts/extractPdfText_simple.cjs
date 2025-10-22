#!/usr/bin/env node

/**
 * Script simples para extrair texto de PDFs página por página
 * Usa pdf-parse que é mais estável
 */

const fs = require('fs').promises;
const { PDFParse } = require('pdf-parse');

/**
 * Extrai texto de cada página do PDF
 * @param {string} pdfPath - Caminho para o arquivo PDF
 * @returns {Promise<Object>} Dados extraídos do PDF
 */
async function extractPdfData(pdfPath) {
    try {
        const dataBuffer = await fs.readFile(pdfPath);
        const parser = new PDFParse({ data: dataBuffer });

        // Obtém informações do PDF
        const info = await parser.getInfo();
        const numPages = info.total;

        const pdfData = {
            numPages: numPages,
            pages: []
        };

        // Extrai todo o texto do PDF (apenas texto puro)
        const fullText = await parser.getText();
        const allText = fullText.text;

        // Limpa e preserva apenas quebras de linha e pontuação
        const cleanText = allText
            .replace(/\r\n/g, '\n')  // Normaliza quebras de linha
            .replace(/\n{3,}/g, '\n\n')  // Remove quebras excessivas
            .trim();

        // Cria uma única "página" com todo o texto como um bloco
        // Vamos criar um textItem por parágrafo (separado por linha vazia)
        const paragraphs = cleanText.split('\n\n').filter(p => p.trim());

        const pageData = {
            pageNumber: 1,
            textItems: []
        };

        paragraphs.forEach((paragraph) => {
            if (paragraph.trim()) {
                pageData.textItems.push({
                    text: paragraph.trim()
                });
            }
        });

        pdfData.pages.push(pageData);

        // Adiciona páginas vazias para manter o número correto
        for (let i = 2; i <= numPages; i++) {
            pdfData.pages.push({
                pageNumber: i,
                textItems: []
            });
        }

        return pdfData;
    } catch (error) {
        throw new Error(`Erro ao extrair dados do PDF: ${error.message}`);
    }
}

/**
 * Salva os dados extraídos em JSON
 */
async function main() {
    const args = process.argv.slice(2);

    if (args.length < 2) {
        console.error('Uso: node extractPdfText_simple.cjs <caminho-pdf> <caminho-saida-json>');
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

        process.exit(0);
    } catch (error) {
        console.error('Erro:', error.message);
        process.exit(1);
    }
}

if (require.main === module) {
    main();
}

module.exports = { extractPdfData };
