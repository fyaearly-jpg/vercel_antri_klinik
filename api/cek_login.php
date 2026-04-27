<?php
// api/cek_login.php
include 'koneksi.php';

// ... (logika ambil email & password) ...

if ($user && password_verify($password, $user['password'])) {
    // Tentukan data yang mau disimpan
    $user_data = [
        'id' => $user['id'],
        'nama' => $user['nama_lengkap'],
        'role' => $user['role']
    ];

    // Simpan ke Cookie selama 1 jam (3600 detik)
    // base64_encode hanya agar data tidak berantakan di browser
    setcookie("user_session", base64_encode(json_encode($user_data)), time() + 3600, "/", "", true, true);

    header("Location: /dashboard_admin");
    exit();
}

// 2. Cek Tabel Pasien
$res_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email = '$email_safe' LIMIT 1");
$d_pasien   = mysqli_fetch_assoc($res_pasien);

if ($d_pasien && password_verify($password, $d_pasien['password'])) {
    $_SESSION['id_pasien']   = $d_pasien['id'];
    $_SESSION['nama_pasien'] = $d_pasien['nama_pasien'];
    $_SESSION['role']        = 'pasien';
    
    session_write_close();
    header("Location: /dashboard_pasien");
    exit();
}

header("Location: /login?error=invalid");
exit();