<?php
// antrian_pasien.php
include 'koneksi.php';

$cookie_raw  = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}

$id_pasien   = (int)$cookie_data['id'];
$nama_pasien = htmlspecialchars($cookie_data['nama']);
$hari_ini    = date('Y-m-d');

$q = mysqli_query($koneksi, "SELECT nomor_antrean, poli, status, waktu_daftar 
                              FROM antrian 
                              WHERE id_pasien='$id_pasien' 
                              AND DATE(created_at)='$hari_ini' 
                              ORDER BY id DESC LIMIT 1");
$tiket = mysqli_fetch_assoc($q);

if (!$tiket) {
    header("Location: /dashboard_pasien");
    exit();
}

$poli_esc  = mysqli_real_escape_string($koneksi, $tiket['poli']);
$nomor_esc = mysqli_real_escape_string($koneksi, $tiket['nomor_antrean']);

$q_posisi = mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM antrian 
                                    WHERE poli='$poli_esc' 
                                    AND status='menunggu' 
                                    AND DATE(created_at)='$hari_ini'
                                    AND id < (SELECT id FROM antrian WHERE nomor_antrean='$nomor_esc' LIMIT 1)");
$posisi_row      = mysqli_fetch_assoc($q_posisi);
$antrian_didepan = (int)($posisi_row['jumlah'] ?? 0);
$estimasi_menit  = $antrian_didepan * 7;
$estimasi_jam    = date('H:i', strtotime("+{$estimasi_menit} minutes"));

// Data BPS statis (5 tahun terakhir sebagai contoh tabel)
$data_bps = [
    ['tahun'=>'2023','persen'=>'67.85','jumlah'=>'189.2 juta'],
    ['tahun'=>'2022','persen'=>'65.72','jumlah'=>'183.5 juta'],
    ['tahun'=>'2021','persen'=>'63.10','jumlah'=>'176.2 juta'],
    ['tahun'=>'2020','persen'=>'59.47','jumlah'=>'165.8 juta'],
    ['tahun'=>'2019','persen'=>'56.83','jumlah'=>'158.6 juta'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrean Saya – Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0fdf8; min-height: 100vh; }

        .tiket-nomor {
            font-size: 5rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -2px;
        }
        .pulse-ring { animation: pulseRing 2s ease-out infinite; }
        @keyframes pulseRing {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
            70%  { box-shadow: 0 0 0 14px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        .badge-menunggu  { background:#fef3c7; color:#d97706; }
        .badge-dipanggil { background:#d1fae5; color:#059669; }
        .badge-selesai   { background:#f1f5f9; color:#64748b; }

        .btn-keluar {
            background: white; color: #ef4444;
            border: 2px solid #fecaca; border-radius: 1rem;
            padding: 0.875rem 2rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s; width: 100%;
        }
        .btn-keluar:hover { background: #fef2f2; border-color: #ef4444; }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 50;
            display: flex; align-items: flex-end; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity 0.3s;
        }
        .modal-overlay.show { opacity: 1; pointer-events: all; }
        .modal-sheet {
            background: white; border-radius: 2rem 2rem 0 0;
            padding: 2rem; width: 100%; max-width: 480px;
            transform: translateY(100%); transition: transform 0.3s ease;
            max-height: 90vh; overflow-y: auto;
        }
        .modal-overlay.show .modal-sheet { transform: translateY(0); }

        .wave-top {
            position: absolute; top: 0; left: 0; right: 0;
            height: 180px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 0 0 3rem 3rem;
        }
        .card { background: white; border-radius: 1.5rem; padding: 1.25rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }

        /* BPS tabel */
        .bps-card { cursor: pointer; transition: all 0.2s; }
        .bps-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .bps-table th { background: #eff6ff; color: #1d4ed8; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem 1rem; }
        .bps-table td { padding: 0.6rem 1rem; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; }
        .bps-table tr:last-child td { border-bottom: none; }
        .bar { height: 6px; border-radius: 99px; background: linear-gradient(90deg,#3b82f6,#6366f1); }
    </style>
</head>
<body>

<div class="wave-top"></div>

<!-- Navbar -->
<nav class="relative z-10 flex items-center justify-between px-6 pt-6">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-hospital text-white text-sm"></i>
        </div>
        <span class="font-bold text-white text-lg">Klinik Sehat</span>
    </div>
    <span class="text-white/80 text-sm font-medium"><?= $nama_pasien ?></span>
</nav>

<div class="relative z-10 px-6 pt-5 pb-12 text-center">
    <p class="text-white/70 text-sm">Poli <?= htmlspecialchars($tiket['poli']) ?></p>
    <h1 class="text-white font-extrabold text-xl mt-1">Tiket Antreanmu</h1>
</div>

<!-- Content -->
<div class="relative z-10 px-4 -mt-8 space-y-4 pb-10">

    <!-- Tiket utama -->
    <div class="card text-center py-8">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Nomor Antreanmu</p>
        <div class="tiket-nomor"><?= htmlspecialchars($tiket['nomor_antrean']) ?></div>
        <p class="text-slate-500 text-sm mt-3 font-medium"><?= $nama_pasien ?></p>

        <?php
        $badge_class = 'badge-menunggu'; $badge_text = 'Menunggu';
        if ($tiket['status']==='dipanggil') { $badge_class='badge-dipanggil'; $badge_text='📢 Dipanggil!'; }
        if ($tiket['status']==='selesai')   { $badge_class='badge-selesai';   $badge_text='Selesai'; }
        ?>
        <span class="inline-block mt-3 px-4 py-1.5 rounded-full text-xs font-bold <?= $badge_class ?>" id="status-badge">
            <?= $badge_text ?>
        </span>

        <div class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-2 gap-4 text-left">
            <div>
                <p class="text-xs text-slate-400 font-medium">Poli Tujuan</p>
                <p class="font-bold text-slate-700 mt-0.5"><?= htmlspecialchars($tiket['poli']) ?></p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Daftar Pukul</p>
                <p class="font-bold text-slate-700 mt-0.5"><?= date('H:i', strtotime($tiket['waktu_daftar'])) ?></p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Antrean di Depan</p>
                <p class="font-bold text-slate-700 mt-0.5" id="antrian-depan"><?= $antrian_didepan ?> orang</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Estimasi Dipanggil</p>
                <p class="font-bold text-emerald-600 mt-0.5" id="estimasi-waktu">~<?= $estimasi_jam ?></p>
            </div>
        </div>
    </div>

    <!-- Real-time nomor dipanggil -->
    <div class="card">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full pulse-ring"></div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Sedang Dipanggil Sekarang</p>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-4xl font-black text-emerald-600 tracking-tight" id="nomor-realtime">--</p>
                <p class="text-sm text-slate-400 mt-1">Poli: <span id="poli-realtime" class="font-semibold text-slate-600">Memuat...</span></p>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center">
                <i class="fas fa-bullhorn text-emerald-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- BPS Card — klik untuk buka tabel -->
    <div class="card bps-card" onclick="document.getElementById('modal-bps').classList.add('show')">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-bar text-blue-500 text-sm"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Data BPS Indonesia • Tap untuk detail</p>
                <p class="font-bold text-slate-700 text-sm">Kepemilikan Ponsel</p>
            </div>
            <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
        </div>
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-3xl font-black text-blue-600">67.85%</p>
                <p class="text-xs text-slate-500 mt-0.5">penduduk memiliki ponsel (2023)</p>
            </div>
            <div class="text-4xl">📱</div>
        </div>
        <p class="text-xs text-slate-400 mt-3">
            <i class="fas fa-info-circle mr-1"></i>
            Antrean digital ini membantu lebih dari separuh penduduk Indonesia yang sudah melek ponsel.
        </p>
    </div>

    <!-- Tombol Keluar -->
    <button class="btn-keluar" onclick="document.getElementById('modal-feedback').classList.add('show')">
        <i class="fas fa-sign-out-alt mr-2"></i>Keluar & Beri Feedback
    </button>

</div>

<!-- ===== MODAL BPS TABEL ===== -->
<div class="modal-overlay" id="modal-bps">
    <div class="modal-sheet">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-extrabold text-slate-800 text-lg">Data Kepemilikan Ponsel</h3>
                <p class="text-xs text-slate-400 mt-0.5">Sumber: Badan Pusat Statistik (BPS) RI</p>
            </div>
            <button onclick="document.getElementById('modal-bps').classList.remove('show')"
                class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <table class="bps-table w-full rounded-xl overflow-hidden border border-slate-100 mb-4">
            <thead>
                <tr>
                    <th class="text-left">Tahun</th>
                    <th class="text-left">Persentase</th>
                    <th class="text-left">Estimasi Pengguna</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_bps as $bps): ?>
                <tr>
                    <td class="font-bold text-slate-700"><?= $bps['tahun'] ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-blue-600"><?= $bps['persen'] ?>%</span>
                            <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                <div class="bar" style="width:<?= $bps['persen'] ?>%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="text-slate-500"><?= $bps['jumlah'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="bg-blue-50 rounded-xl p-3 text-xs text-blue-600 font-medium">
            <i class="fas fa-lightbulb mr-1"></i>
            Tren ini mendukung digitalisasi layanan kesehatan seperti antrean online yang kamu gunakan sekarang.
        </div>
    </div>
</div>

<!-- ===== MODAL FEEDBACK ===== -->
<div class="modal-overlay" id="modal-feedback">
    <div class="modal-sheet">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-extrabold text-slate-800 text-lg">Bagaimana pelayanannya?</h3>
            <button onclick="document.getElementById('modal-feedback').classList.remove('show')"
                class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="/simpan_feedback" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-3">Tingkat Kepuasan</label>
                <div class="grid grid-cols-4 gap-2">
                    <?php
                    $pilihan = [
                        ['emoji'=>'😍','label'=>'Sangat Puas','value'=>'Sangat Puas'],
                        ['emoji'=>'🙂','label'=>'Puas','value'=>'Puas'],
                        ['emoji'=>'😐','label'=>'Cukup','value'=>'Cukup'],
                        ['emoji'=>'🙁','label'=>'Kurang','value'=>'Kurang'],
                    ];
                    foreach ($pilihan as $p): ?>
                    <label class="cursor-pointer text-center p-3 rounded-2xl border-2 border-slate-100 hover:border-emerald-300 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <input type="radio" name="kepuasan" value="<?= $p['value'] ?>" class="sr-only" <?= $p['value']==='Puas'?'checked':'' ?>>
                        <div class="text-2xl"><?= $p['emoji'] ?></div>
                        <div class="text-xs font-semibold text-slate-600 mt-1"><?= $p['label'] ?></div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Saran (opsional)</label>
                <textarea name="saran" rows="3"
                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-emerald-400 outline-none transition-all text-sm resize-none"
                    placeholder="Ceritakan pengalamanmu..."></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="window.location.href='/logout'"
                    class="flex-1 py-3 rounded-2xl border-2 border-slate-200 text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">
                    Lewati
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-colors shadow-lg shadow-emerald-100">
                    Kirim & Keluar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tutup modal klik backdrop -->
<script>
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});

// =============================================
// REAL-TIME
// =============================================
function pantauAntrean() {
    fetch('/get_antrian_sekarang')
        .then(r => r.json())
        .then(data => {
            document.getElementById('nomor-realtime').textContent = data.nomor_antrean ?? '--';
            document.getElementById('poli-realtime').textContent  = data.poli ?? '-';
        }).catch(() => {});
}

function cekStatusTiket() {
    fetch('/cek_status_antrean')
        .then(r => r.json())
        .then(data => {
            if (!data || data.status === 'none') return;
            const badge = document.getElementById('status-badge');
            if (data.status === 'dipanggil') {
                badge.className = 'inline-block mt-3 px-4 py-1.5 rounded-full text-xs font-bold badge-dipanggil';
                badge.textContent = '📢 Dipanggil! Segera ke poli';
            } else if (data.status === 'selesai') {
                badge.className = 'inline-block mt-3 px-4 py-1.5 rounded-full text-xs font-bold badge-selesai';
                badge.textContent = 'Selesai';
            }
        }).catch(() => {});
}

pantauAntrean();
cekStatusTiket();
setInterval(pantauAntrean, 3000);
setInterval(cekStatusTiket, 5000);
</script>

</body>
</html>