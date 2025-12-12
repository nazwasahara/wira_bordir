@extends('layouts.order')

@section('title', 'Pembayaran')

@section('content')
<section class="bg-linear-to-b from-white/75 via-white/90 to-sage min-h-screen py-16 px-6 md:px-12 lg:px-16">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-lg text-center space-y-6">
            <h2 class="text-4xl font-bold font-display text-moss mb-6 text-center">Pembayaran Selempang</h2>
            <p class="text-text-dark">Terima kasih telah melakukan pemesanan!<br>Silakan download QRIS di bawah untuk melakukan pembayaran.</p>

            {{-- Summary Order --}}
            <div class="bg-sage/20 p-4 rounded-lg border border-sage shadow-inner transition-transform duration-300 ease-in-out transform hover:scale-101 hover:shadow-md">
                <p class="text-lg font-semibold text-text-dark">
                    Total Harga Pesanan: 
                    <span class="text-moss hover:text-rose">Rp{{ number_format($order->total_price) }}</span>
                </p>
            </div>

            {{-- QRIS --}}
            <div class="bg-gray-100 py-6 rounded-lg shadow-inner">
                <img src="{{ asset('images/qris.jpeg') }}" alt="QRIS Pembayaran" class="mx-auto h-90 object-contain mb-5 rounded-lg shadow-md border border-moss p-2 bg-white transition-transform duration-300 ease-in-out transform hover:scale-101 hover:shadow-xl">
                <a href="{{ asset('images/qris.jpeg') }}" download
                class="bg-leaf hover:bg-kuning text-white hover:text-text-dark font-bold rounded-full py-3 px-8 transition-colors shadow-md uppercase tracking-wide">
                    Download QRIS
                </a>
            </div>

            {{-- Catatan --}}
            <p class="text-sm text-text-dark mt-2">Catatan: Simpan QRIS dan lakukan pembayaran sesuai total harga pesanan Anda.</p>

            {{-- Tombol ke Riwayat --}}
            <div class="flex justify-end mt-8">
                <a href="{{ route('order.history') }}"
                class="flex items-center gap-1 pl-7 pr-6 py-3 bg-leaf text-white font-bold rounded-full hover:bg-rose transition-colors shadow-md uppercase tracking-wide">
                Lihat Riwayat Pemesanan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                </a>
            </div>

        </div>
</section>
@endsection
