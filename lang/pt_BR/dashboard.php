<?php

return [
    'hero' => [
        'badge' => 'Plataforma inteligente de tradução',
        'title' => 'Traduza livros em PDF com poucos cliques',
        'description' => 'Converta páginas individuais ou obras completas e receba versões revisadas, mantendo formatação, sumário e notas.',
        'primary_cta' => 'Começar agora',
        'secondary_cta' => 'Integrar com Google Cloud',
        'image_alt' => 'Tradução de livros',
    ],
    'summary' => [
        'title' => 'Resumo do projeto',
        'items' => [
            'Upload seguro de PDFs com suporte a livros completos',
            'Tradução por página ou documento inteiro mantendo layout',
            'Exportação automática para PDF revisado, pronto para distribuição',
            'Integração prevista com Google Cloud Translation API',
        ],
        'next_step_title' => 'Próximo passo',
        'next_step_body' => 'Definir credenciais da API e mapear o pipeline de tradução para produção.',
    ],
    'form' => [
        'section_title' => 'Configurar tradução',
        'section_description' => 'Faça o upload do seu PDF, escolha o escopo da tradução e defina o idioma de destino. Para páginas específicas, informe faixa ou número individual.',
        'pdf_label' => 'Arquivo PDF',
        'drag_prompt' => 'Arraste o PDF aqui ou',
        'drag_action' => 'selecione um arquivo',
        'no_file' => 'Nenhum arquivo selecionado',
        'scope_label' => 'Escopo da tradução',
        'scope_options' => [
            'full' => 'Documento inteiro',
            'pages' => 'Página ou intervalo',
        ],
        'page_start_label' => 'Página inicial',
        'page_end_label' => 'Página final (opcional)',
        'page_start_placeholder' => 'Ex: 5',
        'page_end_placeholder' => 'Ex: 12',
        'source_language_label' => 'Idioma original',
        'target_language_label' => 'Idioma de destino',
        'language_autodetect' => 'Detectar automaticamente',
        'language_options' => [
            'pt-BR' => 'Português (Brasil)',
            'en' => 'Inglês',
            'es' => 'Espanhol',
            'fr' => 'Francês',
            'de' => 'Alemão',
        ],
        'target_language_options' => [
            'en' => 'Inglês',
            'pt-BR' => 'Português (Brasil)',
            'es' => 'Espanhol',
            'fr' => 'Francês',
            'it' => 'Italiano',
            'de' => 'Alemão',
        ],
        'output_format_label' => 'Formato de saída',
        'output_format_options' => [
            'pdf' => 'PDF traduzido',
            'docx' => 'Documento Word (DOCX)',
            'html' => 'HTML para revisão',
        ],
        'glossary_label' => 'Glossário (opcional)',
        'glossary_placeholder' => 'ID da base terminológica',
        'submit' => 'Enviar para tradução',
    ],
    'preview' => [
        'title' => 'Pré-visualização e revisão',
        'description' => 'A pré-visualização apresenta a tradução com formatação preservada. Faça marcações rápidas e aprove comentários antes de exportar.',
        'processed_label' => 'Quantidade de páginas processadas',
        'processed_placeholder' => '0/0',
        'placeholder' => 'Pré-visualização será exibida aqui após o processamento.',
    ],
    'workflow' => [
        'step_labels' => [
            'first' => 'Passo 1',
            'second' => 'Passo 2',
            'third' => 'Passo 3',
        ],
    ],
    'history' => [
        'title' => 'Histórico de traduções',
        'description' => 'Consulte os últimos arquivos traduzidos, refaça exportações e acompanhe status de longas execuções.',
        'download' => 'Download',
        'empty' => 'Nenhuma tradução concluída ainda.',
        'default_name' => 'Documento',
    ],
    'integration' => [
        'title' => 'Integração com Google Cloud Translation API',
        'description' => 'Conecte suas credenciais do Google Cloud para aproveitar traduções de alto desempenho com suporte a mais de 130 idiomas.',
        'cta_label' => 'Guia de configuração',
        'cards' => [
            [
                'title' => '1. Crie um projeto',
                'body' => 'No Google Cloud Console, crie um projeto dedicado e habilite a API Cloud Translation.',
            ],
            [
                'title' => '2. Gere credenciais',
                'body' => 'Gere uma chave de serviço JSON e configure a variável de ambiente GOOGLE_APPLICATION_CREDENTIALS.',
            ],
            [
                'title' => '3. Configure o Laravel',
                'body' => 'Instale o client oficial ou SDK escolhido e ajuste os jobs para enviar requisições aos endpoints translateText e translateDocument.',
            ],
        ],
    ],
    'loading_overlay' => 'Processando PDF e preparando tradução...',
    'js' => [
        'no_file' => 'Nenhum arquivo selecionado',
        'invalid_pdf' => 'Selecione um arquivo PDF válido.',
    ],
];
