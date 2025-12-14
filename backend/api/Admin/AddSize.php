<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$size = $data["size"] ?? "";

// Validate
if (trim($size) === "") {
    echo json_encode(["status" => "error", "message" => "Size không được để trống"]);
    exit;
}

// Insert
$stmt = $pdo->prepare("INSERT INTO bienthesize (size) VALUES (?)");
$stmt->execute([$size]);

echo json_encode(["status" => "success", "message" => "Thêm size thành công"]);
exit;
?>
