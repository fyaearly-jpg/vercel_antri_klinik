<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Petugas | Admin</title>
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
            <h1 class="text-2xl font-extrabold text-slate-800">Manajemen Akun Petugas</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola data petugas klinik</p>
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
                    <th class="p-4 rounded-tl-xl">Nama</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Jabatan</th>
                    <th class="p-4 rounded-tr-xl text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-petugas" class="divide-y divide-slate-100">
                <tr><td colspan="4" class="p-10 text-center text-slate-300"><i class="fas fa-spinner fa-spin"></i></td></tr>
            </tbody>
        </table>
    </div>
</div>
 
<!-- MODAL EDIT -->
<div id="modal-edit" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <h3 class="text-xl font-bold">Edit Profil Petugas</h3>
            <p id="modal-email" class="text-indigo-100 text-sm mt-1"></p>
        </div>
        <div class="p-8 space-y-5">
            <input type="hidden" id="edit-id">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" id="edit-nama" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-400 outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jabatan / Role</label>
                <select id="edit-role" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-400 outline-none bg-white">
                    <option value="staff">Staff</option>
                    <option value="dokter">Dokter</option>
                    <option value="nurse">Nurse (Perawat)</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button onclick="tutupModal()" class="flex-1 bg-slate-100 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-200 transition-all">Batal</button>
                <button onclick="simpanEdit()" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition-all">Simpan</button>
            </div>
        </div>
    </div>
</div>
 
<script>
async function loadPetugas() {
    try {
        const res  = await fetch('/api/get_petugas.php');
        const list = await res.json();
        const tbody = document.getElementById('tbody-petugas');
 
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="p-10 text-center text-slate-400">Belum ada data petugas.</td></tr>';
            return;
        }
 
        tbody.innerHTML = list.map(row => {
            const roleColor = {admin:'bg-purple-100 text-purple-700', super_admin:'bg-red-100 text-red-700', staff:'bg-blue-100 text-blue-700', dokter:'bg-emerald-100 text-emerald-700', nurse:'bg-pink-100 text-pink-700'};
            const badge = `<span class="${roleColor[row.role] || 'bg-slate-100 text-slate-600'} px-2 py-1 rounded-lg text-xs font-bold">${row.role.toUpperCase()}</span>`;
            return `<tr class="hover:bg-slate-50 transition-all">
                <td class="p-4 font-bold text-slate-700">${row.nama_lengkap}</td>
                <td class="p-4 text-slate-500 text-sm">${row.email}</td>
                <td class="p-4">${badge}</td>
                <td class="p-4 text-center flex justify-center gap-2">
                    <button onclick="bukaEdit(${row.id},'${row.nama_lengkap}','${row.email}','${row.role}')" class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-100 transition-all">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="hapusPetugas(${row.id},'${row.nama_lengkap}')" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition-all">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </button>
                </td>
            </tr>`;
        }).join('');
    } catch (e) {
        document.getElementById('tbody-petugas').innerHTML = '<tr><td colspan="4" class="p-10 text-center text-red-400">Gagal memuat data.</td></tr>';
    }
}
 
function bukaEdit(id, nama, email, role) {
    document.getElementById('edit-id').value    = id;
    document.getElementById('edit-nama').value  = nama;
    document.getElementById('edit-role').value  = role;
    document.getElementById('modal-email').textContent = email;
    document.getElementById('modal-edit').classList.remove('hidden');
    document.getElementById('modal-edit').classList.add('flex');
}
 
function tutupModal() {
    document.getElementById('modal-edit').classList.add('hidden');
    document.getElementById('modal-edit').classList.remove('flex');
}
 
async function simpanEdit() {
    const fd = new FormData();
    fd.append('id',   document.getElementById('edit-id').value);
    fd.append('nama', document.getElementById('edit-nama').value);
    fd.append('role', document.getElementById('edit-role').value);
 
    const res  = await fetch('/api/edit_petugas.php', { method: 'POST', body: fd });
    const data = await res.json();
 
    const msg = document.getElementById('alert-msg');
    if (data.success) {
        msg.className   = 'text-sm px-4 py-3 rounded-xl mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700';
        msg.textContent = 'Data berhasil diupdate!';
        tutupModal();
        loadPetugas();
    } else {
        msg.className   = 'text-sm px-4 py-3 rounded-xl mb-5 bg-red-50 border border-red-200 text-red-600';
        msg.textContent = 'Gagal menyimpan data.';
    }
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 3000);
}
 
async function hapusPetugas(id, nama) {
    if (!confirm(`Yakin ingin menghapus akun petugas "${nama}"?`)) return;
    const res  = await fetch(`/api/hapus_user.php?id=${id}&tabel=petugas`);
    const data = await res.json();
 
    const msg = document.getElementById('alert-msg');
    if (data.success) {
        msg.className   = 'text-sm px-4 py-3 rounded-xl mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700';
        msg.textContent = 'Petugas berhasil dihapus!';
        loadPetugas();
    } else {
        msg.className   = 'text-sm px-4 py-3 rounded-xl mb-5 bg-red-50 border border-red-200 text-red-600';
        msg.textContent = 'Gagal menghapus petugas.';
    }
    msg.classList.remove('hidden');
    setTimeout(() => msg.classList.add('hidden'), 3000);
}
 
loadPetugas();
</script>
 
</body>
</html>