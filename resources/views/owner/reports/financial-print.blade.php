<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - {{ $report['period']['label'] }}</title>
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header h2 {
            color: #666;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #999;
            margin: 5px 0;
        }
        
        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .summary-card.revenue {
            border-color: #28a745;
            background: #f0f9f4;
        }
        
        .summary-card.expense {
            border-color: #dc3545;
            background: #fdf4f5;
        }
        
        .summary-card.profit {
            border-color: #007bff;
            background: #f0f6ff;
        }
        
        .summary-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .summary-card .amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .summary-card.revenue .amount { color: #28a745; }
        .summary-card.expense .amount { color: #dc3545; }
        .summary-card.profit .amount { color: #007bff; }
        
        .summary-card .detail {
            font-size: 11px;
            color: #666;
        }
        
        /* Section Title */
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
            margin: 30px 0 15px 0;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #f8f9fa;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        table th {
            font-weight: bold;
            color: #333;
        }
        
        table tfoot {
            background: #e9ecef;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #007bff; }
        
        /* Payment Status Grid */
        .payment-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .payment-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }
        
        .payment-box h4 {
            font-size: 24px;
            margin: 10px 0;
        }
        
        .payment-box small {
            color: #666;
            font-size: 11px;
        }
        
        /* No Print Elements */
        .no-print {
            text-align: center;
            margin: 20px 0;
        }
        
        .no-print button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        
        .no-print button:hover {
            background: #0056b3;
        }
        
        .no-print .btn-secondary {
            background: #6c757d;
        }
        
        .no-print .btn-secondary:hover {
            background: #545b62;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 10px;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            @page {
                margin: 1cm;
            }
        }
        
        @media screen {
            body {
                background: #f5f5f5;
            }
            
            .container {
                background: white;
                padding: 40px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Buttons -->
        <div class="no-print">
            <button onclick="window.print()">
                <i class="fas fa-print"></i> Cetak / Save as PDF
            </button>
            <button class="btn-secondary" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
        
        <!-- Header -->
        <div class="header">
            <h1>LAPORAN KEUANGAN</h1>
            <h2>{{ $report['period']['label'] }}</h2>
            <p>Periode: {{ $report['period']['start']->format('d M Y') }} - {{ $report['period']['end']->format('d M Y') }}</p>
            <p>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB</p>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card revenue">
                <h3>Total Pemasukan</h3>
                <div class="amount">Rp {{ number_format($report['revenue']['total'], 0, ',', '.') }}</div>
                <div class="detail">
                    {{ $report['revenue']['count'] }} transaksi<br>
                    Rata-rata: Rp {{ number_format($report['revenue']['average'], 0, ',', '.') }}
                </div>
            </div>
            
            <div class="summary-card expense">
                <h3>Total Pengeluaran</h3>
                <div class="amount">Rp {{ number_format($report['expenses']['total'], 0, ',', '.') }}</div>
                <div class="detail">
                    {{ $report['expenses']['count'] }} invoice<br>
                    {{ $report['expenses']['by_category']->count() }} kategori
                </div>
            </div>
            
            <div class="summary-card profit">
                <h3>{{ $report['net_profit'] >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</h3>
                <div class="amount">Rp {{ number_format(abs($report['net_profit']), 0, ',', '.') }}</div>
                <div class="detail">
                    Profit Margin: {{ number_format($report['profit_margin'], 2) }}%
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <h3 class="section-title">STATUS PEMBAYARAN</h3>
        <div class="payment-grid">
            <div class="payment-box">
                <small>Lunas</small>
                <h4 style="color: #28a745;">{{ $report['payment_status']['fully_paid'] }}</h4>
            </div>
            <div class="payment-box">
                <small>Sebagian</small>
                <h4 style="color: #ffc107;">{{ $report['payment_status']['partial_paid'] }}</h4>
            </div>
            <div class="payment-box">
                <small>Belum Bayar</small>
                <h4 style="color: #dc3545;">{{ $report['payment_status']['unpaid'] }}</h4>
            </div>
            <div class="payment-box" style="background: #fff3cd;">
                <small>Total Piutang</small>
                <h4 style="color: #856404;">Rp {{ number_format($report['payment_status']['total_receivable'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Top Revenue Sources -->
        <h3 class="section-title">TOP 5 SUMBER PENDAPATAN</h3>
        <table>
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Produk</th>
                    <th class="text-center" width="100">Qty</th>
                    <th class="text-right" width="180">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['top_sources']->take(5) as $index => $source)
                <tr>
                    <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
                    <td><strong>{{ $source->product_name }}</strong></td>
                    <td class="text-center">{{ $source->total_quantity }}</td>
                    <td class="text-right text-success"><strong>Rp {{ number_format($source->total_revenue, 0, ',', '.') }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="page-break"></div>

        <!-- Detailed Revenue -->
        <h3 class="section-title">DETAIL PEMASUKAN HARIAN</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th class="text-center" width="100">Order</th>
                    <th class="text-right" width="150">Revenue</th>
                    <th class="text-right" width="150">Cash Diterima</th>
                    <th class="text-right" width="150">Rata-rata</th>
                </tr>
            </thead>
            <tbody>
                @php $totalOrders = 0; $totalRevenue = 0; $totalPaid = 0; @endphp
                @forelse($report['detailed_revenue'] as $day)
                    @php
                        $totalOrders += $day->orders_count;
                        $totalRevenue += $day->total_revenue;
                        $totalPaid += $day->total_paid;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</strong>
                            <br><small style="color: #666;">{{ \Carbon\Carbon::parse($day->date)->locale('id')->isoFormat('dddd') }}</small>
                        </td>
                        <td class="text-center">{{ $day->orders_count }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($day->total_revenue, 0, ',', '.') }}</strong></td>
                        <td class="text-right">Rp {{ number_format($day->total_paid, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($day->avg_order_value, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data pemasukan</td>
                    </tr>
                @endforelse
            </tbody>
            @if($report['detailed_revenue']->isNotEmpty())
            <tfoot>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ number_format($totalOrders) }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalPaid, 0, ',', '.') }}</strong></td>
                    <td class="text-right">-</td>
                </tr>
            </tfoot>
            @endif
        </table>

        <div class="page-break"></div>

        <!-- Detailed Expenses -->
        <h3 class="section-title">DETAIL PENGELUARAN (INVOICE)</h3>
        <table>
            <thead>
                <tr>
                    <th width="80">Invoice</th>
                    <th width="90">Tanggal</th>
                    <th>Item</th>
                    <th width="80">Kategori</th>
                    <th class="text-center" width="50">Qty</th>
                    <th class="text-right" width="120">Harga Satuan</th>
                    <th class="text-right" width="130">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @forelse($report['detailed_expenses'] as $invoiceId => $items)
                    @php
                        $invoiceTotal = $items->sum('line_total');
                        $grandTotal += $invoiceTotal;
                    @endphp
                    @foreach($items as $item)
                    <tr>
                        <td><strong>#{{ str_pad($invoiceId, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($item->invoice_date)->format('d M Y') }}</td>
                        <td>{{ $item->item_name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($item->item_type) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background: #f8f9fa;">
                        <td colspan="6" class="text-right"><strong>Subtotal Invoice #{{ $invoiceId }}:</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($invoiceTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pengeluaran</td>
                    </tr>
                @endforelse
            </tbody>
            @if($report['detailed_expenses']->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>GRAND TOTAL PENGELUARAN:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
            @endif
        </table>

        <!-- Final Summary -->
        <table style="margin-top: 30px;">
            <tr>
                <td style="width: 70%; text-align: right; border: none;"><h3>Total Pemasukan:</h3></td>
                <td style="width: 30%; text-align: right; border: none;">
                    <h3 style="color: #28a745;">Rp {{ number_format($report['revenue']['total'], 0, ',', '.') }}</h3>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; border: none;"><h3>Total Pengeluaran:</h3></td>
                <td style="text-align: right; border: none;">
                    <h3 style="color: #dc3545;">Rp {{ number_format($report['expenses']['total'], 0, ',', '.') }}</h3>
                </td>
            </tr>
            <tr style="background: #e9ecef;">
                <td style="text-align: right; padding: 15px;"><h2>{{ $report['net_profit'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}:</h2></td>
                <td style="text-align: right; padding: 15px;">
                    <h2 style="color: {{ $report['net_profit'] >= 0 ? '#007bff' : '#ffc107' }};">
                        Rp {{ number_format(abs($report['net_profit']), 0, ',', '.') }}
                    </h2>
                    <small>Profit Margin: {{ number_format($report['profit_margin'], 2) }}%</small>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Laporan ini dibuat secara otomatis oleh sistem.</p>
            <p>&copy; {{ date('Y') }} - Sash Custom Management System</p>
        </div>
    </div>
</body>
</html>