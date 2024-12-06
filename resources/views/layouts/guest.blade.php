<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TagPdf</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo/tagPdf_icone.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>



<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Logo -->
        <div>
            <img src="{{ asset('logo/tagPdf_logo.png') }}" alt="Logo" style="max-width: 200px;">
        </div>

        <!-- Conteúdo Principal -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>

        <!-- Termos de Uso -->
        <div class="mt-4 text-center">
            <h3 class="text-sm text-gray-600 font-medium">
                <a href="#" class="text-indigo-600 underline hover:text-indigo-800">Termos de Uso de Dados</a>.
            </h3>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="bg-white w-full py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-6">
            <div class="flex justify-center items-center space-x-2">
                <!-- Exemplo de Logos -->
                <img src="{{ asset('icones/linkedIn/linkedin_Black.png') }}" alt="Logo LinkedIn" class="h-6">
                <h2 class="font-sans text-gray-800 font-bold text-base">
                    TagPDF
                </h2>
                <img src="{{ asset('icones/instagram/instagram_black.png') }}" alt="Logo Instagram" class="h-6">
                <h2 class="font-sans text-gray-800 font-bold text-base">
                    TagPDF
                </h2>
            </div>
        </div>
    </footer>
</body>


</html>