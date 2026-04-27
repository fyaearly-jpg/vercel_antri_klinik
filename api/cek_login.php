<?php
// api/cek_login.php
include 'koneksi.php';

if (mysqli_connect_errno()) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek di tabel petugas (Admin/Staff/Petugas)
    $query_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email = '$email'");
    $user = mysqli_fetch_assoc($query_petugas);

    if ($user && password_verify($password, $user['password'])) {
        // Bersihkan role: hapus spasi dan jadikan huruf kecil semua
        $role_db = strtolower(trim($user['role']));
        
        $session_data = [
            'id' => $user['id'],
            'nama' => $user['nama_lengkap'],
            'role' => $role_db
        ];
        
        // PERBAIKAN: Parameter Secure diubah ke false agar jalan di localhost
        // setcookie(nama, data, waktu, path, domain, secure, httponly)
        setcookie("user_session", base64_encode(json_encode($session_data)), time() + 3600, "/", "", false, true);
        
        // PERBAIKAN: Redirect sesuai Role
        if ($role_db === 'admin') {
            header("Location: /dashboard_admin");
        } else {
            // Jika role-nya petugas atau staff, kirim ke dashboard petugas
            header("Location: /dashboard_petugas");
        }
        exit();
    }

    // 2. Cek di tabel pasien
    $query_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email = '$email'");
    $pasien = mysqli_fetch_assoc($query_pasien);

    if ($pasien && password_verify($password, $pasien['password'])) {
        $session_data = [
            'id' => $pasien['id'],
            'nama' => $pasien['nama_pasien'],
            'role' => 'pasien'
        ];
        
        // Sesuaikan secure ke false juga
        setcookie("user_session", base64_encode(json_encode($session_data)), time() + 3600, "/", "", false, true);
        
        header("Location: /dashboard_pasien");
        exit();
    }

    header("Location: /login?error=1");
    exit();
}
?>