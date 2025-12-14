<?php
// ==== CORS ====
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require_once '../../config/db_utils.php';

$db = new DB_UTILS();

$sql = "
    SELECT d.*, 
           k.tenKH AS tenKH, 
           k.email AS email,
           k.sodienthoai AS phone
    FROM donhang d
    LEFT JOIN khachhang k ON d.id_khachhang = k.id_khachhang
    ORDER BY d.id_donhang DESC
";
$orders = $db->getAll($sql);

echo json_encode($orders);
