<?php
// api/cek_login.php
include "koneksi.php";

$email    = mysqli_real_escape_string($koneksi, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: /login?pesan=gagal");
    exit();
}

$user         = null;
$nama_session = null;
$role_session = null;

// =============================================
// 1. CEK DI TABEL PASIEN
// =============================================
$q   = mysqli_query($koneksi, "SELECT id, nama_pasien, email, password FROM pasien WHERE email='$email' LIMIT 1");
$row = mysqli_fetch_assoc($q);

if ($row && password_verify($password, $row['password'])) {
    $user         = $row;
    $nama_session = $row['nama_pasien'];
    $role_session = 'pasien';
}

// =============================================
// 2. KALAU BUKAN PASIEN, CEK DI TABEL PETUGAS
// =============================================
if (!$user) {
    $q2   = mysqli_query($koneksi, "SELECT id, nama_lengkap, email, password, role, status FROM petugas WHERE email='$email' LIMIT 1");
    $row2 = mysqli_fetch_assoc($q2);

    if ($row2 && password_verify($password, $row2['password'])) {
        if ($row2['status'] == 0) {
            header("Location: /login?pesan=belum_aktif");
            exit();
        }
        $user         = $row2;
        $nama_session = $row2['nama_lengkap'];
        $role_session = $row2['role'];
    }
}

// =============================================
// 3. SIMPAN COOKIE DAN REDIRECT
// =============================================
if ($user && $role_session) {
    $cookie_data = base64_encode(json_encode([
        'id'   => $user['id'],
        'nama' => $nama_session,
        'role' => $role_session
    ]));

    setcookie('user_session', $cookie_data, time() + (86400 * 30), "/");

    if ($role_session === 'pasien') {
        header("Location: /dashboard_pasien");
    } elseif ($role_session === 'admin' || $role_session === 'super_admin') {
        header("Location: /dashboard_admin");
    } else {
        header("Location: /dashboard_petugas");
    }
} else {
    header("Location: /login?pesan=gagal");
}
exit();
?>