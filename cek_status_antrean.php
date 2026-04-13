<?php
session_start();
include 'koneksi.php';

$nomor = $_SESSION['punya_antrean'];
$response = ['status' => 'menunggu'];

$query = mysqli_query($conn, "SELECT status, nomor_antrian, poli FROM antrian WHERE nomor_antrian = '$nomor' AND status = 'dipanggil'");

if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);
    $response = [
        'status' => 'dipanggil',
        'nomor'  => $data['nomor_antrian'],
        'poli'   => $data['poli']
    ];
}

echo json_encode($response);