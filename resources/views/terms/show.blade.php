<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Termos de Uso</title>
    <link rel="icon" href="{{ asset('icones/logo/favicon.png') }}" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.8;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 150px;
        }

        h1 {
            font-size: 2.5em;
            color: #004BAD;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
        }

        p {
            margin: 15px 0;
            text-align: justify;
        }

        /* Botão Sair com Cores Cinzas */
        .logout-btn {
            display: block;
            margin: 40px auto 0;
            background-color: #333333;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            text-transform: uppercase;
        }

        .logout-btn:hover {
            background-color: #444;
            /* Cor de fundo ao passar o mouse */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('icones/logo/tagpdf_logo.png') }}" alt="Logo TagPDF">
        </div>

        <h1>{{ $term->title }}</h1>

        <div>
            {!! nl2br(e($term->content)) !!}
        </div>

        <!-- Botão Voltar -->
        <button type="button" class="logout-btn" onclick="history.back()">Voltar</button>
    </div>
</body>

</html>