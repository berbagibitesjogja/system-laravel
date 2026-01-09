@extends('layouts.main')
@section('container')
    @auth
        <button onclick="addHeroes()" class="bg-navy hover:bg-navy-700 p-2 text-white rounded-md shadow-md transition duration-300">
            + Kontributor
        </button>
    @endauth
    <div class="mt-6">
        <div>
            {{ $heroes->links() }}
        </div>
        <x-table class="mt-12">
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
                        <th scope="row" class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $item->name }}
                            <span class="md:hidden italic font-normal text-gray-500 block">
                                <a href="{{ route('hero.faculty', $item->faculty_id) }}">
                                    {{ $item->faculty->name }}
                                    @if ($item->quantity > 1)
                                        ({{ $item->quantity }} Orang)
                                    @endif
                                </a>
                            </span>
                        </th>
                        @auth
                            <td class="px-2 py-4 hidden sm:table-cell">
                                <a href="{{ route('hero.faculty', $item->faculty_id) }}">
                                    {{ $item->faculty->name }} <br> ({{ $item->faculty->university->name }})
                                    @if ($item->quantity > 1)
                                        ({{ $item->quantity }} Orang)
                                    @endif
                                </a>
                            </td>
                        @endauth
                        <td class="px-2 py-4 flex flex-col">
                            <a href="{{ route('donation.show', $item->donation->id) }}" class="block">
                                <span class="block">
                                    {{ $item->donation->sponsor->name }}
                                </span>
                                {{ \Carbon\Carbon::parse($item->donation->take)->isoFormat('dddd, DD MMMM Y') }}
                            </a>
                        </td>
                    </x-tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
    <div id="popup" class="hidden w-full h-full absolute left-0 top-0 z-50 bg-navy bg-opacity-50 pt-12">
        <form method="POST" action="{{ route('hero.contributor') }}"
            class="max-w-md mx-auto shadow-md px-10 bg-white  py-6 rounded-md transform transition-all duration-300 opacity-0 ease-in-out translate-y-[-20px] scale-95">
            @csrf
            
            <x-select name="donation_id" label="Pilih Donasi">
                <option value="">Donasi</option>
                @foreach ($donations as $item)
                    <option value="{{ $item->id }}">{{ $item->sponsor->name }}</option>
                @endforeach
            </x-select>
            
            <x-select name="faculty_id" label="Asal Kontributor">
                 <option value="">Asal</option>
                @foreach ($faculties as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </x-select>
            
            <x-input name="name" label="Nama Kontributor" required />
            <x-input name="quantity" label="Jumlah" type="number" required />

            <x-btn type="button" onclick="remove()" variant="pink">Batal</x-btn>
            <x-btn type="submit" variant="primary">Submit</x-btn>
        </form>
    </div>
    <script>
        function addHeroes() {
            document.querySelector('#popup').classList.remove('hidden')
            document.querySelector('#popup>form').classList.remove('opacity-0', 'translate-y-[-20px]',
                'scale-95');
            document.querySelector('#popup>form').classList.add('opacity-100', 'translate-y-0', 'scale-100');
        }

        function remove() {
            document.querySelector('#popup').classList.add('hidden')
            document.querySelector('#popup>form').reset()
        }
    </script>
@endsection
