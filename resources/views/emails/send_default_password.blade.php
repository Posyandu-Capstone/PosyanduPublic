<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Baru</title>
</head>
<body>
    <h2>Halo, {{ $data['name'] }}!</h2>
    <p>Password akun Anda telah direset. Berikut password default baru Anda:</p>
    <div style="padding: 10px; background-color: #f3f3f3; border: 1px solid #ccc; display: inline-block;">
        <strong>{{ $data['default_password'] }}</strong>
    </div>
    <p>Silakan login dan segera ubah password Anda demi keamanan.</p>
    <p>Terima kasih,</p>
    <p><strong>Tim PosyanduCare</strong></p>
</body>
</html>