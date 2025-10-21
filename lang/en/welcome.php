<?php

return [
    'meta_title' => 'Aurora Translate · Intelligent PDF translation',
    'hero' => [
        'badge' => 'Translation assistance for books',
        'title' => 'The most elegant way to translate PDF books.',
        'description' => 'Deliver manuscripts, technical reports, and bestsellers to new markets without losing formatting, chapters, or footnotes. Batch operations, integrated review, and automatic export to a finalized PDF.',
        'primary_cta' => 'Access platform',
        'secondary_cta' => 'View integrations',
    ],
    'card' => [
        'title' => 'Ready for end-to-end translations',
        'highlights' => [
            'Translate single pages, chapters, or the entire work',
            'Terminology control with custom glossaries',
            'Version history and side-by-side comparisons',
            'Instant export to PDF, DOCX, or HTML',
        ],
    ],
    'info' => [
        'items' => [
            [
                'title' => 'Advanced PDF processing',
                'body' => 'We extract text, images, and chapter structure to preserve the original layout. Built for very large books.',
            ],
            [
                'title' => 'Hybrid human + AI workflow',
                'body' => 'Combine the Cloud Translation API with in-house reviewers. Compare versions and apply adjustments without leaving the platform.',
            ],
            [
                'title' => 'Delivery in multiple formats',
                'body' => 'Generate final PDFs, DOCX drafts for editorial review, or HTML optimized for digital publishing.',
            ],
        ],
    ],
    'workflow' => [
        'badge' => 'In three steps',
        'title' => 'From upload to publication in minutes',
        'steps' => [
            [
                'title' => 'Smart upload',
                'body' => 'Select an entire PDF or target pages. We auto-detect the source language and validate metadata.',
            ],
            [
                'title' => 'Orchestrated translation',
                'body' => 'Connect to Google Cloud Translation API and tailor glossaries and custom models for your catalog.',
            ],
            [
                'title' => 'Refinement and export',
                'body' => 'Review, compare, and approve. Then generate the final PDF with table of contents, covers, and notes preserved.',
            ],
        ],
    ],
    'integration' => [
        'badge' => 'Google Cloud Translation API',
        'title' => 'Official, secure integration with Google Cloud.',
        'description' => 'Use the translateText and translateDocument endpoints for synchronous or batch translations. Native support for glossaries, Adaptive MT datasets, and long-running operation monitoring.',
        'cards' => [
            [
                'title' => 'Fast provisioning',
                'body' => 'Create a project, enable the API, and generate a service key. Set the GOOGLE_APPLICATION_CREDENTIALS variable.',
            ],
            [
                'title' => 'Official clients',
                'body' => 'Use Google SDKs or REST requests. Full support for language detection, glossaries, and romanization.',
            ],
            [
                'title' => 'Continuous monitoring',
                'body' => 'Track long jobs with projects.locations.operations, cancel, or reprocess whenever needed.',
            ],
        ],
    ],
    'cta' => [
        'title' => 'Start translating right now.',
        'description' => 'A complete platform for publishers, universities, and freelance translators. Test a chapter translation and watch the output in real time.',
        'button' => 'Start translating',
    ],
    'footer' => 'Aurora Translate · Professional PDF translation platform',
];
