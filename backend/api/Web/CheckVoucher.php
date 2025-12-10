<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/database.php";
$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data["user_id"] ?? 0;
$code = $data["code"] ?? "";

if (!$user_id || !$code) {
    echo json_encode(["success" => false, "msg" => "Thiếu dữ liệu"]);
    exit;
}

$sql = $conn->prepare("
    SELECT 
        v.id_voucher,
        v.ma_voucher,
        v.loai_giam,
        v.gia_tri,
        v.toi_da,
        v.dieu_kien,
        v.ngay_het_han,
        shv.trang_thai
    FROM voucher v
    JOIN so_huu_voucher shv ON shv.id_voucher = v.id_voucher
    WHERE shv.id_khachhang = ? AND v.ma_voucher = ?
");
$sql->execute([$user_id, $code]);
$voucher = $sql->fetch(PDO::FETCH_ASSOC);

if (!$voucher) {
    echo json_encode(["success" => false, "msg" => "Mã voucher không hợp lệ hoặc bạn chưa sở hữu!"]);
    exit;
}

// kiểm tra hạn sử dụng
if (strtotime($voucher["ngay_het_han"]) < time()) {
    echo json_encode(["success" => false, "msg" => "Voucher đã hết hạn!"]);
    exit;
}

echo json_encode([
    "success" => true,
    "data" => $voucher
]);
