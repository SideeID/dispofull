<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Surat - {{ $agenda->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 2cm;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .header p {
            font-size: 9pt;
            margin: 2px 0;
            color: #333;
        }
        
        .info-section {
            margin: 15px 0 20px 0;
            background: #f5f5f5;
            padding: 10px;
            border-left: 4px solid #333;
        }
        
        .info-section table {
            width: 100%;
        }
        
        .info-section td {
            padding: 3px 5px;
            font-size: 10pt;
        }
        
        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        .content-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
        }
        
        table.letters-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.letters-table thead th {
            background: #333;
            color: #fff;
            padding: 8px 5px;
            font-size: 9pt;
            text-align: left;
            border: 1px solid #333;
        }
        
        table.letters-table tbody td {
            padding: 6px 5px;
            font-size: 9pt;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        
        table.letters-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .date-group {
            margin-top: 15px;
        }
        
        .date-group-header {
            background: #e0e0e0;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .badge-incoming {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-outgoing {
            background: #cce5ff;
            color: #004085;
        }
        
        .badge-draft {
            background: #e0e0e0;
            color: #555;
        }
        
        .badge-signed {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-archived {
            background: #f8d7da;
            color: #721c24;
        }
        
        .summary {
            margin-top: 20px;
            padding: 10px;
            background: #f5f5f5;
            font-size: 10pt;
        }
        
        .footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-area {
            width: 40%;
            float: right;
            text-align: center;
            margin-top: 20px;
        }
        
        .signature-area p {
            margin: 5px 0;
            font-size: 10pt;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Universitas Bakrie</h1>
        <h2>Agenda Surat</h2>
        <p>Jl. H.R. Rasuna Said Kav. C-22, Kuningan, Jakarta Selatan 12920</p>
        <p>Telp: (021) 526 1448 | Email: info@bakrie.ac.id</p>
    </div>

    <!-- Agenda Information -->
    <div class="info-section">
        <table>
            <tr>
                <td>Judul Agenda</td>
                <td>: {{ $agenda->title }}</td>
            </tr>
            <tr>
                <td>Tipe</td>
                <td>: 
                    @if($agenda->type === 'daily')
                        Harian
                    @elseif($agenda->type === 'weekly')
                        Mingguan
                    @elseif($agenda->type === 'monthly')
                        Bulanan
                    @else
                        {{ ucfirst($agenda->type) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>: {{ \Carbon\Carbon::parse($agenda->start_date)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($agenda->end_date)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Agenda</td>
                <td>: {{ \Carbon\Carbon::parse($agenda->agenda_date)->format('d F Y') }}</td>
            </tr>
            @if($agenda->department)
            <tr>
                <td>Departemen</td>
                <td>: {{ $agenda->department->name }}</td>
            </tr>
            @endif
            @if($agenda->description)
            <tr>
                <td>Deskripsi</td>
                <td>: {{ $agenda->description }}</td>
            </tr>
            @endif
            <tr>
                <td>Jumlah Surat</td>
                <td>: {{ $letters->count() }} surat</td>
            </tr>
            <tr>
                <td>Dicetak</td>
                <td>: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Letters Content -->
    <div class="content-title">Daftar Surat</div>

    @if($letters->isEmpty())
        <div class="no-data">
            Tidak ada surat yang sesuai dengan kriteria agenda ini
        </div>
    @else
        <!-- Group letters by date -->
        @php
            $lettersByDate = $letters->groupBy(function($letter) {
                return \Carbon\Carbon::parse($letter->letter_date)->format('Y-m-d');
            });
        @endphp

        @foreach($lettersByDate as $date => $dateLetters)
            <div class="date-group">
                <div class="date-group-header">
                    📅 {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    ({{ $dateLetters->count() }} surat)
                </div>

                <table class="letters-table">
                    <thead>
                        <tr>
                            <th width="4%">No</th>
                            <th width="15%">Nomor Surat</th>
                            <th width="28%">Perihal</th>
                            <th width="15%">Dari/Ke</th>
                            <th width="12%">Jenis</th>
                            <th width="10%">Arah</th>
                            <th width="10%">Status</th>
                            <th width="6%">Lamp.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dateLetters as $index => $letter)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $letter->letter_number ?: '-' }}</td>
                            <td>{{ $letter->subject }}</td>
                            <td>
                                @if($letter->direction === 'incoming')
                                    <strong>Dari:</strong> {{ $letter->sender_name ?: $letter->sender_department }}
                                @else
                                    <strong>Ke:</strong> {{ $letter->recipient_name ?: $letter->recipient_department }}
                                @endif
                            </td>
                            <td>{{ $letter->letterType->name ?? '-' }}</td>
                            <td>
                                @if($letter->direction === 'incoming')
                                    <span class="badge badge-incoming">Masuk</span>
                                @else
                                    <span class="badge badge-outgoing">Keluar</span>
                                @endif
                            </td>
                            <td>
                                @if($letter->status === 'draft')
                                    <span class="badge badge-draft">Draft</span>
                                @elseif($letter->status === 'signed')
                                    <span class="badge badge-signed">Signed</span>
                                @elseif($letter->status === 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($letter->status === 'archived')
                                    <span class="badge badge-archived">Archived</span>
                                @else
                                    {{ ucfirst($letter->status) }}
                                @endif
                            </td>
                            <td class="text-center">{{ $letter->attachments_count > 0 ? $letter->attachments_count : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <!-- Summary -->
        <div class="summary">
            <strong>Ringkasan:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Total Surat: {{ $letters->count() }} surat</li>
                <li>Surat Masuk: {{ $letters->where('direction', 'incoming')->count() }} surat</li>
                <li>Surat Keluar: {{ $letters->where('direction', 'outgoing')->count() }} surat</li>
                <li>Dengan Lampiran: {{ $letters->where('attachments_count', '>', 0)->count() }} surat</li>
            </ul>
        </div>
    @endif

    <!-- Footer with Signature -->
    <div class="footer clearfix">
        <div class="signature-area">
            <p>Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p><strong>{{ $agenda->department ? 'Kepala ' . $agenda->department->name : 'Administrator' }}</strong></p>
            <div class="signature-line">
                <p><strong>{{ $agenda->creator->name ?? 'System' }}</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
