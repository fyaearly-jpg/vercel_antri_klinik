<?php
session_start();
include 'koneksi.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800">Panel Kendali Admin</h1>
            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded">Logout</a>
        </header>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-xl font-bold mb-2">Manajemen Petugas</h3>
                <p class="text-gray-500 text-sm mb-4">Edit atau hapus akun Dokter, Perawat, dan Staff.</p>
                <a href="kelola_petugas.php" class="text-blue-600 font-bold hover:underline">Kelola Petugas &rarr;</a>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-xl font-bold mb-2">Manajemen Pasien</h3>
                <p class="text-gray-500 text-sm mb-4">Edit data profil pasien atau reset akun pasien.</p>
                <a href="kelola_pasien.php" class="text-green-600 font-bold hover:underline">Kelola Pasien &rarr;</a>
            </div>
        </div>
    </div>
</body>
</html>