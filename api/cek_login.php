<?php
session_start(); // Cukup satu kali di paling atas
include "koneksi.php";

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek di Tabel Petugas
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        $_SESSION['nama_lengkap'] = $d_petugas['nama_lengkap'];
        $_SESSION['role'] = $d_petugas['role'];
        
        // Sesuaikan dengan route di vercel.json
        if ($d_petugas['role'] === 'admin') {
            header("Location: /dashboard_admin");
        } else {
            header("Location: /dashboard_petugas");
        }
        exit();
    } 

    // 2. Cek di Tabel Pasien
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    $d_pasien = mysqli_fetch_assoc($q_pasien);

    if ($d_pasien && password_verify($password, $d_pasien['password'])) {
        $_SESSION['role'] = 'pasien';
        $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
        $_SESSION['id_pasien'] = $d_pasien['id'];
        
        header("Location: /dashboard_pasien"); // Pakai / agar lewat vercel.json
        exit();
    } else {
        // Jika tidak ketemu di petugas maupun pasien
        echo "<script>alert('Email atau Password Salah!'); window.location.href='/login';</script>";
        exit();
    }
} else {
    // Jika akses file ini tanpa kirim form
    header("Location: /login");
    exit();
}
?>