<?php
require_once "session_config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login");
    exit();
}

// Pastikan include koneksi jika diperlukan atau gunakan variabel dari session_config
include "koneksi.php"; 

$nama_admin = $_SESSION['nama']; 

$total_pasien = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pasien"))['total'];
$total_antrian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM antrian"))['total'];
$total_petugas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM petugas"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Klinik Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
    <nav class="bg-white shadow-sm p-4 flex justify-between items-center">
        <span class="font-bold text-emerald-600">ADMIN CONTROL PANEL</span>
        <div class="flex items-center gap-4">
            <span>Halo, <strong><?php echo htmlspecialchars($nama_admin); ?></strong></span>
            <a href="/logout" class="bg-red-500 text-white px-4 py-1 rounded-lg text-sm">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-emerald-500">
                <p class="text-slate-500 text-sm">Total Pasien</p>
                <h3 class="text-2xl font-bold"><?php echo $total_pasien; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500">
                <p class="text-slate-500 text-sm">Total Antrian</p>
                <h3 class="text-2xl font-bold"><?php echo $total_antrian; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-purple-500">
                <p class="text-slate-500 text-sm">Total Petugas</p>
                <h3 class="text-2xl font-bold"><?php echo $total_petugas; ?></h3>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="/kelola_petugas" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg">Kelola Petugas</a>
            <a href="/kelola_pasien" class="bg-white text-emerald-600 border border-emerald-600 px-6 py-3 rounded-xl font-bold">Data Pasien</a>
        </div>
    </div>
</body>
</html>