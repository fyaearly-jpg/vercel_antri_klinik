<?php
include "koneksi.php"; // Pastikan koneksi.php ada di folder yang sama
session_start();

$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

// Cek Pasien
$q = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
$d = mysqli_fetch_assoc($q);

if ($d && password_verify($password, $d['password'])) {
    echo json_encode([
        "success" => true,
        "role" => "pasien",
        "nama" => $d['nama_pasien'],
        "id" => $d['id'],
        "redirect" => "dashboard_pasien.php"
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Email atau Password Salah!"]);
}
?>