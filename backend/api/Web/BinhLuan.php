<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../config/db_utils.php";
$db = new DB_UTILS();

$data = json_decode(file_get_contents("php://input"), true);

$id_khachhang = $data["id_khachhang"] ?? null;
$id_sanpham = $data["id_sanpham"] ?? null;
$sosao = $data["sosao"] ?? 0;
$noidung = $data["noidung"] ?? "";

if (!$id_khachhang || !$id_sanpham) {
    echo json_encode(["status" => false, "msg" => "Thiếu dữ liệu"]);
    exit;
}

$db->execute("
    INSERT INTO binhluan (id_khachhang, id_sanpham, noidung, sosao, thoigianbinhluan, trangthai)
    VALUES (?, ?, ?, ?, NOW(), 'Hiện')
", [
    $id_khachhang,
    $id_sanpham,
    $noidung,
    $sosao
]);

echo json_encode(["status" => true]);
?>