<?php
// api/ambil_antrean_terbaru.php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poli = mysqli_real_escape_string($koneksi, $_POST['poli']);
    $id_pasien = mysqli_real_escape_string($koneksi, $_POST['id_pasien']);
    $tanggal = date('Y-m-d');

    // 1. Ambil nomor terakhir (Gunakan 'nomor_antrian' sesuai file CSV)
    $query_max = "SELECT MAX(CAST(SUBSTRING(nomor_antrian, 3) AS UNSIGNED)) as max_no 
                  FROM antrian 
                  WHERE poli = '$poli' AND DATE(created_at) = '$tanggal'";
    
    $cek_nomor = mysqli_query($koneksi, $query_max);
    $row = mysqli_fetch_assoc($cek_nomor);
    $next_no = ($row['max_no'] ?? 0) + 1;

    // 2. Format nomor baru (U-011, G-005, dll)
    $kode_poli = strtoupper(substr($poli, 0, 1));
    $nomor_baru = $kode_poli . "-" . $next_no; // Sesuaikan format dengan CSV Anda (contoh: U-4)

    // 3. Simpan ke Database
    $sql_insert = "INSERT INTO antrian (nomor_antrian, status, id_pasien, poli, created_at, updated_at) 
                   VALUES ('$nomor_baru', 'menunggu', '$id_pasien', '$poli', NOW(), NOW())";

    if (mysqli_query($koneksi, $sql_insert)) {
        header("Location: /dashboard_pasien?status=sukses");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>