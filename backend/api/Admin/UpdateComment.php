<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection();

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["id"]) || !isset($input["trangthai"])) {
    echo json_encode(["status" => "error", "msg" => "Thiếu dữ liệu"]);
    exit;
}

$id = $input["id"];
$trangthai = $input["trangthai"]; // "Hiển thị" hoặc "Ẩn"

$sql = "UPDATE binhluan SET trangthai = ? WHERE id_binhluan = ?";
$stmt = $conn->prepare($sql);
$ok = $stmt->execute([$trangthai, $id]);

echo json_encode([
    "status" => $ok ? "success" : "error",
    "msg" => $ok ? "Cập nhật trạng thái thành công!" : "Cập nhật thất bại!"
], JSON_UNESCAPED_UNICODE);
