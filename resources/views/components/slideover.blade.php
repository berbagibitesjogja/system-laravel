@props(['id', 'title'])

<div
    x-show="{{ $id }}"
    class="fixed inset-0 overflow-hidden z-[70]"
    aria-labelledby="slide-over-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <div class="absolute inset-0 overflow-hidden">
        {{-- Backdrop --}}
        <div 
            x-show="{{ $id }}"
            x-transition:enter="ease-in-out duration-500"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="{{ $id }} = false"
            class="absolute inset-0 bg-navy-900/40 backdrop-blur-sm transition-opacity" 
            aria-hidden="true"
        ></div>

        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div 
                x-show="{{ $id }}"
                x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="pointer-events-auto w-screen max-w-md"
                @click.away="{{ $id }} = false"
            >
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl">
                    <div class="px-6 py-6 border-b border-navy-50">
                        <div class="flex items-start justify-between">
                            <h2 class="text-xl font-bold text-navy-900" id="slide-over-title">{{ $title }}</h2>
                            <div class="ml-3 flex h-7 items-center">
                                <button 
                                    type="button" 
                                    class="rounded-xl bg-white text-navy-400 hover:text-navy-600 focus:outline-none focus:ring-2 focus:ring-tosca-500 transition-all"
                                    @click="{{ $id }} = false"
                                >
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="relative flex-1 px-6 py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
