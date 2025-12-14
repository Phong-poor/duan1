<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id_thuonghieu"] ?? null;
$tenTH = $data["tenTH"] ?? "";

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Thiếu ID thương hiệu"]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE thuonghieu
    SET tenTH = ?
    WHERE id_thuonghieu = ?
");

$stmt->execute([$tenTH, $id]);

echo json_encode(["status" => "success", "message" => "Cập nhật thương hiệu thành công"]);
exit;
?>
