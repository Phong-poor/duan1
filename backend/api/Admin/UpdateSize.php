<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$id_size = $data["id_size"] ?? null;
$size = $data["size"] ?? "";

if (!$id_size) {
    echo json_encode(["status" => "error", "message" => "Thiếu ID size"]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE bienthesize 
    SET size = ? 
    WHERE id_size = ?
");
$stmt->execute([$size, $id_size]);

echo json_encode(["status" => "success", "message" => "Cập nhật size thành công"]);
exit;
?>
