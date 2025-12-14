<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

session_start();
require_once "../../config/config.php";
require_once "../../config/database.php";   // 🔹 PDO
require_once "../../config/db_utils.php";   // 🔹 execute()

try {
    // ✅ PDO từ Database
    $database = new Database();
    $pdo = $database->getConnection();

    // ✅ DB_UTILS để chạy query
    $db = new DB_UTILS();

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["id"] ?? 0;

    if (!$id) {
        echo json_encode(["status" => "error", "msg" => "Thiếu ID khách hàng"]);
        exit;
    }

    // 🔐 TRANSACTION
    $pdo->beginTransaction();

    // 1️⃣ Wishlist
    $db->execute("DELETE FROM yeuthich WHERE id_khachhang = ?", [$id]);

    // 2️⃣ Giỏ hàng
    $db->execute("DELETE FROM giohang WHERE id_khachhang = ?", [$id]);

    // 3️⃣ Liên hệ
    $db->execute("DELETE FROM lienhe WHERE id_khachhang = ?", [$id]);

    // 4️⃣ Voucher
    $db->execute("DELETE FROM so_huu_voucher WHERE id_khachhang = ?", [$id]);

    // 5️⃣ Bình luận
    $db->execute("DELETE FROM binhluan WHERE id_khachhang = ?", [$id]);

    // 6️⃣ Chi tiết đơn
    $db->execute("
        DELETE ct FROM hoadonchitiet ct
        INNER JOIN donhang dh ON ct.id_donhang = dh.id_donhang
        WHERE dh.id_khachhang = ?
    ", [$id]);

    // 7️⃣ Đơn hàng
    $db->execute("DELETE FROM donhang WHERE id_khachhang = ?", [$id]);

    // 8️⃣ Khách hàng
    $db->execute("DELETE FROM khachhang WHERE id_khachhang = ?", [$id]);

    // ✅ COMMIT
    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "msg" => "Đã xóa khách hàng và toàn bộ dữ liệu liên quan"
    ]);

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "status" => "error",
        "msg" => "Lỗi khi xóa khách hàng",
        "error" => $e->getMessage()
    ]);
}
