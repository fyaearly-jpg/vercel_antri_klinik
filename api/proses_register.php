<?php
include "koneksi.php"; // Ini mendefinisikan $koneksi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']); 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'pasien';

    // Sesuaikan kolom: nama_pasien (sesuai SQL)
    $sql = "INSERT INTO pasien (email, nama_pasien, password, role) VALUES ('$email', '$nama', '$password', '$role')";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Berhasil Daftar!'); window.location.href='/login';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>