<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

// ĐÚNG ĐƯỜNG DẪN
require_once '../../config/db_utils.php';

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Thiếu id đơn hàng"]);
    exit();
}

$id = intval($_GET['id']);

$db = new DB_UTILS();
$conn = $db->connection; // PDO

$sql = "SELECT hdc.*, sp.tenSP, sp.giaSP, sp.hinhAnhGoc, sp.maSP
        FROM hoadonchitiet hdc
        JOIN sanpham sp ON hdc.id_sanpham = sp.id_sanpham
        WHERE hdc.id_donhang = :id_donhang";

$stmt = $conn->prepare($sql);
$stmt->execute(["id_donhang" => $id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items, JSON_UNESCAPED_UNICODE);
?>
