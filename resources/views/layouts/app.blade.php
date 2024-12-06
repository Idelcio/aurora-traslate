<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TagPdf</title>

    <link rel="icon" href="{{ asset('icones/logo/tagPdf_logo.png') }}" type="image/png">


    <!-- Fonts -->

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">


    <script src="https://kit.fontawesome.com/91b85c5ce4.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.0/html2pdf.bundle.min.js" integrity="sha512-w3u9q/DeneCSwUDjhiMNibTRh/1i/gScBVp2imNVAMCt6cUHIw6xzhzcPFIaL3Q1EbI2l+nu17q2aLJJLo4ZYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- footer -->


    <footer class="bg-white w-full py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-6">
            <div class="flex justify-center items-center space-x-2">
                <!-- logos -->
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