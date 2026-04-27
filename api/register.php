<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
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

        <form action="proses_register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all" placeholder="Nama sesuai identitas" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all" placeholder="nama@email.com" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Daftar Sebagai</label>
                <select name="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 outline-none transition-all bg-white">
                    <option value="pasien">Pasien</option>
                    <option value="staff">Petugas (Staff)</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-emerald-200 mt-2">
                DAFTAR SEKARANG
            </button>
        </form>

        <p class="text-center text-sm text-slate-400 mt-6">
            Sudah punya akun? <a href="login.php" class="text-emerald-600 font-bold hover:underline">Masuk</a>
        </p>
    </div>
</div>

</body>
</html>