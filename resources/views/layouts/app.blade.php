<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TagPdf</title>

    <link rel="icon" href="{{ asset('icones/logo/tagpdf_logo.png') }}" type="image/png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/91b85c5ce4.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.0/html2pdf.bundle.min.js" integrity="sha512-w3u9q/DeneCSwUDjhiMNibTRh/1i/gScBVp2imNVAMCt6cUHIw6xzhzcPFIaL3Q1EbI2l+nu17q2aLJJLo4ZYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        /* Reset de margens e rolagem */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            font-family: 'Montserrat', sans-serif;
        }

        /* Layout Principal */
        .min-h-screen {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            padding: 20px;
            background-color: #f3f4f6;
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

<body class="font-sans antialiased">
    <div class="min-h-screen">
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

        <!-- Footer -->

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

        </footer>

    </div>
</body>

</html>