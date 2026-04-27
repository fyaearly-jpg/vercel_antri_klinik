<?php
session_start();
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    // Pastikan name="nama_pasien" pada form input di file register.php
    $nama_pasien = mysqli_real_escape_string($koneksi, $_POST['nama_pasien']); 
    
    // Hash password agar aman masuk ke database
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'pasien';

    // Perhatikan nama kolom tabel agar sama persis
    $query = "INSERT INTO pasien (email, nama_pasien, password, role) VALUES ('$email', '$nama_pasien', '$password', '$role')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Registrasi sukses!'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>