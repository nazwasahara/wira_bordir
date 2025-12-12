@extends('layouts.order')

@section('title', 'Riwayat Pemesanan')

@section('content')
<section class="bg-linear-to-b from-white/75 via-white/90 to-sage min-h-screen py-12 px-6 md:px-12 lg:px-12">

    <div class="flex justify-end mb-10">
        <a href="{{ route('home') }}"
        class="bg-moss hover:bg-rose text-white font-semibold px-5 py-3 rounded-full shadow-lg transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4m10-11v10a1 1 0 01-1 1h-4" />
            </svg>
            Kembali ke Home
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <h2 class="text-5xl font-bold font-display mb-8 text-center text-moss">Order History</h2>

        @if($orders->isEmpty())
            <p class="text-center text-gray-600">Belum ada pemesanan.</p>
        @else
            <div class="overflow-x-auto rounded-2xl shadow-2xl">
                <table class="min-w-full bg-white">
                    <thead class="bg-moss text-white text-lg uppercase">
                        <tr>
                            <th class="py-3 px-5 text-center">Order ID</th>
                            <th class="py-3 px-5 text-center">Customer Name</th>
                            <th class="py-3 px-5 text-center">Date</th>
                            <th class="py-3 px-5 text-center">Status</th>
                            <th class="py-3 px-5 text-center">Total</th>
                            <th class="py-3 px-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-t border-moss hover:bg-gray-50">
                                <td class="py-3 px-5 text-center">{{ $order->id }}</td>
                                <td class="py-3 px-5 text-left">{{ $order->customer_name }}</td>
                                <td class="py-3 px-5 text-center">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="py-3 px-5 text-center">
                                    @php
                                        $status = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                                            'paid' => 'bg-blue-100 text-blue-700 border border-blue-300',
                                            'confirm' => 'bg-green-100 text-green-700 border border-green-300',
                                            'processing' => 'bg-purple-100 text-purple-700 border border-purple-300',
                                            'done' => 'bg-emerald-100 text-emerald-700 border border-emerald-300',
                                            'cancel' => 'bg-red-100 text-red-700 border border-red-300',
                                        ];
                                    @endphp

                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $status[$order->status] ?? 'bg-gray-200 text-gray-700' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-right">Rp{{ number_format($order->total_price) }}</td>
                                <td class="py-3 px-5 text-center">
                                    <a href="{{ route('order.detail', $order->id) }}" 
                                    class="bg-leaf hover:bg-rose text-white hover:text-white text-sm font-semibold px-4 py-2 gap-2 rounded-full transition-colors inline-flex items-center uppercase" title="Lihat Detail Pesanan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
