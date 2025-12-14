<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// IMPORT DB UTIL
require_once __DIR__ . "/../../config/db_utils.php";

$db = new DB_UTILS();

// KIỂM TRA THAM SỐ
$id_donhang = $_GET["id"] ?? null;
$action = $_GET["action"] ?? null;
$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
$lydo = $body["lydo"] ?? "";

if (!$id_donhang || !$action) {
    echo json_encode(["status" => false, "msg" => "Thiếu tham số id hoặc action"]);
    exit;
}
// LẤY DANH SÁCH SẢN PHẨM TRONG ĐƠN
$items = $db->getAll(
    "SELECT id_bienthe, soLuongMua 
     FROM hoadonchitiet 
     WHERE id_donhang = ?", 
     [$id_donhang]
);
if (!$items || count($items) == 0) {
    echo json_encode(["status" => false, "msg" => "Không tìm thấy sản phẩm trong đơn"]);
    exit;
}
// ==============================
// 🔥 HÀM TĂNG TỒN KHO
// ==============================
function addStock($db, $id_bienthe, $qty) {
    return $db->execute(
        "UPDATE bienthe SET so_luong = so_luong + ? WHERE id_bienthe = ?",
        [$qty, $id_bienthe]
    );
}
// ==============================
// 🔥 HÀM GIẢM TỒN KHO
// ==============================
function subtractStock($db, $id_bienthe, $qty) {
    // kiểm tra tồn kho hiện tại
    $stock = $db->getValue(
        "SELECT so_luong FROM bienthe WHERE id_bienthe = ?",
        [$id_bienthe]
    );
    if ($stock < $qty) {
        return false; // không đủ kho
    }
    return $db->execute(
        "UPDATE bienthe SET so_luong = so_luong - ? WHERE id_bienthe = ?",
        [$qty, $id_bienthe]
    );
}
function saveReason($db, $id_donhang, $lydo) {
    if (empty(trim($lydo))) return;

    $db->execute(
        "UPDATE donhang SET lydo = ? WHERE id_donhang = ?",
        [$lydo, $id_donhang]
    );
}


// =====================================
// 🔥 XỬ LÝ 3 HÀNH ĐỘNG CHÍNH
// =====================================
switch ($action) {
    // ================== HỦY ĐƠN ==================
    case "cancel":

        // 🔥 Lưu lý do hủy đơn
        saveReason($db, $id_donhang, $lydo);
        foreach ($items as $row) {
            addStock($db, $row["id_bienthe"], $row["soLuongMua"]);
        }
        $db->execute(
            "UPDATE donhang SET trangthai = 'Hủy đơn' WHERE id_donhang = ?",
            [$id_donhang]
        );
        echo json_encode(["status" => true, "msg" => "Hủy đơn thành công"]);
        break;
    // ================== TRẢ HÀNG ==================
    case "return":
        // 🔥 Lưu lý do trả hàng
        saveReason($db, $id_donhang, $lydo);
        foreach ($items as $row) {
            addStock($db, $row["id_bienthe"], $row["soLuongMua"]);
        }
        $db->execute(
            "UPDATE donhang SET trangthai = 'Trả hàng' WHERE id_donhang = ?",
            [$id_donhang]
        );
        echo json_encode(["status" => true, "msg" => "Trả hàng thành công"]);
        break;
    // ================== MUA LẠI ==================
    case "rebuy":
        // 1. Lấy thông tin đơn cũ
        $oldOrder = $db->getOne(
            "SELECT * FROM donhang WHERE id_donhang = ?",
            [$id_donhang]
        );
        if (!$oldOrder) {
            echo json_encode(["status" => false, "msg" => "Không tìm thấy đơn hàng gốc"]);
            exit;
        }
        // 2. Kiểm tra tồn kho từng biến thể
        foreach ($items as $row) {

            // Lấy tên màu, size và tồn kho chính xác
            $variant = $db->getOne("
                SELECT 
                    m.mausac AS mausac,
                    s.size AS size,
                    b.so_luong AS tonkho
                FROM bienthe b
                JOIN bienthemausac m ON m.id_mausac = b.id_mausac
                JOIN bienthesize s ON s.id_size = b.id_size
                WHERE b.id_bienthe = ?
            ", [$row['id_bienthe']]);

            if (!$variant) {
                echo json_encode([
                    "status" => false,
                    "msg" => "Không tìm thấy thông tin biến thể sản phẩm"
                ]);
                exit;
            }

            // Kiểm tra tồn kho
            if ($variant["tonkho"] < $row["soLuongMua"]) {
                echo json_encode([
                    "status" => false,
                    "msg" => "Sản phẩm có màu {$variant['mausac']} + size {$variant['size']} không đủ tồn kho"
                ]);
                exit;
            }
        }
        // 4. Tạo mã đơn hàng mới
        $newCode = "DH" . rand(10000, 99999);
        // 5. Tạo đơn hàng mới
        $db->execute(
            "INSERT INTO donhang (maDatHang, id_khachhang, sodienthoai, diachi, tongtien, PTTT, trangthai, thoigiantao)
             VALUES (?, ?, ?, ?, ?, ?, 'Chờ xác nhận', NOW())",
            [
                $newCode,
                $oldOrder["id_khachhang"],
                $oldOrder["sodienthoai"],
                $oldOrder["diachi"],
                $oldOrder["tongtien"],
                $oldOrder["PTTT"]
            ]
        );
        $newOrderId = $db->getLastInsertId();
        // 6. Tạo chi tiết đơn hàng mới
        foreach ($items as $row) {
            $db->execute(
                "INSERT INTO hoadonchitiet (id_sanpham, id_donhang, id_bienthe, soLuongMua)
                 VALUES (?, ?, ?, ?)",
                [
                    // lấy từ đơn cũ
                    $db->getValue("SELECT id_sanpham FROM bienthe WHERE id_bienthe = ?", [$row["id_bienthe"]]),
                    $newOrderId,
                    $row["id_bienthe"],
                    $row["soLuongMua"]
                ]
            );
        }
        echo json_encode([
            "status" => true,
            "msg" => "Tạo đơn hàng mới thành công!",
            "new_order_id" => $newOrderId,
            "new_code" => $newCode
        ]);
        break;
    default:
        echo json_encode(["status" => false, "msg" => "Action không hợp lệ"]);
        break;
}
?>
