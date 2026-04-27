<?php
session_start(); // Cukup satu kali di paling atas
include "koneksi.php";

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek di Tabel Petugas
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    // GANTI bagian pengecekan role admin dan petugas menjadi seperti ini:

    if ($data['role'] == 'admin') {
        $_SESSION['role'] = 'admin';
        header("Location: dashboard_admin.php"); // Pastikan ekstensi .php terpasang
        exit();
    } else if ($data['role'] == 'petugas' || $data['role'] == 'staff') { 
        $_SESSION['role'] = 'petugas';
        header("Location: dashboard_petugas.php"); // Pastikan ekstensi .php terpasang
        exit();
    }

    // 2. Cek di Tabel Pasien
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    $d_pasien = mysqli_fetch_assoc($q_pasien);

    // Ganti bagian akhir cek_login.php kamu jadi begini:
    if ($d_pasien && password_verify($password, $d_pasien['password'])) {
        $_SESSION['role'] = 'pasien';
        $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
        $_SESSION['id_pasien'] = $d_pasien['id'];
        
        session_write_close(); // PENTING: Simpan session sebelum pindah
        header("Location: /dashboard_pasien"); 
        exit();
    } else {
        // Gunakan URL lengkap atau path yang terdaftar di vercel.json
        header("Location: /login?error=1");
        exit();
    }
} else {
    // Jika akses file ini tanpa kirim form
    header("Location: /login");
    exit();
}
?>