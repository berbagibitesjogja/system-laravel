@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('hero.index') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
        <div class="px-4 py-2 bg-navy-500 text-white rounded-xl shadow-md font-semibold">
            Heroes: {{ $heroes->total() }} Orang
        </div>
    </div>
    
    @if($heroes->count() > 0)
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-navy-900">{{ $heroes[0]->faculty->name }}</h1>
            <p class="text-navy-400">{{ $heroes[0]->faculty->university->name }}</p>
        </div>
    @endif
    
    <div class="mb-6">
        {{ $heroes->links() }}
    </div>
    
    <x-table>
        <x-slot:head>
            <x-th>Nama</x-th>
            <x-th class="hidden sm:table-cell">Fakultas</x-th>
            <x-th>Donasi</x-th>
        </x-slot:head>
        <x-slot:body>
            @foreach ($heroes as $item)
                @php
                    $donation = $item->donation;
                @endphp
                <x-tr>
                    <x-td>
                        <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <a href="{{ route('hero.faculty', $item->faculty) }}" class="hover:text-tosca-600 transition-colors">
                            <p class="font-medium">{{ $item->faculty->name }}</p>
                            @if ($item->quantity > 1)
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-lime-400 text-navy-900">
                                    {{ $item->quantity }} Orang
                                </span>
                            @endif
                        </a>
                    </x-td>
                    <x-td>
                        <a href="{{ route('donation.show', $donation->id) }}" class="hover:text-tosca-600 transition-colors">
                            <p class="font-medium text-navy-900">{{ $donation->sponsor->name }}</p>
                            <p class="text-sm text-navy-400">{{ \Carbon\Carbon::parse($donation->take)->isoFormat('dddd, DD MMMM Y') }}</p>
                        </a>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot:body>
    </x-table>
@endsection
