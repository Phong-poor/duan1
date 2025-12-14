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

$mausac = $data["mausac"] ?? "";

// Validate
if (trim($mausac) === "") {
    echo json_encode(["status" => "error", "message" => "Tên màu sắc không được để trống"]);
    exit;
}

// INSERT
$stmt = $pdo->prepare("INSERT INTO bienthemausac (mausac) VALUES (?)");
$stmt->execute([$mausac]);

echo json_encode(["status" => "success", "message" => "Thêm màu sắc thành công"]);
exit;
?>
