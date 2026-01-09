@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <x-btn-link href="{{ route('hero.index') }}" variant="navy">
            ← Heroes
        </x-btn-link>
    </div>
    
    <div class="mb-6">
        {{ $backups->links() }}
    </div>
    
    <x-table>
        <x-slot:head>
            <x-th>Nama</x-th>
            <x-th class="hidden sm:table-cell">Fakultas</x-th>
            <x-th class="hidden sm:table-cell">Telepon</x-th>
            <x-th class="hidden sm:table-cell">Donasi</x-th>
            <x-th class="text-center">Aksi</x-th>
        </x-slot:head>
        <x-slot:body>
            @foreach ($backups as $item)
                @php
                    $donation = $item->donation;
                    $sponsor = $donation->sponsor;
                @endphp
                <x-tr>
                    <x-td>
                        <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <span class="text-navy-600">{{ $item->faculty->name }}</span>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <span class="text-navy-400">{{ $item->phone }}</span>
                    </x-td>
                    <x-td class="hidden sm:table-cell">
                        <a href="{{ route('sponsor.show', $sponsor->id) }}" class="text-tosca-500 hover:text-tosca-600 transition-colors font-medium block">
                            {{ $sponsor->name }}
                        </a>
                        <a href="{{ route('donation.show', $donation->id) }}" class="text-sm text-navy-400 hover:text-navy-500 transition-colors">
                            {{ $donation->take }}
                        </a>
                    </x-td>
                    <x-td>
                        <div class="flex justify-center gap-2">
                            <x-btn-link href="{{ route('hero.restore', $item->id) }}" variant="yellow" class="!p-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                                </svg>
                            </x-btn-link>
                            <form action="{{ route('hero.trash', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-btn type="submit" variant="danger" class="!p-2">
                                    <svg width="16" height="16" viewBox="0 0 18 17" fill="currentColor">
                                        <path d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 0.779068 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"/>
                                    </svg>
                                </x-btn>
                            </form>
                        </div>
                    </x-td>
                </x-tr>
            @endforeach
        </x-slot:body>
    </x-table>
@endsection
