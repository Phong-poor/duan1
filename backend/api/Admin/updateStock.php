<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["id_bienthe"]) || !isset($data["so_luong"])) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

$id  = (int)$data["id_bienthe"];
$qty = (int)$data["so_luong"];

require_once "../../config/database.php";
$pdo = (new Database())->getConnection();

$sql = "UPDATE bienthe 
        SET so_luong = so_luong + :qty 
        WHERE id_bienthe = :id";

$stmt = $pdo->prepare($sql);

$ok = $stmt->execute([
    ":qty" => $qty,
    ":id"  => $id
]);

if ($ok) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Không thể cập nhật"]);
}
