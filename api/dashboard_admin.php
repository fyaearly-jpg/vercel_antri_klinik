<?php
// api/dashboard_admin.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

// 2. Proteksi Halaman: Cek apakah login sebagai admin
if (!$cookie_data || $cookie_data['role'] !== 'admin') {
    header("Location: /login");
    exit();
}

$nama_admin = $cookie_data['nama'];

// 3. Ambil statistik untuk dashboard
$total_pasien = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pasien"))['total'];
$total_antrian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian"))['total'];
$total_petugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM petugas"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">Admin Klinik</h1>
        <div class="flex items-center gap-4">
            <span>Halo, <strong><?php echo htmlspecialchars($nama_admin); ?></strong></span>
            <a href="logout.php" class="text-red-500">Keluar</a>
        </div>
    </nav>
    </body>
</html>