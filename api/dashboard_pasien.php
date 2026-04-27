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
    <title>Dashboard Pasien - ImuniCare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans">
    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <i class="fas fa-clinic-medical text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">ImuniCare</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="hidden md:block text-slate-600 font-medium">Halo, <?php echo htmlspecialchars($nama_pasien); ?></span>
                    <a href="/logout" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition-all text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-ticket-alt text-blue-500"></i> Status Antrean Anda
                    </h2>
                    
                    <?php if ($antrian_saya): ?>
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 text-center">
                            <p class="text-blue-600 font-medium mb-1">Nomor Antrean Anda</p>
                            <h3 class="text-5xl font-black text-blue-700 mb-2"><?php echo $antrian_saya['nomor_antrian']; ?></h3>
                            <div class="flex justify-center gap-2 mb-4">
                                <span class="px-3 py-1 bg-white rounded-full text-xs font-bold text-blue-600 border border-blue-200 uppercase">
                                    Poli: <?php echo $antrian_saya['poli']; ?>
                                </span>
                                <span class="px-3 py-1 bg-blue-600 rounded-full text-xs font-bold text-white uppercase">
                                    Status: <?php echo $antrian_saya['status']; ?>
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10 border-2 border-dashed border-slate-200 rounded-xl">
                            <i class="fas fa-calendar-plus text-slate-300 text-4xl mb-3"></i>
                            <p class="text-slate-500">Belum ada antrean.</p>
                            <button onclick="document.getElementById('modalAmbil').classList.remove('hidden')" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-full font-bold hover:bg-blue-700 transition-all">
                                Ambil Antrean
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-green-500"></i> Statistik Kesehatan (BPS)
                        </h2>
                        <button onclick="openBpsModal()" class="text-blue-600 text-sm font-semibold hover:underline">Detail Data</button>
                    </div>
                    <?php include 'tabel_bps.php'; ?>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white text-center">
                    <p class="text-sm opacity-80 mb-1 font-bold italic uppercase tracking-wider">Panggilan Terkini</p>
                    <h4 id="no-terbaru" class="text-5xl font-black mb-2">--</h4>
                    <p id="poli-terbaru" class="text-xs bg-white/20 inline-block px-4 py-1 rounded-full backdrop-blur-sm">Menghubungkan...</p>
                </div>

                <button onclick="document.getElementById('modalFeedback').classList.remove('hidden')" 
                        class="w-full py-4 bg-white border border-indigo-100 text-indigo-600 rounded-2xl font-bold shadow-sm hover:shadow-md hover:bg-indigo-50 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-star text-yellow-400"></i> Beri Masukan Layanan
                </button>
            </div>
        </div>
    </main>

    <div id="modalAmbil" class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl scale-in">
            <h3 class="text-2xl font-black text-slate-800 mb-2">Ambil Antrean</h3>
            <p class="text-slate-500 text-sm mb-6">Silakan pilih unit layanan kesehatan yang ingin Anda tuju.</p>
            <form action="/proses_ambil_antrean" method="POST" class="space-y-4">
                <select name="poli" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-medium text-slate-700" required>
                    <option value="">Pilih Poli Tujuan</option>
                    <option value="Umum">Poli Umum</option>
                    <option value="Gigi">Poli Gigi</option>
                    <option value="KIA">Poli KIA (Ibu & Anak)</option>
                </select>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('modalAmbil').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 rounded-2xl font-bold text-slate-600 hover:bg-slate-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-blue-600 rounded-2xl font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalFeedback" class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-800">Feedback</h3>
                    <p class="text-slate-500 text-sm">Bagikan pengalaman Anda hari ini.</p>
                </div>
                <button onclick="document.getElementById('modalFeedback').classList.add('hidden')" class="w-10 h-10 bg-slate-50 rounded-full text-slate-400 flex items-center justify-center hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="/api/simpan_feedback.php" method="POST" class="space-y-4">
                <input type="hidden" name="nama_pasien" value="<?php echo htmlspecialchars($nama_pasien); ?>">
                
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Kepuasan</label>
                    <select name="kepuasan" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium" required>
                        <option value="">Bagaimana Layanan Kami?</option>
                        <option value="Sangat Puas">Sangat Puas 😊</option>
                        <option value="Puas">Puas 🙂</option>
                        <option value="Cukup">Cukup 😐</option>
                        <option value="Kurang">Kurang ☹️</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Saran & Kritik</label>
                    <textarea name="saran" rows="4" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none font-medium" placeholder="Apa yang bisa kami tingkatkan?" required></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('modalFeedback').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 rounded-2xl font-bold text-slate-600 hover:bg-slate-200 transition-all">Nanti Saja</button>
                    <button type="submit" class="flex-1 py-4 bg-indigo-600 rounded-2xl font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">Kirim Sekarang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePanggilan() {
            fetch('/api/ambil_antrean_terbaru.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('no-terbaru').innerText = data.nomor_antrian;
                    document.getElementById('poli-terbaru').innerText = data.poli;
                });
        }
        setInterval(updatePanggilan, 5000);
        updatePanggilan();
    </script>
</body>
</html>