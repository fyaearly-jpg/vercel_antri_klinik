<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 50px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-8">
 
<script>
    // Proteksi halaman
    const _role = localStorage.getItem('role');
    const _nama = localStorage.getItem('nama');
    if (!_role || (_role !== 'admin' && _role !== 'super_admin')) {
        window.location.href = '/login.php';
    }
</script>
 
<div class="max-w-5xl mx-auto">
 
    <!-- HEADER -->
    <header class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Panel Kendali Admin</h1>
            <p class="text-slate-500">Selamat datang, <span id="nama-admin" class="font-bold text-emerald-600"></span></p>
        </div>
        <button onclick="logout()" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-red-100 transition-all flex items-center gap-2">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </header>
 
    <!-- MENU CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="kelola_petugas.php" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <i class="fas fa-user-md text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Petugas</h2>
            <p class="text-slate-400 text-sm mb-4 leading-relaxed">Kelola akun Dokter, Perawat, dan Staff klinik Anda di sini.</p>
            <span class="text-emerald-500 font-bold">Kelola Petugas <i class="fas fa-arrow-right ml-1"></i></span>
        </a>
 
        <a href="kelola_pasien.php" class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i class="fas fa-users text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Manajemen Pasien</h2>
            <p class="text-slate-400 text-sm mb-4 leading-relaxed">Edit profil pasien atau reset akun pasien yang terdaftar.</p>
            <span class="text-blue-500 font-bold">Kelola Pasien <i class="fas fa-arrow-right ml-1"></i></span>
        </a>
    </div>
 
    <!-- FEEDBACK SECTION -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">Suara Pasien</h2>
                <p class="text-slate-400 text-sm">Masukan terbaru untuk evaluasi pelayanan</p>
            </div>
            <button onclick="resetSemuaFeedback()" class="bg-red-50 text-red-600 px-5 py-2 rounded-xl text-xs font-bold border border-red-100 hover:bg-red-100 transition-all flex items-center gap-2">
                <i class="fas fa-trash-alt"></i> RESET SEMUA
            </button>
        </div>
 
        <div id="feedback-list" class="overflow-y-auto pr-2 space-y-3 custom-scrollbar" style="max-height: 450px;">
            <div class="text-center py-10 text-slate-300">
                <i class="fas fa-spinner fa-spin text-3xl"></i>
                <p class="mt-2 text-sm">Memuat data...</p>
            </div>
        </div>
    </div>
 
</div>
 
<script>
// Set nama admin
document.getElementById('nama-admin').textContent = localStorage.getItem('nama') || 'Admin';
 
// Logout
function logout() {
    localStorage.clear();
    window.location.href = '/login.php';
}
 
// Load feedback
async function loadFeedback() {
    try {
        const res  = await fetch('/api/get_feedback.php');
        const list = await res.json();
        const el   = document.getElementById('feedback-list');
 
        if (!list.length) {
            el.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 text-slate-300">
                    <i class="fas fa-inbox text-5xl mb-4"></i>
                    <p class="italic font-medium">Belum ada masukan dari pasien.</p>
                </div>`;
            return;
        }
 
        el.innerHTML = list.map(f => {
            const kep = (f.kepuasan || '').toUpperCase();
            let badge = 'bg-slate-100 text-slate-500';
            if (kep === 'SANGAT PUAS') badge = 'bg-emerald-100 text-emerald-600';
            else if (kep === 'PUAS')   badge = 'bg-blue-100 text-blue-600';
            else if (kep === 'KURANG') badge = 'bg-red-100 text-red-600';
 
            const tgl = new Date(f.created_at);
            const tglStr = tgl.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
            const jamStr = tgl.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
 
            return `
            <div class="group flex flex-col md:flex-row md:items-center justify-between p-5 rounded-3xl border border-slate-50 hover:bg-slate-50/80 transition-all">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-white shadow-sm rounded-2xl flex items-center justify-center text-slate-300 group-hover:text-emerald-500 transition-colors">
                        <i class="fas fa-comment-dots text-xl"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-bold text-slate-700">${f.nama_pasien || 'Anonim'}</span>
                            <span class="text-[9px] ${badge} px-2.5 py-0.5 rounded-full font-black tracking-wider uppercase">${f.kepuasan || '-'}</span>
                        </div>
                        <p class="text-sm text-slate-500 italic">"${f.saran || '(tidak ada saran)'}"</p>
                    </div>
                </div>
                <div class="flex items-center justify-between md:justify-end gap-6 mt-4 md:mt-0">
                    <div class="text-right">
                        <p class="text-[10px] text-slate-400 font-bold uppercase">${tglStr}</p>
                        <p class="text-[10px] text-slate-300">${jamStr} WIB</p>
                    </div>
                    <button onclick="hapusFeedback(${f.id})" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-slate-300 hover:bg-red-50 hover:text-red-500 shadow-sm transition-all">
                        <i class="fas fa-trash-alt text-sm"></i>
                    </button>
                </div>
            </div>`;
        }).join('');
    } catch (err) {
        document.getElementById('feedback-list').innerHTML = '<p class="text-red-400 text-center py-10">Gagal memuat feedback.</p>';
    }
}
 
async function hapusFeedback(id) {
    if (!confirm('Hapus feedback ini?')) return;
    const res  = await fetch(`/api/hapus_feedback.php?id=${id}`);
    const data = await res.json();
    if (data.success) loadFeedback();
}
 
async function resetSemuaFeedback() {
    if (!confirm('Yakin ingin menghapus SEMUA feedback?')) return;
    const res  = await fetch('/api/hapus_feedback.php?aksi=reset_semua');
    const data = await res.json();
    if (data.success) loadFeedback();
}
 
loadFeedback();
</script>
 
</body>
</html>