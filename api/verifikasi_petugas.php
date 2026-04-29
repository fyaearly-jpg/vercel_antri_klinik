<?php
include "koneksi.php";
$id = $_GET['id'];
mysqli_query($koneksi, "UPDATE petugas SET status = 1 WHERE id = '$id'");
header("Location: /kelola_petugas?status=berhasil_aktif");
?>