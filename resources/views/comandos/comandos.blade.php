<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandos</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f3f4f6;
            /* Fundo claro */
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">

    <!-- Modal -->
    <div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="relative bg-white rounded-lg w-[400px] shadow-lg">
            <!-- Título com Fundo Cinza Escuro -->
            <div class="bg-[#333333] text-white font-bold text-[18px] p-3 rounded-t-lg flex items-center justify-between">
                <span>Comandos:</span>
                <!-- Botão Fechar -->
                <a href="/" class="text-white text-xl
                rounded-full w-8 h-8 flex items-center justify-center hover:bg-white hover:text-black transition">
                    &times;
                </a>
            </div>

            <!-- Conteúdo -->
            <div class="space-y-4 p-4">
                <div class="flex items-start">
                    <img src="{{ asset('icones/icones_comandos/icone_tamanho_tag_(1).png') }}" alt="Definir Tamanho" class="w-14 h-14 mr-3">
                    <div>
                        <h3 class="font-bold text-[16px] text-gray-800">Definir tamanho da tag:</h3>
                        <p class="text-gray-600 text-[14px]">Roda do mouse para cima ou para baixo sobre a tag.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <img src="{{ asset('icones/icones_comandos/icone_zom.png') }}" alt="Zoom" class="w-14 h-14 mr-3">
                    <div>
                        <h3 class="font-bold text-[16px] text-gray-800">Zoom:</h3>
                        <p class="text-gray-600 text-[14px]">Roda do mouse para cima ou para baixo.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <img src="{{ asset('icones/icones_comandos/icone_mover.png') }}" alt="Mover PDF" class="w-18 h-14 mr-3">
                    <div>
                        <h3 class="font-bold text-[16px] text-gray-800">Mover PDF:</h3>
                        <p class="text-gray-600 text-[14px]">Clique e segure para mover.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_mouse.png') }}" alt="Adicionar Tag" class="w-14 h-14 mr-3">
                    <div>
                        <h3 class="font-bold text-[16px] text-gray-800">Adicionar tag:</h3>
                        <p class="text-gray-600 text-[14px]">Clique com botão esquerdo na cota que deseja marcar.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_direito_mouse.png') }}" alt="Excluir Tag" class="w-14 h-14 mr-3">
                    <div>
                        <h3 class="font-bold text-[16px] text-gray-800">Excluir tag:</h3>
                        <p class="text-gray-600 text-[14px]">Clique com botão direito sobre a tag que deseja excluir.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <img src="{{ asset('icones/icones_comandos/icone_clique_e_segure_mouse_(1).png') }}" alt="Mover Tag" class="w-14 h-14 mr-3">
                    <div>
                        <h3 class="font-bold text-[16px] text-gray-800">Mover tag:</h3>
                        <p class="text-gray-600 text-[14px]">Clique e segure com botão direito sobre a tag.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>