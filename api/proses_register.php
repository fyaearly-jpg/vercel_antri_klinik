<?php
header("Content-Type: application/json");
include "koneksi.php";
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Method tidak valid"]);
    exit();
}
 
$nama  = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
$email = mysqli_real_escape_string($koneksi, trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';
$role  = $_POST['role'] ?? 'pasien';
 
if (empty($nama) || empty($email) || empty($pass)) {
    echo json_encode(["success" => false, "message" => "Semua field wajib diisi"]);
    exit();
}
 
$hashed = password_hash($pass, PASSWORD_DEFAULT);
 
// Cek email sudah terdaftar
$cek1 = mysqli_query($koneksi, "SELECT id FROM petugas WHERE email='$email' LIMIT 1");
$cek2 = mysqli_query($koneksi, "SELECT id FROM pasien WHERE email='$email' LIMIT 1");
 
if (mysqli_num_rows($cek1) > 0 || mysqli_num_rows($cek2) > 0) {
    echo json_encode(["success" => false, "message" => "Email sudah terdaftar!"]);
    exit();
}
 
if ($role !== 'pasien') {
    $q = mysqli_query($koneksi, "INSERT INTO petugas (nama_lengkap, email, password, role) VALUES ('$nama','$email','$hashed','$role')");
} else {
    $q = mysqli_query($koneksi, "INSERT INTO pasien (nama_pasien, email, password, role) VALUES ('$nama','$email','$hashed','pasien')");
}
 
if ($q) {
    echo json_encode(["success" => true, "message" => "Berhasil daftar sebagai $role!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal mendaftar: " . mysqli_error($koneksi)]);
}
?>