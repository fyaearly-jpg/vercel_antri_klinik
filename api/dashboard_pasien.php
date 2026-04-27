<?php
// api/dashboard_pasien.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

// 2. Jika tidak ada cookie, tendang ke login
if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}

$id_user = $cookie_data['id'];
$nama_user = $cookie_data['nama'];

// 3. Ambil data antrean terbaru pasien ini
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_user' ORDER BY id DESC LIMIT 1");
$data_antrian = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Pasien - ImuniCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans min-h-screen">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 h-16 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-clinic-medical text-blue-600 text-xl"></i>
                <span class="font-bold text-slate-800 tracking-tight">ImuniCare</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-slate-500 hidden md:block">Halo, <?php echo htmlspecialchars($nama_pasien); ?></span>
                <a href="/logout" class="text-red-500 text-sm font-bold bg-red-50 px-4 py-2 rounded-full">Keluar</a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">

        <?php if (!$antrian_saya): ?>
            <div class="max-w-md mx-auto text-center space-y-8 py-10">
                <div>
                    <h1 class="text-3xl font-black text-slate-800">Selamat Datang</h1>
                    <p class="text-slate-500">Silakan pilih poli tujuan untuk memulai antrean.</p>
                </div>

                <form action="/proses_ambil_antrean" method="POST" class="grid gap-4">
                    <button name="poli" value="Umum" class="group p-6 bg-white border-2 border-slate-100 rounded-3xl text-left hover:border-blue-500 hover:shadow-xl hover:shadow-blue-100 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i class="fas fa-user-md text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800">Poli Umum</h3>
                                    <p class="text-xs text-slate-400">Pemeriksaan kesehatan umum</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-500"></i>
                        </div>
                    </button>

                    <button name="poli" value="Gigi" class="group p-6 bg-white border-2 border-slate-100 rounded-3xl text-left hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-100 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                    <i class="fas fa-tooth text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800">Poli Gigi</h3>
                                    <p class="text-xs text-slate-400">Perawatan gigi dan mulut</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-500"></i>
                        </div>
                    </button>

                    <button name="poli" value="KIA" class="group p-6 bg-white border-2 border-slate-100 rounded-3xl text-left hover:border-pink-500 hover:shadow-xl hover:shadow-pink-100 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-pink-100 rounded-2xl flex items-center justify-center text-pink-600 group-hover:bg-pink-600 group-hover:text-white transition-all">
                                    <i class="fas fa-baby text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800">Poli KIA</h3>
                                    <p class="text-xs text-slate-400">Kesehatan Ibu dan Anak</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-pink-500"></i>
                        </div>
                    </button>
                </form>
            </div>

        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div id="notif-panggil" class="hidden p-6 rounded-3xl text-center text-white font-black animate-bounce shadow-2xl">
                        SILAKAN MASUK KE POLI SEKARANG!
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4">
                            <span class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase">Tiket Aktif</span>
                        </div>
                        <p class="text-slate-400 font-bold text-sm uppercase tracking-widest mb-2">Nomor Antrean Anda</p>
                        <h2 class="text-7xl font-black text-slate-800 mb-4"><?php echo $antrian_saya['nomor_antrian']; ?></h2>
                        <div class="inline-flex items-center gap-2 bg-slate-100 px-4 py-2 rounded-full">
                            <i class="fas fa-hospital-user text-blue-500"></i>
                            <span class="font-bold text-slate-600"><?php echo $antrian_saya['poli']; ?></span>
                        </div>
                        <div id="status-badge" class="mt-4 font-bold text-blue-600 uppercase text-xs">
                             Status: <?php echo $antrian_saya['status']; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i> Info Kesehatan Nasional (BPS)
                        </h3>
                        <?php include 'tabel_bps.php'; ?>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-slate-900 rounded-3xl p-6 text-white text-center shadow-xl">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-tighter">Sedang Dilayani</p>
                        <h4 id="no-terbaru" class="text-5xl font-black mb-1">--</h4>
                        <p id="poli-terbaru" class="text-xs text-blue-400 font-bold uppercase">Menunggu Data...</p>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <h3 class="font-bold text-slate-800 mb-4">Feedback Layanan</h3>
                        <form action="/api/simpan_feedback.php" method="POST" class="space-y-3">
                            <input type="hidden" name="nama_pasien" value="<?php echo htmlspecialchars($nama_pasien); ?>">
                            <select name="kepuasan" class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:border-blue-500" required>
                                <option value="">Tingkat Kepuasan?</option>
                                <option value="Sangat Puas">Sangat Puas 😊</option>
                                <option value="Puas">Puas 🙂</option>
                                <option value="Kurang">Kurang ☹️</option>
                            </select>
                            <textarea name="saran" placeholder="Saran Anda..." class="w-full p-3 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none focus:border-blue-500" rows="3" required></textarea>
                            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all">Kirim Masukan</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <script>
        // Logika Suara & Cek Status Otomatis
        let statusTerakhir = "<?php echo $antrian_saya['status'] ?? 'none'; ?>";
        const nomorSaya = "<?php echo $antrian_saya['nomor_antrian'] ?? ''; ?>";
        const poliSaya = "<?php echo $antrian_saya['poli'] ?? ''; ?>";

        function ejaNomor(nomor) {
            return nomor.split('').join(' ');
        }

        function panggilSuara(nomor, poli) {
            window.speechSynthesis.cancel();
            const teks = `Nomor antrean ${ejaNomor(nomor)}, silakan masuk ke Poli ${poli}.`;
            const utterance = new SpeechSynthesisUtterance(teks);
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }

        function cekStatus() {
            if (!nomorSaya) return;

            // 1. Ambil Panggilan Terkini (Untuk Sidebar)
            fetch('/api/ambil_antrean_terbaru.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('no-terbaru').innerText = data.nomor_antrian;
                    document.getElementById('poli-terbaru').innerText = data.poli;
                });

            // 2. Cek Status Nomor Saya
            fetch(`/api/cek_status_antrean.php?nomor=${nomorSaya}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'dipanggil' && statusTerakhir !== 'dipanggil') {
                        statusTerakhir = 'dipanggil';
                        panggilSuara(nomorSaya, poliSaya);
                        
                        const notif = document.getElementById('notif-panggil');
                        notif.classList.remove('hidden');
                        notif.classList.add('bg-emerald-500');
                        document.getElementById('status-badge').innerText = "Status: DIPANGGIL";
                    }
                });
        }

        // Jalankan polling setiap 5 detik
        if(nomorSaya) {
            setInterval(cekStatus, 5000);
            cekStatus();
        }
    </script>
</body>
</html>