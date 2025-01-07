<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TagPdf</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('icones/logo/simbolo_tag.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* CSS Global */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            font-family: 'Figtree', sans-serif;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f3f4f6;
            padding: 20px;
        }

        footer {
            width: 100%;
            background-color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .footer-content img {
            height: 24px;
        }

        .footer-content h2 {
            font-size: 1rem;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="main-container">
        <!-- Conteúdo Principal -->
        <div class="content">
            <!-- Logo -->
            <div>
                <a href="{{ url('/') }}">
                    <img src="{{ asset('icones/logo/tagpdf_logo.png') }}" alt="Logo" style="max-width: 200px;">
                </a>
            </div>


            <!-- Conteúdo Principal -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <!-- Termos de Uso -->
            <div class="mt-4 text-center">
                <h3 class="text-sm text-gray-600 font-medium">
                    <a href="{{ route('terms.show', 1) }}" class="text-indigo-600 underline hover:text-indigo-800">Termos de Uso de Dados</a>
                </h3>
            </div>
        </div>

        <!-- Rodapé -->
        <footer class="w-full bg-white py-4 text-center">
            <div class="flex justify-center items-center gap-4">
                <!-- LinkedIn -->
                <a href="https://www.linkedin.com/company/tagpdf" target="_blank" class="hover:opacity-80">
                    <img src="{{ asset('icones/linkedIn/linkedin_Black.png') }}"
                        alt="Logo LinkedIn"
                        class="h-7 w-auto aspect-square" />
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/tagpdf/" target="_blank" class="hover:opacity-80">
                    <img src="{{ asset('icones/instagram/instagram_black.png') }}"
                        alt="Logo Instagram"
                        class="h-7 w-auto aspect-square" />
                </a>

                <h2 class="text-lg font-regular text-gray-800">TagPDF</h2>
            </div>
        </footer>

    </div>

</body>

</html>
