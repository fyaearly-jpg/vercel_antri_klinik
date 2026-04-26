<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="p-4">
 
<script>
    // Kalau sudah login, langsung redirect
    const role = localStorage.getItem('role');
    if (role === 'pasien') window.location.href = '/dashboard_pasien.php';
    else if (role) window.location.href = '/dashboard_petugas.php';
</script>
 
<div class="w-full max-w-md">
    <div class="bg-white rounded-[2rem] shadow-2xl p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hospital text-emerald-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800">KLINIK SEHAT</h2>
            <p class="text-slate-400 text-sm mt-1">Silakan masuk ke akun Anda</p>
        </div>
 
        <div id="alert-box" class="hidden bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-5"></div>
 
        <form onsubmit="doLogin(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" id="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 focus:border-transparent outline-none transition-all" placeholder="nama@email.com" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" id="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 focus:border-transparent outline-none transition-all" placeholder="••••••••" required>
            </div>
            <button type="submit" id="btn-login" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-emerald-200 mt-2">
                MASUK
            </button>
        </form>
 
        <p class="text-center text-sm text-slate-400 mt-6">
            Belum punya akun? <a href="register.php" class="text-emerald-600 font-bold hover:underline">Daftar</a>
        </p>
    </div>
</div>
 
<script>
async function doLogin(e) {
    e.preventDefault();
    const btn   = document.getElementById('btn-login');
    const alert = document.getElementById('alert-box');
    btn.disabled    = true;
    btn.textContent = 'Memproses...';
    alert.classList.add('hidden');
 
    const fd = new FormData();
    fd.append('email',    document.getElementById('email').value);
    fd.append('password', document.getElementById('password').value);
 
    try {
        const res  = await fetch('/api/cek_login.php', { method: 'POST', body: fd });
        const data = await res.json();
 
        if (data.success) {
            localStorage.setItem('role', data.role);
            localStorage.setItem('nama', data.nama);
            localStorage.setItem('id',   data.id);
            window.location.href = data.redirect;
        } else {
            alert.textContent = data.message;
            alert.classList.remove('hidden');
            btn.disabled    = false;
            btn.textContent = 'MASUK';
        }
    } catch (err) {
        alert.textContent = 'Terjadi kesalahan koneksi, coba lagi.';
        alert.classList.remove('hidden');
        btn.disabled    = false;
        btn.textContent = 'MASUK';
    }
}
</script>
 
</body>
</html>