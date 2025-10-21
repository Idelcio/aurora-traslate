<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Aurora Translate</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('branding/aurora-favicon.svg') }}" type="image/svg+xml">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/91b85c5ce4.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.8.0/html2pdf.bundle.min.js" integrity="sha512-w3u9q/DeneCSwUDjhiMNibTRh/1i/gScBVp2imNVAMCt6cUHIw6xzhzcPFIaL3Q1EbI2l+nu17q2aLJJLo4ZYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 font-['Poppins',sans-serif] antialiased">
    <div class="flex min-h-screen flex-col">
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
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row sm:justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('branding/aurora-mark.svg') }}" alt="Aurora Translate" class="h-8 w-8">
                        <span class="text-sm font-semibold text-slate-700">Aurora Translate</span>
                    </div>
                    <p class="text-center text-xs text-slate-500">
                        © {{ date('Y') }} Aurora Translate. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
