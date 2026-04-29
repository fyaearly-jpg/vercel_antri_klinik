<?php
// api/kelola_petugas.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

// 2. Proteksi Wajib Admin
if (!$cookie_data || strtolower($cookie_data['role'] ?? '') !== 'admin') {
    header("Location: /login");
    exit();
}

// 3. Ambil data petugas (Gunakan variabel $koneksi sesuai koneksi.php)
$query = mysqli_query($koneksi, "SELECT * FROM petugas ORDER BY status ASC, role ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 p-6 md:p-10">

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Manajemen Petugas</h1>
                <p class="text-sm text-slate-400">Verifikasi akun dan kelola hak akses staf</p>
            </div>
            <a href="/dashboard_admin" class="bg-slate-100 text-slate-600 px-6 py-2 rounded-xl font-bold hover:bg-slate-200 transition-all flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest text-left">
                        <th class="p-4 font-bold">Informasi Petugas</th>
                        <th class="p-4 font-bold text-center">Status Akun</th>
                        <th class="p-4 font-bold text-center">Jabatan</th>
                        <th class="p-4 font-bold text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query)) : ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-700"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                                <div class="text-xs text-slate-400"><?= htmlspecialchars($row['email']); ?></div>
                            </td>

                            <td class="p-4 text-center">
                                <?php if ($row['status'] == 0): ?>
                                    <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                <?php else: ?>
                                    <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="p-4 text-center">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                    <?= htmlspecialchars($row['role']); ?>
                                </span>
                            </td>

                            <td class="p-4 text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <?php if ($row['status'] == 0): ?>
                                        <a href="/verifikasi_petugas?id=<?= $row['id']; ?>" 
                                           class="bg-emerald-500 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-emerald-600 shadow-sm shadow-emerald-100">
                                           Verifikasi
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="/edit_petugas?id=<?= $row['id']; ?>" 
                                       class="text-orange-500 hover:bg-orange-50 p-2 rounded-lg transition-all" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="/hapus_user?id=<?= $row['id']; ?>&tabel=petugas" 
                                       onclick="return confirm('Yakin ingin menghapus akun ini?')"
                                       class="text-red-400 hover:text-red-600 p-2 rounded-lg transition-all" title="Hapus Akun">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-400 italic font-medium">
                                <i class="fas fa-users-slash block text-3xl mb-2 opacity-20"></i>
                                Belum ada petugas terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>