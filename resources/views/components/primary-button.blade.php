<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-3 py-1 bg-[#004BAD] border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-[#0066E0] focus:bg-[#0066E0] active:bg-[#003A80] focus:outline-none focus:ring-2 focus:ring-[#004BAD] focus:ring-offset-2 transition ease-in-out duration-150 font-sans'
]) }}>
    {{ $slot }}
</button>