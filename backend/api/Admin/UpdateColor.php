<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id_mausac"] ?? null;
$mausac = $data["mausac"] ?? "";

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Thiếu ID màu sắc"]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE bienthemausac
    SET mausac = ?
    WHERE id_mausac = ?
");

$stmt->execute([$mausac, $id]);

echo json_encode(["status" => "success", "message" => "Cập nhật màu sắc thành công"]);
exit;
?>
