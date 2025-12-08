<?php
// GetBinhLuan.php - Để lấy danh sách bình luận từ database
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../config/db_utils.php";
$db = new DB_UTILS();

// Lấy id_sanpham từ query string
$id_sanpham = $_GET['id_sanpham'] ?? null;

if (!$id_sanpham) {
    echo json_encode(["status" => false, "msg" => "ID sản phẩm không hợp lệ"]);
    exit;
}

try {
    // Truy vấn tất cả bình luận của sản phẩm
    $comments = $db->getAll("
        SELECT b.id_sanpham ,b.id_binhluan, b.noidung, b.sosao, b.thoigianbinhluan, b.trangthai, u.tenKH
        FROM binhluan b
        LEFT JOIN khachhang u ON b.id_khachhang = u.id_khachhang
        WHERE b.id_sanpham = ? AND b.trangthai = 'Hiện'
        ORDER BY b.thoigianbinhluan DESC
    ", [$id_sanpham]);

    if ($comments) {
        echo json_encode(["status" => true, "msg" => "Lấy bình luận thành công", "data" => $comments]);
    } else {
        echo json_encode(["status" => true, "msg" => "Không có bình luận nào hiện tại", "data" => []]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => false, "msg" => "Có lỗi khi lấy bình luận"]);
}
?>
