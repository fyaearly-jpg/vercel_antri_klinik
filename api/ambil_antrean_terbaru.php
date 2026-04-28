<?php
// api/ambil_antrean_terbaru.php
include 'koneksi.php';

// 1. Ambil data dari Cookie (Agar ID Pasien aman)
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data) {
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poli = mysqli_real_escape_string($koneksi, $_POST['poli'] ?? 'Umum');
    $id_pasien = $cookie_data['id']; // Mengambil ID dari session cookie
    $tanggal = date('Y-m-d');

    // 2. AMBIL NOMOR TERAKHIR (Menggunakan kolom: nomor_antrean)
    $query_max = "SELECT MAX(CAST(SUBSTRING(nomor_antrean, 3) AS UNSIGNED)) as max_no 
                  FROM antrian 
                  WHERE poli = '$poli' AND DATE(created_at) = '$tanggal'";
    
    $res_max = mysqli_query($koneksi, $query_max);
    $row = mysqli_fetch_assoc($res_max);
    $next_no = ($row['max_no'] ?? 0) + 1;

    // 3. FORMAT NOMOR (Contoh: U-1, G-1)
    $kode_poli = strtoupper(substr($poli, 0, 1));
    $nomor_baru = $kode_poli . "-" . $next_no;

    // 4. INSERT KE DATABASE (Menggunakan kolom: nomor_antrean)
    $sql_insert = "INSERT INTO antrian (nomor_antrean, status, id_pasien, poli, created_at, updated_at) 
                   VALUES ('$nomor_baru', 'menunggu', '$id_pasien', '$poli', NOW(), NOW())";

    if (mysqli_query($koneksi, $sql_insert)) {
        // Berhasil, arahkan kembali ke dashboard pasien
        header("Location: /dashboard_pasien?status=sukses");
        exit();
    } else {
        die("Gagal simpan antrean: " . mysqli_error($koneksi));
    }
}