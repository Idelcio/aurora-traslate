<?php

return [
    'hero' => [
        'badge' => 'Plataforma inteligente de traducción',
        'title' => 'Traduce libros en PDF con solo unos clics',
        'description' => 'Convierte páginas sueltas o libros completos y recibe versiones revisadas manteniendo formato, índice y anotaciones.',
        'primary_cta' => 'Comenzar ahora',
        'secondary_cta' => 'Integrar Google Cloud',
        'image_alt' => 'Traducción de libros',
    ],
    'summary' => [
        'title' => 'Resumen del proyecto',
        'items' => [
            'Carga segura de PDFs con soporte para libros completos',
            'Traducción por página o documento completo manteniendo el diseño',
            'Exportación automática a PDF revisado listo para distribución',
            'Integración prevista con Google Cloud Translation API',
        ],
        'next_step_title' => 'Próximo paso',
        'next_step_body' => 'Definir credenciales de la API y mapear el flujo de traducción para producción.',
    ],
    'form' => [
        'section_title' => 'Configurar traducción',
        'section_description' => 'Sube tu PDF, elige el alcance de la traducción y define el idioma de destino. Para páginas específicas, indica un número o un rango.',
        'pdf_label' => 'Archivo PDF',
        'drag_prompt' => 'Arrastra el PDF aquí o',
        'drag_action' => 'selecciona un archivo',
        'no_file' => 'Ningún archivo seleccionado',
        'scope_label' => 'Alcance de la traducción',
        'scope_options' => [
            'full' => 'Documento completo',
            'pages' => 'Página o rango',
        ],
        'page_start_label' => 'Página inicial',
        'page_end_label' => 'Página final (opcional)',
        'page_start_placeholder' => 'Ej: 5',
        'page_end_placeholder' => 'Ej: 12',
        'source_language_label' => 'Idioma original',
        'target_language_label' => 'Idioma destino',
        'language_autodetect' => 'Detectar automáticamente',
        'language_options' => [
            'pt-BR' => 'Portugués (Brasil)',
            'en' => 'Inglés',
            'es' => 'Español',
            'fr' => 'Francés',
            'de' => 'Alemán',
        ],
        'target_language_options' => [
            'en' => 'Inglés',
            'pt-BR' => 'Portugués (Brasil)',
            'es' => 'Español',
            'fr' => 'Francés',
            'it' => 'Italiano',
            'de' => 'Alemán',
        ],
        'output_format_label' => 'Formato de salida',
        'output_format_options' => [
            'pdf' => 'PDF traducido',
            'docx' => 'Documento Word (DOCX)',
            'html' => 'HTML para revisión',
        ],
        'glossary_label' => 'Glosario (opcional)',
        'glossary_placeholder' => 'ID de la base terminológica',
        'submit' => 'Enviar a traducción',
    ],
    'preview' => [
        'title' => 'Previsualización y revisión',
        'description' => 'La vista previa muestra la traducción con el formato preservado. Haz anotaciones rápidas y aprueba comentarios antes de exportar.',
        'processed_label' => 'Páginas procesadas',
        'processed_placeholder' => '0/0',
        'placeholder' => 'La previsualización aparecerá aquí después del procesamiento.',
    ],
    'workflow' => [
        'step_labels' => [
            'first' => 'Paso 1',
            'second' => 'Paso 2',
            'third' => 'Paso 3',
        ],
    ],
    'history' => [
        'title' => 'Historial de traducciones',
        'description' => 'Consulta los últimos archivos traducidos, rehace exportaciones y sigue el estado de ejecuciones largas.',
        'download' => 'Descargar',
        'empty' => 'Aún no hay traducciones completadas.',
        'default_name' => 'Documento',
    ],
    'integration' => [
        'title' => 'Integración con Google Cloud Translation API',
        'description' => 'Conecta tus credenciales de Google Cloud para aprovechar traducciones de alto rendimiento con soporte para más de 130 idiomas.',
        'cta_label' => 'Guía de configuración',
        'cards' => [
            [
                'title' => '1. Crea un proyecto',
                'body' => 'En Google Cloud Console, crea un proyecto dedicado y habilita la API Cloud Translation.',
            ],
            [
                'title' => '2. Genera credenciales',
                'body' => 'Genera una clave de servicio JSON y configura la variable de entorno GOOGLE_APPLICATION_CREDENTIALS.',
            ],
            [
                'title' => '3. Configura Laravel',
                'body' => 'Instala el cliente oficial o el SDK elegido y ajusta los jobs para llamar a los endpoints translateText y translateDocument.',
            ],
        ],
    ],
    'loading_overlay' => 'Procesando PDF y preparando la traducción...',
    'js' => [
        'no_file' => 'Ningún archivo seleccionado',
        'invalid_pdf' => 'Selecciona un archivo PDF válido.',
    ],
];
