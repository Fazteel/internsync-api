<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternSync - Autentikasi</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; color: #1f2937; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; }
        .header { background-color: #111827; padding: 24px; text-align: center; } 
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 0.5px; }
        .header span { color: #3b82f6; } 
        .content { padding: 32px; }
        .greeting { font-size: 20px; font-weight: 600; margin-bottom: 16px; color: #111827; }
        .text { font-size: 15px; line-height: 1.6; margin-bottom: 24px; color: #4b5563; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; padding: 14px 28px; background-color: #3b82f6; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; }
        .footer { background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Intern<span>Sync</span></h1>
        </div>
        <div class="content">
            <div class="greeting">Halo, {{ $user->name }}!</div>
            
            @if($type === 'activation')
                <p class="text">Selamat datang di Sistem Manajemen PKL (InternSync). Akun Anda telah berhasil didaftarkan oleh Administrator sekolah.</p>
                <p class="text">Untuk mengaktifkan akun dan mulai menggunakan sistem, silakan atur password baru Anda dengan menekan tombol di bawah ini:</p>
            @else
                <p class="text">Kami menerima permintaan untuk mereset password akun InternSync Anda.</p>
                <p class="text">Jika Anda memang merasa meminta reset password, silakan buat password baru melalui tombol di bawah ini. Jika bukan Anda, abaikan saja email ini dan akun Anda akan tetap aman.</p>
            @endif

            <div class="btn-container">
                <a href="{{ $link }}" class="btn">
                    {{ $type === 'activation' ? 'Set Password Sekarang' : 'Reset Password' }}
                </a>
            </div>

            <p class="text" style="font-size: 14px; text-align: center; color: #9ca3af;">
                Link tautan ini bersifat rahasia dan hanya berlaku selama 60 menit ke depan.
            </p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem InternSync.</p>
            <p>Harap tidak membalas pesan email ini.</p>
        </div>
    </div>
</body>
</html>