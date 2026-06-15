<?php
// api/cek_login.php
session_start();
include "koneksi.php";

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // ==========================================
    // 1. CEK DI TABEL PETUGAS / ADMIN
    // ==========================================
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        // Cek status aktif (jika ada fitur status untuk petugas)
        if (isset($d_petugas['status']) && $d_petugas['status'] == 0) {
            header("Location: /login?pesan=belum_aktif");
            exit();
        }

        // Simpan sesi untuk Petugas/Admin
        $_SESSION['nama_lengkap'] = $d_petugas['nama_lengkap'];
        $_SESSION['role'] = $d_petugas['role'];
        
        session_write_close();
        if ($d_petugas['role'] === 'admin') {
            header("Location: /dashboard_admin");
        } else {
            header("Location: /dashboard_petugas");
        }
        exit();
    } 

    // ==========================================
    // 2. CEK DI TABEL PASIEN
    // ==========================================
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    if (mysqli_num_rows($q_pasien) > 0) {
        $d_pasien = mysqli_fetch_assoc($q_pasien);
        // Cocokkan password hash
        if (password_verify($password, $d_pasien['password'])) {
            $_SESSION['role'] = 'pasien'; 
            
            // PERBAIKAN DI SINI: Gunakan 'nama_pasien' sesuai dengan kolom database
            $_SESSION['nama'] = $d_pasien['nama_pasien']; 
            $_SESSION['nama_pasien'] = $d_pasien['nama_pasien']; // Simpan dua-duanya agar aman
            $_SESSION['id_pasien'] = $d_pasien['id']; 
            
            header("Location: /dashboard_pasien"); 
            exit();
        }
    }
?>