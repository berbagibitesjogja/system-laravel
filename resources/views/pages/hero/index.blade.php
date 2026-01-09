@extends('layouts.main')
@section('container')
    @auth
        <div class="flex flex-wrap gap-3 mb-6">
            <x-btn type="button" onclick="addHeroes()" variant="primary">
                + Kontributor
            </x-btn>
        </div>
    @endauth
    
    <div class="mb-6">
        {{ $heroes->links() }}
    </div>
    
    <x-table>
        <x-slot:head>
            <x-th>Nama</x-th>
            @auth
                <x-th class="hidden sm:table-cell">Asal</x-th>
            @endauth
            <x-th>Donasi</x-th>
        </x-slot:head>
        <x-slot:body>
            @foreach ($heroes as $item)
                <x-tr>
                    <x-td>
                        <div>
                            <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                            <a href="{{ route('hero.faculty', $item->faculty_id) }}" class="md:hidden text-sm text-tosca-500 hover:text-tosca-600 transition-colors">
                                {{ $item->faculty->name }}
                                @if ($item->quantity > 1)
                                    <span class="text-navy-400">({{ $item->quantity }} Orang)</span>
                                @endif
                            </a>
                        </div>
                    </x-td>
                    @auth
                        <x-td class="hidden sm:table-cell">
                            <a href="{{ route('hero.faculty', $item->faculty_id) }}" class="hover:text-tosca-600 transition-colors">
                                <p class="font-medium">{{ $item->faculty->name }}</p>
                                <p class="text-sm text-navy-400">({{ $item->faculty->university->name }})</p>
                                @if ($item->quantity > 1)
                                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-lime-400 text-navy-900">
                                        {{ $item->quantity }} Orang
                                    </span>
                                @endif
                            </a>
                        </x-td>
                    @endauth
                    <x-td>
                        <a href="{{ route('donation.show', $item->donation->id) }}" class="hover:text-tosca-600 transition-colors">
                            <p class="font-medium text-navy-900">{{ $item->donation->sponsor->name }}</p>
                            <p class="text-sm text-navy-400">{{ \Carbon\Carbon::parse($item->donation->take)->isoFormat('dddd, DD MMMM Y') }}</p>
                        </a>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot:body>
    </x-table>

    <div id="popup" class="hidden fixed inset-0 z-50 bg-navy-900/50 backdrop-blur-sm flex items-start justify-center pt-12 px-4">
        <form method="POST" action="{{ route('hero.contributor') }}"
            class="w-full max-w-md bg-white shadow-2xl rounded-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="popupForm">
            @csrf
            
            <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-center text-white">
                <h2 class="text-xl font-bold">Tambah Kontributor</h2>
                <p class="text-sm text-white/80 mt-1">Catat penerima makanan baru</p>
            </div>
            
            <div class="p-6 space-y-4">
                <x-select name="donation_id" label="Pilih Donasi">
                    <option value="">Pilih Donasi</option>
                    @foreach ($donations as $item)
                        <option value="{{ $item->id }}">{{ $item->sponsor->name }}</option>
                    @endforeach
                </x-select>
                
                <x-select name="faculty_id" label="Asal Kontributor">
                    <option value="">Pilih Asal</option>
                    @foreach ($faculties as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </x-select>
                
                <x-input name="name" label="Nama Kontributor" required />
                <x-input name="quantity" label="Jumlah Orang" type="number" required />

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
        function addHeroes() {
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
