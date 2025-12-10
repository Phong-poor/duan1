<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection();

$user_id = $_GET["user_id"] ?? 0;

$sql = "
    SELECT v.*, 
           (SELECT COUNT(*) 
            FROM so_huu_voucher shv
            WHERE shv.id_khachhang = :user_id 
              AND shv.id_voucher = v.id_voucher
           ) AS da_nhan
    FROM voucher v
    WHERE v.trang_thai = 'Hoạt động'
      AND NOW() BETWEEN v.ngay_bat_dau AND v.ngay_het_han
    ORDER BY v.id_voucher DESC
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $user_id);
$stmt->execute();

echo json_encode([
    "status" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
?>
