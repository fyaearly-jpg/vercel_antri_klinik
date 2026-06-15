<?php
// api/proses_edit_petugas.php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role  = mysqli_real_escape_string($koneksi, $_POST['role']);

    $update = mysqli_query($koneksi, "UPDATE petugas SET 
        nama_lengkap = '$nama', 
        email = '$email', 
        role = '$role' 
        WHERE id = '$id'");

    if ($update) {
        header("Location: /kelola_petugas?status=update_berhasil");
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}
exit();