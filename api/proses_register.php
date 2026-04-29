<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /register");
    exit();
}

$email    = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
$nama     = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
$role_raw = $_POST['role'] ?? 'pasien';
$password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

$allowed_roles = ['pasien', 'staff', 'admin'];
$role = in_array($role_raw, $allowed_roles) ? $role_raw : 'pasien';

if (empty($email) || empty($nama)) {
    header("Location: /register?error=data_kosong");
    exit();
}

// Cek apakah email sudah ada di tabel pasien
$cek_pasien = mysqli_query($koneksi, "SELECT id FROM pasien WHERE email='$email' LIMIT 1");
// Cek apakah email sudah ada di tabel petugas
$cek_petugas = mysqli_query($koneksi, "SELECT id FROM petugas WHERE email='$email' LIMIT 1");

if (mysqli_num_rows($cek_pasien) > 0 || mysqli_num_rows($cek_petugas) > 0) {
    header("Location: /register?error=email_exists");
    exit();
}

if ($role === 'pasien') {
    $query_simpan = mysqli_query($koneksi, "INSERT INTO pasien (email, nama_pasien, password) VALUES ('$email', '$nama', '$password')");
} else {
    $query_simpan = mysqli_query($koneksi, "INSERT INTO petugas (email, nama_lengkap, password, role) VALUES ('$email', '$nama', '$password', '$role')");
}

if ($query_simpan) {
    header("Location: /login?success=register");
} else {
    header("Location: /register?error=gagal_simpan");
}
exit();
?>