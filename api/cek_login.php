<?php
// api/cek_login.php
session_start();
include "koneksi.php";

$email    = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];

$role_session = null;
$nama_session = null;
$user         = null;

// 1. Cek di tabel Pasien dulu
$q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
$d_pasien = mysqli_fetch_assoc($q_pasien); // ← FIX: simpan ke $d_pasien

if ($d_pasien && password_verify($password, $d_pasien['password'])) {
    // ← FIX: pakai $d_pasien, bukan $user
    $user         = $d_pasien;
    $nama_session = $user['nama_pasien'];
    $role_session = 'pasien';
} else {
    // 2. Jika bukan pasien, cek di tabel Petugas
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        // Cek Verifikasi khusus untuk Petugas/Admin
        if ($d_petugas['status'] == 0) {
            header("Location: /login?pesan=belum_aktif");
            exit();
        }
        $user         = $d_petugas;
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
    if ($role_session == 'admin')        header("Location: /dashboard_admin");
    elseif ($role_session == 'pasien')   header("Location: /dashboard_pasien");
    else                                  header("Location: /dashboard_petugas");
} else {
    header("Location: /login?pesan=gagal");
}
?>