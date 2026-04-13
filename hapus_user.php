<?php
session_start();
include 'koneksi.php';

// Proteksi: Hanya admin yang boleh akses file ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

if (isset($_GET['id']) && isset($_GET['tabel'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $tabel = $_GET['tabel'];

    // Validasi nama tabel agar tidak sembarang hapus
    if ($tabel === 'petugas') {
        $query = "DELETE FROM petugas WHERE id = '$id'";
        $redirect = "kelola_petugas.php";
    } elseif ($tabel === 'pasien') {
        $query = "DELETE FROM pasien WHERE id = '$id'";
        $redirect = "kelola_pasien.php";
    } else {
        die("Tabel tidak valid!");
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Akun berhasil dihapus!'); window.location.href='$redirect';</script>";
    } else {
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
}
?>