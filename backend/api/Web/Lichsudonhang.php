<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once "../../config/db_utils.php";
$db = new DB_UTILS();

$user_id = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : 0;

if ($user_id <= 0) {
    echo json_encode([
        "status" => false,
        "message" => "User ID không hợp lệ"
    ]);
    exit();
}

$sql = "
    SELECT 
        dh.id_donhang,
        dh.maDatHang,
        dh.thoigiantao,

        -- ⭐ TỔNG TIỀN SAU GIẢM (được lưu trong DB)
        CAST(dh.tongtien AS UNSIGNED) AS tongtien,

        dh.trangthai,

        (
            SELECT COUNT(*)
            FROM hoadonchitiet ct
            WHERE ct.id_donhang = dh.id_donhang
        ) AS total_items
    FROM donhang dh
    WHERE dh.id_khachhang = ?
    ORDER BY dh.thoigiantao DESC
";

$orders = $db->getAll($sql, [$user_id]);

echo json_encode([
    "status" => true,
    "count"  => count($orders),
    "data"   => $orders
], JSON_UNESCAPED_UNICODE);
