<?php
// api/cek_login.php
session_start();
include "koneksi.php";

$email    = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

// 1. Cek di tabel Pasien dulu
$q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
$user = mysqli_fetch_assoc($q_pasien);

if ($user && password_verify($password, $user['password'])) {
    $nama_session = $user['nama_pasien'];
    $role_session = 'pasien';
} else {
    // 2. Jika bukan pasien, cek di tabel Petugas
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $user = mysqli_fetch_assoc($q_petugas);

    if ($user && password_verify($password, $user['password'])) {
        // Cek Verifikasi khusus untuk Petugas/Admin
        if ($user['status'] == 0) {
            header("Location: /login?pesan=belum_aktif");
            exit();
        }
        $nama_session = $user['nama_lengkap'];
        $role_session = $user['role'];
    }
}

// 3. Jika user ditemukan di salah satu tabel
if (isset($role_session)) {
    $user_data = base64_encode(json_encode([
        'id'   => $user['id'],
        'nama' => $nama_session,
        'role' => $role_session
    ]));
    setcookie('user_session', $user_data, time() + (86400 * 30), "/");

    // Redirect ke dashboard masing-masing
    if ($role_session == 'admin') header("Location: /dashboard_admin");
    elseif ($role_session == 'pasien') header("Location: /dashboard_pasien");
    else header("Location: /dashboard_petugas");
} else {
    header("Location: /login?pesan=gagal");
}
?>