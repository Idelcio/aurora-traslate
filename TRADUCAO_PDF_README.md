# Sistema de Tradução de PDFs

## Visão Geral

Este sistema traduz arquivos PDF mantendo:
- ✅ **Número exato de páginas** do original (1 página = 1 página traduzida, 10 páginas = 10 páginas traduzidas)
- ✅ **Fonte legível** (Helvetica/Arial) em texto preto
- ✅ **Posições originais** do texto
- ✅ **Tamanhos de fonte** originais

## Como Funciona

O processo de tradução ocorre em 3 etapas:

1. **Extração**: Script Node.js extrai todo o texto do PDF com informações de posição, tamanho e fonte
2. **Tradução**: Google Translate API traduz cada fragmento de texto individualmente
3. **Reconstrução**: Script Node.js reconstrói o PDF com texto traduzido, mantendo layout original

## Requisitos

- ✅ Node.js v22+ (instalado)
- ✅ PHP 8.1+ (instalado)
- ✅ Laravel 10+ (instalado)
- ✅ Google Translate API Key (configurada)
- ✅ Pacotes Node.js: pdf-lib, pdfjs-dist, fontkit (instalados)

## Estrutura de Arquivos

```
Tradutor/
├── scripts/
│   ├── extractPdfText.js          # Extrai texto do PDF
│   └── rebuildPdfWithTranslation.js # Reconstrói PDF traduzido
├── app/
│   ├── Http/Controllers/
│   │   └── PdfTranslateController.php # Controller de tradução
│   └── Services/
│       └── GoogleTranslateService.php # Serviço do Google Translate
├── storage/
│   └── app/public/pdfs/
│       ├── temp/                   # PDFs temporários durante processamento
│       └── translated/             # PDFs traduzidos finais
└── test_translate.php              # Script de teste do sistema
```

## Como Usar

### 1. Verificar Sistema

Execute o script de teste para garantir que tudo está configurado:

```bash
php test_translate.php
```

### 2. Traduzir um PDF via Interface

1. Inicie o servidor Laravel:
   ```bash
   php artisan serve
   ```

2. Acesse o dashboard em: `http://localhost:8000/dashboard`

3. No formulário "Novo Livro":
   - Faça upload do arquivo PDF
   - Selecione o idioma original (ou deixe em "Auto-detectar")
   - Selecione o idioma de destino
   - Clique em "Iniciar Tradução"

4. Aguarde o processamento (pode levar alguns minutos dependendo do tamanho)

5. Quando concluído, o PDF traduzido aparecerá na lista "Seus Livros" com botão de download

### 3. Traduzir um PDF via API

Você também pode usar a API diretamente:

```bash
curl -X POST http://localhost:8000/pdf/translate \
  -H "Content-Type: multipart/form-data" \
  -F "pdf=@/caminho/para/seu/arquivo.pdf" \
  -F "target_language=pt" \
  -F "source_language=en"
```

## Rotas Disponíveis

| Rota | Método | Descrição |
|------|--------|-----------|
| `/pdf/translate` | POST | Inicia tradução de PDF |
| `/pdf/translate/download/{filename}` | GET | Baixa PDF traduzido |

## Configuração

### Google Translate API

Certifique-se de que a chave da API está configurada no `.env`:

```env
GOOGLE_TRANSLATE_API_KEY=sua_chave_aqui
```

### Limite de Páginas

O sistema respeita o limite de páginas do plano do usuário. Configure os planos em:
- `app/Models/Plan.php`

## Características Técnicas

### Fontes

- **Fonte padrão**: Helvetica (Arial)
- **Cor do texto**: Sempre preto (#000000)
- **Objetivo**: Máxima legibilidade

### Preservação de Layout

O sistema mantém:
- Posição X e Y de cada fragmento de texto
- Tamanho da fonte original
- Largura aproximada do texto

### Limitações Conhecidas

1. **Imagens**: Texto em imagens não é traduzido (requer OCR)
2. **Tabelas complexas**: Layout pode ser afetado se o texto traduzido for muito maior
3. **Fontes especiais**: Todas as fontes são convertidas para Helvetica
4. **Cores**: Todo texto fica preto (pode ser customizado se necessário)

## Troubleshooting

### Erro: "Node.js não encontrado"

**Solução**: Instale o Node.js de https://nodejs.org/

### Erro: "Pacote 'pdf-lib' não instalado"

**Solução**: Execute `npm install` na raiz do projeto

### Erro: "Google Translate API Error"

**Solução**:
1. Verifique se a API Key está correta no `.env`
2. Confirme que a API está habilitada no Google Cloud Console
3. Verifique se há créditos disponíveis

### PDF traduzido está vazio

**Solução**:
1. Verifique os logs: `storage/logs/laravel.log`
2. Teste o script de extração manualmente:
   ```bash
   node scripts/extractPdfText.js caminho/para/teste.pdf saida.json
   ```

### Texto sobreposto ou ilegível

**Solução**:
- Isso pode ocorrer se o texto traduzido for muito maior que o original
- O sistema tenta respeitar a largura original, mas há limitações

## Melhorias Futuras

- [ ] Suporte a OCR para texto em imagens
- [ ] Detecção automática de tabelas
- [ ] Opção de escolher fonte de saída
- [ ] Suporte a múltiplas cores de texto
- [ ] Tradução em batch (múltiplos PDFs)
- [ ] Preview antes do download
- [ ] Ajuste automático de tamanho de fonte

## Suporte

Para problemas ou dúvidas:
1. Verifique os logs em `storage/logs/laravel.log`
2. Execute o script de teste: `php test_translate.php`
3. Consulte a documentação do Laravel em https://laravel.com/docs

---

**Versão**: 1.0
**Última atualização**: 2025-01-22

## Dependencias Python

- Python 3.10+ instalado (recomendado 64 bits)
- Executar: python -m pip install -r scripts/python/requirements.txt
- Variavel de ambiente PYTHON_TRANSLATE_BINARY pode ajustar o caminho do interpretador
