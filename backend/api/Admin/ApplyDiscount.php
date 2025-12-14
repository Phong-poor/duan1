<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit();

require_once "../../config/database.php";
$db = new Database();
$pdo = $db->getConnection();

/* Đọc JSON */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Không nhận được JSON hợp lệ",
        "raw" => $raw
    ]);
    exit;
}

$id      = $data["id_sanpham"] ?? null;
$percent = $data["percent"] ?? null;
$start   = $data["giamgia_start"] ?? null;
$end     = $data["giamgia_end"] ?? null;
$originalPrice = $data["giaSP"] ?? null;

if (!$id || $percent === null || $originalPrice === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu bắt buộc gửi lên server"
    ]);
    exit;
}
/* Tính giá sau giảm */
$discountedPrice = $originalPrice - ($originalPrice * $percent / 100);

/* Update */
$stmt = $pdo->prepare("
    UPDATE sanpham
    SET 
        giamgiaSP = ?, 
        giamgia_start = ?, 
        giamgia_end = ?
    WHERE id_sanpham = ?
");

$stmt->execute([$discountedPrice, $start, $end, $id]);

echo json_encode([
    "status" => "success",
    "message" => "Cập nhật giảm giá thành công",
    "giamgiaSP" => $discountedPrice
]);
exit;
?>
