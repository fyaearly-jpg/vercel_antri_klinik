<?php
// api/cek_login.php
include 'koneksi.php';

// 1. Perbaikan Line 7: Cek koneksi terlebih dahulu
if (mysqli_connect_errno()) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. Pastikan hanya memproses jika ada data yang dikirim lewat POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 3. Cek di tabel petugas (Admin/Staff)
    $query_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email = '$email'");
    $user = mysqli_fetch_assoc($query_petugas);

    // Bagian Admin/Petugas di cek_login.php
    // Di dalam api/cek_login.php bagian pengecekan petugas
   // Bagian pengecekan petugas di api/cek_login.php
    if ($user && password_verify($password, $user['password'])) {
        $current_role = strtolower($user['role']); // Ambil role dari DB dan kecilkan hurufnya
        
        $session_data = [
            'id'    => $user['id'],
            'nama'  => $user['nama_lengkap'],
            'role'  => $current_role
        ];
        
        setcookie("user_session", base64_encode(json_encode($session_data)), time() + 3600, "/", "", true, true);
        
        // Redirect berdasarkan role yang ada di CSV
        if ($current_role === 'admin') {
            header("Location: /dashboard_admin");
        } else {
            // Jika di CSV tulisannya 'staff', dia akan masuk ke dashboard_petugas
            header("Location: /dashboard_petugas");
        }
        exit();
    }
    // 4. Cek di tabel pasien jika data petugas tidak ditemukan
    $query_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email = '$email'");
    $pasien = mysqli_fetch_assoc($query_pasien);

    if ($pasien && password_verify($password, $pasien['password'])) {
        $session_data = [
            'id'    => $pasien['id'],
            'nama'  => $pasien['nama_pasien'],
            'role'  => 'pasien'
        ];
        
        setcookie("user_session", base64_encode(json_encode($session_data)), time() + 3600, "/", "", true, true);
        
        header("Location: /dashboard_pasien");
        exit();
    }

    // 5. Jika semua pengecekan gagal
    header("Location: /login?error=1");
    exit();
} else {
    // Jika mencoba akses file ini langsung tanpa POST
    header("Location: /login");
    exit();
}
?>