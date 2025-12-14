<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Preflight
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// ====== IMPORT DB_UTILS ======
require_once __DIR__ . "/../../config/db_utils.php";
$db = new DB_UTILS();

// Nhận ID đơn hàng
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id <= 0) {
    echo json_encode([
        "status" => false,
        "message" => "ID đơn hàng không hợp lệ"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// =============================
// LẤY THÔNG TIN ĐƠN HÀNG + KHÁCH HÀNG
// =============================
$order = $db->getOne("
    SELECT 
        d.id_donhang,
        d.thoigiantao,
        d.tongtien,
        d.trangthai,
        d.diachi,
        d.ghichu,
        kh.tenKH AS ten_khachhang,
        kh.sodienthoai,
        kh.email
    FROM donhang d
    JOIN khachhang kh ON d.id_khachhang = kh.id_khachhang
    WHERE d.id_donhang = ?
", [$id]);

if (!$order) {
    echo json_encode([
        "status" => false,
        "message" => "Không tìm thấy đơn hàng"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// =============================
// LẤY DANH SÁCH SẢN PHẨM TRONG ĐƠN
// =============================
$items = $db->getAll("
    SELECT 
        c.id_sanpham,
        c.soLuongMua,
        sp.giaSP,
        (c.soLuongMua * sp.giaSP) AS thanhtien,
        sp.tenSP,
        ms.mausac AS mauSac,
        s.size AS sizeSP,
        sp.hinhAnhgoc
    FROM hoadonchitiet c
    JOIN sanpham sp ON c.id_sanpham = sp.id_sanpham
    JOIN bienthe bt ON bt.id_bienthe = c.id_bienthe
    LEFT JOIN bienthemausac ms ON ms.id_mausac = bt.id_mausac
    LEFT JOIN bienthesize s ON s.id_size = bt.id_size
    WHERE c.id_donhang = ?
", [$id]);
// ============ XỬ LÝ ẢNH ============ 
$UPLOAD_URL = "http://localhost/duan1/backend/";


foreach ($items as &$it) {
    $file = $it["hinhAnhgoc"];

    if ($file) {
        $it["hinhAnhgoc"] = $UPLOAD_URL . $file;
    } else {
        $it["hinhAnhgoc"] = $UPLOAD_URL . "uploads/Product/no-image.png";
    }

    // chuẩn hóa
    $it["tensanpham"] = $it["tenSP"];
    $it["gia"] = $it["giaSP"];
}
unset($it);

// =============================
// TRẢ VỀ KẾT QUẢ
// =============================
echo json_encode([
    "status" => true,
    "order" => $order,
    "items" => $items
], JSON_UNESCAPED_UNICODE);
?>
