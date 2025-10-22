#!/usr/bin/env node

/**
 * Script SUPER SIMPLES para criar PDF traduzido
 * Apenas texto puro, sem formatação complexa
 */

const fs = require('fs').promises;
const { PDFDocument, rgb, StandardFonts } = require('pdf-lib');

/**
 * Cria PDF simples com texto traduzido
 */
async function rebuildSimplePdf(translatedJsonPath, outputPath, numPages) {
    try {
        // Carrega dados traduzidos
        const fileContent = await fs.readFile(translatedJsonPath, 'utf-8');
        const translatedData = JSON.parse(fileContent);

        // Cria novo PDF
        const pdfDoc = await PDFDocument.create();

        // Embeda fonte Helvetica
        const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
        const fontSize = 12;
        const margin = 50;
        const lineHeight = 15;

        // Coleta todo o texto traduzido
        let allTranslatedText = [];

        for (const page of translatedData.pages) {
            for (const item of page.textItems) {
                if (item.translatedText && item.translatedText.trim()) {
                    allTranslatedText.push(item.translatedText.trim());
                }
            }
        }

        // Junta todo o texto com espaços entre parágrafos
        // Remove quebras de linha internas de cada parágrafo
        const cleanedParagraphs = allTranslatedText.map(p => p.replace(/\n/g, ' '));
        const fullText = cleanedParagraphs.join(' ');

        // Divide o texto em palavras e cria linhas
        const words = fullText.split(' ');
        let lines = [];
        let currentLine = '';

        const maxWidth = 595 - (margin * 2); // A4 width
        const maxHeight = 842 - (margin * 2); // A4 height
        const linesPerPage = Math.floor(maxHeight / lineHeight);

        // Cria todas as linhas
        for (const word of words) {
            const testLine = currentLine ? `${currentLine} ${word}` : word;
            const testWidth = font.widthOfTextAtSize(testLine, fontSize);

            if (testWidth > maxWidth) {
                if (currentLine) {
                    lines.push(currentLine);
                    currentLine = word;
                } else {
                    lines.push(word);
                    currentLine = '';
                }
            } else {
                currentLine = testLine;
            }
        }

        if (currentLine) {
            lines.push(currentLine);
        }

        console.log(`Total de linhas: ${lines.length}, Páginas: ${numPages}`);

        // Distribui as linhas entre as páginas
        let lineIndex = 0;

        for (let i = 0; i < numPages; i++) {
            const page = pdfDoc.addPage([595, 842]); // A4
            const { width, height } = page.getSize();

            let y = height - margin;
            let linesDrawn = 0;

            // Desenha linhas nesta página
            while (lineIndex < lines.length && linesDrawn < linesPerPage) {
                try {
                    page.drawText(lines[lineIndex], {
                        x: margin,
                        y: y,
                        size: fontSize,
                        font: font,
                        color: rgb(0, 0, 0),
                    });

                    y -= lineHeight;
                    lineIndex++;
                    linesDrawn++;
                } catch (drawError) {
                    console.error('Erro ao desenhar linha:', drawError.message);
                    lineIndex++;
                }
            }
        }

        console.log(`Linhas desenhadas: ${lineIndex}/${lines.length}`);

        // Salva o PDF
        const pdfBytes = await pdfDoc.save();
        await fs.writeFile(outputPath, pdfBytes);

        console.log(`✓ PDF criado com ${numPages} página(s): ${outputPath}`);
        return true;
    } catch (error) {
        throw new Error(`Erro ao criar PDF: ${error.message}`);
    }
}

/**
 * Main function
 */
async function main() {
    const args = process.argv.slice(2);

    if (args.length < 3) {
        console.error('Uso: node rebuildSimplePdf.cjs <dados-traduzidos.json> <pdf-saida> <num-paginas>');
        process.exit(1);
    }

    const [translatedJson, outputPdf, numPages] = args;

    try {
        await rebuildSimplePdf(translatedJson, outputPdf, parseInt(numPages));
        process.exit(0);
    } catch (error) {
        console.error('Erro:', error.message);
        process.exit(1);
    }
}

if (require.main === module) {
    main();
}

module.exports = { rebuildSimplePdf };
