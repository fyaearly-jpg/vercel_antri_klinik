<?php
// api/ambil_antrean_terbaru.php
include 'koneksi.php';

// 1. Ambil data dari Cookie
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

if (!$cookie_data) {
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poli'])) {
    $id_pasien = $cookie_data['id'];
    $poli = mysqli_real_escape_string($koneksi, $_POST['poli']);
    $tanggal = date('Y-m-d');

    // 2. Cek apakah pasien sudah punya antrean aktif hari ini (mencegah double antrean)
    $cek_aktif = mysqli_query($koneksi, "SELECT id FROM antrian WHERE id_pasien='$id_pasien' AND DATE(created_at)='$tanggal' AND status != 'selesai'");
    if (mysqli_num_rows($cek_aktif) > 0) {
        header("Location: /dashboard_pasien?error=sudah_ada_antrean");
        exit();
    }

    // 3. Cari nomor antrean terakhir hari ini di poli tersebut
    $cek_nomor = mysqli_query($koneksi, "SELECT MAX(CAST(SUBSTRING(nomor_antrian, 3) AS UNSIGNED)) as max_no FROM antrian WHERE poli='$poli' AND DATE(created_at)='$tanggal'");
    $data = mysqli_fetch_assoc($cek_nomor);
    $next_no = ($data['max_no'] ?? 0) + 1;
    
    // Format: U-001, G-001, dsb.
    $kode_poli = strtoupper(substr($poli, 0, 1));
    $nomor_antrian = $kode_poli . "-" . str_pad($next_no, 3, "0", STR_PAD_LEFT);

    // 4. Insert ke Database
    $query_insert = "INSERT INTO antrian (id_pasien, nomor_antrian, poli, status, created_at) 
                     VALUES ('$id_pasien', '$nomor_antrian', '$poli', 'menunggu', NOW())";
    
    if (mysqli_query($koneksi, $query_insert)) {
        header("Location: /dashboard_pasien?status=sukses");
    } else {
        // Jika error, cetak untuk debug (bisa dihapus nanti)
        echo "Error: " . mysqli_error($koneksi);
    }
    exit();
} else {
    header("Location: /dashboard_pasien");
    exit();
}
?>