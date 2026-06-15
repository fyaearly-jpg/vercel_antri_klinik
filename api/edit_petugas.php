<?php
// api/edit_petugas.php
include 'koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$query = mysqli_query($koneksi, "SELECT * FROM petugas WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: /kelola_petugas");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 p-10">
    <div class="max-w-lg mx-auto bg-white p-8 rounded-3xl shadow-sm">
        <h2 class="text-2xl font-bold mb-6">Edit Data Petugas</h2>
        
        <form action="/proses_edit_petugas" method="POST" class="space-y-4">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $data['nama_lengkap'] ?>" class="w-full p-3 border rounded-xl" required>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Email</label>
                <input type="email" name="email" value="<?= $data['email'] ?>" class="w-full p-3 border rounded-xl" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 mb-2">Role</label>
                <select name="role" class="w-full p-3 border rounded-xl">
                    <option value="petugas" <?= $data['role'] == 'petugas' ? 'selected' : '' ?>>Petugas</option>
                    <option value="staff" <?= $data['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-emerald-500 text-white py-3 rounded-xl font-bold">Simpan Perubahan</button>
                <a href="/kelola_petugas" class="flex-1 bg-slate-100 text-center py-3 rounded-xl font-bold text-slate-600">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>