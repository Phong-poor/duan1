<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection();

$user_id = $_GET["user_id"] ?? 0;

/*
  Chỉ lấy voucher mà user chưa nhận:
  - LEFT JOIN so_huu_voucher
  - Nếu s.id_khachhang IS NULL → chưa nhận
*/

$sql = "
    SELECT v.*
    FROM voucher v
    LEFT JOIN so_huu_voucher s 
      ON s.id_voucher = v.id_voucher 
     AND s.id_khachhang = :user_id
    WHERE v.trang_thai = 'Hoạt động'
      AND NOW() BETWEEN v.ngay_bat_dau AND v.ngay_het_han
      AND s.id_khachhang IS NULL   -- Chỉ lấy voucher chưa nhận
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
