@extends('layouts.order')

@section('title', 'Detail Pesanan')

@section('content')
<section class="bg-linear-to-b from-white/75 via-white/90 to-sage min-h-screen py-12 px-6 md:px-12 lg:px-12">

    <div class="flex justify-end gap-4 mb-15">
        <a href="{{ route('order.history') }}"
        class="bg-moss hover:bg-rose text-white font-semibold px-5 py-3 rounded-full shadow-lg transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 25 25" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1.68 9.46A2 2 0 0115.34 22H8.66a2 2 0 01-1.98-1.54L5 11z" />
            </svg>
            Riwayat Pemesanan
        </a>
        <a href="{{ route('home') }}"
        class="bg-moss hover:bg-rose text-white font-semibold px-5 py-3 rounded-full shadow-lg transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4m10-11v10a1 1 0 01-1 1h-4" />
            </svg>
            Home
        </a>
    </div>

    <div class="max-w-4xl mx-auto space-y-8">

        {{-- JUDUL + STATUS --}}
        <div class="flex justify-between items-center border-b-3 pb-4">
            <h2 class="md:text-5xl text-4xl font-display font-bold text-moss">Detail Pesanan</h2>

            @php
                $statusClass = [
                    'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                    'paid' => 'bg-blue-100 text-blue-700 border border-blue-300',
                    'confirm' => 'bg-green-100 text-green-700 border border-green-300',
                    'processing' => 'bg-purple-100 text-purple-700 border border-purple-300',
                    'done' => 'bg-emerald-100 text-emerald-700 border border-emerald-300',
                    'cancel' => 'bg-red-100 text-red-700 border border-red-300',
                ];
            @endphp

            <span class="px-4 py-2 text-lg font-bold rounded-full uppercase {{ $statusClass[$order->status] ?? 'bg-gray-200 text-gray-700' }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        {{-- PROGRESS BAR --}}
        @php
            $stepLabels = [
                'pending' => 'Menunggu Pembayaran',
                'paid' => 'Pembayaran Diterima',
                'confirm' => 'Pesanan Dikonfirmasi',
                'processing' => 'Sedang Dikerjakan',
                'done' => 'Pesanan Selesai',
            ];

            $stepColors = [
                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'paid' => 'bg-blue-100 text-blue-700 border-blue-300',
                'confirm' => 'bg-green-100 text-green-700 border-green-300',
                'processing' => 'bg-purple-100 text-purple-700 border-purple-300',
                'done' => 'bg-emerald-100 text-emerald-700 border-emerald-300',
            ];

            $cancelColor = 'bg-red-100 text-red-700 border-red-300';

            $statusOrder = array_keys($stepLabels);
            $currentIndex = array_search($order->status, $statusOrder);
        @endphp

        @if($order->status !== 'cancel')
            <div class="w-full flex justify-center py-8">
                <div class="flex items-center flex-wrap justify-center gap-y-6">

                    @foreach ($statusOrder as $index => $statusKey)
                        @php
                            $isActive = $index <= $currentIndex;
                            $isLast = $loop->last;
                        @endphp

                        <div class="flex items-center">

                            {{-- BULATAN --}}
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 md:w-12 md:h-12 rounded-full flex items-center justify-center border
                                    {{ $isActive ? $stepColors[$statusKey] : 'bg-gray-200 text-gray-500 border-gray-300' }}">
                                    <span class="text-md md:text-base">{{ $index + 1 }}</span>
                                </div>

                                <p class="text-sm md:text-xs mt-3 text-center w-16 md:w-20
                                    {{ $isActive ? '' : 'text-gray-500' }}">
                                    {{ $stepLabels[$statusKey] }}
                                </p>
                            </div>

                            {{-- GARIS --}}
                            @unless($isLast)
                            <div class="h-1 w-10 md:w-20
                                {{ $index < $currentIndex ? 'bg-emerald-500' : 'bg-gray-300' }}">
                            </div>
                            @endunless

                        </div>
                    @endforeach

                </div>
            </div>
        @endif

        @if($order->status === 'cancel')
            <div class="flex flex-col items-center mt-6">
                <div class="w-12 h-12 flex items-center justify-center rounded-full border {{ $cancelColor }}">
                    ✕
                </div>
                <p class="mt-2 font-semibold text-red-700">Pesanan Dibatalkan</p>
            </div>
        @endif

        {{-- Jika Pesanan Dibatalkan --}}
        @if($order->status === 'cancel' && $cancelData)
            <div class="bg-red-50 p-6 rounded-2xl shadow-md space-y-2">
                <h3 class="text-xl font-bold text-red-700 mb-4 uppercase">Dibatalkan Pada</h3>
                <div class="grid grid-cols-[170px_10px_1fr] gap-1">
                    <span class="font-semibold">Tanggal</span>
                    <span class="text-center font-semibold">:</span>
                    <span>{{ $cancelData->cancellation_date->timezone('Asia/Jakarta')->format('d M Y') }}</span>
                        
                    <span class="font-semibold">Waktu</span>
                    <span class="text-center font-semibold">:</span>
                    <span>{{ $cancelData->cancellation_date->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</span>
                    
                    <span class="font-semibold">Alasan Pembatalan</span>
                    <span class="text-center font-semibold">:</span>
                    <span>{{ $cancelData->cancellation_reason }}</span>
                </div>
            </div>
        @endif

        {{-- DATA PEMESAN --}}
        <div class="bg-white p-6 rounded-xl shadow-md space-y-2">
            <h3 class="text-xl font-bold text-moss uppercase mb-4">Data Diri Pemesan</h3>
            <div class="grid grid-cols-[110px_10px_1fr] gap-2">
                <span class="font-semibold">Nama</span>
                <span class="text-center font-semibold">:</span>
                <span>{{ $order->customer_name }}</span>

                <span class="font-semibold">No. Telepon</span>
                <span class="text-center font-semibold">:</span>
                <span>{{ $order->customer_phone_number }}</span>

                <span class="font-semibold">Alamat</span>
                <span class="text-center font-semibold">:</span>
                <span>{{ $order->customer_address }}</span>
            </div>
        </div>

        {{-- DETAIL ITEM --}}
        <div class="bg-white p-6 rounded-2xl shadow-xl space-y-6">
            <h3 class="text-2xl font-bold text-moss uppercase mb-4 text-center">Detail Pemesanan</h3>

            @foreach($items as $index => $item)
                <div class="border border-moss p-5 rounded-xl shadow-lg transition-transform duration-300 ease-in-out transform hover:scale-101 hover:shadow-xl">
                    <h4 class="font-semibold text-lg mb-3 text-moss uppercase">Selempang {{ $index + 1 }}</h4>

                    <div class="grid md:grid-cols-2 gap-4 text-md">
                        {{-- Kolom Kiri --}}
                        <div class="grid grid-cols-[70px_10px_1fr] gap-1">
                            <span class="font-semibold">Produk</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->product->product_name ?? '-' }}</span>

                            <span class="font-semibold">Jenis</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->sashType->name ?? '-' }}</span>

                            <span class="font-semibold">Bahan</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->material->name ?? '-' }}</span>

                            <span class="font-semibold">Warna</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->materialColor->name ?? '-' }}</span>

                            <span class="font-semibold">Font</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->font->name ?? '-' }}</span>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="grid grid-cols-[120px_10px_1fr] gap-1">
                            {{-- Motif --}}
                            @if($item->sideMotif)
                                <span class="font-semibold">Motif Samping</span>
                                <span class="text-center font-semibold">:</span>
                                <span>{{ $item->sideMotif->name ?? '-' }}</span>
                            @endif

                            {{-- Warna Pita --}}
                            @if($item->ribbonColor)
                                <span class="font-semibold">Warna Pita</span>
                                <span class="text-center font-semibold">:</span>
                                <span>{{ $item->ribbonColor->name }}</span>
                            @endif

                            {{-- Renda --}}
                            @if($item->laceOption)
                                <span class="font-semibold">Renda</span>
                                <span class="text-center font-semibold">:</span>
                                <span>{{ $item->laceOption->color }} - {{ $item->laceOption->size }}</span>
                            @endif

                            {{-- Rombe --}}
                            @if($item->rombeOption)
                                <span class="font-semibold">Rombe</span>
                                <span class="text-center font-semibold">:</span>
                                <span>{{ $item->rombeOption->color }} - {{ $item->rombeOption->size }}</span>
                            @endif

                            {{-- Pita Motif --}}
                            @if($item->motifRibbonOption)
                                <span class="font-semibold">Pita Motif</span>
                                <span class="text-center font-semibold">:</span>
                                <span>{{ $item->motifRibbonOption->color }} - {{ $item->motifRibbonOption->size }}</span>
                            @endif

                            {{-- Item Tambahan --}}
                            @if($item->additionalItemOption)
                                <span class="font-semibold">Tambahan</span>
                                <span class="text-center font-semibold">:</span>
                                <span>{{ $item->additionalItemOption->item_name ?? ($item->additionalItemOption->model.' - '.$item->additionalItemOption->color) }}</span>
                            @endif

                            {{-- Logo Upload --}}
                            @if($item->logo_path)
                                <span class="font-semibold">Logo</span>
                                <span class="text-center font-semibold">:</span>
                                <span><img src="{{ asset('storage/' . $item->logo_path) }}" class="h-20 border border-moss rounded-lg p-1 bg-gray-50 object-contain"></span>
                            @endif
                        </div>
                    </div>
                        
                    @if($item->sashType->id == 1)
                        <div class="grid grid-cols-[120px_10px_1fr] gap-1 mt-4">
                            <span class="font-semibold">Teks Sisi Kanan</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->text_right }}</span>
                            
                            <span class="font-semibold">Teks Sisi Kiri</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->text_left }}</span>
                        </div>
                    @else
                        <div class="grid grid-cols-[120px_10px_1fr] gap-1 mt-4">
                            <span class="font-semibold">Teks Selempang</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->text_single }}</span>
                        </div>
                    @endif
                    
                    <div class="grid md:grid-cols-2 gap-4 text-md">
                        {{-- Kolom Tambahan --}}
                        <div class="grid grid-cols-[120px_10px_1fr] gap-1 mt-4">
                            <span class="font-semibold">Jumlah</span>
                            <span class="text-center font-semibold">:</span>
                            <span>{{ $item->quantity }}</span>

                            <span class="font-semibold">Harga Satuan</span>
                            <span class="text-center font-semibold">:</span>
                            <span>Rp{{ number_format($item->final_price) }}</span>

                            <span class="font-semibold">Subtotal</span>
                            <span class="text-center font-semibold">:</span>
                            <span>Rp{{ number_format($item->final_price * $item->quantity) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
            {{-- TOTAL PEMBAYARAN --}}
            <div class="flex justify-end items-center bg-sage/20 p-4 rounded-lg border border-sage shadow-inner transition-transform duration-300 ease-in-out transform hover:scale-101 hover:shadow-md">
                <p class="text-xl font-semibold">
                    Total Pembayaran: <span class="hover:text-rose ml-1">Rp{{ number_format($order->total_price) }}</span>
                </p>
            </div>
        </div>

        {{-- Tema warna berdasar status --}}
        @php
            $themes = [
                'pending' => [
                    'bg' => 'bg-blue-50',
                    'border' => 'border-blue-300',
                    'title' => 'text-blue-700 uppercase text-center',
                    'text' => 'text-blue-600 text-center',
                    'button' => 'bg-blue-600 hover:bg-blue-700',
                ],
                'paid' => [
                    'bg' => 'bg-yellow-50',
                    'border' => 'border-yellow-300',
                    'title' => 'text-yellow-700 uppercase text-center',
                    'text' => 'text-yellow-600 text-center',
                    'button' => 'bg-yellow-600 hover:bg-yellow-700',
                ],
                'confirm' => [
                    'bg' => 'bg-green-50',
                    'border' => 'border-green-300',
                    'title' => 'text-green-700 uppercase text-center',
                    'text' => 'text-green-600 text-center',
                    'button' => 'bg-green-600 hover:bg-green-700',
                ],
                'processing' => [
                    'bg' => 'bg-purple-50',
                    'border' => 'border-purple-300',
                    'title' => 'text-purple-700 uppercase text-center',
                    'text' => 'text-purple-600 text-center',
                    'button' => 'bg-purple-600 hover:bg-purple-700',
                ],
                'done' => [
                    'bg' => 'bg-emerald-50',
                    'border' => 'border-emerald-300',
                    'title' => 'text-emerald-700 uppercase text-center',
                    'text' => 'text-emerald-600 text-center',
                    'button' => 'bg-emerald-600 hover:bg-emerald-700',
                ],
            ];

            $theme = $themes[$order->status] ?? $themes['pending'];
        @endphp

        @if($order->status !== 'cancel')
        <div class="{{ $theme['bg'] }} border {{ $theme['border'] }} p-6 rounded-2xl shadow-xl">
            {{-- ================== STATUS: PENDING → UPLOAD ================== --}}
            @if($order->status === 'pending')
                <h3 class="text-xl font-bold mb-2 {{ $theme['title'] }}">Upload Bukti Pembayaran</h3>
                <p class="text-sm mb-6 {{ $theme['text'] }}">
                    Silakan unggah bukti transfer untuk melanjutkan proses pesanan.
                </p>

                <form action="{{ route('order.uploadPayment', $order->id) }}"
                    method="POST" enctype="multipart/form-data"
                    class="flex flex-col items-center gap-4">
                    @csrf

                    {{-- Preview --}}
                    <img id="imagePreview" class="hidden w-full max-w-sm rounded-xl border border-blue-300 shadow-md object-contain" />

                    <input type="file" id="paymentInput" name="payment_proof"
                        accept="image/*"
                        class="border border-blue-300 focus:ring-blue-500 p-2 rounded w-80 max-w-sm text-sm bg-white cursor-pointer" required>
                        
                    <p class="text-xs text-gray-500 text-center">
                        Format JPG/JPEG/PNG, maksimal 2MB. Pastikan tulisan terlihat jelas.
                    </p>

                    <button class="{{ $theme['button'] }} text-white font-bold px-5 py-2 rounded-lg shadow transition uppercase tracking-wider">
                        Upload
                    </button>
                </form>

                <p class="text-sm mt-6 text-gray-500">
                    Catatan: Jika sudah melakukan pembayaran, maka tidak bisa membatalkan pesanan.
                </p>

            {{-- ================== STATUS: PAID → MENUNGGU VERIFIKASI ================== --}}
            @elseif($order->payment_proof && $order->status === 'paid')
                <h3 class="text-xl font-bold mb-2 {{ $theme['title'] }}">Menunggu Konfirmasi Admin</h3>

                <p class="text-sm mb-6 {{ $theme['text'] }}">
                    Bukti pembayaran sudah diterima.  
                    Admin sedang memverifikasi pembayaran kamu.
                </p>

                <img src="{{ asset('storage/' . $order->payment_proof) }}"
                    class="w-80 max-w-md mx-auto rounded-xl shadow border border-yellow-300 object-contain" />

            {{-- ================== STATUS: CONFIRM ================== --}}
            @elseif($order->payment_proof && $order->status === 'confirm')
                <h3 class="text-xl font-bold mb-2 {{ $theme['title'] }}">Pembayaran Diverifikasi</h3>

                <p class="text-sm mb-6 {{ $theme['text'] }}">
                    Pembayaran kamu telah berhasil diverifikasi.
                    Pesanan akan segera masuk ke tahap proses produksi.
                </p>

                <img src="{{ asset('storage/' . $order->payment_proof) }}"
                    class="w-80 max-w-md mx-auto rounded-xl shadow border border-green-300 object-contain" />

            {{-- ================== STATUS: PROCESSING ================== --}}
            @elseif($order->status === 'processing')
                <h3 class="text-xl font-bold mb-2 {{ $theme['title'] }}">Pesanan Sedang Diproses</h3>

                <p class="text-sm mb-6 {{ $theme['text'] }}">
                    Pesanan kamu sedang dalam tahap pengerjaan oleh tim kami.
                    Silakan pantau terus status pesanan secara berkala.
                </p>

                {{-- Estimasi pengerjaan --}}
                <div class="p-4 bg-purple-100 rounded-xl shadow border {{ $theme['border'] }} shadow-inner transition-transform duration-300 ease-in-out transform hover:scale-101 hover:shadow-md">
                    <p class="text-lg font-bold uppercase {{ $theme['text'] }}">
                        Estimasi pengerjaan: <span class="text-red-600">3–7 hari kerja</span>
                    </p>
                </div>

            {{-- ================== STATUS: DONE ================== --}}
            @elseif($order->status === 'done')
                @php
                    $waNumber = '6281234567890';
                    $waMessage = urlencode(
                        "Halo kak, saya ingin konfirmasi pesanan saya yang sudah selesai.\n\n" .
                        "Order ID: {$order->id}\n" .
                        "Nama: {$order->customer_name}"
                    );

                    $mapsUrl = "https://www.google.com/maps?q=" . urlencode("Wira Bordir Computer");
                @endphp

                <div class="space-y-5"> 
                    <h3 class="text-xl font-bold mb-2 {{ $theme['title'] }}">
                        Pesanan Selesai 🎉
                    </h3>

                    <p class="text-sm mb-4 {{ $theme['text'] }}">
                        Pesanan kamu sudah selesai! Terima kasih telah mempercayai layanan kami.
                    </p>

                    {{-- CARD INFORMASI PENGAMBILAN --}}
                    <div class="p-5 bg-white rounded-xl shadow border {{ $theme['border'] }}">

                        <p class="text-md font-semibold text-gray-700 uppercase">
                            📍 Lokasi Pengambilan Pesanan
                        </p>

                        <p class="font-bold text-emerald-700 text-center mt-2 text-md">
                            Wira Bordir Computer. Tembung, Kec. Percut Sei Tuan, Kabupaten Deli Serdang.
                        </p>

                        {{-- Tombol menuju Google Maps --}}
                        <a href="{{ $mapsUrl }}" target="_blank"
                        class="mt-3 block w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center py-2 px-4 rounded-lg shadow font-semibold transition">
                            Buka Lokasi di Google Maps
                        </a>

                        <hr class="my-4">

                        <p class="text-md font-semibold text-gray-700 uppercase">
                            📞 Membutuhkan Bantuan?
                        </p>

                        <p class="text-sm text-gray-600 mt-1">
                            Kamu bisa menghubungi admin untuk koordinasi pengambilan pesanan.
                        </p>

                        {{-- Tombol WhatsApp --}}
                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}"
                        target="_blank"
                        class="mt-3 block w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center py-2 px-4 rounded-lg shadow font-semibold flex items-center justify-center gap-2 transition">

                            <svg xmlns="http://www.w3.org/2000/svg" 
                                fill="currentColor" 
                                viewBox="0 0 24 24"
                                class="w-5 h-5">
                                <path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.96C.155 5.3 5.366 0 12.02 0c3.184 0 6.167 1.24 8.413 3.488a11.82 11.82 0 013.495 8.414c-.003 6.653-5.312 11.865-11.98 11.865a11.9 11.9 0 01-5.958-1.594L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.593 5.448 0 9.886-4.434 9.889-9.877.003-5.462-4.415-9.89-9.881-9.893-5.46-.003-9.89 4.422-9.893 9.887a9.822 9.822 0 001.588 5.258l-.999 3.648 3.904-1.616zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.867-2.03-.967-.272-.099-.47-.148-.668.149-.198.297-.767.966-.94 1.164-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.149-.173.198-.297.297-.495.099-.198.05-.372-.025-.521-.074-.149-.668-1.611-.916-2.21-.242-.579-.487-.5-.668-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.007-1.413.248-.694.248-1.289.173-1.413z"/>
                            </svg>
                            Hubungi Admin via WhatsApp
                        </a>
                    </div>
                </div>
            @endif
        </div>
        @endif

        {{-- AJUKAN PEMBATALAN (Jika Pending) --}}
        @if($order->status === 'pending')
            <div x-data="{ openCancel: false }" class="mt-6">

                {{-- Tombol utama --}}
                <div class="mb-6">
                    <button 
                        @click="openCancel = !openCancel"
                        class="px-5 py-2.5 rounded-lg font-semibold shadow-lg transition
                            bg-red-600 text-white hover:bg-red-700">
                        <span x-show="!openCancel">Ingin Membatalkan Pesanan?</span>
                        <span x-show="openCancel">Tutup Form Pembatalan</span>
                    </button>
                </div>

                {{-- FORM PEMBATALAN --}}
                <div 
                    x-show="openCancel"
                    x-transition
                    class="bg-red-50 border border-red-300 p-5 rounded-xl shadow-lg">
                    <h3 class="text-xl font-bold text-red-700 mb-4 uppercase text-center">
                        Ajukan Pembatalan
                    </h3>

                    <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <label class="block font-medium mb-2 text-red-600">
                            Alasan Pembatalan
                        </label>

                        <textarea name="cancellation_reason"
                            class="w-full border-red-300 focus:ring-red-500 rounded-lg p-3 mb-3"
                            placeholder="Mengapa kamu ingin membatalkan pesanan?"
                            required></textarea>

                        <button type="button"
                                onclick="validateCancel()"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>
</section>

@push('scripts')

<script>
    // Preview sebelum upload
    document.getElementById('paymentInput')?.addEventListener('change', e => {
        const preview = document.getElementById('imagePreview');
        preview.src = URL.createObjectURL(e.target.files[0]);
        preview.classList.remove('hidden');
    });
</script>

<script>
    function validateCancel() {
        const reason = document.querySelector('textarea[name="cancellation_reason"]').value.trim();

        if (reason === "") {
            alert("Alasan pembatalan wajib diisi ya!");
            return;
        }

        if (confirm("Yakin ingin membatalkan pesanan ini?")) {
            document.querySelector('form[action*="order"]').submit();
        }
    }
</script>


@endpush
@endsection