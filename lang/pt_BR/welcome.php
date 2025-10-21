<?php

return [
    'meta_title' => 'Aurora Translate · Tradução inteligente de PDFs',
    'hero' => [
        'badge' => 'Tradução assistida para livros',
        'title' => 'A maneira mais elegante de traduzir livros em PDF.',
        'description' => 'Tenha seus manuscritos, relatórios técnicos e best-sellers prontos para novos mercados sem perder formatação, capítulos ou notas de rodapé. Operações em lote, revisão integrada e exportação automática para PDF finalizado.',
        'primary_cta' => 'Acessar plataforma',
        'secondary_cta' => 'Ver integrações',
    ],
    'card' => [
        'title' => 'Pronto para traduções completas',
        'highlights' => [
            'Tradução por página, capítulos ou obra inteira',
            'Controle de terminologia com glossários personalizados',
            'Histórico de versões e comparativos lado a lado',
            'Exportação imediata para PDF, DOCX ou HTML',
        ],
    ],
    'info' => [
        'items' => [
            [
                'title' => 'Processamento avançado de PDFs',
                'body' => 'Extraímos texto, imagens e estruturas de capítulos para preservar o layout original. Compatível com livros muito extensos.',
            ],
            [
                'title' => 'Fluxo híbrido humano + IA',
                'body' => 'Combine a Cloud Translation API com revisores internos. Compare versões e faça ajustes sem sair da plataforma.',
            ],
            [
                'title' => 'Entrega em múltiplos formatos',
                'body' => 'Gere PDFs finais, rascunhos em DOCX para revisão editorial ou HTML otimizado para publicação digital.',
            ],
        ],
    ],
    'workflow' => [
        'badge' => 'Em três passos',
        'title' => 'Do upload à publicação em minutos',
        'steps' => [
            [
                'title' => 'Upload inteligente',
                'body' => 'Selecione um PDF completo ou informe as páginas desejadas. Detectamos idioma automaticamente e validamos metadados.',
            ],
            [
                'title' => 'Tradução orquestrada',
                'body' => 'Conecte-se à Google Cloud Translation API e personalize com glossários e modelos treinados especificamente para o seu acervo.',
            ],
            [
                'title' => 'Refinamento e exportação',
                'body' => 'Revise, compare e aprove. Em seguida, gere o PDF final com sumário, capas e notas preservadas.',
            ],
        ],
    ],
    'integration' => [
        'badge' => 'Google Cloud Translation API',
        'title' => 'Integração oficial e segura com a Google Cloud.',
        'description' => 'Utilize as rotas translateText e translateDocument para traduções síncronas ou em lote. Apoio nativo para glossários, datasets de Adaptive MT e monitoramento de operações longas.',
        'cards' => [
            [
                'title' => 'Provisionamento rápido',
                'body' => 'Crie um projeto, habilite a API e gere uma chave de serviço. Configure a variável GOOGLE_APPLICATION_CREDENTIALS.',
            ],
            [
                'title' => 'Clientes oficiais',
                'body' => 'Utilize os SDKs disponibilizados ou requisições REST. Suporte completo a detecção de idioma, glossários e romanização de textos.',
            ],
            [
                'title' => 'Monitoramento contínuo',
                'body' => 'Acompanhe jobs longos com projects.locations.operations, cancele ou reprocesse conforme necessário.',
            ],
        ],
    ],
    'cta' => [
        'title' => 'Comece a traduzir agora mesmo.',
        'description' => 'Uma plataforma completa para editoras, universidades e tradutores independentes. Experimente a tradução de um capítulo e acompanhe o resultado em tempo real.',
        'button' => 'Iniciar tradução',
    ],
    'footer' => 'Aurora Translate · Plataforma de tradução profissional para PDFs',
];
