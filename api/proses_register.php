<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = $_POST['role'];

    // Cek apakah email sudah ada di tabel petugas ATAU tabel pasien
    $cek_petugas = mysqli_query($koneksi, "SELECT email FROM petugas WHERE email='$email'");
    $cek_pasien  = mysqli_query($koneksi, "SELECT email FROM pasien WHERE email='$email'");

    if (mysqli_num_rows($cek_petugas) > 0 || mysqli_num_rows($cek_pasien) > 0) {
    echo "<script>alert('Email ini sudah terdaftar di sistem!'); window.history.back();</script>";
    exit();
}

    if ($role !== 'pasien') {
        $query = "INSERT INTO petugas (nama_lengkap, email, password, role) VALUES ('$nama', '$email', '$pass', '$role')";
    } else {
        $query = "INSERT INTO pasien (nama_pasien, email, password, role) VALUES ('$nama', '$email', '$pass', 'pasien')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Berhasil daftar sebagai $role! Silakan login.'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>