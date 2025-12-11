<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk Terlaris</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #ffc107;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #ffc107;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .no-print {
            text-align: center;
            margin: 20px 0;
        }
        
        .no-print button {
            background: #ffc107;
            color: #333;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Cetak / Save as PDF
        </button>
        <button style="background: #6c757d; color: white;" onclick="window.close()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
    
    <div class="header">
        <h1>LAPORAN PRODUK TERLARIS</h1>
        <p>Periode: {{ request('period', '30 hari terakhir') }}</p>
        <p>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB</p>
    </div>
    
    <!-- Content will be added if needed -->
</body>
</html>