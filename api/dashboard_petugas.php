<?php
// api/dashboard_petugas.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

// 2. Logika Proteksi: Izinkan jika role adalah 'petugas' ATAU 'admin'
// Gunakan strtolower untuk menghindari masalah huruf besar/kecil
// Di bagian atas api/dashboard_petugas.php
$role = isset($cookie_data['role']) ? strtolower($cookie_data['role']) : '';

// Tambahkan 'staff' di dalam pengecekan
if (!$cookie_data || ($role !== 'petugas' && $role !== 'admin' && $role !== 'staff')) {
    header("Location: /login");
    exit();
}

$nama_petugas = $cookie_data['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <h1>Selamat Datang, <?php echo htmlspecialchars($nama_petugas); ?></h1>
    </body>
</html>