<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $kepuasan = mysqli_real_escape_string($conn, $_POST['kepuasan']);
    $saran = mysqli_real_escape_string($conn, $_POST['saran']);

    $query = mysqli_query($conn, "INSERT INTO feedback (nama_pasien, kepuasan, saran) VALUES ('$nama', '$kepuasan', '$saran')");

    if ($query) {
        echo "<script>alert('Terima kasih! Feedback Anda sangat berharga.'); window.location.href='dashboard_pasien.php';</script>";
    } else {
        echo "Gagal mengirim feedback: " . mysqli_error($conn);
    }
}
?>