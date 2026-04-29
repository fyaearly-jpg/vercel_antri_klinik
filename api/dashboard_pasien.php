<?php
include 'koneksi.php';

$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}

$id_user   = (int)$cookie_data['id'];
$nama_user = $cookie_data['nama'] ?? 'Pasien';

// SUPER SIMPLE LOGIC: Cari 1 tiket milik pasien ini yang belum 'selesai'. Hiraukan tanggal.
$sql = "SELECT * FROM antrian WHERE id_pasien = '$id_user' AND status != 'selesai' ORDER BY id DESC LIMIT 1";
$query = mysqli_query($koneksi, $sql);
$data_antrian = mysqli_fetch_assoc($query);

// Jika ada datanya, berarti dia punya antrean aktif, pindah ke Halaman Tiket
$punya_antrean_aktif = ($data_antrian) ? true : false;
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
 
    <!-- ======================== -->
    <!-- HALAMAN 1: PILIH POLI   -->
    <!-- ======================== -->
    <?php if (!$punya_antrean_aktif) : ?>
    <div class="card-custom p-8 text-center">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-800 mb-2">Pendaftaran</h2>
        <p class="mb-6 text-slate-500 text-sm">Halo, <strong><?php echo htmlspecialchars($nama_user); ?></strong>. Silakan pilih layanan.</p>
 
        <div id="alert-antrean" class="hidden bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-4"></div>
 
        <select id="poli" class="w-full p-3 border border-slate-200 rounded-xl mb-4 outline-none focus:ring-2 focus:ring-emerald-400">
            <option value="">-- Pilih Poli --</option>
            <option value="Umum">Poli Umum</option>
            <option value="Gigi">Poli Gigi</option>
            <option value="Anak">Poli Anak</option>
        </select>
        <button onclick="ambilAntrean()" id="btn-ambil" class="bg-emerald-600 text-white px-4 py-3 rounded-xl w-full font-bold hover:bg-emerald-700 transition-all">
            Ambil Nomor Antrean
        </button>
    </div>
 
    <!-- ======================================= -->
    <!-- HALAMAN 2: TIKET + SEDANG DILAYANI + BPS -->
    <!-- ======================================= -->
    <?php else : ?>
 
    <!-- Tiket Nomor Antrean -->
    <div class="card-custom overflow-hidden">
        <div class="p-8 text-center">
            <div id="notif-panggil" class="hidden text-white p-4 rounded-2xl mb-6 font-black text-center animate-bounce shadow-lg bg-emerald-600">
                📢 SILAKAN MASUK KE POLI!
            </div>
 
            <span class="inline-block px-4 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-widest mb-4">Nomor Anda</span>
            <div class="nomor-besar mb-2">
                <?php echo htmlspecialchars($data_antrian['nomor_antrean']); ?>
            </div>
            <p class="text-slate-500 font-medium mb-6">Poli Tujuan: <span class="text-slate-800 font-bold"><?php echo htmlspecialchars($data_antrian['poli']); ?></span></p>
 
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Status</p>
                    <p id="status-display" class="font-bold text-slate-700"><?php echo ucfirst($data_antrian['status']); ?></p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Estimasi</p>
                    <p class="font-bold text-slate-700">15 Menit</p>
                </div>
            </div>
 
            <button onclick="bukaModal()" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all shadow-lg">
                Selesai Berobat
            </button>
        </div>
    </div>
 
    <!-- Sedang Dilayani -->
    <div class="card-custom p-6 flex flex-col items-center justify-center text-center">
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4 text-xl">
            <i class="fas fa-bullhorn"></i>
        </div>
        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-tighter mb-1">Sedang Dilayani</h3>
        <div id="no-terbaru" class="text-4xl font-black text-slate-800">--</div>
        <div id="poli-terbaru" class="mt-2 text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Memuat...</div>
    </div>
 
    <!-- Info BPS -->
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
 
    <?php endif; ?>
</div>
 
<!-- MODAL FEEDBACK -->
<div id="modal-feedback" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[999] p-4">
    <div class="bg-white p-8 rounded-[2.5rem] max-w-md w-full shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-comment-dots"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-800">Selesai Berobat?</h2>
            <p class="text-slate-500 text-sm">Berikan masukan agar kami lebih baik lagi.</p>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block mb-2 text-xs font-black text-slate-400 uppercase">Tingkat Kepuasan</label>
                <select id="kepuasan" class="w-full p-4 border border-slate-100 rounded-2xl bg-slate-50 font-medium">
                    <option value="Sangat Puas">Sangat Puas 😍</option>
                    <option value="Puas">Puas 🙂</option>
                    <option value="Cukup">Cukup 😐</option>
                    <option value="Kurang">Kurang 🙁</option>
                </select>
            </div>
            <div>
                <label class="block mb-2 text-xs font-black text-slate-400 uppercase">Saran & Masukan</label>
                <textarea id="saran" class="w-full p-4 border border-slate-100 rounded-2xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-500" rows="3" placeholder="Apa yang bisa kami tingkatkan?"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="tutupModal()" class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition-all">Batal</button>
                <button onclick="kirimFeedback()" id="btn-kirim" class="flex-1 bg-emerald-500 text-white py-4 rounded-2xl font-bold hover:bg-emerald-600 shadow-lg shadow-emerald-100 transition-all">Kirim</button>
            </div>
        </div>
    </div>
</div>
 
<!-- MODAL BPS -->
<div id="bpsModal" class="fixed inset-0 z-[999] hidden bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white w-full max-w-3xl rounded-[2rem] shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black text-slate-800">Statistik JKN Nasional</h2>
                    <p class="text-emerald-600 text-xs font-bold mt-1">Live Data via BPS API</p>
                </div>
                <button onclick="closeBpsModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div id="bps-content" class="text-center text-slate-400 py-10">
                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>
 
<script>
// =====================
// AMBIL ANTREAN
// =====================
async function ambilAntrean() {
    const poli = document.getElementById('poli').value;
    const alertEl = document.getElementById('alert-antrean');
    const btn = document.getElementById('btn-ambil');

    if (!poli) {
        alertEl.textContent = 'Pilih poli terlebih dahulu!';
        alertEl.classList.remove('hidden');
        return;
    }

    alertEl.classList.add('hidden');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    const fd = new FormData();
    fd.append('poli', poli);

    try {
        const res = await fetch('/tambah_antrian_terbaru', {
            method: 'POST',
            body: fd
        });

        // ✅ FIX: Cek apakah response benar-benar JSON sebelum parse
        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            throw new Error('Response bukan JSON — kemungkinan sesi habis');
        }

        const data = await res.json();

        if (data.success) {
            // ✅ FIX: Tampilkan nomor dulu sebelum redirect, beri waktu DB commit
            btn.textContent = `✓ Antrean ${data.nomor} berhasil!`;
            btn.classList.replace('bg-emerald-600', 'bg-blue-500');
            setTimeout(() => {
                window.location.href = '/dashboard_pasien';
            }, 800); // Delay 800ms agar DB selesai commit sebelum reload
        } else if (data.message && data.message.includes('sesi')) {
            window.location.href = '/login?pesan=sesi_habis';
        } else {
            alertEl.textContent = data.message || 'Terjadi kesalahan, coba lagi.';
            alertEl.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = 'Ambil Nomor Antrean';
        }
    } catch (e) {
        console.error('ambilAntrean error:', e);
        alertEl.textContent = 'Koneksi bermasalah, coba lagi.';
        alertEl.classList.remove('hidden');
        btn.disabled = false;
        btn.textContent = 'Ambil Nomor Antrean';
    }
}
 
// =====================
// MODAL FEEDBACK
// =====================
function bukaModal()  { document.getElementById('modal-feedback').classList.replace('hidden', 'flex'); }
function tutupModal() { document.getElementById('modal-feedback').classList.replace('flex', 'hidden'); }
 
// Di dalam file antri_klinik_digital/api/dashboard_pasien.php

async function kirimFeedback() {
    const btn = document.getElementById('btn-kirim');
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    const fd = new FormData();
    fd.append('nama_pasien', '<?php echo addslashes($nama_user); ?>');
    fd.append('kepuasan', document.getElementById('kepuasan').value);
    fd.append('saran', document.getElementById('saran').value);

    try {
        // Kirim data ke rute simpan_feedback
        await fetch('/simpan_feedback', { method: 'POST', body: fd });
        
        // Setelah sukses simpan, baru arahkan ke logout
        window.location.href = '/logout';
    } catch (e) {
        alert("Gagal mengirim feedback, silakan coba lagi.");
        btn.disabled = false;
        btn.textContent = 'Kirim';
    }
}
 
// =====================
// POLLING SEDANG DILAYANI
// =====================
<?php if ($punya_antrean_aktif) : ?>
function ambilPanggilanTerbaru() {
    fetch('/api/get_antrian_sekarang.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('no-terbaru').innerText   = data.nomor_antrean || '--';
            document.getElementById('poli-terbaru').innerText = data.poli ? 'Poli ' + data.poli : 'Belum ada panggilan';
        })
        .catch(() => {});
}
setInterval(ambilPanggilanTerbaru, 3000);
ambilPanggilanTerbaru();
 
// =====================
// CEK STATUS + TTS
// =====================
let statusTerakhir = '<?php echo $data_antrian['status']; ?>';
const nomorAntrean = '<?php echo $data_antrian['nomor_antrean']; ?>';
const poliAntrean  = '<?php echo $data_antrian['poli']; ?>';
 
function cekStatus() {
    fetch(`/api/cek_status_pasien.php?nomor=${nomorAntrean}`)
        .then(res => res.json())
        .then(data => {
            if (!data.status) return;
 
            const elStatus = document.getElementById('status-display');
            if (elStatus) elStatus.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
 
            if (data.status === 'dipanggil' && statusTerakhir !== 'dipanggil') {
                statusTerakhir = 'dipanggil';
                document.getElementById('notif-panggil').classList.remove('hidden');
                window.speechSynthesis.cancel();
                const teks = `Nomor antrean ${nomorAntrean}, silakan masuk ke Poli ${poliAntrean}`;
                const ssu  = new SpeechSynthesisUtterance(teks);
                ssu.lang   = 'id-ID';
                ssu.rate   = 0.85;
                window.speechSynthesis.speak(ssu);
            }
        })
        .catch(() => {});
}
setInterval(cekStatus, 5000);
<?php endif; ?>
 
// =====================
// MODAL BPS
// =====================
let bpsLoaded = false;

async function openBpsModal() {
    document.getElementById('bpsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (bpsLoaded) return;

    document.getElementById('bps-content').innerHTML = `
        <div class="flex flex-col items-center py-10 text-slate-400">
            <i class="fas fa-circle-notch fa-spin text-3xl text-emerald-400 mb-3"></i>
            <p class="text-sm font-bold">Memuat data dari BPS...</p>
        </div>`;

    try {
        // ✅ Fetch ke proxy PHP di server kita sendiri (tidak kena blokir CORS)
        const res  = await fetch('/api/get_bps.php');
        const json = await res.json();

        if (!json.success || !json.data.length) throw new Error('Data kosong');

        let html = `<table class="w-full text-left text-sm border-separate border-spacing-y-1">
            <thead><tr class="bg-slate-800 text-white">
                <th class="p-3 rounded-l-xl text-center w-12">No</th>
                <th class="p-3">Provinsi</th>
                <th class="p-3 rounded-r-xl text-center">Persentase</th>
            </tr></thead><tbody>`;

        json.data.forEach((r, i) => {
            html += `<tr class="hover:bg-emerald-50 transition-all">
                <td class="p-3 text-slate-400 font-bold text-center rounded-l-xl">${i + 1}</td>
                <td class="p-3 font-bold text-slate-700">${r.provinsi}</td>
                <td class="p-3 text-center rounded-r-xl">
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-bold text-xs">${r.nilai}%</span>
                </td>
            </tr>`;
        });

        html += '</tbody></table>';
        document.getElementById('bps-content').innerHTML = html;
        bpsLoaded = true;

    } catch (e) {
        document.getElementById('bps-content').innerHTML = `
            <div class="flex flex-col items-center py-10 text-slate-400">
                <i class="fas fa-exclamation-triangle text-3xl text-amber-400 mb-3"></i>
                <p class="text-sm font-bold mb-3">Gagal memuat data BPS.</p>
                <button onclick="bpsLoaded=false; openBpsModal()"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-5 py-2 rounded-xl transition-all">
                    <i class="fas fa-redo mr-1"></i> Coba Lagi
                </button>
            </div>`;
    }
}

function closeBpsModal() {
    document.getElementById('bpsModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('bpsModal').addEventListener('click', function(e) {
    if (e.target === this) closeBpsModal();
});
</script>
 
</body>
</html>