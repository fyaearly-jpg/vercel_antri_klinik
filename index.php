<?php 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antri Klinik Digital | Solusi Kesehatan Modern</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .bg-gradient-hero { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
    </style>
</head>

<body class="bg-slate-50 text-slate-800">

    <header class="bg-gradient-hero min-h-[70vh] flex items-center justify-center p-6 rounded-b-[60px] shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <i class="fas fa-plus-circle text-[20rem] absolute -top-20 -left-20"></i>
            <i class="fas fa-heartbeat text-[15rem] absolute -bottom-10 -right-10"></i>
        </div>

        <div class="text-center text-white max-w-4xl z-10 animate-fade-in-up"> 
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight tracking-tight">
                Antri Klinik Digital <br><span class="text-emerald-200">Tanpa Antre Lama</span>
            </h1>
            <p class="text-lg md:text-xl text-emerald-50 mb-10 opacity-90 max-w-2xl mx-auto font-light leading-relaxed">
                Solusi cerdas manajemen antrean pasien secara real-time. Fokus pada kesembuhan, bukan pada durasi menunggu.
            </p>
            <div class="flex flex-col md:flex-row gap-5 justify-center">
                <a href="login.php" class="bg-white text-emerald-700 px-10 py-4 rounded-2xl font-bold text-lg hover:bg-emerald-50 transition-all shadow-xl hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-3"></i> Masuk Ke Sistem
                </a>
                <a href="register.php" class="bg-emerald-800/30 border-2 border-white/50 text-white px-10 py-4 rounded-2xl font-bold text-lg hover:bg-emerald-800/50 transition-all shadow-xl hover:-translate-y-1 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-3"></i> Daftar Akun
                </a>
            </div>
        </div>
    </header>

    <section class="container mx-auto px-6 -mt-20 relative z-20 mb-24"> 
        <div class="grid md:grid-cols-3 gap-8">
            <div class="glass p-10 rounded-[40px] shadow-xl border border-white hover:shadow-2xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-inner">
                    <i class="fas fa-mobile-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-800">Daftar Dari Mana Saja</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Pasien bisa mengambil nomor antrean secara online tanpa harus datang pagi-pagi ke klinik.
                </p>
            </div>

            <div class="glass p-10 rounded-[40px] shadow-xl border border-white hover:shadow-2xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-inner">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-800">Panggilan Otomatis</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Sistem akan memanggil nomor antrean menggunakan suara (Voice Synthesis) secara otomatis.
                </p>
            </div>

            <div class="glass p-10 rounded-[40px] shadow-xl border border-white hover:shadow-2xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 group-hover:text-white transition-all shadow-inner">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-800">Keamanan Data</h3>
                <p class="text-slate-500 leading-relaxed text-sm">
                    Data pasien tersimpan dengan aman dalam sistem database terintegrasi yang teratur.
                </p>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 mb-32">
        <div class="bg-white p-12 md:p-20 rounded-[60px] shadow-sm border border-slate-100 text-center relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-slate-800 mb-6">Misi Kami</h2>
                <p class="text-lg text-slate-500 leading-loose mb-12 max-w-3xl mx-auto">
                    Meningkatkan kualitas pelayanan kesehatan di Indonesia melalui digitalisasi antrean yang transparan dan efisien.
                </p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4">
                        <p class="text-3xl font-black text-emerald-600">100%</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Digital</p>
                    </div>
                    <div class="p-4">
                        <p class="text-3xl font-black text-emerald-600">Real-time</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Update</p>
                    </div>
                    <div class="p-4">
                        <p class="text-3xl font-black text-emerald-600">Free</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Access</p>
                    </div>
                    <div class="p-4">
                        <p class="text-3xl font-black text-emerald-600">Easy</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Interface</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-10 bg-slate-900 text-slate-500 text-sm font-medium">
        <p>&copy; 2026 Antri Klinik Digital &bull; Universitas Sebelas Maret Informatics</p>
        <div class="mt-4 flex justify-center gap-6 text-slate-600">
            <i class="fab fa-instagram hover:text-emerald-500 cursor-pointer"></i>
            <i class="fab fa-github hover:text-emerald-500 cursor-pointer"></i>
            <i class="fab fa-linkedin hover:text-emerald-500 cursor-pointer"></i>
        </div>
    </footer>
    
</body>
</html>