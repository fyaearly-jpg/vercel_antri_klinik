<?php
include 'koneksi.php';
session_start();

// Proteksi: Hanya admin/petugas yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'pasien') {
    header("Location: index.php");
    exit();
}

// Logika Hapus Satu Data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM feedback WHERE id = '$id'");
}

// Logika Reset Semua Data
if (isset($_GET['aksi']) && $_GET['aksi'] === 'reset_semua') {
    mysqli_query($koneksi, "TRUNCATE TABLE feedback");
}

header("Location: /dashboard_admin"); 
exit();
?>