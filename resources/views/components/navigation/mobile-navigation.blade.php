<div class="m-4 top-1 z-10 rounded-xl bg-gray-900 xl:hidden flex justify-between w-auto p-6 items-center max-h-[100vh]">
    <!--- more free and premium Tailwind CSS components at https://tailwinduikit.com/ --->
    <div class="flex justify-between  items-center space-x-3">
        <a class="flex justify-start gap-3" href="{{ Route('home') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="logo" class="w-8 h-8 rounded-full">
            <p class="text-2xl font-semibold">Boardchive</p>
        </a>
    </div>
    <div aria-label="toggler" class="flex justify-center items-center">
        <button aria-label="open" id="openIcon" class=" focus:outline-none focus:ring-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M4 12H20" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M4 18H20" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <button aria-label="close" id="closeIcon" class="hidden focus:outline-none focus:ring-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6 6L18 18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</div>
