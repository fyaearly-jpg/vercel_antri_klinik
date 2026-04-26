<?php
/**
 * File: api_bps.php
 * Dataset: Var 2279 (Data Kesehatan/Sosial Terbaru)
 */
function fetchBpsData() {
    // Link Dataset 2279 yang kamu berikan
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
    
    // Ambil data dari BPS
    $response = @file_get_contents($url);
    
    if ($response === FALSE) {
        return []; // Jika gagal ambil data
    }

    $data = json_decode($response, true);
    
    if (isset($data['status']) && $data['status'] == 'OK') {
        $vervar = $data['vervar']; // Daftar Provinsi/Wilayah
        $datacontent = $data['datacontent']; // Nilai data

        $final_results = [];
        
        foreach ($vervar as $v) {
            $id_wilayah = $v['val'];
            $nama_wilayah = $v['label'];
            
            // Cari nilai di datacontent yang mengandung ID Wilayah
            $nilai_data = "0";
            foreach ($datacontent as $key => $val) {
                // Key BPS biasanya formatnya: variabel + wilayah + tahun + turvar
                // Kita cek apakah ID wilayah ada di dalam deretan string Key tersebut
                if (strpos($key, $id_wilayah) !== false) {
                    $nilai_data = $val;
                    break; 
                }
            }

            $final_results[] = [
                'provinsi' => $nama_wilayah,
                'nilai' => $nilai_data
            ];
        }
        return $final_results;
    }
    
    return [];
}
?>