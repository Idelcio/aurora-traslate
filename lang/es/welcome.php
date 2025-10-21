<?php

return [
    'meta_title' => 'Aurora Translate · Traducción inteligente de PDFs',
    'hero' => [
        'badge' => 'Traducción asistida para libros',
        'title' => 'La forma más elegante de traducir libros en PDF.',
        'description' => 'Ten tus manuscritos, informes técnicos y best sellers listos para nuevos mercados sin perder formato, capítulos ni notas al pie. Operaciones en lote, revisión integrada y exportación automática a PDFs finales.',
        'primary_cta' => 'Acceder a la plataforma',
        'secondary_cta' => 'Ver integraciones',
    ],
    'card' => [
        'title' => 'Listo para traducciones completas',
        'highlights' => [
            'Traduce páginas, capítulos o la obra completa',
            'Control terminológico con glosarios personalizados',
            'Historial de versiones y comparaciones paralelas',
            'Exportación instantánea a PDF, DOCX o HTML',
        ],
    ],
    'info' => [
        'items' => [
            [
                'title' => 'Procesamiento avanzado de PDFs',
                'body' => 'Extraemos texto, imágenes y estructuras de capítulos para preservar el diseño original. Compatible con libros muy extensos.',
            ],
            [
                'title' => 'Flujo híbrido humano + IA',
                'body' => 'Combina la Cloud Translation API con revisores internos. Compara versiones y ajusta sin salir de la plataforma.',
            ],
            [
                'title' => 'Entrega en múltiples formatos',
                'body' => 'Genera PDFs finales, borradores en DOCX para revisión editorial o HTML optimizado para publicación digital.',
            ],
        ],
    ],
    'workflow' => [
        'badge' => 'En tres pasos',
        'title' => 'Del upload a la publicación en minutos',
        'steps' => [
            [
                'title' => 'Carga inteligente',
                'body' => 'Selecciona un PDF completo o especifica páginas. Detectamos el idioma automáticamente y validamos metadatos.',
            ],
            [
                'title' => 'Traducción orquestada',
                'body' => 'Conéctate a la Google Cloud Translation API y personaliza glosarios y modelos adaptados a tu catálogo.',
            ],
            [
                'title' => 'Refinamiento y exportación',
                'body' => 'Revisa, compara y aprueba. Luego genera el PDF final con índice, portadas y notas preservadas.',
            ],
        ],
    ],
    'integration' => [
        'badge' => 'Google Cloud Translation API',
        'title' => 'Integración oficial y segura con Google Cloud.',
        'description' => 'Utiliza los endpoints translateText y translateDocument para traducciones síncronas o por lotes. Soporte nativo para glosarios, conjuntos Adaptive MT y monitoreo de operaciones extensas.',
        'cards' => [
            [
                'title' => 'Provisionamiento rápido',
                'body' => 'Crea un proyecto, habilita la API y genera una clave de servicio. Configura la variable GOOGLE_APPLICATION_CREDENTIALS.',
            ],
            [
                'title' => 'Clientes oficiales',
                'body' => 'Usa los SDK de Google o solicitudes REST. Soporte completo para detección de idioma, glosarios y romanización.',
            ],
            [
                'title' => 'Monitoreo continuo',
                'body' => 'Sigue los procesos largos con projects.locations.operations, cancela o reprocesa cuando sea necesario.',
            ],
        ],
    ],
    'cta' => [
        'title' => 'Comienza a traducir ahora mismo.',
        'description' => 'Una plataforma completa para editoriales, universidades y traductores independientes. Prueba la traducción de un capítulo y observa el resultado en tiempo real.',
        'button' => 'Empezar a traducir',
    ],
    'footer' => 'Aurora Translate · Plataforma profesional de traducción de PDFs',
];
