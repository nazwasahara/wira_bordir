<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .invoice-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 14px;
            color: #666;
            font-style: italic;
        }

        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
        }

        .invoice-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-meta-item {
            flex: 1;
        }

        .invoice-meta-item h3 {
            font-size: 14px;
            color: #667eea;
            margin-bottom: 10px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }

        .invoice-meta-item p {
            margin: 5px 0;
        }

        .invoice-meta-item strong {
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table thead {
            background: #667eea;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .item-details {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .item-details-list {
            list-style: none;
            margin: 5px 0;
            padding-left: 10px;
        }

        .item-details-list li {
            margin: 3px 0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-table {
            margin-left: auto;
            width: 300px;
            margin-bottom: 30px;
        }

        .summary-table td {
            padding: 8px 12px;
        }

        .summary-table .total-row {
            background: #667eea;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-confirmed {
            background: #17a2b8;
            color: white;
        }

        .status-processing {
            background: #007bff;
            color: white;
        }

        .status-completed {
            background: #28a745;
            color: white;
        }

        .status-cancelled {
            background: #dc3545;
            color: white;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #667eea;
            color: #666;
            font-size: 11px;
        }

        .notes {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 30px;
        }

        .notes h4 {
            color: #667eea;
            margin-bottom: 10px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .invoice-container {
                box-shadow: none;
            }
        }

        .print-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Print Buttons -->
        <div class="print-buttons no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-name">SASH CUSTOM</div>
                <div class="company-tagline">Custom Sash & Accessories</div>
            </div>
            <div class="invoice-title">INVOICE</div>
        </div>

        <!-- Invoice Meta -->
        <div class="invoice-meta">
            <div class="invoice-meta-item">
                <h3>Informasi Pesanan</h3>
                <p><strong>No. Invoice:</strong> {{ $order->order_number }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y') }}</p>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-{{ $order->status }}">
                        {{ $order->status_text }}
                    </span>
                </p>
            </div>
            <div class="invoice-meta-item">
                <h3>Informasi Pelanggan</h3>
                <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
                <p><strong>Telepon:</strong> {{ $order->customer_phone_number }}</p>
                <p><strong>Alamat:</strong> {{ $order->customer_address }}</p>
            </div>
        </div>

        <!-- Order Items Table -->
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="55%">Deskripsi Item</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $item->product->product_name ?? 'N/A' }}</strong>
                        
                        <ul class="item-details-list">
                            @if($item->material)
                                <li>• Material: {{ $item->material->name }}</li>
                            @endif
                            @if($item->materialColor)
                                <li>• Warna Material: {{ $item->materialColor->name }}</li>
                            @endif
                            @if($item->sashType)
                                <li>• Tipe Sash: {{ $item->sashType->name }}</li>
                            @endif
                            @if($item->font)
                                <li>• Font: {{ $item->font->name }}</li>
                            @endif
                            @if($item->sideMotif)
                                <li>• Motif Samping: {{ $item->sideMotif->name }}</li>
                            @endif
                            @if($item->ribbonColor)
                                <li>• Warna Pita: {{ $item->ribbonColor->name }}</li>
                            @endif
                            @if($item->laceOption)
                                <li>• Renda: {{ $item->laceOption->color }} ({{ $item->laceOption->size_indonesia }})</li>
                            @endif
                            @if($item->rombeOption)
                                <li>• Rombe: {{ $item->rombeOption->color }} ({{ $item->rombeOption->size_indonesia }})</li>
                            @endif
                            @if($item->motifRibbonOption)
                                <li>• Pita Motif: {{ $item->motifRibbonOption->color }} ({{ $item->motifRibbonOption->size_indonesia }})</li>
                            @endif
                            @if($item->additionalItemOption)
                                <li>• Item Tambahan: {{ $item->additionalItemOption->additionalItem->name ?? 'N/A' }} - {{ $item->additionalItemOption->color }} ({{ $item->additionalItemOption->model }})</li>
                            @endif
                            @if($item->text_right)
                                <li>• Teks Kanan: {{ $item->text_right }}</li>
                            @endif
                            @if($item->text_left)
                                <li>• Teks Kiri: {{ $item->text_left }}</li>
                            @endif
                            @if($item->text_single)
                                <li>• Teks Tunggal: {{ $item->text_single }}</li>
                            @endif
                            <li>• Logo: {{ $item->logo_path ? 'Ada' : 'Tidak' }}</li>
                        </ul>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $item->formatted_final_price }}</td>
                    <td class="text-right"><strong>{{ $item->formatted_subtotal }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <table class="summary-table">
            <tr>
                <td><strong>Total Pesanan:</strong></td>
                <td class="text-right"><strong>{{ $order->formatted_total_price }}</strong></td>
            </tr>
            <tr>
                <td><strong>Jumlah Dibayar:</strong></td>
                <td class="text-right">{{ $order->formatted_amount_paid }}</td>
            </tr>
            @if(!$order->isPaymentComplete())
            <tr>
                <td><strong>Sisa Pembayaran:</strong></td>
                <td class="text-right" style="color: #dc3545;"><strong>{{ $order->formatted_remaining_payment }}</strong></td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">{{ $order->formatted_total_price }}</td>
            </tr>
        </table>

        <!-- Notes -->
        <div class="notes">
            <h4>Catatan:</h4>
            <p>• Invoice ini adalah bukti pemesanan yang sah.</p>
            <p>• Harap lakukan pembayaran sesuai dengan jumlah yang tertera.</p>
            <p>• Pesanan akan diproses setelah pembayaran dikonfirmasi.</p>
            @if($order->status === \App\Models\Order::STATUS_DONE)
                <p style="color: #28a745;"><strong>✓ Pesanan ini telah selesai.</strong></p>
            @endif
            @if($order->status === \App\Models\Order::STATUS_CANCEL && $order->cancelledTransaction)
                <p style="color: #dc3545;"><strong>✗ Pesanan ini telah dibatalkan.</strong></p>
                <p style="color: #dc3545;">Alasan: {{ $order->cancelledTransaction->cancellation_reason }}</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda!</p>
            <p><strong>SASH CUSTOM</strong> - Custom Sash & Accessories</p>
            <p>Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB</p>
        </div>
    </div>

    <script>
        // Auto print when loaded (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>