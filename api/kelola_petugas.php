<?php
include 'koneksi.php';
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = mysqli_query($koneksi, "SELECT * FROM petugas ORDER BY role ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-3xl shadow-lg">
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Manajemen Akun Petugas</h1>
            <a href="dashboard_admin.php" class="text-blue-500 underline">Kembali</a>
        </div>
        
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Jabatan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
    <?php while($row = mysqli_fetch_assoc($query)) : ?>
    <tr class="border-b">
        <td class="p-3"><?= $row['nama_lengkap']; ?></td>
        <td class="p-3"><?= $row['email']; ?></td>
        <td class="p-3">
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                <?= strtoupper($row['role']); ?>
            </span>
        </td>
        <td class="p-3">
            <a href="edit_petugas.php?id=<?= $row['id']; ?>" class="text-orange-500 font-bold hover:underline">Edit</a>
            <span class="text-gray-300 mx-1">|</span> 
            <a href="hapus_user.php?id=<?= $row['id']; ?>&tabel=petugas" 
               class="text-red-500 font-bold hover:underline" 
               onclick="return confirm('Yakin ingin menghapus akun petugas ini?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>
        </table>
    </div>
</body>
</html>