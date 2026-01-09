@extends('layouts.main')
@section('container')
    <div class="flex flex-wrap gap-3 items-center">
        <x-btn-link href="{{ route('volunteer.home') }}" variant="ghost">
            ← Kembali
        </x-btn-link>
        @if (auth()->user()->role == 'super')
            <x-btn-link href="{{ route('volunteer.create') }}" variant="success">
                + Tambah Volunteer
            </x-btn-link>
        @endif
    </div>
    
    <div class="mt-8">
        <x-table>
            <x-slot:head>
                <x-th>Nama</x-th>
                <x-th class="hidden sm:table-cell">Divisi</x-th>
                <x-th class="hidden sm:table-cell">Kontribusi</x-th>
                @if (auth()->user()->role == 'super')
                    <x-th class="hidden sm:table-cell text-center">Action</x-th>
                @endif
            </x-slot:head>
            <x-slot:body>
                @foreach ($users as $item)
                    <x-tr>
                        <x-td>
                            <div>
                                <p class="font-semibold text-navy-900">{{ $item->name }}</p>
                                <a href="https://wa.me/{{ $item->phone }}" class="text-sm text-tosca-500 hover:text-tosca-600 transition-colors">
                                    {{ $item->phone }}
                                </a>
                            </div>
                        </x-td>
                        <x-td class="hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-navy-100 text-navy-700">
                                {{ $item->division->name }}
                            </span>
                            <span class="text-navy-400 text-xs ml-1">({{ $item->role }})</span>
                        </x-td>
                        <x-td class="hidden sm:table-cell">
                            <span class="font-semibold text-tosca-600">{{ $item->attendances->count() }}</span>
                            <span class="text-navy-400">aksi</span>
                        </x-td>
                        @if (auth()->user()->role == 'super')
                            <x-td class="hidden sm:table-cell">
                                <div class="flex justify-center gap-2">
                                    <x-btn-link href="{{ route('volunteer.show', $item->id) }}" variant="tosca" class="!p-2">
                                        <svg width="18" height="14" viewBox="0 0 20 15" fill="currentColor">
                                            <path d="M9.99972 0C14.4931 0 18.2314 3.23333 19.0156 7.5C18.2322 11.7667 14.4931 15 9.99972 15C5.50639 15 1.76805 11.7667 0.983887 7.5C1.76722 3.23333 5.50639 0 9.99972 0ZM9.99972 13.3333C11.6993 13.333 13.3484 12.7557 14.6771 11.696C16.0058 10.6363 16.9355 9.15689 17.3139 7.5C16.9341 5.84442 16.0038 4.36667 14.6752 3.30835C13.3466 2.25004 11.6983 1.67377 9.99972 1.67377C8.30113 1.67377 6.65279 2.25004 5.3242 3.30835C3.9956 4.36667 3.06536 5.84442 2.68555 7.5C3.06397 9.15689 3.99361 10.6363 5.32234 11.696C6.65106 12.7557 8.30016 13.333 9.99972 13.3333V13.3333ZM9.99972 11.25C9.00516 11.25 8.05133 10.8549 7.34807 10.1516C6.64481 9.44839 6.24972 8.49456 6.24972 7.5C6.24972 6.50544 6.64481 5.55161 7.34807 4.84835C8.05133 4.14509 9.00516 3.75 9.99972 3.75C10.9943 3.75 11.9481 4.14509 12.6514 4.84835C13.3546 5.55161 13.7497 6.50544 13.7497 7.5C13.7497 8.49456 13.3546 9.44839 12.6514 10.1516C11.9481 10.8549 10.9943 11.25 9.99972 11.25Z"/>
                                        </svg>
                                    </x-btn-link>
                                    <form action="{{ route('volunteer.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-btn type="submit" variant="danger" class="!p-2" onclick="return confirm('Yakin ingin menghapus?')">
                                            <svg width="16" height="16" viewBox="0 0 18 17" fill="currentColor">
                                                <path d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 0.779068 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"/>
                                            </svg>
                                        </x-btn>
                                    </form>
                                </div>
                            </x-td>
                        @endif
                    </x-tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
