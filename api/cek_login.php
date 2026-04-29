<?php
// api/cek_login.php
session_start();
include "koneksi.php";

$email    = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
$user  = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {
    
    // CEK STATUS VERIFIKASI
    if ($user['status'] == 0) {
        header("Location: /login?pesan=belum_aktif");
        exit();
    }

    // Jika aktif (1), buat cookie session
    $user_data = base64_encode(json_encode([
        'id' => $user['id'],
        'nama' => $user['nama_lengkap'],
        'role' => $user['role']
    ]));
    setcookie('user_session', $user_data, time() + (86400 * 30), "/");

    if ($user['role'] == 'admin') header("Location: /dashboard_admin");
    else header("Location: /dashboard_petugas");

} else {
    header("Location: /login?pesan=gagal");
}
?>