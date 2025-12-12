<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");

require_once "../../config/config.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection(); // PDO CONNECTION

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["id_voucher"])) {
    echo json_encode([
        "status" => "error",
        "msg" => "Missing voucher ID!"
    ]);
    exit;
}

$id = $data["id_voucher"];
$ma = $data["ma_voucher"];
$gia_tri = $data["gia_tri"];
$dieu_kien = $data["dieu_kien"];
$trang_thai = $data["trang_thai"];
$toi_da = $data["toi_da"] ?? null;
$ngay_bat_dau = $data["ngay_bat_dau"] ?? null;
$ngay_het_han = $data["ngay_het_han"] ?? null; // percent mới có

$sql = "UPDATE voucher SET 
            ma_voucher = ?, 
            gia_tri = ?, 
            dieu_kien = ?, 
            trang_thai = ?, 
            toi_da = ?,
            ngay_bat_dau = ?,
            ngay_het_han = ?
        WHERE id_voucher = ?";

$stmt = $conn->prepare($sql);

$success = $stmt->execute([
    $ma,
    $gia_tri,
    $dieu_kien,
    $trang_thai,
    $toi_da,
    $ngay_bat_dau,
    $ngay_het_han,
    $id
]);

echo json_encode([
    "status" => $success ? "success" : "error",
    "msg" => $success ? "Cập nhật voucher thành công!" : "Cập nhật thất bại!"
]);
?>
