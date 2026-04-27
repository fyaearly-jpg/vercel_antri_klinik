<?php
session_start();
include "koneksi.php";

// 1. Cek di Tabel Petugas (Admin & Staff)
if ($d_petugas && password_verify($password, $d_petugas['password'])) {
    // Set Session Umum
    $_SESSION['email'] = $d_petugas['email'];
    $_SESSION['nama'] = $d_petugas['nama_lengkap'];

    // Cek Role untuk Redirect menggunakan rute bersih Vercel
    if ($d_petugas['role'] == 'admin') {
        $_SESSION['role'] = 'admin';
        session_write_close(); // Simpan session sebelum redirect
        header("Location: /dashboard_admin");
        exit();
    } else if ($d_petugas['role'] == 'staff' || $d_petugas['role'] == 'petugas') {
        $_SESSION['role'] = 'petugas';
        session_write_close(); // Simpan session sebelum redirect
        header("Location: /dashboard_petugas");
        exit();
    }
}

// 2. Jika tidak ada di petugas, Cek di Tabel Pasien
if ($d_pasien && password_verify($password, $d_pasien['password'])) {
    $_SESSION['id_pasien'] = $d_pasien['id'];
    $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
    $_SESSION['role'] = 'pasien';
    
    session_write_close(); // Simpan session sebelum redirect
    header("Location: /dashboard_pasien");
    exit();
}

// 3. Jika Email tidak ditemukan atau Password salah
// Mengarahkan kembali ke rute /login yang terdaftar di vercel.json
header("Location: /login?error=1");
exit();