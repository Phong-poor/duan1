<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Xử lý preflight CORS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id_danhmuc"] ?? null;
$tenDM = $input["tenDM"] ?? "";

// validate
if (!$id) {
    echo json_encode(["status" => "error", "message" => "Thiếu ID danh mục"]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE danhmuc 
    SET tenDM = ?
    WHERE id_danhmuc = ?
");

$stmt->execute([$tenDM, $id]);

echo json_encode(["status" => "success", "message" => "Cập nhật danh mục thành công"]);
exit;
?>