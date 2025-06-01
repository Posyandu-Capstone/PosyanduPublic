<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Reset Password - PosyanduCare</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $data['nama'] }}, {{ $data['email'] }}!</h2>

    <p>Kami menerima permintaan untuk mereset kata sandi akun Anda di <strong>PosyanduCare</strong>.</p>

    <p>Apakah Anda benar-benar ingin mereset kata sandi Anda?</p>

    <p>Jika ya, silakan klik tombol di bawah ini untuk mengonfirmasi dan mendapatkan kata sandi default baru:</p>

    <p>
        <a href="{{ url('/password/reset-now?email=' . urlencode($data['email'])) }}"
            style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
            Reset Password
        </a>
    </p>

    <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini. Tidak akan ada perubahan pada akun Anda.</p>

    <br>
    <p>Terima kasih,</p>
    <p><strong>Tim PosyanduCare</strong></p>
</body>

</html>