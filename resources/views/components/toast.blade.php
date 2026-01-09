@props(['type' => 'success', 'message' => ''])

<div
    x-data="{ 
        show: false, 
        progress: 100,
        init() {
            this.$nextTick(() => { 
                this.show = true;
                let interval = setInterval(() => {
                    this.progress -= 1;
                    if (this.progress <= 0) {
                        this.show = false;
                        clearInterval(interval);
                    }
                }, 30);
            });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed z-[60] top-5 right-5 w-full max-w-sm overflow-hidden bg-white rounded-2xl shadow-2xl border border-navy-50 pointer-events-auto"
>
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                @if($type === 'success')
                    <div class="p-2 bg-lime-100 rounded-xl">
                        <svg class="w-6 h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                @else
                    <div class="p-2 bg-red-100 rounded-xl">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-bold text-navy-900">
                    {{ $type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan' }}
                </p>
                <p class="mt-1 text-sm text-navy-500 leading-relaxed">
                    {{ $message }}
                </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click="show = false" class="bg-white rounded-lg inline-flex text-navy-300 hover:text-navy-500 focus:outline-none transition-colors">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    {{-- Progress Bar --}}
    <div class="h-1 w-full bg-navy-50">
        <div 
            class="h-full {{ $type === 'success' ? 'bg-lime-500' : 'bg-red-500' }} transition-all ease-linear duration-30 transition-all"
            :style="`width: ${progress}%`"
        ></div>
    </div>
</div>
