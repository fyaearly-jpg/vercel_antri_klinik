<?php
header("Content-Type: application/json");
 
$url     = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/2279/th/125/key/7788dbf0dae1b2bdeb03cdb04fbc23e8/";
$context = stream_context_create(['http' => ['timeout' => 5]]);
$resp    = @file_get_contents($url, false, $context);
 
if ($resp === false) {
    echo json_encode([]);
    exit();
}
 
$data = json_decode($resp, true);
 
if (!isset($data['status']) || $data['status'] !== 'OK') {
    echo json_encode([]);
    exit();
}
 
$vervar  = $data['vervar'] ?? [];
$content = $data['datacontent'] ?? [];
$result  = [];
 
foreach ($vervar as $v) {
    $id    = $v['val'];
    $label = $v['label'];
    $val   = "0";
    foreach ($content as $key => $value) {
        if (strpos($key, $id) !== false) {
            $val = $value;
            break;
        }
    }
    $result[] = ['provinsi' => $label, 'nilai' => $val];
}
 
echo json_encode($result);
?>