@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('beneficiary.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
    </div>

    <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6 mb-8">
        <h1 class="text-2xl font-bold text-navy-900 mb-2">{{ $beneficiary->name }}</h1>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
            <div class="bg-gradient-to-br from-white to-tosca-50 p-4 rounded-xl border border-navy-100">
                <p class="text-sm text-navy-400">Jumlah Penerima</p>
                <p class="text-2xl font-bold text-tosca-600">{{ $beneficiary->heroes->sum('quantity') }} <span class="text-sm text-navy-400">Orang</span></p>
            </div>
            <div class="bg-gradient-to-br from-white to-lime-50 p-4 rounded-xl border border-navy-100">
                <p class="text-sm text-navy-400">Makanan Diterima</p>
                <p class="text-2xl font-bold text-lime-600">{{ ceil($beneficiary->foods() / 1000) }} <span class="text-sm text-navy-400">Kg</span></p>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-bold text-navy-900 mb-4">Donasi Untuk {{ $beneficiary->name }}</h2>
    
    <x-table>
        <x-slot:head>
            <x-th>Tanggal</x-th>
            <x-th class="hidden sm:table-cell">Penerima</x-th>
            <x-th class="hidden sm:table-cell">Donatur</x-th>
            <x-th class="hidden sm:table-cell">Makanan</x-th>
            <x-th class="text-center">Aksi</x-th>
        </x-slot:head>
        <x-slot:body>
            @foreach ($donations->sortBy('take') as $item)
                <x-tr>
                    <x-td>
                        <div>
                            <p class="font-medium text-navy-900">{{ \Carbon\Carbon::parse($item->take)->isoFormat('dddd') }}</p>
                            <p class="text-sm text-navy-400">{{ \Carbon\Carbon::parse($item->take)->isoFormat('D MMMM Y') }}</p>
                            <p class="sm:hidden text-sm text-tosca-500 mt-1">{{ $item->quota - $item->remain }} Orang</p>
                        </div>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <span class="font-semibold text-tosca-600">{{ $item->quota - $item->remain }}</span>
                        <span class="text-navy-400">Orang</span>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <span class="font-medium text-navy-700">{{ $item->sponsor->name }}</span>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <span class="font-semibold text-lime-600">{{ ceil($item->foods->sum('weight') / 1000) }}</span>
                        <span class="text-navy-400">Kg</span>
                    </x-td>
                    <x-td>
                        <div class="flex justify-center">
                            <x-btn-link href="{{ route('donation.show', $item->id) }}" variant="tosca" class="!p-2">
                                <svg width="18" height="14" viewBox="0 0 20 15" fill="currentColor">
                                    <path d="M9.99972 0C14.4931 0 18.2314 3.23333 19.0156 7.5C18.2322 11.7667 14.4931 15 9.99972 15C5.50639 15 1.76805 11.7667 0.983887 7.5C1.76722 3.23333 5.50639 0 9.99972 0ZM9.99972 13.3333C11.6993 13.333 13.3484 12.7557 14.6771 11.696C16.0058 10.6363 16.9355 9.15689 17.3139 7.5C16.9341 5.84442 16.0038 4.36667 14.6752 3.30835C13.3466 2.25004 11.6983 1.67377 9.99972 1.67377C8.30113 1.67377 6.65279 2.25004 5.3242 3.30835C3.9956 4.36667 3.06536 5.84442 2.68555 7.5C3.06397 9.15689 3.99361 10.6363 5.32234 11.696C6.65106 12.7557 8.30016 13.333 9.99972 13.3333V13.3333ZM9.99972 11.25C9.00516 11.25 8.05133 10.8549 7.34807 10.1516C6.64481 9.44839 6.24972 8.49456 6.24972 7.5C6.24972 6.50544 6.64481 5.55161 7.34807 4.84835C8.05133 4.14509 9.00516 3.75 9.99972 3.75C10.9943 3.75 11.9481 4.14509 12.6514 4.84835C13.3546 5.55161 13.7497 6.50544 13.7497 7.5C13.7497 8.49456 13.3546 9.44839 12.6514 10.1516C11.9481 10.8549 10.9943 11.25 9.99972 11.25Z"/>
                                </svg>
                            </x-btn-link>
                        </div>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot:body>
    </x-table>
    
    <div class="mt-6">
        {{ $donations->links() }}
    </div>

    @if ($beneficiary->variant != 'foundation')
        <div class="mt-12">
            <div class="flex flex-wrap gap-3 items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-navy-900">Persebaran Di {{ $beneficiary->name }}</h2>
                @if (auth()->user()->role == 'super')
                    <x-btn type="button" onclick="addSector()" variant="success">
                        + Tambah Sektor
                    </x-btn>
                @endif
            </div>
            
            <x-table>
                <x-slot:head>
                    <x-th>Nama</x-th>
                    <x-th class="text-center">Jumlah</x-th>
                    <x-th class="text-center">Aksi</x-th>
                </x-slot:head>
                <x-slot:body>
                    @foreach ($faculties as $item)
                        <x-tr>
                            <x-td>
                                <p class="font-medium text-navy-900">{{ $item->name }}</p>
                            </x-td>
                            <x-td class="text-center">
                                <span class="font-semibold text-tosca-600">{{ $item->heroes->sum('quantity') }}</span>
                                <span class="text-navy-400">Orang</span>
                            </x-td>
                            <x-td>
                                <div class="flex justify-center">
                                    <x-btn-link href="{{ route('hero.faculty', $item->id) }}" variant="tosca" class="!p-2">
                                        <svg width="18" height="14" viewBox="0 0 20 15" fill="currentColor">
                                            <path d="M9.99972 0C14.4931 0 18.2314 3.23333 19.0156 7.5C18.2322 11.7667 14.4931 15 9.99972 15C5.50639 15 1.76805 11.7667 0.983887 7.5C1.76722 3.23333 5.50639 0 9.99972 0ZM9.99972 13.3333C11.6993 13.333 13.3484 12.7557 14.6771 11.696C16.0058 10.6363 16.9355 9.15689 17.3139 7.5C16.9341 5.84442 16.0038 4.36667 14.6752 3.30835C13.3466 2.25004 11.6983 1.67377 9.99972 1.67377C8.30113 1.67377 6.65279 2.25004 5.3242 3.30835C3.9956 4.36667 3.06536 5.84442 2.68555 7.5C3.06397 9.15689 3.99361 10.6363 5.32234 11.696C6.65106 12.7557 8.30016 13.333 9.99972 13.3333V13.3333ZM9.99972 11.25C9.00516 11.25 8.05133 10.8549 7.34807 10.1516C6.64481 9.44839 6.24972 8.49456 6.24972 7.5C6.24972 6.50544 6.64481 5.55161 7.34807 4.84835C8.05133 4.14509 9.00516 3.75 9.99972 3.75C10.9943 3.75 11.9481 4.14509 12.6514 4.84835C13.3546 5.55161 13.7497 6.50544 13.7497 7.5C13.7497 8.49456 13.3546 9.44839 12.6514 10.1516C11.9481 10.8549 10.9943 11.25 9.99972 11.25Z"/>
                                        </svg>
                                    </x-btn-link>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforeach
                </x-slot:body>
            </x-table>
        </div>
        
        <div class="mt-6">
            {{ $faculties->links() }}
        </div>
    @endif

    <div id="popup" class="hidden fixed inset-0 z-50 bg-navy-900/50 backdrop-blur-sm flex items-start justify-center pt-12 px-4">
        <form method="POST" action="{{ route('beneficiary.store') }}"
            class="w-full max-w-md bg-white shadow-2xl rounded-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="popupForm">
            @csrf
            <input type="hidden" name="university_id" value="{{ $beneficiary->id }}">
            
            <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white">
                <h2 class="text-xl font-bold">Tambah Sektor</h2>
                <p class="text-sm text-white/80 mt-1">Tambahkan sektor baru untuk {{ $beneficiary->name }}</p>
            </div>
            
            <div class="p-6">
                <x-input name="name" label="Nama Sektor" required />

                <div class="flex gap-3 pt-4">
                    <x-btn type="button" onclick="closePopup()" variant="ghost" class="flex-1">
                        Batal
                    </x-btn>
                    <x-btn type="submit" variant="primary" class="flex-1">
                        Simpan
                    </x-btn>
                </div>
            </div>
        </form>
    </div>
    
    <script>
        function addSector() {
            document.querySelector('#popup').classList.remove('hidden');
            setTimeout(() => {
                document.querySelector('#popupForm').classList.remove('scale-95', 'opacity-0');
                document.querySelector('#popupForm').classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closePopup() {
            document.querySelector('#popupForm').classList.add('scale-95', 'opacity-0');
            document.querySelector('#popupForm').classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                document.querySelector('#popup').classList.add('hidden');
                document.querySelector('#popupForm').reset();
            }, 300);
        }
        
        document.querySelector('#popup').addEventListener('click', function(e) {
            if (e.target === this) {
                closePopup();
            }
        });
    </script>
@endsection
