<?php
// Wajib include session_config dulu
require_once "session_config.php"; 

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: /login?error=empty");
    exit();
}

$email_safe = mysqli_real_escape_string($koneksi, $email);

// 1. Cek Tabel Petugas
$res_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email = '$email_safe' LIMIT 1");
$d_petugas   = mysqli_fetch_assoc($res_petugas);

if ($d_petugas && password_verify($password, $d_petugas['password'])) {
    $_SESSION['email'] = $d_petugas['email'];
    $_SESSION['nama']  = $d_petugas['nama_lengkap'];
    $_SESSION['role']  = $d_petugas['role'];

    session_write_close(); // Simpan data ke TiDB sebelum redirect
    
    if ($_SESSION['role'] == 'admin') {
        header("Location: /dashboard_admin");
    } else {
        header("Location: /dashboard_petugas");
    }
    exit();
}

// 2. Cek Tabel Pasien
$res_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email = '$email_safe' LIMIT 1");
$d_pasien   = mysqli_fetch_assoc($res_pasien);

if ($d_pasien && password_verify($password, $d_pasien['password'])) {
    $_SESSION['id_pasien']   = $d_pasien['id'];
    $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
    $_SESSION['role']        = 'pasien';
    
    session_write_close();
    header("Location: /dashboard_pasien");
    exit();
}

header("Location: /login?error=invalid");
exit();