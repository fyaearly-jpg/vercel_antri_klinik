<?php
session_start();
include "koneksi.php";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek di Tabel Petugas (Admin & Staff)
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        // Set Session Umum
        $_SESSION['email'] = $d_petugas['email'];
        $_SESSION['nama'] = $d_petugas['nama_lengkap'];

        // Cek Role untuk Redirect
        if ($d_petugas['role'] == 'admin') {
            $_SESSION['role'] = 'admin';
            header("Location: /dashboard_admin");
            exit();
        } else if ($d_petugas['role'] == 'staff' || $d_petugas['role'] == 'petugas') {
            $_SESSION['role'] = 'petugas';
            header("Location: /dashboard_petugas");
            exit();
        }
    }

    // 2. Jika tidak ada di petugas, Cek di Tabel Pasien
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    $d_pasien = mysqli_fetch_assoc($q_pasien);

    if ($d_pasien && password_verify($password, $d_pasien['password'])) {
        $_SESSION['id_pasien'] = $d_pasien['id'];
        $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
        $_SESSION['role'] = 'pasien';
        
        header("Location: /dashboard_pasien");
        exit();
    }

    // 3. Jika Email tidak ditemukan atau Password salah
    header("Location: /login?error=1");
    exit();

} else {
    // Jika akses file langsung tanpa form
    header("Location: /login");
    exit();
}