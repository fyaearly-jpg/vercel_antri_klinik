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
                
                <?php if (!isset($_SESSION['punya_antrean'])) : ?>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Pendaftaran</h2>
                    <p class="mb-6 text-slate-500 text-sm">Halo, <strong><?php echo htmlspecialchars($nama_lengkap); ?></strong>. Silakan pilih layanan.</p>
                    <form method="POST" class="max-w-xs mx-auto">
                        <select name="poli" required class="w-full p-4 mb-4 border border-slate-200 rounded-2xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Pilih Poli</option>
                            <option value="Umum">Poli Umum</option>
                            <option value="Gigi">Poli Gigi</option>
                            <option value="Anak">Poli Anak</option>
                        </select>
                        <button type="submit" name="ambil_antrean" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                            Ambil Antrean
                        </button>
                    </form>

                <?php else : ?>
                    <div id="notif-panggil" class="hidden text-white p-4 rounded-2xl mb-6 font-black text-center animate-bounce shadow-lg"></div>
                    
                    <span class="inline-block px-4 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest mb-4">Nomor Anda</span>
                    <div class="nomor-besar mb-2">
                        <?php echo $_SESSION['punya_antrean']; ?>
                    </div>
                    <p class="text-slate-500 font-medium mb-6">Poli Tujuan: <span class="text-slate-800 font-bold"><?php echo $_SESSION['poli_terpilih']; ?></span></p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Status</p>
                            <p class="font-bold text-slate-700">Menunggu</p>
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

        <?php if (isset($_SESSION['punya_antrean'])) : ?>
        <div class="grid md:grid-cols-2 gap-6">
            
            <div class="card-custom p-6 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4 text-xl">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-tighter mb-1">Sedang Dilayani</h3>
                <div id="no-terbaru" class="text-4xl font-black text-slate-800">--</div>
                <div id="poli-terbaru" class="mt-2 text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Menunggu data...</div>
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
        <?php endif; ?>

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
    // LOGIKA JS (SAMA SEPERTI SEBELUMNYA DENGAN PENYESUAIAN WARNA NOTIFIKASI)
    function bukaModal() {
        const modal = document.getElementById('modal-feedback');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function tutupModal() {
        const modal = document.getElementById('modal-feedback');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Polling Panggilan Terkini
    function ambilPanggilanTerbaru() {
        fetch('ambil_antrean_terbaru.php')
            .then(res => res.json())
            .then(data => {
                document.getElementById('no-terbaru').innerText = data.nomor_antrian;
                document.getElementById('poli-terbaru').innerText = data.poli;
            });
    }
    
    if ("<?php echo isset($_SESSION['punya_antrean']); ?>") {
        setInterval(ambilPanggilanTerbaru, 3000);
        ambilPanggilanTerbaru();
    }

    // TTS & Cek Status
    function ejaNomorAntrean(nomor) {
        const bagian = nomor.split('-');
        if (bagian.length !== 2) return nomor;
        return `${bagian[0].toUpperCase()} ${parseInt(bagian[1], 10)}`;
    }

    function panggilSuara(nomor, poli) {
        window.speechSynthesis.cancel();
        const teks = `Nomor antrean ${ejaNomorAntrean(nomor)}, silakan masuk ke Poli ${poli}.`;
        const ssu = new SpeechSynthesisUtterance(teks);
        ssu.lang = 'id-ID';
        ssu.rate = 0.8;
        window.speechSynthesis.speak(ssu);
    }

    let statusTerakhir = 'menunggu';
    const nomorAntrean = "<?php echo $_SESSION['punya_antrean'] ?? ''; ?>";
    const poliAntrean = "<?php echo $_SESSION['poli_terpilih'] ?? ''; ?>";

    function cekStatus() {
        if (!nomorAntrean) return;
        fetch(`cek_status_pasien.php?nomor=${nomorAntrean}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'dipanggil' && statusTerakhir !== 'dipanggil') {
                    panggilSuara(nomorAntrean, poliAntrean);
                    statusTerakhir = 'dipanggil';
                    const notif = document.getElementById('notif-panggil');
                    if (notif) {
                        notif.innerText = "SILAKAN MASUK KE POLI SEKARANG!";
                        notif.classList.remove('hidden');
                        notif.classList.add('bg-blue-600');
                    }
                }
            });
    }

    if (nomorAntrean) {
        setInterval(cekStatus, 5000);
    }
    </script>

    <?php include 'tabel_bps.php'; ?>
</body>
</html>