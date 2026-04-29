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
$query = mysqli_query($koneksi, "SELECT * FROM petugas ORDER BY role ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 p-6 md:p-10">

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Manajemen Petugas</h1>
                <p class="text-sm text-slate-400">Kelola hak akses dan akun staf klinik</p>
            </div>
            <a href="/dashboard_admin" class="bg-slate-100 text-slate-600 px-6 py-2 rounded-xl font-bold hover:bg-slate-200 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-widest text-left">
                        <th class="p-4 font-bold">Nama Petugas</th>
                        <th class="p-4 font-bold">Email</th>
                        <th class="p-4 font-bold">Jabatan</th>
                        <th class="p-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query)) : ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-700"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                            </td>
                            <td class="p-4 text-slate-500 text-sm"><?= htmlspecialchars($row['email']); ?></td>
                            <td class="p-4">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                    <?= htmlspecialchars($row['role']); ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-4">
                                    <a href="/edit_petugas?id=<?= $row['id']; ?>" class="text-orange-500 hover:text-orange-600">
                                        <span class="font-bold text-sm">Edit</span>
                                    </a>
                                    <span class="text-slate-200">|</span>
                                    <a href="/hapus_user?id=<?= $row['id']; ?>&tabel=petugas" 
                                       onclick="return confirm('Yakin ingin menghapus akun ini?')"
                                       class="text-red-500 hover:text-red-600">
                                        <span class="font-bold text-sm">Hapus</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-400 italic">Belum ada petugas terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>