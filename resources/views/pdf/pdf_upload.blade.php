<x-app-layout>
    <div class="container mx-auto mt-5">
        <h2 class="font-semibold">Carregar PDF</h2>

        @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-3 rounded mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Formulário para upload do PDF -->
        <form id="pdf-upload-form" action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div>
                <!-- Rótulo vazio para o botão -->
                <label for="pdf" class="block mb-2"></label>

                <!-- Input do tipo file (botão oculto e estilizado) -->
                <input type="file" name="pdf" id="pdf-input" accept="application/pdf" required class="hidden">

                <!-- Botão personalizado -->
                <button type="button" id="choose-file-btn" class="bg-gray-700 text-white rounded p-2">
                    Escolher arquivo
                </button>
            </div>
        </form>

        <!-- Exibição do PDF -->
        @if (session('pdf_filename'))
        <h2 class="mt-8 font-semibold">Visualizar PDF:</h2>
        <iframe src="{{ route('pdf.show', ['filename' => session('pdf_filename')]) }}" width="100%" height="600px">
            Este recurso não está disponível.
        </iframe>
        @endif
    </div>

    <script>
        // Quando o botão personalizado for clicado, o input file será acionado
        document.getElementById('choose-file-btn').addEventListener('click', function() {
            document.getElementById('pdf-input').click();
        });

        // Submete automaticamente o formulário quando o arquivo for selecionado
        document.getElementById('pdf-input').addEventListener('change', function() {
            document.getElementById('pdf-upload-form').submit();
        });
    </script>
</x-app-layout>