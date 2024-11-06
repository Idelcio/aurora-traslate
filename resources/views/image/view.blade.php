<x-app-layout>
    <div class="container mx-auto mt-5">
        <h2 class="font-semibold">Visualizando Imagem</h2>

        <div class="relative">
            <!-- Exibe a imagem carregada -->
            <img id="image" src="{{ asset('storage/images/' . $filename) }}" class="w-full max-h-96 border" alt="Imagem Carregada">

            <!-- O canvas para desenhar as bolinhas -->
            <canvas id="canvas" class="absolute top-0 left-0"></canvas>
        </div>

        <!-- Formulário para adicionar bolinhas -->
        <div class="mt-4">
            <label for="ball-count">Número de Bolinhas:</label>
            <input type="number" id="ball-count" min="1" value="5" class="border rounded p-2">
            <button id="addBalls" class="bg-blue-500 text-white rounded p-2 mt-2">Adicionar Bolinhas</button>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const img = document.getElementById('image');
                const canvas = document.getElementById('canvas');
                const ctx = canvas.getContext('2d');

                // Aguarda a imagem carregar antes de configurar o canvas
                img.onload = function() {
                    // Ajusta o tamanho do canvas conforme a imagem
                    canvas.width = img.width;
                    canvas.height = img.height;
                }

                // Função para desenhar bolinhas numeradas
                function drawBall(x, y, number) {
                    const radius = 20;
                    ctx.beginPath();
                    ctx.arc(x, y, radius, 0, Math.PI * 2, false);
                    ctx.fillStyle = 'red'; // Cor da bolinha
                    ctx.fill();
                    ctx.fillStyle = 'white'; // Cor do texto
                    ctx.font = '12px Arial';
                    ctx.fillText(number, x - 5, y + 5); // Desenha o número dentro da bolinha
                    ctx.closePath();
                }

                // Adicionar bolinhas ao clicar
                document.getElementById('addBalls').addEventListener('click', function() {
                    const ballCount = parseInt(document.getElementById('ball-count').value);

                    // Limpa o canvas antes de desenhar novas bolinhas
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    // Desenha as bolinhas
                    for (let i = 0; i < ballCount; i++) {
                        const x = Math.random() * (canvas.width - 40) + 20; // Gera uma posição aleatória para X
                        const y = Math.random() * (canvas.height - 40) + 20; // Gera uma posição aleatória para Y
                        drawBall(x, y, i + 1); // Desenha a bolinha numerada
                    }
                });
            });
        </script>
    </div>
</x-app-layout>