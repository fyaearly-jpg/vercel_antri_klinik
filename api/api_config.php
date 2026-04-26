<?php
// Fungsi untuk mengambil data BPS (Persentase Kepemilikan Ponsel)
function getBPSMobileData() {
    // API BPS: Persentase penduduk yang memiliki ponsel (Contoh Variabel)
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/393/key/YOUR_API_KEY";
    
    // Kita gunakan timeout agar jika koneksi lambat, web tidak macet
    $ctx = stream_context_create(['http' => ['timeout' => 2]]); 
    
    try {
        // @ digunakan untuk menyembunyikan warning jika koneksi gagal
        $response = @file_get_contents($url, false, $ctx);
        
        if ($response === FALSE) {
            return "67.85%"; // Angka default (rata-rata nasional) jika API gagal
        }

        $data = json_decode($response, true);
        // Ambil data pertama dari array hasil BPS
        return ($data['data'][0]['value'] ?? "67.85") . "%";

    } catch (Exception $e) {
        return "67.85%"; // Angka default jika terjadi error
    }
}

// Simpan hasil ke variabel untuk dipanggil di index
$data_jateng = getBPSMobileData();
?>