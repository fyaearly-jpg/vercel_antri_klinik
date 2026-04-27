<?php
include "session_config.php";
include "koneksi.php";
 
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
 
if (empty($email) || empty($password)) {
    header("Location: /login?error=1");
    exit();
}
 
$email_safe   = mysqli_real_escape_string($koneksi, $email);
 
// 1. Cek Tabel Petugas
$stmt_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email = '$email_safe' LIMIT 1");
$d_petugas    = mysqli_fetch_assoc($stmt_petugas);
 
if ($d_petugas && password_verify($password, $d_petugas['password'])) {
    $_SESSION['email'] = $d_petugas['email'];
    $_SESSION['nama']  = $d_petugas['nama_lengkap'];
 
    if ($d_petugas['role'] == 'admin') {
        $_SESSION['role'] = 'admin';
        session_write_close();
        header("Location: /dashboard_admin");
        exit();
    } elseif ($d_petugas['role'] == 'staff' || $d_petugas['role'] == 'petugas') {
        $_SESSION['role'] = 'petugas';
        session_write_close();
        header("Location: /dashboard_petugas");
        exit();
    }
}
 
// 2. Cek Tabel Pasien
$stmt_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email = '$email_safe' LIMIT 1");
$d_pasien    = mysqli_fetch_assoc($stmt_pasien);
 
if ($d_pasien && password_verify($password, $d_pasien['password'])) {
    $_SESSION['id_pasien']   = $d_pasien['id'];
    $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
    $_SESSION['role']        = 'pasien';
    session_write_close();
    header("Location: /dashboard_pasien");
    exit();
}
 
header("Location: /login?error=1");
exit();