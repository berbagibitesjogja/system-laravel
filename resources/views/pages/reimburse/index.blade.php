@extends('layouts.main')

@section('container')
    <div
        class="max-w-7xl mx-auto"
        x-data="{
            showConfirmModal: false,
            submitting: false,
            hasFile: false,
            fileName: '',
            confirmUrl: '',
            confirmItem: { user: '', method: '', target: '', amount: '', notes: '' },
            openConfirm(data) {
                this.confirmUrl = data.url;
                this.confirmItem = {
                    user: data.user,
                    method: data.method,
                    target: data.target,
                    amount: data.amount,
                    notes: data.notes,
                };
                this.hasFile = false;
                this.fileName = '';
                this.submitting = false;
                if (this.$refs.paymentInput) {
                    this.$refs.paymentInput.value = '';
                }
                this.showConfirmModal = true;
            },
            closeConfirm() {
                if (this.submitting) return;
                this.showConfirmModal = false;
            }
        }"
    >
        <div class="flex flex-wrap gap-3 items-center mb-8">
            <x-btn-link href="{{ route('volunteer.home') }}" variant="ghost">
                ← Kembali
            </x-btn-link>
            <h1 class="text-2xl font-bold text-navy-900">Manajemen Reimburse</h1>
        </div>

        <div class="bg-white rounded-2xl border border-navy-100 shadow-md overflow-hidden">
            <x-table>
                <x-slot:head>
                    <x-th>User</x-th>
                    <x-th class="hidden sm:table-cell">Metode</x-th>
                    <x-th class="hidden sm:table-cell">Tujuan</x-th>
                    <x-th>Jumlah</x-th>
                    <x-th class="hidden sm:table-cell">Pengajuan</x-th>
                    <x-th class="hidden sm:table-cell">Pembayaran</x-th>
                    <x-th>Status</x-th>
                    <x-th class="hidden sm:table-cell">Notes</x-th>
                    <x-th class="text-center">Aksi</x-th>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($reimburse as $item)
                        <x-tr>
                            <x-td>
                                <div>
                                    <p class="font-semibold text-navy-900">{{ $item->user->name ?? 'Unknown' }}</p>
                                    <p class="sm:hidden text-sm text-navy-400">{{ $item->method }} • {{ $item->target }}</p>
                                    <div class="sm:hidden flex flex-wrap gap-3 mt-1 text-xs">
                                        <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                            class="text-tosca-500 hover:text-tosca-600 font-medium">
                                            📄 Pengajuan
                                        </a>
                                        @if ($item->payment)
                                            <a href="{{ asset('storage/' . $item->payment) }}" target="_blank"
                                                class="text-tosca-500 hover:text-tosca-600 font-medium">
                                                📄 Bukti TF
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </x-td>
                            <x-td class="hidden sm:table-cell">{{ $item->method }}</x-td>
                            <x-td class="hidden sm:table-cell">{{ $item->target }}</x-td>
                            <x-td>
                                <span class="font-bold text-tosca-600">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                            </x-td>
                            <x-td class="hidden sm:table-cell">
                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                    class="text-tosca-500 hover:text-tosca-600 font-medium transition-colors">
                                    📄 Lihat
                                </a>
                            </x-td>
                            <x-td class="hidden sm:table-cell">
                                @if ($item->payment)
                                <a href="{{ asset('storage/' . $item->payment) }}" target="_blank"
                                    class="text-tosca-500 hover:text-tosca-600 font-medium transition-colors">
                                    📄 Bukti
                                </a>
                                @else
                                <span class="text-navy-400">—</span>
                                @endif
                            </x-td>
                            <x-td>
                                @if ($item->done == true)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-lime-400 text-navy-900">
                                        ✓ Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-300 text-navy-900">
                                        ⏳ Proses
                                    </span>
                                @endif
                            </x-td>
                            <x-td class="hidden sm:table-cell">{{ $item->notes ?? '-' }}</x-td>
                            <x-td>
                                <div class="flex justify-center gap-2">
                                    @if ($item->done == false)
                                        <x-btn
                                            type="button"
                                            variant="success"
                                            class="!px-3 !py-1.5 text-xs"
                                            data-url="{{ route('reimburse.update', $item->id) }}"
                                            data-user="{{ $item->user->name ?? 'Unknown' }}"
                                            data-method="{{ $item->method }}"
                                            data-target="{{ $item->target }}"
                                            data-amount="Rp {{ number_format($item->amount, 0, ',', '.') }}"
                                            data-notes="{{ $item->notes ?? '-' }}"
                                            @click="openConfirm($el.dataset)"
                                        >
                                            ✓ Sudah
                                        </x-btn>

                                        <form action="{{ route('reimburse.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-btn type="submit" variant="danger" class="!px-3 !py-1.5 text-xs">
                                                ✕ Batal
                                            </x-btn>
                                        </form>
                                    @else
                                        <span class="text-navy-400 text-sm">—</span>
                                    @endif
                                </div>
                            </x-td>
                        </x-tr>
                    @empty
                        <x-tr>
                            <x-td colspan="9" class="text-center py-8">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">📋</span>
                                    <p class="text-navy-400">Belum ada pengajuan reimburse</p>
                                </div>
                            </x-td>
                        </x-tr>
                    @endforelse
                </x-slot:body>
            </x-table>
        </div>

        {{-- Modal Konfirmasi & Upload Bukti Transfer (Wajib) --}}
        <div
            x-show="showConfirmModal"
            class="fixed inset-0 z-[60] overflow-y-auto"
            style="display: none;"
            @keydown.escape.window="closeConfirm()"
        >
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    x-show="showConfirmModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/75 transition-opacity"
                    @click="closeConfirm()"
                ></div>

                <div
                    x-show="showConfirmModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full"
                >
                    <form
                        :action="confirmUrl"
                        method="POST"
                        enctype="multipart/form-data"
                        @submit="if (!hasFile) { $event.preventDefault(); return; } submitting = true"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-lime-200 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-lime-800" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                                    <h3 class="text-lg font-semibold leading-6 text-navy-900">
                                        Konfirmasi Penyelesaian Reimburse
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Pastikan dana telah ditransfer dan unggah bukti transfer di bawah ini untuk menyelesaikan reimburse.
                                    </p>

                                    <div class="mt-4 rounded-xl bg-gray-50 border border-navy-100 p-3.5 text-left space-y-1.5 text-sm">
                                        <div class="flex justify-between gap-2">
                                            <span class="text-gray-500">Penerima</span>
                                            <span class="font-semibold text-navy-900" x-text="confirmItem.user"></span>
                                        </div>
                                        <div class="flex justify-between gap-2">
                                            <span class="text-gray-500">Metode & Tujuan</span>
                                            <span class="font-medium text-navy-800">
                                                <span x-text="confirmItem.method"></span> • <span x-text="confirmItem.target"></span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between gap-2">
                                            <span class="text-gray-500">Nominal</span>
                                            <span class="font-bold text-tosca-600" x-text="confirmItem.amount"></span>
                                        </div>
                                    </div>

                                    <div class="mt-4 text-left">
                                        <label for="payment" class="block mb-2 text-sm font-semibold text-navy-700">
                                            Bukti Transfer <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            x-ref="paymentInput"
                                            type="file"
                                            name="payment"
                                            id="payment"
                                            accept="image/*,.pdf"
                                            required
                                            @change="hasFile = $event.target.files.length > 0; fileName = $event.target.files[0]?.name || ''"
                                            class="w-full text-sm text-navy-600
                                                file:mr-4 file:py-2.5 file:px-4
                                                file:rounded-xl file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-tosca-500 file:text-white
                                                hover:file:bg-tosca-600
                                                file:transition-colors file:duration-300
                                                file:cursor-pointer
                                                border border-navy-200 rounded-xl
                                                focus:outline-none focus:ring-2 focus:ring-tosca-300"
                                        >
                                        <p class="mt-1.5 text-xs text-gray-500">
                                            Wajib diisi. Format: JPG, PNG, WEBP, atau PDF (maks. 5MB).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                            <button
                                type="submit"
                                :disabled="!hasFile || submitting"
                                :class="(!hasFile || submitting) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-lime-500 cursor-pointer'"
                                class="inline-flex w-full justify-center items-center gap-2 rounded-xl bg-lime-400 px-4 py-2.5 text-sm font-semibold text-navy-900 shadow-sm transition-all sm:w-auto"
                            >
                                <svg x-show="submitting" class="animate-spin h-4 w-4 text-navy-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="submitting ? 'Menyimpan...' : '✓ Konfirmasi & Selesaikan'"></span>
                            </button>
                            <button
                                type="button"
                                :disabled="submitting"
                                @click="closeConfirm()"
                                class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-navy-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <x-toast type="error" :message="$errors->first()" />
    @endif
@endsection
