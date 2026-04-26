<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pasien | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 p-6 md:p-10">
 
<script>
    const _role = localStorage.getItem('role');
    if (!_role || (_role !== 'admin' && _role !== 'super_admin')) window.location.href = '/login.php';
</script>
 
<div class="max-w-5xl mx-auto bg-white p-8 rounded-3xl shadow-lg">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Manajemen Akun Pasien</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola data pasien terdaftar</p>
        </div>
        <a href="dashboard_admin.php" class="bg-slate-100 text-slate-600 px-5 py-2 rounded-xl font-semibold hover:bg-slate-200 transition-all">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
 
    <div id="alert-msg" class="hidden text-sm px-4 py-3 rounded-xl mb-5"></div>
 
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-800 text-white text-left text-sm">
                    <th class="p-4 rounded-tl-xl">Nama Pasien</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Terdaftar</th>
                    <th class="p-4 rounded-tr-xl text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-pasien" class="divide-y divide-slate-100">
                <tr><td colspan="4" class="p-10 text-center text-slate-300"><i class="fas fa-spinner fa-spin"></i></td></tr>
            </tbody>
        </table>
    </div>
</div>
 
<script>
async function loadPasien() {
    try {
        const res  = await fetch('/api/get_pasien.php');
        const list = await res.json();
        const tbody = document.getElementById('tbody-pasien');
 
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="p-10 text-center text-slate-400">Belum ada data pasien.</td></tr>';
            return;
        }
 
        tbody.innerHTML = list.map(row => {
            const tgl = new Date(row.created_at).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
            return `<tr class="hover:bg-slate-50 transition-all">
                <td class="p-4 font-bold text-slate-700">${row.nama_pasien}</td>
                <td class="p-4 text-slate-500 text-sm">${row.email}</td>
                <td class="p-4 text-slate-400 text-sm">${tgl}</td>
                <td class="p-4 text-center">
                    <button onclick="hapusPasien(${row.id},'${row.nama_pasien}')" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition-all">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </button>
                </td>
            </tr>`;
        }).join('');
    } catch (e) {
        document.getElementById('tbody-pasien').innerHTML = '<tr><td colspan="4" class="p-10 text-center text-red-400">Gagal memuat data.</td></tr>';
    }
}
 
async function hapusPasien(id, nama) {
    if (!confirm(`Yakin ingin menghapus akun pasien "${nama}"?`)) return;
    const res  = await fetch(`/api/hapus_user.php?id=${id}&tabel=pasien`);
    const data = await res.json();
 
    const msg = document.getElementById('alert-msg');
    if (data.success) {
        msg.className   = 'text-sm px-4 py-3 rounded-xl mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700';
        msg.textContent = 'Pasien berhasil dihapus!';
        loadPasien();
    } else {
        msg.className   = 'text-sm px-4 py-3 rounded-xl mb-5 bg-red-50 border border-red-200 text-red-600';
        msg.textContent = 'Gagal menghapus pasien.';
    }
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 3000);
}
 
loadPasien();
</script>
 
</body>
</html>