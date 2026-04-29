<?php
// api/get_bps.php — Proxy server-side untuk BPS API (menghindari CORS block di browser)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
 
$url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
 
$opts = [
    "http" => [
        "method"  => "GET",
        "header"  => "User-Agent: Mozilla/5.0\r\nAccept: application/json\r\n",
        "timeout" => 10
    ],
    "ssl" => [
        "verify_peer"      => false,
        "verify_peer_name" => false
    ]
];
 
$context  = stream_context_create($opts);
$response = @file_get_contents($url, false, $context);
 
if ($response === false) {
    http_response_code(502);
    echo json_encode(["success" => false, "message" => "Gagal koneksi ke BPS"]);
    exit();
}
 
$data = json_decode($response, true);
 
if (!isset($data['status']) || $data['status'] !== 'OK') {
    http_response_code(502);
    echo json_encode(["success" => false, "message" => "Response BPS tidak valid"]);
    exit();
}
 
$vervar      = $data['vervar']      ?? [];
$datacontent = $data['datacontent'] ?? [];
$results     = [];
 
foreach ($vervar as $v) {
    $id    = (string)$v['val'];
    $label = $v['label'] ?? '';
    if (empty($label)) continue;
 
    $nilai = '0';
    foreach ($datacontent as $key => $val) {
        if (strpos((string)$key, $id) !== false) {
            $nilai = $val;
            break;
        }
    }
 
    $results[] = [
        'provinsi' => $label,
        'nilai'    => $nilai
    ];
}
 
echo json_encode(["success" => true, "data" => $results]);
?>