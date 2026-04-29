<?php
// api/proses_register.php
include "koneksi.php";

$email    = mysqli_real_escape_string($koneksi, $_POST['email']);
$nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role_input = $_POST['role']; 

// Tentukan tabel dan status awal
if ($role_input === 'pasien') {
    $tabel = 'pasien';
    $role = 'pasien';
    $status_awal = 1; // Pasien langsung aktif
    $kolom_nama = 'nama_pasien';
} else {
    $tabel = 'petugas';
    $role = ($role_input === 'admin') ? 'admin' : 'staff';
    $status_awal = 0; // Petugas/Admin butuh verifikasi
    $kolom_nama = 'nama_lengkap';
}

// Cek email duplikat di tabel yang sesuai
$cek = mysqli_query($koneksi, "SELECT id FROM $tabel WHERE email='$email'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: /register?error=email_exists");
    exit();
}

// Simpan data
if ($tabel === 'pasien') {
    $query = mysqli_query($koneksi, "INSERT INTO pasien (nama_pasien, email, password, role) 
                                     VALUES ('$nama', '$email', '$password', 'pasien')");
} else {
    $query = mysqli_query($koneksi, "INSERT INTO petugas (nama_lengkap, email, password, role, status) 
                                     VALUES ('$nama', '$email', '$password', '$role', $status_awal)");
}

if ($query) {
    // Redirect berbeda berdasarkan role
    if ($role_input === 'pasien') {
        header("Location: /login?pesan=sukses_daftar"); // Pasien langsung bisa login
    } else {
        header("Location: /login?pesan=menunggu_verifikasi"); // Petugas harus tunggu
    }
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>