<?php
header("Content-Type: application/json");
include "koneksi.php";
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Method tidak valid"]);
    exit();
}
 
$email    = mysqli_real_escape_string($koneksi, trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
 
if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Email dan password wajib diisi"]);
    exit();
}
 
// Cek tabel petugas
$q = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email' LIMIT 1");
$d = mysqli_fetch_assoc($q);
 
if ($d && password_verify($password, $d['password'])) {
    $role     = $d['role'];
    $redirect = ($role === 'admin' || $role === 'super_admin')
        ? '/dashboard_admin.php'
        : '/dashboard_petugas.php';
 
    echo json_encode([
        "success"  => true,
        "role"     => $role,
        "nama"     => $d['nama_lengkap'],
        "id"       => $d['id'],
        "redirect" => $redirect
    ]);
    exit();
}
 
// Cek tabel pasien
$q2 = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email' LIMIT 1");
$d2 = mysqli_fetch_assoc($q2);
 
if ($d2 && password_verify($password, $d2['password'])) {
    echo json_encode([
        "success"  => true,
        "role"     => "pasien",
        "nama"     => $d2['nama_pasien'],
        "id"       => $d2['id'],
        "redirect" => 'api/dashboard_pasien.php'
    ]);
    exit();
}
 
echo json_encode(["success" => false, "message" => "Email atau password salah!"]);
?>