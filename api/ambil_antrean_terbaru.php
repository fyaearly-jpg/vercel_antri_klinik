<?php
// api/tambah_antrian.php
include 'koneksi.php';

// Ambil data dari Cookie
$cookie_data = isset($_COOKIE['user_session']) ? json_decode(base64_decode($_COOKIE['user_session']), true) : null;

if (!$cookie_data) {
    header("Location: /login");
    exit();
}

if (isset($_POST['poli'])) {
    $id_pasien = $cookie_data['id'];
    $poli = mysqli_real_escape_string($koneksi, $_POST['poli']);
    $tanggal = date('Y-m-d');

    // 1. Cari nomor antrean terakhir hari ini di poli tersebut
    $cek = mysqli_query($koneksi, "SELECT MAX(CAST(SUBSTRING(nomor_antrian, 3) AS UNSIGNED)) as max_no FROM antrian WHERE poli='$poli' AND DATE(created_at)='$tanggal'");
    $data = mysqli_fetch_assoc($cek);
    $next_no = $data['max_no'] + 1;
    $nomor_antrian = strtoupper(substr($poli, 0, 1)) . "-" . str_pad($next_no, 3, "0", STR_PAD_LEFT);

    // 2. Insert ke Database
    $insert = mysqli_query($koneksi, "INSERT INTO antrian (id_pasien, nomor_antrian, poli, status) VALUES ('$id_pasien', '$nomor_antrian', '$poli', 'menunggu')");

    if ($insert) {
        header("Location: /dashboard_pasien?status=sukses");
    } else {
        echo "Gagal mengambil antrean: " . mysqli_error($koneksi);
    }
    exit();
}
?>