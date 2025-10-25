<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas</title>
</head>
<body class="font-['Arial',sans-serif] m-0 p-5 text-[12pt]">

    <div class="leading-relaxed">
        <p>Wakil Rektor I Universitas Bakrie dengan ini menugaskan kepada nama-nama di bawah ini:</p>
        
        <p>{!! $content ?? '[Isi daftar nama personil yang ditugaskan]' !!}</p>
        
        <p>Kegiatan tersebut tidak mengganggu tugas pokok dan kegiatan lainnya yang ditetapkan oleh Universitas Bakrie.</p>
        
        <p>Demikian Surat Tugas ini diberikan untuk dilaksanakan sebaik-baiknya.</p>
    </div>
    
    <div class="mt-12 text-left float-right w-2/5">
        <p class="mb-1 leading-relaxed">Jakarta, {{ $letterDate ?? date('d F Y') }}</p>
        <br><br><br><br>
        <p class="mb-1 leading-relaxed">{{ $signerName ?? '[Nama Penandatangan]' }}</p>
        <p class="mb-1 leading-relaxed">{{ $signerPosition ?? '[Jabatan Penandatangan]' }}</p>
    </div>
    
    <div class="mt-20 text-[9pt] clear-both" style="page-break-inside: avoid;">
        Bakrie Tower, Lantai 40, 41, 42 <br>
        Jl. H.R. Rasuna Said Kav. B-1, Kuningan, Jakarta Selatan <br>
        Telp: +62 21 526 3337, Fax: +62 21 526 3335, +62 21 526 7784
    </div>
</body>
</html>