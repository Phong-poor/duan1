<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/database.php";
$db = new Database();
$conn = $db->getConnection();

$user_id = $_GET["user_id"] ?? 0;

$sql = $conn->prepare("
    SELECT 
        shv.id_voucher,
        v.ma_voucher,
        v.loai_giam,      -- % hoặc VNĐ
        v.gia_tri,        -- giá trị giảm
        v.toi_da,         -- giảm tối đa (nếu có)
        v.mo_ta,          -- mô tả voucher
        v.ngay_het_han,   -- hạn sử dụng
        shv.ngay_nhan,
        shv.trang_thai
    FROM so_huu_voucher shv
    JOIN voucher v ON v.id_voucher = shv.id_voucher
    WHERE shv.id_khachhang = ?
");
$sql->execute([$user_id]);

$data = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => true,
    "data" => $data
]);
