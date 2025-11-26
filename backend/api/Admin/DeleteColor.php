<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$id = $_GET["id"] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Thiếu ID màu sắc"]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM bienthemausac WHERE id_mausac = ?");
$stmt->execute([$id]);

echo json_encode(["status" => "success", "message" => "Xóa màu sắc thành công"]);
exit;
?>
