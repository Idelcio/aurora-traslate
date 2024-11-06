<x-app-layout>
    <div class="container mx-auto mt-5">

        @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-3 rounded mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div>
                <label for="pdf" class="block mb-2">Escolha um arquivo PDF:</label>
                <input type="file" name="pdf" accept="application/pdf" required class="border rounded p-2">
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white rounded p-2">Carregar PDF</button>
        </form>

        @if (session('pdf_filename'))
        <h2 class="mt-8 font-semibold">Visualizar PDF:</h2>
        <iframe src="{{ route('pdf.show', ['filename' => session('pdf_filename')]) }}" width="100%" height="600px">
            Este recurso não está disponível.
        </iframe>
        @endif
    </div>

</x-app-layout>