@component('mail::message')
# 🔐 Permintaan Reset Password

Halo, kami menerima permintaan untuk mereset password akun Anda.  
Klik tombol di bawah ini untuk melanjutkan proses reset password:

@component('mail::button', ['url' => $url, 'color' => 'primary'])
🔁 Reset Password Sekarang
@endcomponent

> Demi keamanan akun Anda, tautan ini hanya berlaku untuk jangka waktu tertentu.

Jika Anda **tidak merasa meminta reset password**, abaikan email ini.  
Akun Anda tetap aman selama tidak ada aksi yang dilakukan.

---

Salam hangat,  
**Tim {{ config('app.name') }}**
@endcomponent
