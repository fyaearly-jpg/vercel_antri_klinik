<?php
include "koneksi.php";

if (isset($_POST['email'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if ($role == 'pasien') {
        $query = "INSERT INTO pasien (nama_pasien, email, password) VALUES ('$nama', '$email', '$password')";
    } else {
        $query = "INSERT INTO petugas (nama_lengkap, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Pendaftaran Berhasil! Silakan Login'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Gagal Daftar: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
}
?>