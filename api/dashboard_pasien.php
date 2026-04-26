<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien | Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; font-family: 'Arial', sans-serif; }
        .nomor-besar { font-size: 72px; color: #059669; font-weight: 900; line-height: 1; }
    </style>
</head>
<body>
 
<script>
    const _role = localStorage.getItem('role');
    if (!_role || _role !== 'pasien') window.location.href = '/login.php';
</script>
 
<div class="min-h-screen flex flex-col items-center justify-start pt-12 pb-12 px-4">
 
    <!-- CARD UTAMA -->
    <div class="bg-white w-full max-w-sm rounded-[1.5rem] shadow-lg overflow-hidden mb-5">
 
        <!-- Form ambil antrean -->
        <div id="form-antrean" class="p-6 text-center">
            <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-emerald-600 text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold text-emerald-600 mb-1">Pendaftaran Antrean</h2>
            <p class="text-slate-500 text-sm mb-5">Halo, <strong id="nama-pasien"></strong> 👋</p>
 
            <div id="alert-antrean" class="hidden text-sm px-4 py-3 rounded-xl mb-4 bg-red-50 border border-red-200 text-red-600"></div>
 
            <select id="poli" class="w-full p-3 mb-4 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-400 transition-all">
                <option value="">-- Pilih Poli --</option>
                <option value="Umum">Poli Umum</option>
                <option value="Gigi">Poli Gigi</option>
                <option value="Anak">Poli Anak</option>
            </select>
            <button onclick="ambilAntrean()" id="btn-ambil" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all">
                Ambil Antrean
            </button>
        </div>
 
        <!-- Tiket antrean -->
        <div id="tiket-antrean" class="hidden p-6 text-center">
            <h2 class="text-xl font-bold text-slate-800 mb-1">Nomor Antrean Anda</h2>
            <p class="text-slate-500 text-sm mb-3">Poli: <strong id="poli-display"></strong></p>
 
            <div id="notif-panggil" class="hidden bg-emerald-600 text-white p-2 rounded-lg mb-3 font-bold text-sm animate-bounce">
                📢 SILAKAN MASUK KE POLI!
            </div>
 
            <div class="nomor-besar my-4" id="nomor-display">-</div>
 
            <div id="status-badge" class="inline-block px-4 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700 mb-4">
                ⏳ Menunggu
            </div>
 
            <div class="bg-emerald-50 p-3 rounded-xl mb-5 text-emerald-700 text-sm">
                Estimasi Menunggu: <strong>15 Menit</strong>
            </div>
 
            <button onclick="bukaModal()" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-black transition-all">
                Selesai Berobat
            </button>
        </div>
    </div>
 
    <!-- Tombol Info BPS -->
    <button onclick="openBpsModal()" class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-200 hover:bg-emerald-50 transition-all group">
        <i class="fas fa-chart-bar text-emerald-500 group-hover:scale-110 transition-transform"></i>
        <span class="text-sm font-bold text-slate-700">Lihat Info Kesehatan Nasional</span>
    </button>
</div>
 
<!-- MODAL FEEDBACK & LOGOUT -->
<div id="modal-feedback" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[999] p-4">
    <div class="bg-white p-8 rounded-3xl max-w-md w-full shadow-2xl">
        <h2 class="text-xl font-bold mb-4 text-slate-800">Selesai Berobat?</h2>
        <p class="text-slate-500 text-sm mb-5">Berikan penilaian Anda untuk pelayanan hari ini.</p>
 
        <div class="mb-4">
            <label class="block mb-2 font-bold text-slate-700 text-sm">Tingkat Kepuasan</label>
            <select id="kepuasan" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                <option value="Sangat Puas">Sangat Puas 😍</option>
                <option value="Puas">Puas 🙂</option>
                <option value="Cukup">Cukup 😐</option>
                <option value="Kurang">Kurang 🙁</option>
            </select>
        </div>
        <div class="mb-6">
            <label class="block mb-2 font-bold text-slate-700 text-sm">Saran (opsional)</label>
            <textarea id="saran" class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none" rows="3" placeholder="Masukkan saran anda..."></textarea>
        </div>
        <div class="flex gap-2">
            <button onclick="tutupModal()" class="flex-1 bg-slate-100 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-200 transition-all">Batal</button>
            <button onclick="kirimFeedback()" class="flex-1 bg-emerald-500 text-white py-3 rounded-xl font-bold hover:bg-emerald-600 transition-all">Kirim & Selesai</button>
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
                    <p class="text-emerald-600 text-xs font-bold mt-1"><i class="fas fa-sync-alt fa-spin mr-1"></i> Live Data via BPS API</p>
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
const namaPasien = localStorage.getItem('nama') || 'Pasien';
const idPasien   = localStorage.getItem('id')   || '';
 
document.getElementById('nama-pasien').textContent = namaPasien;
 
// Cek apakah sudah punya antrean
const nomorSimpan = localStorage.getItem('nomor_antrean');
const poliSimpan  = localStorage.getItem('poli_terpilih');
 
if (nomorSimpan) {
    tampilkanTiket(nomorSimpan, poliSimpan);
}
 
function tampilkanTiket(nomor, poli) {
    document.getElementById('form-antrean').classList.add('hidden');
    document.getElementById('tiket-antrean').classList.remove('hidden');
    document.getElementById('nomor-display').textContent = nomor;
    document.getElementById('poli-display').textContent  = poli;
}
 
// Ambil antrean
async function ambilAntrean() {
    const poli = document.getElementById('poli').value;
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
    fd.append('id_pasien', idPasien);
    fd.append('poli', poli);
 
    try {
        const res  = await fetch('/api/ambil_antrean.php', { method: 'POST', body: fd });
        const data = await res.json();
 
        if (data.success) {
            localStorage.setItem('nomor_antrean', data.nomor);
            localStorage.setItem('poli_terpilih', data.poli);
            tampilkanTiket(data.nomor, data.poli);
        } else {
            alert.textContent = data.message || 'Gagal mengambil antrean.';
            alert.classList.remove('hidden');
            btn.disabled    = false;
            btn.textContent = 'Ambil Antrean';
        }
    } catch (e) {
        alert.textContent = 'Terjadi kesalahan koneksi.';
        alert.classList.remove('hidden');
        btn.disabled    = false;
        btn.textContent = 'Ambil Antrean';
    }
}
 
// Modal feedback
function bukaModal() {
    document.getElementById('modal-feedback').classList.remove('hidden');
    document.getElementById('modal-feedback').classList.add('flex');
}
function tutupModal() {
    document.getElementById('modal-feedback').classList.add('hidden');
    document.getElementById('modal-feedback').classList.remove('flex');
}
 
async function kirimFeedback() {
    const kepuasan = document.getElementById('kepuasan').value;
    const saran    = document.getElementById('saran').value;
 
    const fd = new FormData();
    fd.append('nama_pasien', namaPasien);
    fd.append('kepuasan',    kepuasan);
    fd.append('saran',       saran);
 
    await fetch('/api/kirim_feedback.php', { method: 'POST', body: fd });
 
    // Bersihkan localStorage antrean lalu logout
    localStorage.removeItem('nomor_antrean');
    localStorage.removeItem('poli_terpilih');
    localStorage.clear();
    window.location.href = '/login.php';
}
 
// Voice synthesis
window.speechSynthesis.getVoices();
window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
 
function getVoiceWanita() {
    const voices = window.speechSynthesis.getVoices();
    return voices.find(v => v.lang === 'id-ID' && v.name.toLowerCase().includes('google'))
        || voices.find(v => v.lang === 'id-ID')
        || null;
}
 
function ejaNomor(nomor) {
    const bagian = nomor.split('-');
    if (bagian.length !== 2) return nomor;
    const namaHuruf = {A:'A',B:'Be',C:'Ce',D:'De',E:'E',G:'Ge',H:'Ha',I:'I',K:'Ka',U:'U'};
    const huruf = bagian[0].toUpperCase();
    return `${namaHuruf[huruf] || huruf} ${parseInt(bagian[1])}`;
}
 
function ucapkan(teks, cb) {
    const ssu = new SpeechSynthesisUtterance(teks);
    ssu.lang  = 'id-ID'; ssu.rate = 0.85; ssu.pitch = 1.3;
    const v = getVoiceWanita();
    if (v) ssu.voice = v;
    if (cb) ssu.onend = cb;
    window.speechSynthesis.speak(ssu);
}
 
function panggilSuara(nomor, poli) {
    window.speechSynthesis.cancel();
    const teks = `Nomor antrean ${ejaNomor(nomor)}, silakan masuk ke Poli ${poli}.`;
    ucapkan(teks, () => setTimeout(() => ucapkan(teks, null), 1200));
}
 
// Cek status antrean tiap 3 detik
let statusTerakhir = 'menunggu';
 
async function cekStatus() {
    const nomor = localStorage.getItem('nomor_antrean');
    if (!nomor) return;
 
    try {
        const res  = await fetch(`/api/cek_status_pasien.php?nomor=${nomor}`);
        const data = await res.json();
 
        if (!data.status) return;
 
        // Update badge status
        const badge = document.getElementById('status-badge');
        if (data.status === 'dipanggil') {
            badge.className = 'inline-block px-4 py-1 rounded-full text-sm font-bold bg-orange-100 text-orange-600 mb-4';
            badge.textContent = '📢 Dipanggil!';
            if (statusTerakhir !== 'dipanggil') {
                panggilSuara(nomor, localStorage.getItem('poli_terpilih') || '');
                document.getElementById('notif-panggil').classList.remove('hidden');
                statusTerakhir = 'dipanggil';
            }
        } else if (data.status === 'selesai') {
            badge.className = 'inline-block px-4 py-1 rounded-full text-sm font-bold bg-emerald-100 text-emerald-700 mb-4';
            badge.textContent = '✅ Selesai';
        } else {
            badge.className = 'inline-block px-4 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700 mb-4';
            badge.textContent = '⏳ Menunggu';
        }
    } catch (e) {}
}
 
if (nomorSimpan) {
    setInterval(cekStatus, 3000);
}
 
// BPS Modal
async function openBpsModal() {
    document.getElementById('bpsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    document.getElementById('bps-content').innerHTML = '<i class="fas fa-spinner fa-spin text-2xl text-emerald-500"></i>';
 
    try {
        const res  = await fetch('/api/get_bps.php');
        const list = await res.json();
        if (!list.length) throw new Error();
        let html = `<table class="w-full text-left text-sm">
            <thead><tr class="bg-slate-800 text-white">
                <th class="p-3 rounded-l-xl">No</th>
                <th class="p-3">Provinsi</th>
                <th class="p-3 rounded-r-xl text-center">Persentase</th>
            </tr></thead><tbody>`;
        list.forEach((r, i) => {
            html += `<tr class="border-b border-slate-50 hover:bg-emerald-50 transition-all">
                <td class="p-3 text-slate-400 font-bold">${i+1}</td>
                <td class="p-3 font-bold text-slate-700">${r.provinsi}</td>
                <td class="p-3 text-center"><span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-bold text-xs">${r.nilai}%</span></td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('bps-content').innerHTML = html;
    } catch (e) {
        document.getElementById('bps-content').innerHTML = '<p class="text-slate-400 italic">Gagal memuat data BPS.</p>';
    }
}
 
function closeBpsModal() {
    document.getElementById('bpsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
</script>
 
</body>
</html>