@extends('layouts.main')
@section('container')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if (array_keys(request()->query())[0] == 'download')
        <div class="flex flex-col items-center justify-center min-h-[400px] text-center p-8">
            <div class="bg-white p-8 rounded-3xl shadow-xl border border-navy-50 max-w-sm w-full animate-fade-in-up">
                <div class="w-20 h-20 bg-tosca-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl">⬇️</span>
                </div>
                <h1 class="text-2xl font-bold text-navy-900 mb-2">Mengunduh QR Code...</h1>
                <p class="text-navy-400 mb-8">Ponsel Anda akan otomatis mengunduh file gambar. Harap tunggu sebentar.</p>
                <div class="w-full bg-gray-100 rounded-full h-2 mb-4">
                    <div class="bg-tosca-500 h-2 rounded-full animate-progress" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <script type="module">
            import "https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js";
            const data = "{{ env('APP_URL', 'https://app.berbagibitesjogja.com') }}/volunteer/precence/qr?datat={{ $precence->latitude }}!{{ $precence->code }}!{{ $precence->longitude }}";

            const qrCode = new QRCodeStyling({
                width: 1000,
                height: 1000,
                type: "svg",
                data: data,
                image: "https://media.berbagibitesjogja.com/logo_transparan.png",
                dotsOptions: { color: "#1A446D", type: "rounded" },
                backgroundOptions: { color: "#FFFFFF" },
                imageOptions: { crossOrigin: "anonymous", margin: 5 },
            });
            qrCode.download({
                name: "QR Code Absensi {{ $precence->created_at }}",
                extension: "png"
            });
            setTimeout(() => { window.history.back() }, 2000);
        </script>
    
    @elseif (array_keys(request()->query())[0] == 'view')
        <div class="flex flex-col items-center justify-center p-8">
            <div class="bg-white p-6 rounded-3xl shadow-xl border border-navy-100">
                <div id="canvas" class="flex justify-center"></div>
                <p class="text-center text-navy-500 text-sm mt-4 font-medium">Scan untuk Presensi</p>
            </div>
            <a href="{{ url()->previous() }}" class="mt-8 px-6 py-3 bg-navy-50 text-navy-700 rounded-xl font-bold hover:bg-navy-100 transition-colors">
                ← Kembali
            </a>
        </div>
        <script type="module">
            import "https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js";
            const data = "{{ env('APP_URL', 'https://app.berbagibitesjogja.site') }}/volunteer/precence/qr?datat={{ $precence->latitude }}!{{ $precence->code }}!{{ $precence->longitude }}";

            const qrCode = new QRCodeStyling({
                width: 300,
                height: 300,
                type: "svg",
                data: data,
                image: "https://media.berbagibitesjogja.com/logo_transparan.png",
                dotsOptions: { color: "#1A446D", type: "rounded" },
                backgroundOptions: { color: "#FFFFFF" },
                imageOptions: { crossOrigin: "anonymous", margin: 10 },
            });
            qrCode.append(document.getElementById("canvas"));
        </script>

    @elseif (array_keys(request()->query())[0] == 'datat')
        <div class="min-h-[500px] flex flex-col items-center justify-center text-center p-8">
            {{-- Loading State --}}
            <div id="loading" class="animate-fade-in-up">
                <div class="w-24 h-24 bg-navy-50 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                    <svg class="w-12 h-12 text-navy-200 animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1A446D" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-navy-900 mb-2">Memproses Presensi</h1>
                <p class="text-navy-500">Mohon tunggu sebentar...</p>
            </div>

            {{-- Success State --}}
            <div id="success" class="hidden animate-scale-in">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-green-700 mb-2">Presensi Berhasil!</h1>
                <p class="text-green-600">Terima kasih telah hadir.</p>
            </div>

            {{-- Failed State --}}
            <div id="failed" class="hidden animate-scale-in">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-red-700 mb-2">Presensi Gagal</h1>
                <p class="text-red-600">Silakan coba lagi atau hubungi admin.</p>
            </div>
        </div>

        <script type="module">
            import QrScanner from 'https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/+esm'
            let userLat;
            let userLong;
            navigator.geolocation.getCurrentPosition(res => {
                userLat = res.coords.latitude
                userLong = res.coords.longitude
                let scanned = `{{ request()->query('datat') }}`
                scanned = scanned.split('!')
                const data = {
                    precenceLat: scanned[0], precenceLong: scanned[2], precenceCode: scanned[1],
                    userLat, userLong
                }
                sendData(data);
            })

            function sendData(data) {
                fetch('/abcence/distance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    document.querySelector('#loading').classList.add('hidden')
                    if (!response.ok) {
                        document.querySelector('#failed').classList.remove('hidden')
                    } else {
                        document.querySelector('#success').classList.remove('hidden')
                    }
                    setTimeout(() => {
                        window.location.href = `{{ env('APP_URL', 'https://app.berbagibitesjogja.site') }}`
                    }, 1500)
                })
            }
        </script>

    @else
        {{-- Scanner UI --}}
        <div class="fixed inset-0 bg-black z-50 flex flex-col">
            <div class="relative flex-1 bg-black overflow-hidden">
                <video class="absolute inset-0 w-full h-full object-cover"></video>
                <div class="absolute inset-0 border-[60px] border-black/50 pointer-events-none grid place-items-center">
                    <div class="w-64 h-64 border-4 border-tosca-400 rounded-3xl relative">
                        <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-white -mt-1 -ml-1"></div>
                        <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-white -mt-1 -mr-1"></div>
                        <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-white -mb-1 -ml-1"></div>
                        <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-white -mb-1 -mr-1"></div>
                    </div>
                </div>
                <div class="absolute top-8 left-0 w-full text-center">
                     <h2 class="text-white font-bold text-lg drop-shadow-md">Scan QR Code</h2>
                     <p class="text-white/80 text-sm">Arahkan kamera ke QR Code presensi</p>
                </div>
            </div>
            
            <div class="bg-navy-900 p-6 flex flex-col items-center gap-4 rounded-t-3xl -mt-6 relative z-10">
                <div id="loading" class="hidden flex items-center gap-3 text-white">
                    <svg class="w-5 h-5 animate-spin text-tosca-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Memproses...</span>
                </div>
                
                <div id="success" class="hidden text-green-400 font-bold flex items-center gap-2">
                    ✅ <span>Berhasil! Mengalihkan...</span>
                </div>

                <div id="failed" class="hidden text-red-400 font-bold flex items-center gap-2">
                    ❌ <span>Gagal. Coba lagi.</span>
                </div>

                <a href="{{ url()->previous() }}" class="text-white/60 text-sm font-medium hover:text-white transition-colors">
                    Batal
                </a>
            </div>
        </div>

        <script type="module">
            import QrScanner from 'https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/+esm'
            let userLat = 0;
            let userLong = 0;
            
            navigator.geolocation.getCurrentPosition(
                res => {
                    userLat = res.coords.latitude
                    userLong = res.coords.longitude
                },
                err => console.error(err)
            )

            const videoElem = document.querySelector('video');
            const qrScanner = new QrScanner(
                videoElem,
                (result) => {
                    document.querySelector('#loading').classList.remove('hidden')
                    qrScanner.stop()
                    
                    try {
                        let scanned = result.data;
                        const startIndex = scanned.indexOf('datat=') + 6
                        if(startIndex < 6) throw new Error("Invalid QR");
                        
                        scanned = scanned.substring(startIndex)
                        scanned = scanned.split('!')
                        
                        const data = {
                            precenceLat: scanned[0],
                            precenceCode: scanned[1],
                            precenceLong: scanned[2],
                            userLat: userLat,
                            userLong: userLong
                        }
                        
                        fetch('/abcence/distance', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => {
                            document.querySelector('#loading').classList.add('hidden')
                            if (!response.ok) {
                                document.querySelector('#failed').classList.remove('hidden')
                                setTimeout(() => qrScanner.start(), 2000) // Retry
                            } else {
                                document.querySelector('#success').classList.remove('hidden')
                                setTimeout(() => {
                                    window.location.href = `{{ env('APP_URL', 'https://app.berbagibitesjogja.site') }}`
                                }, 1500)
                            }
                        })
                        .catch(() => {
                            document.querySelector('#loading').classList.add('hidden')
                            document.querySelector('#failed').classList.remove('hidden')
                        })
                    } catch(e) {
                         console.error(e);
                         document.querySelector('#loading').classList.add('hidden')
                         document.querySelector('#failed').classList.remove('hidden')
                    }
                }, {
                    maxScansPerSecond: 1,
                    highlightScanRegion: true,
                    highlightCodeOutline: true,
                }
            );
            qrScanner.start();
        </script>
    @endif
@endsection
