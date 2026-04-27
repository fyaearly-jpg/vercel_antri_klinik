<?php
session_start();
include "koneksi.php";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek di Tabel Petugas (Admin & Staff)
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    // Gunakan password_verify dan cek variabel $d_petugas
    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        $_SESSION['email'] = $d_petugas['email'];
        $_SESSION['nama'] = $d_petugas['nama_lengkap']; // Sesuai kolom di petugas.sql
        $_SESSION['role'] = $d_petugas['role'];

        if ($d_petugas['role'] == 'admin') {
            session_write_close();
            header("Location: /api/dashboard_admin.php"); // Sesuaikan dengan path file asli karena rute catch-all
            exit();
        } else {
            session_write_close();
            header("Location: /api/dashboard_petugas.php");
            exit();
        }
    }

    // 2. Cek di Tabel Pasien
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    $d_pasien = mysqli_fetch_assoc($q_pasien);

    if ($d_pasien && password_verify($password, $d_pasien['password'])) {
        $_SESSION['id_pasien'] = $d_pasien['id'];
        $_SESSION['nama_pasien'] = $d_pasien['nama_pasien']; // Sesuai kolom di pasien.sql
        $_SESSION['role'] = 'pasien';
        
        session_write_close();
        header("Location: /api/dashboard_pasien.php");
        exit();
    }

    header("Location: /api/login.php?error=1");
    exit();
}
?>