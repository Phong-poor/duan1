<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../../config/config.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    echo json_encode(["status" => "error", "msg" => "Không thể kết nối database"]);
    exit;
}

try {
    // Lấy danh sách voucher
    $sql = "SELECT * FROM voucher ORDER BY id_voucher DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $vouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $today = date("Y-m-d");

    foreach ($vouchers as &$v) {
        // Auto cập nhật trạng thái nếu hết hạn
        if ($v["ngay_het_han"] < $today) {
            $v["trang_thai"] = "Hết hạn";
        } else {
            if ($v["trang_thai"] !== "Ẩn") {
                $v["trang_thai"] = "Hoạt động";
            }
        }
    }

    echo json_encode([
        "status" => "success",
        "data" => $vouchers
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "msg" => "Lỗi truy vấn: " . $e->getMessage()
    ]);
}
