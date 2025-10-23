# Fontes para Geração de PDFs

Este diretório contém fontes especializadas para renderização de textos em diferentes idiomas nos PDFs traduzidos.

## Fontes Disponíveis

### Grego (Greek)
- **Gentium Plus** (`GentiumPlus-Regular.ttf`) - Fonte principal para textos em Grego
  - Suporta caracteres do Grego Clássico e Moderno
  - Inclui caracteres acentuados e diacríticos
  - Fonte: [SIL International](https://software.sil.org/gentium/)

- **Noto Serif Greek** (`NotoSerifGreek-Regular.ttf`) - Fonte alternativa
  - Parte da família Google Noto Fonts
  - Excelente suporte para caracteres gregos
  - Fonte: [Google Noto Fonts](https://fonts.google.com/noto)

### Hebraico (Hebrew)
- **SBL Hebrew** (`SBLHebrew-Regular.ttf`) - Fonte principal para textos em Hebraico
  - Desenvolvida pela Society of Biblical Literature
  - Otimizada para textos bíblicos em Hebraico
  - Fonte: [SBL](https://www.sbl-site.org/educational/BiblicalFonts_SBLHebrew.aspx)

- **Noto Sans Hebrew** (`NotoSansHebrew-Regular.ttf`) - Fonte alternativa
  - Parte da família Google Noto Fonts
  - Suporte completo para Hebraico moderno e bíblico
  - Fonte: [Google Noto Fonts](https://fonts.google.com/noto)

## Como Funciona

O script `create_simple_translated_pdf.py` detecta automaticamente o idioma do texto baseado nos caracteres Unicode:

1. **Detecção de Idioma**:
   - Grego: caracteres Unicode 0370-03FF e 1F00-1FFF
   - Hebraico: caracteres Unicode 0590-05FF

2. **Seleção de Fonte**:
   - Para Grego: usa Gentium Plus como primeira opção, Noto Serif Greek como fallback
   - Para Hebraico: usa SBL Hebrew como primeira opção, Noto Sans Hebrew como fallback
   - Para outros idiomas: usa a fonte padrão (Helvetica)

3. **Renderização RTL** (Right-to-Left):
   - Hebraico é automaticamente alinhado à direita
   - Grego usa alinhamento à esquerda (LTR)

## Testando as Fontes

Use o script de teste para verificar a renderização:

```bash
# Gerar arquivos de teste
python scripts/python/test_fonts.py

# Testar PDF em Grego
python scripts/python/create_simple_translated_pdf.py \
  storage/test_fonts/test_greek.json \
  storage/test_fonts/test_greek.pdf

# Testar PDF em Hebraico
python scripts/python/create_simple_translated_pdf.py \
  storage/test_fonts/test_hebrew.json \
  storage/test_fonts/test_hebrew.pdf
```

## Adicionando Novas Fontes

Para adicionar suporte a novos idiomas:

1. Baixe a fonte TTF apropriada
2. Adicione a fonte neste diretório
3. Atualize a função `get_font_config()` em `create_simple_translated_pdf.py`
4. Adicione padrões de detecção na função `detect_language()`

## Licenças

- **Gentium Plus**: SIL Open Font License 1.1
- **Noto Fonts**: SIL Open Font License 1.1
- **SBL Hebrew**: SIL Open Font License 1.1

Todas as fontes são de uso livre, incluindo uso comercial.
