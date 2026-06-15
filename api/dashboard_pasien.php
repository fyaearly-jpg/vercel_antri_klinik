<?php
// dashboard_pasien.php
$cookie_raw  = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}

$nama_pasien = htmlspecialchars($cookie_data['nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Poli – Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background: #f0fdf8; min-height: 100vh; }

        .poli-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .poli-card:hover {
            border-color: #10b981;
            transform: translateY(-3px);
            box-shadow: 0 12px 32px -8px rgba(16,185,129,0.2);
        }
        .poli-card.selected {
            border-color: #10b981;
            background: #f0fdf4;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.15);
        }
        .poli-card.selected .checkmark {
            opacity: 1;
            transform: scale(1);
        }
        .checkmark {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            width: 1.5rem;
            height: 1.5rem;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }
        .poli-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 1.5rem;
        }

        .btn-ambil {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 1rem;
            padding: 1rem 2rem;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 8px 24px -4px rgba(16,185,129,0.4);
            width: 100%;
        }
        .btn-ambil:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -4px rgba(16,185,129,0.5);
        }
        .btn-ambil:disabled {
            background: #cbd5e1;
            box-shadow: none;
            cursor: not-allowed;
        }

        .wave-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 200px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 0 0 3rem 3rem;
            z-index: 0;
        }
    </style>
</head>
<body>

<div class="wave-top"></div>

<!-- Navbar -->
<nav class="relative z-10 flex items-center justify-between px-6 pt-6 pb-0">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-hospital text-white text-sm"></i>
        </div>
        <span class="font-bold text-white text-lg">Klinik Sehat</span>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-white/80 text-sm font-medium"><?= $nama_pasien ?></span>
        <a href="/logout" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors">
            <i class="fas fa-sign-out-alt text-white text-sm"></i>
        </a>
    </div>
</nav>

<!-- Hero -->
<div class="relative z-10 px-6 pt-6 pb-10 text-center">
    <p class="text-white/70 text-sm font-medium mb-1">Selamat datang,</p>
    <h1 class="text-white text-2xl font-extrabold"><?= $nama_pasien ?> 👋</h1>
    <p class="text-white/80 text-sm mt-2">Pilih poli tujuanmu hari ini</p>
</div>

<!-- Main Card -->
<div class="relative z-10 mx-4 -mt-4">
    <div class="bg-white rounded-3xl shadow-xl p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800 text-lg">Pilih Poli</h2>
            <span class="text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full font-medium">
                <i class="fas fa-clock mr-1"></i><?= date('d M Y') ?>
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-6" id="poli-grid">

            <div class="poli-card" onclick="pilihPoli(this, 'Umum')">
                <div class="checkmark"><i class="fas fa-check text-white text-xs"></i></div>
                <div class="poli-icon bg-blue-50">🩺</div>
                <p class="font-bold text-slate-800 text-sm">Umum</p>
                <p class="text-xs text-slate-400 mt-0.5">Pemeriksaan dasar</p>
            </div>

            <div class="poli-card" onclick="pilihPoli(this, 'Gigi')">
                <div class="checkmark"><i class="fas fa-check text-white text-xs"></i></div>
                <div class="poli-icon bg-emerald-50">🦷</div>
                <p class="font-bold text-slate-800 text-sm">Gigi</p>
                <p class="text-xs text-slate-400 mt-0.5">Kesehatan gigi & mulut</p>
            </div>

            <div class="poli-card" onclick="pilihPoli(this, 'Anak')">
                <div class="checkmark"><i class="fas fa-check text-white text-xs"></i></div>
                <div class="poli-icon bg-yellow-50">👶</div>
                <p class="font-bold text-slate-800 text-sm">Anak</p>
                <p class="text-xs text-slate-400 mt-0.5">Pediatri & tumbuh kembang</p>
            </div>

            <div class="poli-card" onclick="pilihPoli(this, 'KIA')">
                <div class="checkmark"><i class="fas fa-check text-white text-xs"></i></div>
                <div class="poli-icon bg-pink-50">🤰</div>
                <p class="font-bold text-slate-800 text-sm">KIA</p>
                <p class="text-xs text-slate-400 mt-0.5">Ibu & anak</p>
            </div>

            <div class="poli-card" onclick="pilihPoli(this, 'Lansia')">
                <div class="checkmark"><i class="fas fa-check text-white text-xs"></i></div>
                <div class="poli-icon bg-purple-50">👴</div>
                <p class="font-bold text-slate-800 text-sm">Lansia</p>
                <p class="text-xs text-slate-400 mt-0.5">Geriatri</p>
            </div>

            <div class="poli-card" onclick="pilihPoli(this, 'Jiwa')">
                <div class="checkmark"><i class="fas fa-check text-white text-xs"></i></div>
                <div class="poli-icon bg-rose-50">🧠</div>
                <p class="font-bold text-slate-800 text-sm">Jiwa</p>
                <p class="text-xs text-slate-400 mt-0.5">Kesehatan mental</p>
            </div>

        </div>

        <!-- Info -->
        <div id="info-poli" class="hidden mb-4 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3">
            <p class="text-sm text-emerald-700 font-medium">
                <i class="fas fa-check-circle mr-2"></i>Poli <strong id="label-poli">-</strong> dipilih
            </p>
        </div>

        <button class="btn-ambil" id="btn-ambil" disabled onclick="ambilNomor()">
            <i class="fas fa-ticket-alt mr-2"></i>Ambil Nomor Antrean
        </button>

        <!-- Loading state -->
        <div id="loading" class="hidden text-center py-4">
            <div class="inline-flex items-center gap-2 text-emerald-600 font-medium text-sm">
                <i class="fas fa-spinner fa-spin"></i> Memproses...
            </div>
        </div>

    </div>
</div>

<p class="text-center text-slate-400 text-xs mt-6 pb-8">Antrean berlaku untuk hari ini saja</p>

<script>
let poliDipilih = '';

function pilihPoli(el, namaPoli) {
    document.querySelectorAll('.poli-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    poliDipilih = namaPoli;

    document.getElementById('label-poli').textContent = namaPoli;
    document.getElementById('info-poli').classList.remove('hidden');
    document.getElementById('btn-ambil').disabled = false;
}

function ambilNomor() {
    if (!poliDipilih) return;

    document.getElementById('btn-ambil').classList.add('hidden');
    document.getElementById('loading').classList.remove('hidden');

    const formData = new FormData();
    formData.append('poli', poliDipilih);

    fetch('/tambah_antrian_terbaru', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Redirect ke halaman antrean
                window.location.href = '/antrian_pasien';
            } else {
                alert(data.message || 'Gagal mengambil nomor. Coba lagi.');
                document.getElementById('btn-ambil').classList.remove('hidden');
                document.getElementById('loading').classList.add('hidden');
            }
        })
        .catch(() => {
            alert('Koneksi gagal. Periksa internet kamu.');
            document.getElementById('btn-ambil').classList.remove('hidden');
            document.getElementById('loading').classList.add('hidden');
        });
}
</script>

</body>
</html>