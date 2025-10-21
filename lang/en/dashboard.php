<?php

return [
    'hero' => [
        'badge' => 'Intelligent translation platform',
        'title' => 'Translate PDF books in just a few clicks',
        'description' => 'Convert single pages or entire works and receive reviewed versions while preserving formatting, table of contents, and annotations.',
        'primary_cta' => 'Get started',
        'secondary_cta' => 'Connect Google Cloud',
        'image_alt' => 'Book translation workflow',
    ],
    'summary' => [
        'title' => 'Project overview',
        'items' => [
            'Secure PDF upload with support for full-length books',
            'Page-by-page or full document translation while keeping layout',
            'Automatic export to a reviewed PDF ready for distribution',
            'Planned integration with Google Cloud Translation API',
        ],
        'next_step_title' => 'Next step',
        'next_step_body' => 'Define API credentials and map the translation pipeline for production.',
    ],
    'form' => [
        'section_title' => 'Configure translation',
        'section_description' => 'Upload your PDF, choose the translation scope, and set the target language. For specific pages, provide a single number or a range.',
        'pdf_label' => 'PDF file',
        'drag_prompt' => 'Drag the PDF here or',
        'drag_action' => 'select a file',
        'no_file' => 'No file selected',
        'scope_label' => 'Translation scope',
        'scope_options' => [
            'full' => 'Entire document',
            'pages' => 'Page or range',
        ],
        'page_start_label' => 'Start page',
        'page_end_label' => 'End page (optional)',
        'page_start_placeholder' => 'e.g. 5',
        'page_end_placeholder' => 'e.g. 12',
        'source_language_label' => 'Source language',
        'target_language_label' => 'Target language',
        'language_autodetect' => 'Detect automatically',
        'language_options' => [
            'pt-BR' => 'Portuguese (Brazil)',
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
        ],
        'target_language_options' => [
            'en' => 'English',
            'pt-BR' => 'Portuguese (Brazil)',
            'es' => 'Spanish',
            'fr' => 'French',
            'it' => 'Italian',
            'de' => 'German',
        ],
        'output_format_label' => 'Output format',
        'output_format_options' => [
            'pdf' => 'Translated PDF',
            'docx' => 'Word document (DOCX)',
            'html' => 'HTML for review',
        ],
        'glossary_label' => 'Glossary (optional)',
        'glossary_placeholder' => 'Terminology base ID',
        'submit' => 'Send for translation',
    ],
    'preview' => [
        'title' => 'Preview and review',
        'description' => 'Preview the translation with preserved formatting. Add quick notes and approve comments before exporting.',
        'processed_label' => 'Pages processed',
        'processed_placeholder' => '0/0',
        'placeholder' => 'Preview will be displayed here after processing.',
    ],
    'workflow' => [
        'step_labels' => [
            'first' => 'Step 1',
            'second' => 'Step 2',
            'third' => 'Step 3',
        ],
    ],
    'history' => [
        'title' => 'Translation history',
        'description' => 'Review recent files, trigger new exports, and monitor long-running jobs.',
        'download' => 'Download',
        'empty' => 'No translations completed yet.',
        'default_name' => 'Document',
    ],
    'integration' => [
        'title' => 'Google Cloud Translation API integration',
        'description' => 'Connect your Google Cloud credentials to access high-performance translation with support for 130+ languages.',
        'cta_label' => 'Setup guide',
        'cards' => [
            [
                'title' => '1. Create a project',
                'body' => 'In Google Cloud Console, create a dedicated project and enable the Cloud Translation API.',
            ],
            [
                'title' => '2. Generate credentials',
                'body' => 'Create a JSON service key and set the GOOGLE_APPLICATION_CREDENTIALS environment variable.',
            ],
            [
                'title' => '3. Configure Laravel',
                'body' => 'Install the official client or SDK of your choice and adjust jobs to call translateText and translateDocument endpoints.',
            ],
        ],
    ],
    'loading_overlay' => 'Processing PDF and preparing translation...',
    'js' => [
        'no_file' => 'No file selected',
        'invalid_pdf' => 'Please choose a valid PDF file.',
    ],
];
