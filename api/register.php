<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Klinik Sehat</title>
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
 
<div class="w-full max-w-md">
    <div class="bg-white rounded-[2rem] shadow-2xl p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-plus text-emerald-600 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800">Daftar Akun</h2>
            <p class="text-slate-400 text-sm mt-1">Lengkapi data untuk akses sistem</p>
        </div>
 
        <div id="alert-box" class="hidden text-sm px-4 py-3 rounded-xl mb-4"></div>
 
        <form onsubmit="doRegister(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="nama" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all" placeholder="Nama sesuai identitas" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" id="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all" placeholder="nama@email.com" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" id="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Daftar Sebagai</label>
                <select id="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all bg-white">
                    <option value="pasien">Pasien</option>
                    <option value="staff">Petugas (Staff)</option>
                </select>
            </div>
            <button type="submit" id="btn-daftar" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-emerald-200 mt-2">
                DAFTAR SEKARANG
            </button>
        </form>
 
        <p class="text-center text-sm text-slate-400 mt-6">
            Sudah punya akun? <a href="login.php" class="text-emerald-600 font-bold hover:underline">Masuk</a>
        </p>
    </div>
</div>
 
<script>
async function doRegister(e) {
    e.preventDefault();
    const btn   = document.getElementById('btn-daftar');
    const alert = document.getElementById('alert-box');
    btn.disabled    = true;
    btn.textContent = 'Memproses...';
    alert.classList.add('hidden');
 
    const fd = new FormData();
    fd.append('nama',     document.getElementById('nama').value);
    fd.append('email',    document.getElementById('email').value);
    fd.append('password', document.getElementById('password').value);
    fd.append('role',     document.getElementById('role').value);
 
    try {
        const res  = await fetch('/api/proses_register.php', { method: 'POST', body: fd });
        const data = await res.json();
 
        if (data.success) {
            alert.className = 'text-sm px-4 py-3 rounded-xl mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700';
            alert.textContent = data.message + ' Silakan login.';
            alert.classList.remove('hidden');
            setTimeout(() => window.location.href = '/login.php', 2000);
        } else {
            alert.className = 'text-sm px-4 py-3 rounded-xl mb-4 bg-red-50 border border-red-200 text-red-600';
            alert.textContent = data.message;
            alert.classList.remove('hidden');
            btn.disabled    = false;
            btn.textContent = 'DAFTAR SEKARANG';
        }
    } catch (err) {
        alert.className = 'text-sm px-4 py-3 rounded-xl mb-4 bg-red-50 border border-red-200 text-red-600';
        alert.textContent = 'Terjadi kesalahan koneksi.';
        alert.classList.remove('hidden');
        btn.disabled    = false;
        btn.textContent = 'DAFTAR SEKARANG';
    }
}
</script>
 
</body>
</html>