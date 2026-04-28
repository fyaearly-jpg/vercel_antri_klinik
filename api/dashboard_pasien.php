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

// 3. Ambil data antrean terbaru dari Database (Bukan dari Session)
$query = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien = '$id_user' ORDER BY id DESC LIMIT 1");
$data_antrian = mysqli_fetch_assoc($query);

// Cek apakah antrean masih aktif (belum selesai)
$punya_antrean = ($data_antrian && $data_antrian['status'] !== 'selesai');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien</title>
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
                
                <?php if (!$punya_antrean) : ?>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Pendaftaran</h2>
                    <p class="mb-6 text-slate-500 text-sm">Halo, <strong><?= htmlspecialchars($nama_user) ?></strong>. Silakan pilih layanan.</p>
                    <form action="/tambah_antrean_terbaru" method="POST">
                        <select name="poli" class="w-full p-3 border rounded-xl mb-4">
                            <option value="Umum">Poli Umum</option>
                            <option value="Gigi">Poli Gigi</option>
                            <option value="Anak">Poli Anak</option>
                        </select>
                        <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg w-full font-bold">
                            Ambil Nomor Antrean
                        </button>
                    </form>

                <?php else : ?>
                    <div id="notif-panggil" class="hidden text-white p-4 rounded-2xl mb-6 font-black text-center animate-bounce shadow-lg"></div>
                    
                    <span class="inline-block px-4 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase mb-4">Nomor Anda</span>
                    <div class="nomor-besar mb-2"><?= $data_antrian['nomor_antrian'] ?></div>
                    <p class="text-slate-500 font-medium mb-6">Poli Tujuan: <span class="text-slate-800 font-bold"><?= $data_antrian['poli'] ?></span></p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Status</p>
                            <p class="font-bold text-slate-700"><?= ucfirst($data_antrian['status']) ?></p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Estimasi</p>
                            <p class="font-bold text-slate-700">15 Menit</p>
                        </div>
                    </div>

                    <button onclick="bukaModal()" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all">
                        Selesai Berobat
                    </button>
                <?php endif; ?>

            </div>
        </div>
        
        <div class="card-custom p-6 text-center">
             <button onclick="openBpsModal()" class="w-full py-3 border-2 border-slate-100 text-slate-700 rounded-2xl font-bold hover:bg-slate-50 transition-all text-sm">
                <i class="fas fa-chart-line mr-2"></i> Buka Data Statistik BPS
            </button>
        </div>
    </div>

    <div id="modal-feedback" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[999] p-4">
        </div>

    <script>
    function bukaModal() { document.getElementById('modal-feedback').classList.replace('hidden', 'flex'); }
    function tutupModal() { document.getElementById('modal-feedback').classList.replace('flex', 'hidden'); }

    // Polling antrean yang sedang dipanggil
    function ambilPanggilanTerbaru() {
        fetch('/api/get_antrian_sekarang.php')
            .then(res => res.json())
            .then(data => {
                // Update UI jika diperlukan
            }).catch(e => console.log("Error polling"));
    }
    setInterval(ambilPanggilanTerbaru, 5000);

    // Cek apakah nomor pasien dipanggil
    const nomorSaya = "<?= $data_antrian['nomor_antrian'] ?? '' ?>";
    if (nomorSaya) {
        setInterval(() => {
            fetch(`/api/cek_status_pasien.php?nomor=${nomorSaya}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'dipanggil') {
                        const notif = document.getElementById('notif-panggil');
                        if (notif) {
                            notif.innerText = "SILAKAN MASUK KE POLI!";
                            notif.classList.remove('hidden');
                            notif.classList.add('bg-blue-600');
                        }
                    }
                });
        }, 5000);
    }
    </script>

    <?php include_once 'tabel_bps.php'; ?>
</body>
</html>