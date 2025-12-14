<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../../config/database.php";
$db = (new Database())->getConnection();

$body = json_decode(file_get_contents("php://input"), true);

$id_khachhang = $body["id_khachhang"] ?? 0;
$tenKH        = $body["tenKH"] ?? "";
$sodienthoai  = $body["sodienthoai"] ?? "";
$diachi       = $body["diachi"] ?? "";
$ghichu       = $body["ghichu"] ?? "";
$pttt         = $body["pttt"] ?? "COD";

$voucher_list = $body["voucher_list"] ?? [];   // ⭐ danh sách voucher [{id_voucher, discount}]
$total_discount = $body["total_discount"] ?? 0;

if (!$id_khachhang || !$tenKH || !$sodienthoai || !$diachi) {
    echo json_encode(["success" => false, "msg" => "Thiếu thông tin đơn hàng"]);
    exit;
}

/* =====================================================
   LẤY DANH SÁCH GIỎ HÀNG
===================================================== */
$query = "
    SELECT 
        gh.id_giohang,
        gh.id_bienthe,
        gh.so_luong,
        bt.id_sanpham,
        bt.so_luong AS tonkho,
        sp.tenSP,
        sp.giaSP,
        sp.giamgiaSP,
        sp.giamgia_start,
        sp.giamgia_end,
        CASE 
            WHEN sp.giamgiaSP IS NOT NULL 
                AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
            THEN sp.giamgiaSP
            ELSE sp.giaSP
        END AS giaSauGiam
    FROM giohang gh
    JOIN bienthe bt ON gh.id_bienthe = bt.id_bienthe
    JOIN sanpham sp ON bt.id_sanpham = sp.id_sanpham
    WHERE gh.id_khachhang = :id
";

$stmt = $db->prepare($query);
$stmt->execute(["id" => $id_khachhang]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cart) {
    echo json_encode(["success" => false, "msg" => "Giỏ hàng trống"]);
    exit;
}

/* =====================================================
   TÍNH TỔNG + KIỂM TRA TỒN KHO
===================================================== */
$tongtien = 0;

foreach ($cart as $item) {
    if ($item["so_luong"] > $item["tonkho"]) {
        echo json_encode(["success" => false, "msg" => "Sản phẩm {$item['tenSP']} không đủ tồn kho"]);
        exit;
    }
    $tongtien += $item["giaSauGiam"] * $item["so_luong"];
}

$phi_ship = 30000;

$tongtien_thucte = $tongtien + $phi_ship - $total_discount;
if ($tongtien_thucte < 0) $tongtien_thucte = 0;

/* =====================================================
   TẠO ĐƠN HÀNG
===================================================== */
$maDH = "DH" . time();

$insert = $db->prepare("
    INSERT INTO donhang (maDatHang, id_khachhang, sodienthoai, diachi, tongtien, PTTT, trangthai, thoigiantao, ghichu)
    VALUES (?, ?, ?, ?, ?, ?, 'Chờ xác nhận', NOW(), ?)
");
$insert->execute([$maDH, $id_khachhang, $sodienthoai, $diachi, $tongtien_thucte, $pttt, $ghichu]);

$id_donhang = $db->lastInsertId();

/* =====================================================
   LƯU CHI TIẾT ĐƠN HÀNG + TRỪ TỒN
===================================================== */
foreach ($cart as $item) {

    $ins = $db->prepare("
        INSERT INTO hoadonchitiet (id_donhang, id_sanpham, id_bienthe, SoLuongMua)
        VALUES (?, ?, ?, ?)
    ");
    $ins->execute([$id_donhang, $item["id_sanpham"], $item["id_bienthe"], $item["so_luong"]]);

    $update = $db->prepare("
        UPDATE bienthe SET so_luong = so_luong - ? WHERE id_bienthe = ?
    ");
    $update->execute([$item["so_luong"], $item["id_bienthe"]]);
}

/* =====================================================
   LƯU LỊCH SỬ ÁP DỤNG VOUCHER + CHUYỂN TRẠNG THÁI
===================================================== */
foreach ($voucher_list as $vc) {

    // Lưu lịch sử  
    $log = $db->prepare("
        INSERT INTO ap_dung_voucher (id_khachhang, id_voucher, id_donhang, ngay_ap_dung)
        VALUES (?, ?, ?, NOW())
    ");
    $log->execute([$id_khachhang, $vc["id_voucher"], $id_donhang]);

    // Cập nhật trạng thái trong so_huu_voucher
    $updateVoucher = $db->prepare("
        UPDATE so_huu_voucher 
        SET trang_thai = 'Da_dung'
        WHERE id_khachhang = ? AND id_voucher = ?
    ");
    $updateVoucher->execute([$id_khachhang, $vc["id_voucher"]]);
}

/* =====================================================
   XÓA GIỎ HÀNG
===================================================== */
$del = $db->prepare("DELETE FROM giohang WHERE id_khachhang = ?");
$del->execute([$id_khachhang]);

echo json_encode([
    "success" => true,
    "msg" => "Đặt hàng thành công",
    "id_donhang" => $id_donhang,
    "maDatHang" => $maDH,
    "tongtien" => $tongtien_thucte
]);
?>
