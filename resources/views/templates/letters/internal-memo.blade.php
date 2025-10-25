<!-- filepath: resources/views/templates/letters/internal-memo.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Memo</title>
</head>
<body class="font-['Arial',sans-serif] m-0 p-5 text-[12pt]">
   <table class="w-full mb-7" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="py-1.5 w-[100px]">Kepada Yth.</td>
            <td class="py-1.5">: {{ $recipient ?? '[Nama Penerima]' }}</td>
        </tr>
        <tr>
            <td class="py-1.5">Tanggal</td>
            <td class="py-1.5">: {{ $letterDate ?? date('d F Y') }}</td>
        </tr>
        <tr>
            <td class="py-1.5">Perihal</td>
            <td class="py-1.5">: {{ $subject ?? '[Perihal Memo]' }}</td>
        </tr>
    </table>
    <div class="leading-relaxed mb-10">
        <p>Dear {{ $recipientTitle ?? 'Bapak/Ibu' }},</p>
        <p>{!! $content ?? '[Isi memo internal]' !!}</p>
        <p>Demikian memo ini kami sampaikan. Atas perhatiannya kami ucapkan terima kasih.</p>
        <p>Salam,</p>
    </div>
    <table class="w-full mt-16" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="align-top w-1/3 text-center">
                <div class="h-20"></div>
                <p>{{ $senderName ?? '[Nama Pengirim]' }}</p>
                <p>{{ $senderPosition ?? '[Jabatan Pengirim]' }}</p>
            </td>
            <td class="align-top w-1/3 text-center">
                <div class="h-20"></div>
                <p>{{ $reviewerName ?? 'Telah Mengetahui' }}</p>
                <p>{{ $reviewerPosition ?? '[Jabatan Reviewer]' }}</p>
            </td>
            <td class="align-top w-1/3 text-center">
                <div class="h-20"></div>
                <p>{{ $approverName ?? 'Menyetujui' }}</p>
                <p>{{ $approverPosition ?? '[Jabatan Approver]' }}</p>
            </td>
        </tr>
    </table>
</body>
</html>