<?php
// api/cek_login.php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // ==========================================
    // 1. CEK DI TABEL PETUGAS / ADMIN
    // ==========================================
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    if (mysqli_num_rows($q_petugas) > 0) {
        $d_petugas = mysqli_fetch_assoc($q_petugas);
        if (password_verify($password, $d_petugas['password'])) {
            $_SESSION['nama_lengkap'] = $d_petugas['nama_lengkap'];
            $_SESSION['role'] = $d_petugas['role'];
            
            session_write_close();
            if ($d_petugas['role'] === 'admin' || $d_petugas['role'] === 'super_admin') {
                header("Location: /dashboard_admin");
            } else {
                header("Location: /dashboard_petugas");
            }
            exit();
        }
    }

    // ==========================================
    // 2. CEK DI TABEL PASIEN
    // ==========================================
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    if (mysqli_num_rows($q_pasien) > 0) {
        $d_pasien = mysqli_fetch_assoc($q_pasien);
        if (password_verify($password, $d_pasien['password'])) {
            // Set session sesuai kebutuhan dashboard_pasien
            $_SESSION['role'] = 'pasien'; 
            $_SESSION['nama_pasien'] = $d_pasien['nama_pasien']; 
            $_SESSION['id_pasien'] = $d_pasien['id']; 
            
            session_write_close();
            header("Location: /dashboard_pasien"); 
            exit();
        }
    }

    // Jika salah password atau email tidak terdaftar
    header("Location: /login?error=1");
    exit();
} else {
    header("Location: /login");
    exit();
}
?>