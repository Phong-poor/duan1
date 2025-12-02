<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "../../config/config.php";
require_once "../../config/db_utils.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Sai phương thức request"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["id_donhang"]) || !isset($data["trangthai"])) {
    echo json_encode(["success" => false, "message" => "Thiếu dữ liệu đầu vào"]);
    exit;
}

$id = $data["id_donhang"];
$status = $data["trangthai"];

$db = new DB_UTILS();

$sql = "UPDATE donhang SET trangthai = ? WHERE id_donhang = ?";
$result = $db->execute($sql, [$status, $id]);

if ($result) {
    echo json_encode(["success" => true, "message" => "Cập nhật thành công"]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật"]);
}
