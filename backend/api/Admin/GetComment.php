<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection();

$sql = "
    SELECT b.*, 
           k.tenKH, 
           s.tenSP
    FROM binhluan b
    LEFT JOIN khachhang k ON b.id_khachhang = k.id_khachhang
    LEFT JOIN sanpham s ON b.id_sanpham = s.id_sanpham
    ORDER BY b.id_binhluan DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "success",
    "data" => $data
], JSON_UNESCAPED_UNICODE);
