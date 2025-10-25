<!-- filepath: resources/views/templates/letters/surat-izin-atasan.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Izin Atasan</title>
</head>
<body class="font-['Arial',sans-serif] m-0 p-5 text-[12pt]">
    <div class="leading-relaxed">
        <p>Saya yang bertanda tangan di bawah ini:</p>
        <table class="w-full mb-5" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="py-1.5 align-top w-[150px]">Nama</td>
                <td class="py-1.5 align-top">: {{ $signerName ?? 'Prof. Dr. Sofia W. Alisjahbana, M.Sc., Ph.D., IPU., ASEAN Eng.' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">NIDN</td>
                <td class="py-1.5 align-top">: {{ $signerNidn ?? '[NIDN Penandatangan]' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">Pangkat/Golongan</td>
                <td class="py-1.5 align-top">: {{ $signerRank ?? '[Pangkat/Golongan]' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">Jabatan</td>
                <td class="py-1.5 align-top">: {{ $signerPosition ?? 'Rektor' }}</td>
            </tr>
        </table>
        <p>Dengan ini memberikan izin kepada:</p>
        <table class="w-full mb-5" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="py-1.5 align-top w-[150px]">Nama</td>
                <td class="py-1.5 align-top">: {{ $recipientName ?? '[Nama Penerima Izin]' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">NIDN</td>
                <td class="py-1.5 align-top">: {{ $recipientNidn ?? '[NIDN Penerima Izin]' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">Pangkat/Golongan</td>
                <td class="py-1.5 align-top">: {{ $recipientRank ?? '[Pangkat/Golongan]' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">Jabatan</td>
                <td class="py-1.5 align-top">: {{ $recipientPosition ?? '[Jabatan Penerima Izin]' }}</td>
            </tr>
        </table>
        <p>Untuk mengikuti Program:</p>
        <p>{!! $purpose ?? '[Tujuan Izin]' !!}</p>
        <p>Demikian surat izin ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>
    </div>
    <div class="mt-12 text-left float-right w-2/5">
        <p>Jakarta, {{ $letterDate ?? date('d F Y') }}</p>
        <br><br><br><br>
        <p>{{ $signerName ?? 'Prof. Dr. Sofia W. Alisjahbana, M.Sc., Ph.D., IPU., ASEAN Eng.' }}</p>
        <p>{{ $signerPosition ?? 'Rektor' }}</p>
    </div>
</body>
</html>