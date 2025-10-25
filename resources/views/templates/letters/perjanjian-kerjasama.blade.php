<!-- filepath: resources/views/templates/letters/perjanjian-kerjasama.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Kerjasama</title>
</head>
<body class="font-['Arial',sans-serif] m-0 p-5 text-[12pt]">
    <div class="text-left mb-5">Jakarta, {{ $letterDate ?? date('d F Y') }}</div>
    <div class="mb-5">
        <p class="my-1.5">Nomor&nbsp;&nbsp;&nbsp;: {{ $letterNumber ?? '____/UB/R-K/___/____' }}</p>
        <p class="my-1.5">Lampiran : {{ $attachment ?? '-' }}</p>
        <p class="my-1.5">Perihal&nbsp;&nbsp;&nbsp;: {{ $subject ?? 'Permohonan Penandatanganan Perjanjian Kerja Sama (PKS)' }}</p>
    </div>
    <div class="mb-5">
        <p>Kepada Yth.</p>
        <p>{{ $recipientName ?? '[Nama Penerima]' }}</p>
        <p>{{ $recipientPosition ?? '[Jabatan Penerima]' }}</p>
        <p>di Tempat</p>
    </div>
    <div class="leading-relaxed">
        <p>Dengan hormat,</p>
        <p><strong>"{!! $content ?? 'Kami dari pihak Bakrie selaku calon mitra kerja PT dan atas izin SIPT dan seluruh dalam menjalankan kegiatan dibawah ini..."' !!}"</strong></p>
        <p>Demikian permohonan kerjasama ini kami sampaikan. Atas perhatian Bapak, kami ucapkan terima kasih.</p>
        <p>Hormat kami,</p>
    </div>
    <div class="mt-12 text-left float-right w-2/5">
        <br><br><br><br>
        <p>{{ $signerName ?? 'Prof. Dr. Sofia W. Alisjahbana, M.Sc., Ph.D., IPU., ASEAN Eng.' }}</p>
        <p>{{ $signerPosition ?? 'Rektor' }}</p>
    </div>
</body>
</html>