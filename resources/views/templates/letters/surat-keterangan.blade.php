<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan</title>
</head>
<body class="font-['Arial',sans-serif] m-0 p-5 text-[12pt]">
    <div class="leading-relaxed">
        <p>Rektor Universitas Bakrie menyatakan dengan ini :</p>
        <table class="w-full mb-5" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="py-1.5 align-top w-[150px]">Nama</td>
                <td class="py-1.5 align-top">: {{ $nama ?? '' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">Tempat/Tanggal/Lahir</td>
                <td class="py-1.5 align-top">: {{ $tempatTanggalLahir ?? '' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 align-top">Alamat</td>
                <td class="py-1.5 align-top">: {{ $alamat ?? '' }}</td>
            </tr>
        </table>
        <p>adalah benar {!! $content ?? 'telah dinyatakan lulus proses seleksi dan...' !!}</p>
        <p>Demikian Surat Keterangan ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>
    </div>
</body>
</html>