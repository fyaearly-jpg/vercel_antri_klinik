<?php
// api/simpan_feedback.php
include "koneksi.php";
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_pasien'] ?? 'Anonim');
    $kepuasan = mysqli_real_escape_string($koneksi, $_POST['kepuasan'] ?? '');
    $saran    = mysqli_real_escape_string($koneksi, $_POST['saran'] ?? '');
    
    // Simpan ke database jika form diisi
    if (!empty($kepuasan)) {
        mysqli_query($koneksi, "INSERT INTO feedback (nama_pasien, kepuasan, saran) VALUES ('$nama','$kepuasan','$saran')");
    }
    
    // Setelah pasien menekan "Kirim & Selesai", otomatis logout agar aman
    header("Location: /logout");
    exit();
} else {
    // Jika ada yang akses langsung, kembalikan ke dashboard
    header("Location: /dashboard_pasien");
    exit();
}
?>