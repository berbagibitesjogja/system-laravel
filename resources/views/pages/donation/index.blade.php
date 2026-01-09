@extends('layouts.main')
@section('container')
    @auth
        @if (in_array(auth()->user()->role, ['core', 'super']) || auth()->user()->division->name == 'Food')
            <x-btn-link href="{{ route('donation.create') }}" variant="success">
                + Tambah
            </x-btn-link>
        @endif
        <div class="mt-3 flex gap-x-2">
            @if (request()->route()->getName() == 'donation.index')
                <x-btn-link href="{{ route('donation.charity') }}" variant="warning">
                    CSR Only
                </x-btn-link>
                <x-btn-link href="{{ route('donation.rescue') }}" variant="navy">
                    Food Rescue Only
                </x-btn-link>
            @else
                <x-btn-link href="{{ route('donation.index') }}" variant="navy">
                    All Action
                </x-btn-link>
            @endif
        </div>

    @endauth
    <div class="mt-6">
        <div>
            {{ $donations->links() }}
        </div>
        <x-table class="mt-6">
            <x-slot:head>
                 <x-th class="hidden sm:table-cell">Tanggal</x-th>
                 <x-th>Nama Donatur</x-th>
                 @auth
                    <x-th class="hidden sm:table-cell">Kuota</x-th>
                 @else
                    <x-th class="hidden sm:table-cell">Penyelamat Makanan</x-th>
                 @endauth
                 <x-th class="hidden sm:table-cell">Status</x-th>
                 @auth
                    <x-th class="text-center"></x-th>
                 @endauth
            </x-slot:head>
            <x-slot:body>
                @foreach ($donations as $item)
                    @if (auth()->user() || !$item->sponsor->hidden)
                        <x-tr class="text-center">
                            <td class="px-2 py-4 hidden sm:table-cell">
                                {{ \Carbon\Carbon::parse($item->take)->isoFormat('dddd, D MMMM Y') }}
                            </td>
                            <th scope="row" class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $item->sponsor->name }}
                                <span class="md:hidden italic font-normal text-gray-500 block">
                                    @auth
                                        {{ \Carbon\Carbon::parse($item->take)->isoFormat('dddd, D MMMM Y') }}
                                    @else
                                        {{ $item->quota - $item->remain }} Orang
                                    @endauth
                                </span>
                                <span class="md:hidden italic font-normal text-gray-500 block">
                                    {{ $item->status }}
                                </span>
                            </th>
                            <td class="px-2 py-4 hidden sm:table-cell">
                                @auth
                                    @if ($item->partner_id)
                                        {{ $item->partner->quota - $item->partner->remain }}/{{ $item->partner->quota }}
                                    @else
                                        {{ $item->quota - $item->remain }}/{{ $item->quota }}
                                    @endif
                                @else
                                    @if ($item->partner_id)
                                        {{ $item->partner->quota - $item->partner->remain }} Orang
                                    @else
                                        {{ $item->quota - $item->remain }} Orang
                                    @endif
                                @endauth
                            </td>
                            <td class="px-2 py-4 hidden sm:table-cell">
                                {{ $item->status }}
                            </td>
                            @auth
                                <td class="px-2 py-4 flex justify-center gap-2">
                                    <x-btn-link href="{{ route('donation.show', $item->id) }}" variant="tosca">
                                        <svg width="20" height="15" viewBox="0 0 20 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.99972 0C14.4931 0 18.2314 3.23333 19.0156 7.5C18.2322 11.7667 14.4931 15 9.99972 15C5.50639 15 1.76805 11.7667 0.983887 7.5C1.76722 3.23333 5.50639 0 9.99972 0ZM9.99972 13.3333C11.6993 13.333 13.3484 12.7557 14.6771 11.696C16.0058 10.6363 16.9355 9.15689 17.3139 7.5C16.9341 5.84442 16.0038 4.36667 14.6752 3.30835C13.3466 2.25004 11.6983 1.67377 9.99972 1.67377C8.30113 1.67377 6.65279 2.25004 5.3242 3.30835C3.9956 4.36667 3.06536 5.84442 2.68555 7.5C3.06397 9.15689 3.99361 10.6363 5.32234 11.696C6.65106 12.7557 8.30016 13.333 9.99972 13.3333V13.3333ZM9.99972 11.25C9.00516 11.25 8.05133 10.8549 7.34807 10.1516C6.64481 9.44839 6.24972 8.49456 6.24972 7.5C6.24972 6.50544 6.64481 5.55161 7.34807 4.84835C8.05133 4.14509 9.00516 3.75 9.99972 3.75C10.9943 3.75 11.9481 4.14509 12.6514 4.84835C13.3546 5.55161 13.7497 6.50544 13.7497 7.5C13.7497 8.49456 13.3546 9.44839 12.6514 10.1516C11.9481 10.8549 10.9943 11.25 9.99972 11.25ZM9.99972 9.58333C10.5523 9.58333 11.0822 9.36384 11.4729 8.97314C11.8636 8.58244 12.0831 8.05253 12.0831 7.5C12.0831 6.94747 11.8636 6.41756 11.4729 6.02686C11.0822 5.63616 10.5523 5.41667 9.99972 5.41667C9.44719 5.41667 8.91728 5.63616 8.52658 6.02686C8.13588 6.41756 7.91639 6.94747 7.91639 7.5C7.91639 8.05253 8.13588 8.58244 8.52658 8.97314C8.91728 9.36384 9.44719 9.58333 9.99972 9.58333Z"
                                                fill="white" />
                                        </svg>
                                    </x-btn-link>
                                    @if (in_array(auth()->user()->role, ['core', 'super']) || auth()->user()->division->name == 'Food')
                                        <x-btn-link href="{{ route('donation.edit', $item->id) }}" variant="yellow">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M2.58333 15.4167H3.88958L12.85 6.45625L11.5438 5.15L2.58333 14.1104V15.4167ZM0.75 17.25V13.3542L12.85 1.27708C13.0333 1.10903 13.2358 0.979167 13.4573 0.8875C13.6788 0.795833 13.9118 0.75 14.1563 0.75C14.4007 0.75 14.6375 0.795833 14.8667 0.8875C15.0958 0.979167 15.2944 1.11667 15.4625 1.3L16.7229 2.58333C16.9063 2.75139 17.0399 2.95 17.124 3.17917C17.208 3.40833 17.25 3.6375 17.25 3.86667C17.25 4.11111 17.208 4.3441 17.124 4.56562C17.0399 4.78715 16.9063 4.98958 16.7229 5.17292L4.64583 17.25H0.75ZM12.1854 5.81458L11.5438 5.15L12.85 6.45625L12.1854 5.81458Z"
                                                    fill="white" />
                                            </svg>
                                        </x-btn-link>
                                        @if (auth()->user()->role == 'super')
                                            <form action="{{ route('donation.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-md bg-red-300 hover:bg-red-600 transition duration-300">
                                                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 0.779068 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"
                                                            fill="white" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            @endauth
                        </x-tr>
                    @endif
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
@endsection
