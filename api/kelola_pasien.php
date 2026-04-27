<?php
include 'koneksi.php';
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

if (!$cookie_data || $cookie_data['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM pasien ORDER BY nama_pasien ASC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pasien</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-bold mb-6">Daftar Akun Pasien</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3">Nama Pasien</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query)) : ?>
                <tr class="border-b">
                    <td class="p-3"><?= $row['nama_pasien']; ?></td>
                    <td class="p-3"><?= $row['email']; ?></td>
                    <td class="p-3">
                        <a href="hapus_user.php?id=<?php echo $row['id']; ?>&tabel=pasien" 
                        onclick="return confirm('Yakin ingin menghapus?')" 
                        class="text-red-500">
                        Hapus
                    </a>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <a href="dashboard_admin.php" class="text-blue-500 underline">Kembali ke Dashboard</a>
    </div>
</body>
</html>