<?php
// api/dashboard_petugas.php
include 'koneksi.php';

$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

// Izinkan petugas DAN admin untuk masuk ke dashboard petugas
if (!$cookie_data || ($cookie_data['role'] !== 'petugas' && $cookie_data['role'] !== 'admin')) {
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