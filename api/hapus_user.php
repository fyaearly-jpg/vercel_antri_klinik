<?php
// api/hapus_user.php
include "koneksi.php";

$id    = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$tabel = $_GET['tabel'] ?? '';

if (!empty($id) && ($tabel === 'petugas' || $tabel === 'pasien')) {
    // Pastikan menggunakan variabel $koneksi
    $query = mysqli_query($koneksi, "DELETE FROM $tabel WHERE id='$id'");
    
    if ($query) {
        // Redirect kembali ke halaman kelola yang sesuai
        $redirect = ($tabel === 'petugas') ? '/kelola_petugas' : '/kelola_pasien';
        header ("Location: $redirect?status=hapus_berhasil");
    } else {
        echo "Gagal menghapus: " . mysqli_error($koneksi);
    }
} else {
    header("Location: /dashboard_admin");
}
exit();