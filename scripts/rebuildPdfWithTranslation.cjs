#!/usr/bin/env node

/**
 * Script para reconstruir PDF com texto traduzido
 * Mantém: fontes, cores, posições e formatação original
 */

const fs = require('fs').promises;
const { PDFDocument, rgb, StandardFonts } = require('pdf-lib');
const fontkit = require('fontkit');

/**
 * Usa sempre Helvetica (Arial) como fonte de fácil leitura
 * Simplificado: não mantém cor ou fonte original
 */
function getReadableFont() {
    // Sempre retorna Helvetica por ser uma fonte limpa e legível
    return StandardFonts.Helvetica;
}

/**
 * Reconstrói PDF com texto traduzido
 */
async function rebuildPdf(originalPdfPath, extractedDataPath, translatedDataPath, outputPath) {
    try {
        // Carrega o PDF original
        const originalPdfBytes = await fs.readFile(originalPdfPath);
        const pdfDoc = await PDFDocument.load(originalPdfBytes);

        // Registra fontkit para suporte a fontes customizadas
        pdfDoc.registerFontkit(fontkit);

        // Carrega dados extraídos e traduzidos
        const extractedData = JSON.parse(await fs.readFile(extractedDataPath, 'utf-8'));
        const translatedData = JSON.parse(await fs.readFile(translatedDataPath, 'utf-8'));

        // Cache de fontes já embedadas
        const fontCache = {};

        // Processa cada página
        for (let i = 0; i < extractedData.pages.length; i++) {
            const extractedPage = extractedData.pages[i];
            const translatedPage = translatedData.pages[i];
            const page = pdfDoc.getPage(i);

            // Remove todo o texto existente criando um overlay branco
            // (alternativa: criar novo PDF do zero)
            const { width, height } = page.getSize();

            // Para cada item de texto
            for (let j = 0; j < extractedPage.textItems.length; j++) {
                const originalItem = extractedPage.textItems[j];
                const translatedText = translatedPage.textItems[j]?.translatedText || originalItem.text;

                // Usa sempre Helvetica para fácil leitura
                let font;
                if (!fontCache['helvetica']) {
                    font = await pdfDoc.embedFont(getReadableFont());
                    fontCache['helvetica'] = font;
                } else {
                    font = fontCache['helvetica'];
                }

                // Primeiro, "apaga" o texto original desenhando um retângulo branco
                page.drawRectangle({
                    x: originalItem.x,
                    y: originalItem.y,
                    width: originalItem.width,
                    height: originalItem.height,
                    color: rgb(1, 1, 1), // Branco
                    opacity: 1,
                });

                // Desenha o texto traduzido com fonte legível e cor preta
                try {
                    page.drawText(translatedText, {
                        x: originalItem.x,
                        y: originalItem.y,
                        size: originalItem.fontSize,
                        font: font,
                        color: rgb(0, 0, 0), // Sempre preto para fácil leitura
                        maxWidth: originalItem.width,
                    });
                } catch (error) {
                    console.warn(`Aviso: Não foi possível desenhar texto na página ${i + 1}:`, error.message);
                }
            }
        }

        // Salva o PDF modificado
        const pdfBytes = await pdfDoc.save();
        await fs.writeFile(outputPath, pdfBytes);

        console.log(`✓ PDF traduzido salvo em: ${outputPath}`);
        return true;
    } catch (error) {
        console.error('Erro ao reconstruir PDF:', error);
        throw error;
    }
}

/**
 * Main function
 */
async function main() {
    const args = process.argv.slice(2);

    if (args.length < 4) {
        console.error('Uso: node rebuildPdfWithTranslation.js <pdf-original> <dados-extraidos.json> <dados-traduzidos.json> <pdf-saida>');
        process.exit(1);
    }

    const [originalPdf, extractedJson, translatedJson, outputPdf] = args;

    try {
        await rebuildPdf(originalPdf, extractedJson, translatedJson, outputPdf);
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

module.exports = { rebuildPdf };
