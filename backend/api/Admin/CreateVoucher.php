<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../../config/config.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$sql = "INSERT INTO voucher (
    ma_voucher, loai_giam, gia_tri, toi_da, mo_ta, 
    dieu_kien, dieu_kien_loai, ngay_bat_dau, ngay_het_han, trang_thai
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Hoạt động')";

$stmt = $conn->prepare($sql);

$ok = $stmt->execute([
    $data["ma_voucher"],
    $data["loai_giam"],
    $data["gia_tri"],
    $data["toi_da"] ?: null,
    $data["mo_ta"],
    $data["dieu_kien"],
    $data["dieu_kien_loai"],
    $data["ngay_bat_dau"],
    $data["ngay_het_han"]
]);

echo json_encode([
    "status" => $ok ? "success" : "error",
    "msg" => $ok ? "Thêm voucher thành công!" : "Thêm thất bại!"
]);
?>
