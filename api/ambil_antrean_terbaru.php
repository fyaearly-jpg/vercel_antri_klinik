<?php
// api/ambil_antrean_terbaru.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_raw = $_COOKIE['user_session'] ?? null;
$cookie_data = $cookie_raw ? json_decode(base64_decode($cookie_raw), true) : null;

if (!$cookie_data) {
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poli = mysqli_real_escape_string($koneksi, $_POST['poli'] ?? 'Umum');
    $id_pasien = $cookie_data['id']; 
    $tanggal = date('Y-m-d');

    // 2. AMBIL NOMOR TERAKHIR
    // Gunakan 'nomor_antrean' sesuai pesan error database kamu
    $query_max = "SELECT MAX(CAST(SUBSTRING(nomor_antrean, 3) AS UNSIGNED)) as max_no 
                  FROM antrian 
                  WHERE poli = '$poli' AND DATE(created_at) = '$tanggal'";
    
    $res_max = mysqli_query($koneksi, $query_max);
    $row = mysqli_fetch_assoc($res_max);
    $next_no = ($row['max_no'] ?? 0) + 1;

    // 3. FORMAT NOMOR (Contoh: U-1, G-1)
    $kode_poli = strtoupper(substr($poli, 0, 1));
    $nomor_baru = $kode_poli . "-" . $next_no;

    // 4. INSERT KE DATABASE
    // SAYA SAMAKAN SEMUA MENJADI 'nomor_antrean' (sesuai error mysqli kamu)
    $query_insert = "INSERT INTO antrian (id_pasien, nomor_antrean, poli, status, created_at) 
                     VALUES ('$id_pasien', '$nomor_baru', '$poli', 'menunggu', NOW())";

    if (mysqli_query($koneksi, $query_insert)) {
        header("Location: /dashboard_pasien?status=sukses");
        exit();
    } else {
        // Tampilkan error jika query gagal
        die("Gagal simpan antrean: " . mysqli_error($koneksi));
    }
} else {
    // Jika akses langsung tanpa POST
    header("Location: /dashboard_pasien");
    exit();
}