<?php
session_start();
include 'koneksi.php';

// 1. CEK: Apakah ini logout dari Pasien yang ngirim feedback?
if (isset($_POST['kirim_feedback'])) {
    $kepuasan = mysqli_real_escape_string($conn, $_POST['kepuasan']);
    $saran = mysqli_real_escape_string($conn, $_POST['saran']);
    $nama = isset($_SESSION['nama_pasien']) ? $_SESSION['nama_pasien'] : 'Anonim';

    // Simpan feedback
    $query_fb = "INSERT INTO feedback (nama_pasien, kepuasan, saran) VALUES ('$nama', '$kepuasan', '$saran')";
    mysqli_query($conn, $query_fb);
}

// 2. Hapus Session spesifik antrean (biar kalau pasien login lagi sudah bersih)
unset($_SESSION['punya_antrean']);
unset($_SESSION['nomor_antrian']);

// 3. Hancurkan semua session (Petugas, Admin, Pasien semua kena)
$_SESSION = array();
session_destroy();

// 4. Redirect balik ke login
header("Location: index.php");
exit();
?>