<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once "../../config/database.php";
$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"] ?? 0;
$voucher_id = $data["voucher_id"] ?? 0;

// Kiểm tra đã nhận chưa
$check = $conn->prepare("
    SELECT * FROM so_huu_voucher 
    WHERE id_khachhang = ? AND id_voucher = ?
");
$check->execute([$user_id, $voucher_id]);

if ($check->rowCount() > 0) {
    echo json_encode(["status" => false, "msg" => "Bạn đã nhận voucher này rồi!"]);
    exit;
}

// Nhận voucher
$stmt = $conn->prepare("
    INSERT INTO so_huu_voucher(id_khachhang, id_voucher, so_luong, trang_thai, ngay_nhan)
    VALUES (?, ?, 1, 'Chua_dung', NOW())
");
$stmt->execute([$user_id, $voucher_id]);

echo json_encode([
    "status" => true,
    "msg" => "Nhận voucher thành công!",
    "voucher_id" => $voucher_id
]);
