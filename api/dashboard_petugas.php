<?php
// api/dashboard_admin.php
include 'koneksi.php';

// 1. Baca Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

// 2. DEBUG (Opsional): Jika masih terpental, hapus komentar di bawah untuk cek isi cookie
// if (!$cookie_data) { die("Cookie tidak ditemukan atau gagal decode"); }

// 3. Syarat: Harus Login dan Role-nya 'admin'
if (!$cookie_data || $cookie_data['role'] !== 'admin') {
    header("Location: /login"); // Gunakan rute vercel.json
    exit();
}

$nama_admin = $cookie_data['nama'];
// ...
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