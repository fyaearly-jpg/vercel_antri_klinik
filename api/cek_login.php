<?php
// api/cek_login.php
session_start();
include "koneksi.php";

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // ==========================================
    // 1. CEK DI TABEL PETUGAS / ADMIN
    // ==========================================
    $q_petugas = mysqli_query($koneksi, "SELECT * FROM petugas WHERE email='$email'");
    $d_petugas = mysqli_fetch_assoc($q_petugas);

    if ($d_petugas && password_verify($password, $d_petugas['password'])) {
        // Cek status aktif (jika ada fitur status untuk petugas)
        if (isset($d_petugas['status']) && $d_petugas['status'] == 0) {
            header("Location: /login?pesan=belum_aktif");
            exit();
        }

        // Simpan sesi untuk Petugas/Admin
        $_SESSION['nama_lengkap'] = $d_petugas['nama_lengkap'];
        $_SESSION['role'] = $d_petugas['role'];
        
        session_write_close();
        if ($d_petugas['role'] === 'admin') {
            header("Location: /dashboard_admin");
        } else {
            header("Location: /dashboard_petugas");
        }
        exit();
    } 

    // ==========================================
    // 2. CEK DI TABEL PASIEN
    // ==========================================
    $q_pasien = mysqli_query($koneksi, "SELECT * FROM pasien WHERE email='$email'");
    $d_pasien = mysqli_fetch_assoc($q_pasien);

    if ($d_pasien && password_verify($password, $d_pasien['password'])) {
        // Simpan sesi dengan terminologi User dan kolom nama yang benar
        $_SESSION['role'] = 'user'; 
        $_SESSION['nama'] = $d_pasien['nama']; 
        $_SESSION['id'] = $d_pasien['id']; 
        
        session_write_close();
        header("Location: /dashboard_pasien"); 
        exit();
    } 

    // ==========================================
    // 3. JIKA EMAIL/PASSWORD SALAH DI KEDUA TABEL
    // ==========================================
    header("Location: /login?error=1");
    exit();

} else {
    // Jika ada yang mencoba mengakses file ini langsung dari URL tanpa form
    header("Location: /login");
    exit();
}
?>