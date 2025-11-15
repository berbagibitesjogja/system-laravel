@extends('layouts.main')
@section('container')
    <h1 class="text-center text-xl font-bold">Daftar Heroes {{ $donation->sponsor->name }}</h1>
    <h1 class="text-center text-sm italic">{{ \Carbon\Carbon::parse($donation->take)->isoFormat('dddd, D MMMM Y') }}</h1>
    <div class="mt-3 flex gap-3 w-full flex-row flex-wrap">
        <a class="bg-orange-400 hover:bg-orange-600 shadow-md p-2 rounded-md text-white" href="{{ route('donation.index') }}">
            < Kembali</a>
                @if ($donation->partner_id)
                    <div class="w-max bg-navy-500 py-2 px-4 text-white rounded-md shadow-md">
                        Heroes : {{ $donation->partner->quota - $donation->partner->remain }} /
                        {{ $donation->partner->quota }}
                    </div>
                @else
                    <div class="w-max bg-navy-500 py-2 px-4 text-white rounded-md shadow-md">
                        Heroes : {{ $donation->quota - $donation->remain }} /
                        {{ $donation->quota }}
                    </div>
                @endif
                @if ($donation->media)
                    <a class="bg-navy-500 hover:bg-navy-600 shadow-md p-2 rounded-md text-white"
                        href="{{ $donation->media }}">
                        Dokumentasi</a>
                @endif
    </div>
    <div class="mt-10 shadow-md sm:rounded-lg p-6">
        <form method="POST" action="{{ route('donation.update', $donation->id) }}">
            @csrf
            @method('PUT')
            @if ($donation->partners->count() > 0)
                <h1 class="mb-3">Partner</h1>
                @foreach ($donation->partners as $item)
                    <a class="bg-navy-500 hover:bg-navy-600 shadow-md p-2 rounded-md text-white"
                        href="{{ route('donation.show', $item->id) }}">
                        {{ $item->sponsor->name }}</a>
                @endforeach
            @endif
            @if ($donation->quota == 0)
                <h1>Partner</h1>
                <select id="partner" name="partner_id"
                    class="mb-5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">Donasi Partner</option>
                    @foreach ($donations as $item)
                        <option {{ $item->id == $donation->partner_id ? 'selected' : '' }} value="{{ $item->id }}">
                            {{ $item->sponsor->name }}
                            {{ \Carbon\Carbon::parse($item->take)->isoFormat('dddd, DD MMMM Y') }}</option>
                    @endforeach
                </select>
                @if ($donation->partner_id)
                    <a class="bg-navy-500 hover:bg-navy-600 shadow-md p-2 rounded-md text-white"
                        href="{{ route('donation.show', $donation->partner_id) }}">
                        Lihat Partner</a>
                @endif
            @endif
            <h1 class="text-xl my-3">Catatan</h1>
            @if ($donation->notes)
                <textarea class="focus:outline-none w-full rounded-lg" name="notes" rows="3" placeholder="Catatan....">{{ $donation->notes }}</textarea>
            @else
                <textarea class="focus:outline-none w-full rounded-lg" name="notes" rows="3" placeholder="Catatan...."></textarea>
            @endif
            <button type="submit" class="bg-navy-500 hover:bg-navy-700 rounded-md p-2 text-white">Submit</button>
        </form>
    </div>
    <div class="mt-10 shadow-md sm:rounded-lg">
        <table class="text-center w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 hidden sm:table-cell">
                        No
                    </th>
                    <th scope="col" class="px-1 sm:px-6 py-3">
                        Nama
                    </th>
                    <th scope="col" class="hidden sm:table-cell px-6 py-3">
                        Asal
                    </th>
                    <th scope="col" class="px-6 py-3 hidden sm:table-cell">
                        {{ $donation->status == 'aktif' ? 'Telepon' : 'Kode' }}
                    </th>
                    @if ($heroes->where('status', 'belum')->count() > 0)
                        <th scope="col" class="px-0 sm:px-6 py-3 text-center">

                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($heroes as $number => $item)
                    <tr class="odd:bg-white">
                        <td class="hidden sm:table-cell">{{ $number + 1 }}</td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $item->name }}

                            <span class="md:hidden italic font-normal text-gray-500 block">
                                {{ $item->faculty->name }}
                                @if ($item->quantity > 1)
                                    ({{ $item->quantity }} Orang)
                                @endif
                            </span>
                            <span class="md:hidden italic font-normal text-gray-500 block">
                                {{ $item->code }}
                            </span>
                        </th>
                        <td class="px-1 sm:px-6 py-4 hidden sm:table-cell">
                            {{ $item->faculty->name }}<br>
                            ({{ $item->faculty->university->name }})
                            @if ($item->quantity > 1)
                                ({{ $item->quantity }} Orang)
                            @endif
                        </td>
                        {{--
                        <td class="px-6 py-4 hidden sm:table-cell">
                            {{ $item->phone }}

                        </td> --}}
                        <td class="px-6 py-4 hidden sm:table-cell">
                            @if ($donation->status == 'aktif')
                                <a href="https://wa.me/{{ $item->phone }}"
                                    class="p-2 rounded-md bg-tosca-300 hover:bg-tosca-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white"
                                        class="bi bi-headset" viewBox="0 0 16 16">
                                        <path
                                            d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5" />
                                    </svg>
                                </a>
                            @else
                                {{ $item->code }}
                            @endif

                        </td>
                        @if ($heroes->where('status', 'belum')->count() > 0)
                            <td class="px-6 py-4 flex justify-center gap-2">
                                {{--
                            <a href="https://wa.me/{{ $item->phone }}"
                                class="p-2 rounded-md bg-tosca-300 hover:bg-tosca-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white"
                                    class="bi bi-headset" viewBox="0 0 16 16">
                                    <path
                                        d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5" />
                                </svg>


                            </a> --}}
                                @if ($item->status == 'belum')
                                    <form action="{{ route('hero.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="p-2 rounded-md bg-lime-500 hover:bg-lime-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="white" class="bi bi-bag-check" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                                <path
                                                    d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z" />
                                            </svg>


                                        </button>

                                    </form>
                                    <form action="{{ route('hero.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Yakin ingin menghapusnya?')" type="submit"
                                            class="p-2 rounded-md bg-red-300 hover:bg-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="white" class="bi bi-person-walking" viewBox="0 0 16 16">
                                                <path
                                                    d="M9.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0M6.44 3.752A.75.75 0 0 1 7 3.5h1.445c.742 0 1.32.643 1.243 1.38l-.43 4.083a1.8 1.8 0 0 1-.088.395l-.318.906.213.242a.8.8 0 0 1 .114.175l2 4.25a.75.75 0 1 1-1.357.638l-1.956-4.154-1.68-1.921A.75.75 0 0 1 6 8.96l.138-2.613-.435.489-.464 2.786a.75.75 0 1 1-1.48-.246l.5-3a.75.75 0 0 1 .18-.375l2-2.25Z" />
                                                <path
                                                    d="M6.25 11.745v-1.418l1.204 1.375.261.524a.8.8 0 0 1-.12.231l-2.5 3.25a.75.75 0 1 1-1.19-.914zm4.22-4.215-.494-.494.205-1.843.006-.067 1.124 1.124h1.44a.75.75 0 0 1 0 1.5H11a.75.75 0 0 1-.531-.22Z" />
                                            </svg>


                                        </button>

                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="w-full mt-5">
        <h1 class="text-center font-bold text-lg">Daftar Surplus Food</h1>
        <a class="bg-lime-600 hover:bg-lime-800 shadow-md p-2 rounded-md text-white" href="{{ route('food.index') }}">+
            Tambah</a>
        <table class="mt-5 shadow-md sm:rounded-lg w-full text-center text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nama
                    </th>
                    <th scope="col" class="hidden sm:table-cell px-6 py-3">
                        Jumlah
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Berat
                    </th>
                    <th scope="col" class="px-6 py-3 hidden sm:table-cell">
                        Keterangan
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($foods as $item)
                    <tr class="odd:bg-white even:bg-gray-50 border-b">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $item->name }}
                        </th>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            {{ $item->quantity }}

                        </td>
                        <td class="px-6 py-4">
                            {{ $item->weight }} {{ $item->unit }}

                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            {{ $item->notes }}

                        </td>
                        <td class="px-6 py-4 flex justify-center gap-2">
                            <a href="{{ route('food.edit', $item->id) }}"
                                class="p-2 rounded-md bg-yellow-300 hover:bg-yellow-600">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.58333 15.4167H3.88958L12.85 6.45625L11.5438 5.15L2.58333 14.1104V15.4167ZM0.75 17.25V13.3542L12.85 1.27708C13.0333 1.10903 13.2358 0.979167 13.4573 0.8875C13.6788 0.795833 13.9118 0.75 14.1563 0.75C14.4007 0.75 14.6375 0.795833 14.8667 0.8875C15.0958 0.979167 15.2944 1.11667 15.4625 1.3L16.7229 2.58333C16.9063 2.75139 17.0399 2.95 17.124 3.17917C17.208 3.40833 17.25 3.6375 17.25 3.86667C17.25 4.11111 17.208 4.3441 17.124 4.56562C17.0399 4.78715 16.9063 4.98958 16.7229 5.17292L4.64583 17.25H0.75ZM12.1854 5.81458L11.5438 5.15L12.85 6.45625L12.1854 5.81458Z"
                                        fill="white" />
                                </svg>

                            </a>
                            <form action="{{ route('food.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Yakin ingin menghapus?')" type="submit"
                                    class="p-2 rounded-md bg-red-300 hover:bg-red-600">
                                    <svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M13.1665 3.50008H17.3332V5.16675H15.6665V16.0001C15.6665 16.2211 15.5787 16.4331 15.4224 16.5893C15.2661 16.7456 15.0542 16.8334 14.8332 16.8334H3.1665C2.94549 16.8334 2.73353 16.7456 2.57725 16.5893C2.42097 16.4331 2.33317 16.2211 2.33317 16.0001V5.16675H0.666504V3.50008H4.83317V1.00008C4.83317 0.779068 4.92097 0.567106 5.07725 0.410826C5.23353 0.254545 5.44549 0.166748 5.6665 0.166748H12.3332C12.5542 0.166748 12.7661 0.254545 12.9224 0.410826C13.0787 0.567106 13.1665 0.779068 13.1665 1.00008V3.50008ZM13.9998 5.16675H3.99984V15.1667H13.9998V5.16675ZM6.49984 1.83341V3.50008H11.4998V1.83341H6.49984Z"
                                            fill="white" />
                                    </svg>


                                </button>

                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
