<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-[24px] text-gray-800 leading-tight">
            <x-nav-link
                :href="route('pdf.upload.post')"
                :active="request()->routeIs('pdf.upload.post')"
                class="inline-block bg-[#004BAD] text-white px-2 py-1 rounded-md ease-in-out leading-[2rem] hover:bg-[#0066E0] hover:text-white">
                {{ __('Upload de PDF') }}
            </x-nav-link>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            </div>
        </div>
    </div>

</x-app-layout>