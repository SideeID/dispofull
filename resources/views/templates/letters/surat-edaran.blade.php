<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Edaran</title>
</head>
<body class="font-['Arial',sans-serif] m-0 p-5 text-[12pt]">
    <div class="text-center font-bold uppercase my-5 text-[14pt]">{{ $subject ?? 'LAPORAN KEGIATAN UNIT' }}</div>
    <div class="mb-5">
        <p>Kepada Yth.<br>
        Bapak/Ibu Kepala Unit<br>
        di Universitas Bakrie</p>
    </div>
    <div class="leading-relaxed">
        <p>Dengan ini diberitahukan kepada seluruh Unit Kerja di Universitas Bakrie, bahwa:</p>
        <ol class="ml-4 pl-4">
            <li>{!! $point1 ?? '[Isi poin pertama]' !!}</li>
            <li>{!! $point2 ?? '[Isi poin kedua]' !!}</li>
        </ol>
        <p>Demikian Edaran ini disampaikan untuk dapat dipahami dan dilaksanakan dengan sebaik-baiknya.</p>
    </div>
    <div class="mt-12 text-left float-right w-2/5">
        <p>Jakarta, {{ $letterDate ?? date('d F Y') }}</p>
        <br><br><br><br>
        <p>{{ $signerName ?? 'Prof. Dr. Sofia W. Alisjahbana, M.Sc., Ph.D., IPU., ASEAN Eng.' }}</p>
        <p>{{ $signerPosition ?? 'Rektor' }}</p>
    </div>
</body>
</html>