<?php
// api/proses_register.php
include "koneksi.php";

$email    = mysqli_real_escape_string($koneksi, $_POST['email']);
$nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
$role     = $_POST['role']; // admin atau petugas
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Cek email duplikat
$cek = mysqli_query($koneksi, "SELECT id FROM petugas WHERE email='$email'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: /register?error=email_exists");
    exit();
}

// Simpan dengan status = 0 (Menunggu Verifikasi)
$query = mysqli_query($koneksi, "INSERT INTO petugas (nama_lengkap, email, password, role, status) 
                                 VALUES ('$nama', '$email', '$password', '$role', 0)");

if ($query) {
    header("Location: /login?pesan=menunggu_verifikasi");
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>