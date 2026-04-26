<?php
session_start();
if ($_SESSION['role'] !== 'super_admin') {
    die("Akses Ditolak! Anda bukan Super Admin.");
}

// Logika simpan ke tabel 'petugas' dengan role 'admin'
if (isset($_POST['simpan_admin'])) {
    // ... proses insert ke DB dengan role = 'admin'
}
?>
<form method="POST">
    <h3>Tambah Admin Baru</h3>
    <input type="text" name="nama" placeholder="Nama Admin">
    <input type="email" name="email">
    <input type="password" name="password">
    <button type="submit" name="simpan_admin">Daftarkan Admin</button>
</form>