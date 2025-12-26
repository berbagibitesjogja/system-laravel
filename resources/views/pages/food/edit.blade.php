@extends('layouts.main')
@section('container')
    <div class="flex md:flex-row flex-col gap-4">
        <div class="w-full md:w-1/3">
            <h1 class="text-center font-bold text-lg">Edit Surplus Food</h1>
            <div class="shadow-md p-5 rounded-md mt-5">

                <form class="max-w-sm mx-auto" action="{{ route('food.update', $food->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="text" id="disabled-input" aria-label="disabled input"
                        class="mb-5 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed"
                        value="{{ $donation->sponsor->name }} ({{ $donation->take }})" disabled>
                    <div class="relative z-0 w-full mt-6 mb-5 group">
                        <input type="text" name="name" id="name" value="{{ $food->name }}"
                            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " required />
                        <label for="name"
                            class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Nama
                            Makanan</label>
                    </div>
                    <div class="relative z-0 w-full mt-6 mb-5 group">
                        <input type="text" name="quantity" id="quantity" value="{{ $food->quantity }}"
                            class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                            placeholder=" " />
                        <label for="quantity"
                            class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Quantity
                            Makanan (Opsional)</label>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="relative z-0 w-full mb-5 group">
                            <input type="number" name="weight" id="weight" value="{{ $food->weight }}"
                                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" " required />
                            <label for="quantity"
                                class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Berat
                                / Volume</label>
                        </div>
                        <div class="relative z-0 w-full mb-5 group">
                            <select id="unit" name="unit"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">

                                <option value="gr" @if ($food->unit == 'gr') {{ 'selected' }} @endif>gr
                                </option>
                                <option value="ml" @if ($food->unit == 'ml') {{ 'selected' }} @endif>ml
                                </option>

                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex align-middle gap-2 my-2">
                            <input type="checkbox" name="expired" id="expired" {{ $food->expired ? 'checked' : '' }}>
                            <label for="expired" class="text-xs ">Basi</label>
                        </div>

                    </div>
                    <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Keterangan</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Keterangan makanan...">{{ $food->notes }}</textarea>
                    <button type="submit"
                        class="bg-navy hover:bg-navy-700 mt-3 rounded-md p-2 text-white w-full">Simpan</button>

                </form>

            </div>
        </div>
        <div class="w-full md:w-2/3">
            <h1 class="text-center font-bold text-lg">Daftar Surplus Food</h1>
            <table class="mt-5 shadow-md sm:rounded-lg w-full text-center text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="hidden sm:table-cell px-6 py-3">
                            Jumlah
                        </th>
                        <th scope="col" class="hidden sm:table-cell px-6 py-3">
                            unit
                        </th>
                        <th scope="col" class="px-6 py-3 hidden sm:table-cell">
                            Donatur
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($foods as $item)
                        <tr class="odd:bg-white even:bg-gray-50 border-b">
                            <th scope="row" class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $item->name }}
                                <span class="md:hidden sm:table-cell block font-normal">

                                    <a href="{{ route('donation.show', $item->donation->id) }}">
                                        {{ $item->donation->sponsor->name }}
                                        <span class="block italic">
                                            {{ \Carbon\Carbon::parse($item->donation->take)->format('d M Y') }}
                                        </span>
                                    </a>
                                </span>
                            </th>
                            <td class="px-2 py-4 hidden sm:table-cell">
                                {{ $item->quantity }}

                            </td>
                            <td class="px-2 py-4 hidden sm:table-cell">
                                {{ $item->weight }}
                                {{ $item->unit }}

                            </td>
                            <td class="px-2 py-4 hidden sm:table-cell">
                                <a href="{{ route('donation.show', $item->donation->id) }}">
                                    {{ $item->donation->sponsor->name }}
                                    <span class="block italic">
                                        {{ \Carbon\Carbon::parse($item->donation->take)->format('d M Y') }}
                                    </span>
                                </a>

                            </td>
                            <td class="px-2 py-4 flex justify-center gap-2">
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
    </div>

    {{ $foods->links() }}
@endsection
