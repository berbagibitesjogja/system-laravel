@extends('layouts.main')
@section('container')
    <form action="{{ route('report.download') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <div class="bg-white rounded-2xl border border-navy-100 shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-navy-500 to-tosca-500 p-6 text-white">
                        <h2 class="text-xl font-bold">Generate Laporan</h2>
                        <p class="text-sm text-white/80 mt-1">Pilih partner dan rentang tanggal</p>
                    </div>
                    
                    <div class="p-6">
                        <x-select name="sponsor_id" id="sponsorId" label="Pilih Partner">
                            <option value="">Partner</option>
                            @foreach ($sponsors as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </x-select>

                        <div class="flex flex-row gap-3 items-center mb-6">
                            <div class="flex-1">
                                <label class="block mb-2 text-sm font-semibold text-navy-700">Dari Tanggal</label>
                                <input type="date" name="startDate" id="startDate"
                                    class="w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300" required />
                            </div>
                            <p class="text-navy-400 mt-6">s/d</p>
                            <div class="flex-1">
                                <label class="block mb-2 text-sm font-semibold text-navy-700">Sampai Tanggal</label>
                                <input type="date" name="endDate" id="endDate"
                                    class="w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300" required />
                            </div>
                        </div>
                        
                        <x-btn type="button" onclick="searchData()" variant="primary" class="w-full">
                            🔍 Cari Data
                        </x-btn>
                    </div>
                </div>
                
                <div class="flex items-center flex-col mt-6 hidden" id="loading">
                    <p class="text-navy-600">Mengambil data...</p>
                    <div role="status" class="mt-5">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin fill-tosca-500"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                    </div>
                </div>
                
                <div class="flex flex-row flex-wrap gap-3 mt-6 hidden" id="resultSum">
                    <div class="bg-gradient-to-br from-tosca-500 to-tosca-600 py-3 px-5 rounded-xl text-white font-semibold">
                        <span id="actionSum">0</span> Aksi
                    </div>
                    <div class="bg-gradient-to-br from-tosca-500 to-tosca-600 py-3 px-5 rounded-xl text-white font-semibold">
                        <span id="receiverSum">0</span> Penerima
                    </div>
                    <div class="bg-gradient-to-br from-lime-500 to-lime-600 py-3 px-5 rounded-xl text-white font-semibold">
                        <span id="foodsSum">0</span> kg makanan
                    </div>
                    <div class="bg-gradient-to-br from-orange-400 to-orange-500 py-3 px-5 rounded-xl text-white font-semibold">
                        <span id="variantSum">0</span> Jenis makanan
                    </div>
                </div>
            </div>
            
            <div id="donationContainer" class="overflow-y-auto max-h-[500px] space-y-4">
            </div>
        </div>
        
        <x-btn id="downloadButton" type="submit" variant="primary" class="w-full mt-8 hidden">
            📥 Download Laporan
        </x-btn>
    </form>
    
    <script>
        const loading = document.querySelector("#loading")
        const downloadButton = document.querySelector("#downloadButton")
        const resultSum = document.querySelector("#resultSum")
        const sponsorId = document.querySelector("#sponsorId")
        const startDate = document.querySelector("#startDate")
        const donationContainer = document.querySelector("#donationContainer")
        const endDate = document.querySelector("#endDate")

        async function searchData() {
            donationContainer.innerHTML = ""
            loading.classList.remove('hidden')
            resultSum.classList.add('hidden')
            downloadButton.classList.add('hidden')
            const data = await fetch(`/api/sponsor/${sponsorId.value}/${startDate.value}/${endDate.value}`)
                .then(res => res.json())
                .then(res => {
                    document.querySelector('#actionSum').innerHTML = res.totalAction
                    document.querySelector('#receiverSum').innerHTML = res.totalHero
                    document.querySelector('#foodsSum').innerHTML = Math.round(res.totalWeight / 1000)
                    document.querySelector('#variantSum').innerHTML = res.totalFood
                    loading.classList.add('hidden')
                    resultSum.classList.remove('hidden')
                    downloadButton.classList.remove('hidden')
                    return res.donations
                })
            data.forEach(e => {
                donationContainer.innerHTML += `
                <div class="bg-white rounded-2xl border border-navy-100 shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-tosca-100 text-tosca-700 py-1 px-3 rounded-full text-sm font-medium">${e.heroQuantity} Penerima</span>
                            <span class="bg-lime-100 text-lime-700 py-1 px-3 rounded-full text-sm font-medium">${Math.round(e.foodWeight/1000)} kg</span>
                            <span class="bg-orange-100 text-orange-700 py-1 px-3 rounded-full text-sm font-medium">${e.foodQuantity} jenis</span>
                        </div>
                        <span class="bg-navy-100 text-navy-600 py-1 px-2 rounded-lg text-xs">${e.take}</span>
                    </div>
                    <input type="text"
                        class="w-full mb-3 px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300"
                        placeholder="Nama Penerima" name="receiver-${e.id}" required />
                    <input type="text"
                        class="w-full px-4 py-3 text-sm text-navy-900 bg-white border border-navy-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-tosca-300 focus:border-tosca-500 transition-all duration-300"
                        placeholder="Jabatan" name="role-${e.id}" required />
                </div>`
            });
        }
    </script>
@endsection
