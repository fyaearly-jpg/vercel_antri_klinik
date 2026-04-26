<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Monitoring | Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 p-4 md:p-8">
 
<script>
    const _role = localStorage.getItem('role');
    if (!_role || _role === 'pasien') window.location.href = '/login.php';
</script>
 
<div class="max-w-7xl mx-auto">
 
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Panel Petugas</h1>
            <p class="text-slate-500">Kelola antrean real-time hari ini</p>
        </div>
        <div class="flex gap-3">
            <button onclick="resetAntrian()" class="bg-red-50 text-red-600 px-6 py-2 rounded-xl border border-red-100 font-semibold hover:bg-red-100 transition-all">
                <i class="fas fa-sync-alt mr-2"></i>Reset Hari Ini
            </button>
            <a href="dashboard_petugas.php" class="bg-white px-6 py-2 rounded-xl shadow-sm border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>
 
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 
        <!-- TABEL ANTREAN AKTIF -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                <div class="p-6 bg-slate-800 text-white flex items-center justify-between">
                    <h2 class="font-bold flex items-center"><i class="fas fa-list-ol mr-2"></i> Antrean Berjalan</h2>
                    <span id="badge-tunggu" class="bg-slate-700 text-slate-300 text-xs font-bold px-3 py-1 rounded-full">0 menunggu</span>
                </div>
                <div id="tabel-aktif" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 text-xs uppercase tracking-wider">
                                <th class="p-5 font-semibold">No.</th>
                                <th class="p-5 font-semibold">Nama Pasien</th>
                                <th class="p-5 font-semibold">Poli</th>
                                <th class="p-5 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-aktif" class="divide-y divide-slate-100">
                            <tr><td colspan="4" class="p-20 text-center text-slate-300"><i class="fas fa-spinner fa-spin text-2xl"></i></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
 
        <!-- RIWAYAT SELESAI -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                <div class="p-6 bg-blue-600 text-white">
                    <h2 class="font-bold flex items-center"><i class="fas fa-history mr-2"></i> Riwayat Selesai</h2>
                </div>
                <div id="riwayat-list" class="overflow-y-auto max-h-[600px] divide-y divide-slate-100">
                    <div class="p-10 text-center text-slate-300 text-sm italic">Memuat...</div>
                </div>
            </div>
        </div>
 
    </div>
</div>
 
<script>
async function loadAntrian() {
    try {
        const res  = await fetch('/api/get_daftar_antrian.php');
        const data = await res.json();
 
        document.getElementById('badge-tunggu').textContent = `${data.tunggu} menunggu`;
 
        const aktif   = data.list.filter(r => r.status !== 'selesai');
        const selesai = data.list.filter(r => r.status === 'selesai').reverse();
 
        // Render aktif
        const tbody = document.getElementById('tbody-aktif');
        if (!aktif.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="p-20 text-center text-slate-400">Tidak ada antrean aktif.</td></tr>';
        } else {
            tbody.innerHTML = aktif.map(row => {
                const isDipanggil = row.status === 'dipanggil';
                const nomorColor  = isDipanggil ? 'text-orange-500' : 'text-emerald-600';
                const statusBadge = isDipanggil
                    ? '<span class="text-xs text-orange-500 font-bold animate-pulse"><i class="fas fa-bullhorn mr-1"></i>SEDANG DIPANGGIL</span>'
                    : '<span class="text-xs text-slate-400">Status: Menunggu</span>';
                const aksiBtn = isDipanggil
                    ? `<button onclick="aksiAntrian('selesai',${row.id})" class="bg-slate-800 hover:bg-black text-white px-6 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200">SELESAI</button>`
                    : `<button onclick="aksiAntrian('panggil',${row.id})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-xl font-bold transition-all shadow-lg shadow-emerald-100">PANGGIL</button>`;
 
                return `<tr class="hover:bg-slate-50/80 transition-all">
                    <td class="p-5"><span class="text-3xl font-black ${nomorColor}">${row.nomor_antrian}</span></td>
                    <td class="p-5">
                        <div class="font-bold text-slate-700">${row.nama_pasien || '-'}</div>
                        ${statusBadge}
                    </td>
                    <td class="p-5"><span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-sm font-medium">${row.poli}</span></td>
                    <td class="p-5 text-center">${aksiBtn}</td>
                </tr>`;
            }).join('');
        }
 
        // Render riwayat
        const riwayat = document.getElementById('riwayat-list');
        if (!selesai.length) {
            riwayat.innerHTML = '<div class="p-10 text-center text-slate-400 italic text-sm">Belum ada riwayat pelayanan.</div>';
        } else {
            riwayat.innerHTML = selesai.map(row => {
                const jam = new Date(row.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
                return `<div class="p-4 hover:bg-slate-50 transition-all">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-black text-slate-400">#${row.nomor_antrian}</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">${jam}</span>
                    </div>
                    <div class="font-bold text-slate-700 text-sm">${row.nama_pasien || '-'}</div>
                    <div class="text-xs text-slate-400">${row.poli} &bull; <span class="text-blue-500 italic">Selesai</span></div>
                </div>`;
            }).join('');
        }
    } catch (e) {
        document.getElementById('tbody-aktif').innerHTML = '<tr><td colspan="4" class="p-10 text-center text-red-400">Gagal memuat data.</td></tr>';
    }
}
 
async function aksiAntrian(aksi, id) {
    try {
        const res  = await fetch(`/api/aksi_antrian.php?aksi=${aksi}&id=${id}`);
        const data = await res.json();
        if (data.success) loadAntrian();
    } catch (e) {}
}
 
async function resetAntrian() {
    if (!confirm('Yakin ingin mereset semua antrean hari ini?')) return;
    const res  = await fetch('/api/aksi_antrian.php?aksi=reset');
    const data = await res.json();
    if (data.success) loadAntrian();
}
 
loadAntrian();
setInterval(loadAntrian, 5000);
</script>
 
</body>
</html>