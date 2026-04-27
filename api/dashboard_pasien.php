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
$nama_user = $cookie_data['nama']; // Pakai $nama_user sesuai cookie

// 3. Ambil data antrean terbaru pasien ini dari DATABASE (Bukan Session)
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_user' ORDER BY id DESC LIMIT 1");
$data_antrian = mysqli_fetch_assoc($query);

// Tentukan apakah pasien punya antrean aktif (menunggu atau dipanggil)
$punya_antrean_aktif = ($data_antrian && $data_antrian['status'] !== 'selesai');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .card-custom { background: white; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #f1f5f9; }
        .nomor-besar { font-size: 5rem; color: #059669; font-weight: 900; line-height: 1; }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="card-custom overflow-hidden">
            <div class="p-8 text-center">
                
                <?php if (!$punya_antrean_aktif) : ?>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Pendaftaran</h2>
                    <p class="mb-6 text-slate-500 text-sm">Halo, <strong><?php echo htmlspecialchars($nama_user); ?></strong>. Silakan pilih layanan.</p>
                    <form action="/tambah_antrean_terbaru" method="POST">
                        <select name="poli" class="w-full p-3 border rounded-xl mb-4">
                            <option value="Umum">Poli Umum</option>
                            <option value="Gigi">Poli Gigi</option>
                            <option value="Anak">Poli Anak</option>
                        </select>
                        <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg w-full">
                            Ambil Nomor Antrean
                        </button>
                    </form>

                <?php else : ?>
                    <div id="notif-panggil" class="hidden text-white p-4 rounded-2xl mb-6 font-black text-center animate-bounce shadow-lg"></div>
                    
                    <span class="inline-block px-4 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest mb-4">Nomor Anda</span>
                    <div class="nomor-besar mb-2">
                        <?php echo $data_antrian['nomor_antrian']; ?>
                    </div>
                    <p class="text-slate-500 font-medium mb-6">Poli Tujuan: <span class="text-slate-800 font-bold"><?php echo $data_antrian['poli']; ?></span></p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Status</p>
                            <p class="font-bold text-slate-700"><?php echo ucfirst($data_antrian['status']); ?></p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Estimasi</p>
                            <p class="font-bold text-slate-700">15 Menit</p>
                        </div>
                    </div>

                    <button onclick="bukaModal()" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all shadow-lg">
                        Selesai Berobat
                    </button>
                <?php endif; ?>

            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="card-custom p-6 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4 text-xl">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-tighter mb-1">Sedang Dilayani</h3>
                <div id="no-terbaru" class="text-4xl font-black text-slate-800">--</div>
                <div id="poli-terbaru" class="mt-2 text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Memuat...</div>
            </div>

            <div class="card-custom p-6 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-4 text-xl">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-tighter mb-2">Informasi Publik</h3>
                <p class="text-xs text-slate-500 mb-4 px-4">Lihat data statistik kesehatan nasional terbaru dari BPS.</p>
                <button onclick="openBpsModal()" class="w-full py-3 border-2 border-slate-100 text-slate-700 rounded-2xl font-bold hover:bg-slate-50 transition-all text-sm">
                    Buka Data BPS
                </button>
            </div>
        </div>
    </div>

    <div id="modal-feedback" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[999] p-4">
        <div class="bg-white p-8 rounded-[2.5rem] max-w-md w-full shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-800">Selesai Berobat?</h2>
                <p class="text-slate-500 text-sm">Berikan masukan agar kami lebih baik lagi.</p>
            </div>
            <form action="logout.php" method="POST" class="space-y-4">
                <div>
                    <label class="block mb-2 text-xs font-black text-slate-400 uppercase">Tingkat Kepuasan</label>
                    <select name="kepuasan" required class="w-full p-4 border border-slate-100 rounded-2xl bg-slate-50 font-medium">
                        <option value="Sangat Puas">Sangat Puas 😍</option>
                        <option value="Puas">Puas 🙂</option>
                        <option value="Cukup">Cukup 😐</option>
                        <option value="Kurang">Kurang 🙁</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-xs font-black text-slate-400 uppercase">Saran & Masukan</label>
                    <textarea name="saran" class="w-full p-4 border border-slate-100 rounded-2xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-500" rows="3" placeholder="Apa yang bisa kami tingkatkan?"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="tutupModal()" class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" name="kirim_feedback" class="flex-1 bg-emerald-500 text-white py-4 rounded-2xl font-bold hover:bg-emerald-600 shadow-lg shadow-emerald-100 transition-all">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function bukaModal() { document.getElementById('modal-feedback').classList.replace('hidden', 'flex'); }
    function tutupModal() { document.getElementById('modal-feedback').classList.replace('flex', 'hidden'); }

    // Polling Panggilan Terkini
    function ambilPanggilanTerbaru() {
        fetch('/api/get_antrian_sekarang.php') // Gunakan path yang benar
            .then(res => res.json())
            .then(data => {
                document.getElementById('no-terbaru').innerText = data.nomor_antrian || '--';
                document.getElementById('poli-terbaru').innerText = data.poli || 'Menunggu...';
            });
    }
    
    setInterval(ambilPanggilanTerbaru, 3000);
    ambilPanggilanTerbaru();

    // TTS & Cek Status
    let statusTerakhir = '<?php echo $data_antrian['status'] ?? "menunggu"; ?>';
    const nomorAntrean = '<?php echo $data_antrian['nomor_antrian'] ?? ""; ?>';
    const poliAntrean = '<?php echo $data_antrian['poli'] ?? ""; ?>';

    function cekStatus() {
        if (!nomorAntrean) return;
        fetch(`/api/cek_status_pasien.php?nomor=${nomorAntrean}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'dipanggil' && statusTerakhir !== 'dipanggil') {
                    // Logika panggil suara di sini
                    statusTerakhir = 'dipanggil';
                    location.reload(); // Reload agar tampilan berubah jadi status 'dipanggil'
                }
            });
    }

    if (nomorAntrean) { setInterval(cekStatus, 5000); }
    </script>

    <?php include_once 'tabel_bps.php'; ?>
</body>
</html>