<?php
include "koneksi.php";
$email = mysqli_real_escape_string($conn, $_POST['email']);

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek di Tabel Petugas (Admin, Staff, Dokter, dll)
    $q_petugas = mysqli_query($conn, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        $_SESSION['nama_lengkap'] = $d_petugas['nama_lengkap'];
        $_SESSION['role'] = $d_petugas['role'];

        // Cek Role untuk menentukan Dashboard
        if ($d_petugas['role'] === 'admin' || $d_petugas['role'] === 'super_admin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_petugas.php");
        }
        exit();
    } // <-- Penutup blok IF petugas

    // 2. Cek di Tabel Pasien jika tidak ada di tabel petugas
    $q_pasien = mysqli_query($conn, "SELECT * FROM pasien WHERE email='$email'");
    $d_pasien = mysqli_fetch_assoc($q_pasien);

    if ($d_pasien && password_verify($password, $d_pasien['password'])) {
        $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
        $_SESSION['id_pasien'] = $d_pasien['id'];
        $_SESSION['pasien_login'] = true; // Flag untuk dashboard pasien
        $_SESSION['role'] = 'pasien';

        header("Location: dashboard_pasien.php");
        exit();
    } else {
        // Jika tidak ditemukan di kedua tabel
        echo "<script>alert('Email atau Password Salah!'); window.location.href='login.php';</script>";
    }
}
?>