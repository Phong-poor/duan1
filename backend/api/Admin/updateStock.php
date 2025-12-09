<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["id_bienthe"]) || !isset($data["so_luong"])) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

$id = $data["id_bienthe"];
$qty = $data["so_luong"];

require_once "../../config/database.php";
$db = new mysqli("localhost", "root", "", "duan1");

$sql = "UPDATE bienthe SET so_luong = so_luong + ? WHERE id_bienthe = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ii", $qty, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Không thể cập nhật"]);
}
