<?php
require_once '../../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$body = json_decode(file_get_contents("php://input"), true);

// Ghi log để debug
file_put_contents("debug_checkout.txt", print_r($body, true));

function res($success, $msg, $data = null) {
    echo json_encode([
        "success" => $success,
        "msg" => $msg,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}


// =============================
// 1) NHẬN DỮ LIỆU
// =============================
$id_khachhang = $body['id_khachhang'] ?? null;
$tenKH        = $body['tenKH'] ?? null;
$sodienthoai  = $body['sodienthoai'] ?? null;
$diachi       = $body['diachi'] ?? null;
$ghichu       = $body['ghichu'] ?? null;
$pttt         = $body['pttt'] ?? "COD";

if (!$id_khachhang || !$tenKH || !$sodienthoai || !$diachi) {
    res(false, "Thiếu thông tin đặt hàng");
}


// =============================
// 2) LẤY GIỎ HÀNG
// =============================
$query = "
    SELECT 
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
    INNER JOIN bienthe bt ON gh.id_bienthe = bt.id_bienthe
    INNER JOIN sanpham sp ON bt.id_sanpham = sp.id_sanpham
    WHERE gh.id_khachhang = :id";

$stmt = $db->prepare($query);
$stmt->bindParam(":id", $id_khachhang);
$stmt->execute();
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cart) {
    res(false, "Giỏ hàng trống");
}


// =============================
// 3) KIỂM TRA TỒN KHO + TÍNH TIỀN
// =============================
$tongtien = 0;

foreach ($cart as $item) {
    if ($item['so_luong'] > $item['tonkho']) {
        res(false, "Sản phẩm {$item['tenSP']} không đủ tồn kho");
    }

    $tongtien += $item['giaSauGiam'] * $item['so_luong'];
}

$phi_ship = 30000;
$giam_gia = 10000;
$tongtien_thucte = $tongtien + $phi_ship - $giam_gia;


// =============================
// 4) TẠO ĐƠN HÀNG
// =============================
$maDatHang = "DH" . time();

$insertOrder = $db->prepare("
    INSERT INTO donhang (maDatHang, id_khachhang, sodienthoai, diachi, tongtien, PTTT, trangthai, thoigiantao, ghichu) 
    VALUES (:ma, :kh, :sdt, :dc, :tt, :pt, 'Chờ xác nhận', NOW(), :gc)
");



$insertOrder->execute([
    ":ma" => $maDatHang,
    ":kh" => $id_khachhang,
    ":sdt" => $sodienthoai,
    ":dc" => $diachi,
    ":tt" => $tongtien_thucte,
    ":pt" => $pttt,
    ":gc" => $ghichu
]);


$id_donhang = $db->lastInsertId();


// =============================
// 5) INSERT CHI TIẾT (KHÔNG CÓ so_luong, gia_ban)
// =============================
foreach ($cart as $item) {

    $ins = $db->prepare("
        INSERT INTO hoadonchitiet (id_donhang, id_sanpham, id_bienthe, SoLuongMua)
        VALUES (:dh, :sp, :bt, :sl)
    ");

    $ins->execute([
        ":dh" => $id_donhang,
        ":sp" => $item['id_sanpham'],
        ":bt" => $item['id_bienthe'],
        ":sl" => $item['so_luong']

    ]);

    // TRỪ TỒN
    $updateStock = $db->prepare("
        UPDATE bienthe
        SET so_luong = so_luong - :sl
        WHERE id_bienthe = :id
    ");
    $updateStock->execute([
        ":sl" => $item['so_luong'],
        ":id" => $item['id_bienthe']
    ]);
}


// =============================
// 6) XÓA GIỎ HÀNG
// =============================
$del = $db->prepare("DELETE FROM giohang WHERE id_khachhang = ?");
$del->execute([$id_khachhang]);


// =============================
// 7) TRẢ KẾT QUẢ
// =============================
res(true, "Đặt hàng thành công", [
    "id_donhang" => $id_donhang,
    "maDatHang"  => $maDatHang
]);

?>
