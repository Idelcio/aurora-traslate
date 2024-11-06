<x-app-layout>
    <div class="container mx-auto mt-5">
        <h2 class="font-semibold">Carregar Imagem</h2>

        @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-3 rounded mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('image.upload.post') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div>
                <label for="image" class="block mb-2">Escolha uma imagem:</label>
                <input type="file" name="image" accept="image/*" required class="border rounded p-2">
            </div>
            <button type="submit" class="mt-4 bg-blue-500 text-white rounded p-2">Carregar Imagem</button>
        </form>
    </div>
</x-app-layout>