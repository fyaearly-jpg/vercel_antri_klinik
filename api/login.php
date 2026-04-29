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
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="p-4">
<div class="w-full max-w-md">
    <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-10 border border-emerald-50">
        
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-emerald-50 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fas fa-hospital text-emerald-600 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">KLINIK SEHAT</h2>
            <p class="text-slate-400 text-sm mt-1 font-medium">Silakan masuk ke akun Anda</p>
        </div>

        <?php 
        if (isset($_GET['pesan']) || isset($_GET['error'])): 
            $pesan = $_GET['pesan'] ?? '';
            $is_error = isset($_GET['error']) || ($pesan !== 'belum_aktif' && $pesan !== 'menunggu_verifikasi');
            
            $bg_color = $is_error ? 'bg-red-50' : 'bg-orange-50';
            $border_color = $is_error ? 'border-red-100' : 'border-orange-100';
            $text_color = $is_error ? 'text-red-600' : 'text-orange-600';
        ?>
        <div class="mb-6 p-4 rounded-2xl border <?php echo "$bg_color $border_color $text_color"; ?> text-sm text-center font-bold animate-pulse">
            <i class="fas <?php echo $is_error ? 'fa-exclamation-circle' : 'fa-clock'; ?> mr-2"></i>
            <?php 
                if ($pesan == 'belum_aktif') {
                    echo "Akun Anda sedang menunggu verifikasi Admin.";
                } elseif ($pesan == 'menunggu_verifikasi') {
                    echo "Pendaftaran berhasil! Hubungi Admin untuk aktivasi.";
                } else {
                    echo "Email atau Password salah. Silakan coba lagi.";
                }
            ?>
        </div>
        <?php endif; ?>

        <form action="/cek_login" method="POST" class="space-y-5">
            <div class="group">
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1 uppercase tracking-widest">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-300">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" 
                        class="w-full pl-11 pr-4 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all text-slate-700 font-medium" 
                        placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="group">
                <label class="block text-xs font-bold text-slate-500 mb-2 ml-1 uppercase tracking-widest">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-300">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" 
                        class="w-full pl-11 pr-4 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all text-slate-700 font-medium" 
                        placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-4 rounded-2xl transition-all shadow-xl shadow-emerald-200 active:scale-[0.98] mt-4 tracking-widest text-sm">
                MASUK SEKARANG
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-50 text-center">
            <p class="text-sm text-slate-400 font-medium">
                Belum punya akun? 
                <a href="/register" class="text-emerald-600 font-bold hover:text-emerald-700 transition-colors ml-1 underline decoration-2 underline-offset-4">Daftar Akun Baru</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>