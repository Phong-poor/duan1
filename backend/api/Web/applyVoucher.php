<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../../config/database.php";
$db = (new Database())->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$user_id     = $data["user_id"] ?? 0;
$voucher_id  = $data["voucher_id"] ?? 0;
$order_total = $data["order_total"] ?? 0;

if (!$user_id || !$voucher_id) {
    echo json_encode(["success" => false, "msg" => "Thiếu dữ liệu"]);
    exit;
}

/* =======================================================
   1. LẤY THÔNG TIN VOUCHER USER SỞ HỮU
======================================================== */
$sql = $db->prepare("
    SELECT 
        shv.trang_thai,
        v.ma_voucher,
        v.loai_giam,
        v.gia_tri,
        v.toi_da,
        v.dieu_kien,
        v.dieu_kien_loai,
        v.ngay_het_han
    FROM so_huu_voucher shv
    JOIN voucher v ON v.id_voucher = shv.id_voucher
    WHERE shv.id_khachhang = ? AND shv.id_voucher = ?
");
$sql->execute([$user_id, $voucher_id]);

$voucher = $sql->fetch(PDO::FETCH_ASSOC);

if (!$voucher) {
    echo json_encode(["success" => false, "msg" => "Bạn không sở hữu voucher này!"]);
    exit;
}

if (strtotime($voucher["ngay_het_han"]) < time()) {
    echo json_encode(["success" => false, "msg" => "Voucher đã hết hạn!"]);
    exit;
}

/* =======================================================
   2. KIỂM TRA ĐIỀU KIỆN ĐƠN
======================================================== */
$condition_pass = false;

switch ($voucher["dieu_kien_loai"]) {
    case ">=": $condition_pass = $order_total >= $voucher["dieu_kien"]; break;
    case "<=": $condition_pass = $order_total <= $voucher["dieu_kien"]; break;
    case "=":  $condition_pass = $order_total == $voucher["dieu_kien"]; break;
}

if (!$condition_pass) {
    echo json_encode([
        "success" => false,
        "msg" => "Đơn hàng không thỏa điều kiện voucher!"
    ]);
    exit;
}

/* =======================================================
   3. TÍNH GIẢM GIÁ
======================================================== */
if ($voucher["loai_giam"] === "VND") {
    $discount = min($voucher["gia_tri"], $order_total);
} else {
    $discount = ($order_total * $voucher["gia_tri"]) / 100;
    if ($voucher["toi_da"]) {
        $discount = min($discount, $voucher["toi_da"]);
    }
}

echo json_encode([
    "success"  => true,
    "discount" => $discount,
    "voucher"  => $voucher
]);
