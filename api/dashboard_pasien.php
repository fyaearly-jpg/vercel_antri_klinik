<?php
include 'koneksi.php';
 
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;
 
if (!$cookie_data || $cookie_data['role'] !== 'pasien') {
    header("Location: /login");
    exit();
}
 
$id_user   = $cookie_data['id'];
$nama_user = $cookie_data['nama'];
$tanggal   = date('Y-m-d');
 
// Ambil antrean aktif hari ini saja
$query        = mysqli_query($koneksi, "SELECT * FROM antrian WHERE id_pasien='$id_user' AND DATE(created_at)='$tanggal' ORDER BY id DESC LIMIT 1");
$data_antrian = mysqli_fetch_assoc($query);
 
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
 
                <div id="alert-antrean" class="hidden bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl mb-4"></div>
 
                <select id="poli" class="w-full p-3 border rounded-xl mb-4">
                    <option value="">-- Pilih Poli --</option>
                    <option value="Umum">Poli Umum</option>
                    <option value="Gigi">Poli Gigi</option>
                    <option value="Anak">Poli Anak</option>
                </select>
                <button onclick="ambilAntrean()" id="btn-ambil" class="bg-emerald-600 text-white px-4 py-3 rounded-xl w-full font-bold hover:bg-emerald-700 transition-all">
                    Ambil Nomor Antrean
                </button>
 
            <?php else : ?>
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
                <button onclick="kirimFeedback()" class="flex-1 bg-emerald-500 text-white py-4 rounded-2xl font-bold hover:bg-emerald-600 shadow-lg shadow-emerald-100 transition-all">Kirim</button>
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
                <div id="bps-content" class="text-center text-slate-400 py-10"><i class="fas fa-spinner fa-spin text-2xl"></i></div>
            </div>
        </div>
    </div>
</div>
 
<script>
// AMBIL ANTREAN
async function ambilAntrean() {
    const poli  = document.getElementById('poli').value;
    const alert = document.getElementById('alert-antrean');
    const btn   = document.getElementById('btn-ambil');
 
    if (!poli) {
        alert.textContent = 'Pilih poli terlebih dahulu!';
        alert.classList.remove('hidden');
        return;
    }
    alert.classList.add('hidden');
    btn.disabled    = true;
    btn.textContent = 'Memproses...';
 
    const fd = new FormData();
    fd.append('poli',      poli);
    fd.append('id_pasien', '<?php echo $id_user; ?>');
 
    try {
        const res  = await fetch('/api/ambil_antrean_terbaru.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
            location.reload();
        } else {
            alert.textContent = data.message || 'Gagal mengambil antrean.';
            alert.classList.remove('hidden');
            btn.disabled    = false;
            btn.textContent = 'Ambil Nomor Antrean';
        }
    } catch (e) {
        alert.textContent = 'Terjadi kesalahan koneksi.';
        alert.classList.remove('hidden');
        btn.disabled    = false;
        btn.textContent = 'Ambil Nomor Antrean';
    }
}
 
// MODAL FEEDBACK
function bukaModal()  { document.getElementById('modal-feedback').classList.replace('hidden', 'flex'); }
function tutupModal() { document.getElementById('modal-feedback').classList.replace('flex', 'hidden'); }
 
async function kirimFeedback() {
    const fd = new FormData();
    fd.append('nama_pasien', '<?php echo addslashes($nama_user); ?>');
    fd.append('kepuasan',    document.getElementById('kepuasan').value);
    fd.append('saran',       document.getElementById('saran').value);
    await fetch('/api/kirim_feedback.php', { method: 'POST', body: fd });
    window.location.href = '/login';
}
 
// POLLING SEDANG DILAYANI
function ambilPanggilanTerbaru() {
    fetch('/api/get_antrian_sekarang.php')
        .then(res => res.json())
        .then(data => {
            // nomor_antrean sesuai kolom TiDB-mu
            document.getElementById('no-terbaru').innerText   = data.nomor_antrean || '--';
            document.getElementById('poli-terbaru').innerText = data.poli ? 'Poli ' + data.poli : 'Menunggu...';
        })
        .catch(() => {});
}
setInterval(ambilPanggilanTerbaru, 3000);
ambilPanggilanTerbaru();
 
<?php if ($punya_antrean_aktif) : ?>
// CEK STATUS + TTS
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
 
// BPS MODAL
async function openBpsModal() {
    document.getElementById('bpsModal').classList.remove('hidden');
    document.getElementById('bps-content').innerHTML = '<i class="fas fa-spinner fa-spin text-2xl text-emerald-500"></i>';
    try {
        const res  = await fetch('/api/get_bps.php');
        const list = await res.json();
        if (!list.length) throw new Error();
        let html = `<table class="w-full text-left text-sm"><thead><tr class="bg-slate-800 text-white"><th class="p-3 rounded-l-xl">No</th><th class="p-3">Provinsi</th><th class="p-3 rounded-r-xl text-center">Nilai</th></tr></thead><tbody>`;
        list.forEach((r, i) => {
            html += `<tr class="border-b border-slate-50 hover:bg-emerald-50"><td class="p-3 text-slate-400 font-bold">${i+1}</td><td class="p-3 font-bold text-slate-700">${r.provinsi}</td><td class="p-3 text-center"><span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-bold text-xs">${r.nilai}%</span></td></tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('bps-content').innerHTML = html;
    } catch (e) {
        document.getElementById('bps-content').innerHTML = '<p class="text-slate-400 italic">Gagal memuat data BPS.</p>';
    }
}
function closeBpsModal() { document.getElementById('bpsModal').classList.add('hidden'); }
</script>
 
</body>
</html>