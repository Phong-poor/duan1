<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

session_start();
require_once "../../config/config.php";
require_once "../../config/db_utils.php";

try {
    $db = new DB_UTILS();

    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["id"] ?? 0;

    if (!$id) {
        echo json_encode(["status" => "error", "msg" => "Thiếu ID"]);
        exit;
    }

    $result = $db->execute("DELETE FROM khachhang WHERE id_khachhang = ?", [$id]);

    if ($result) {
        echo json_encode(["status" => "success", "msg" => "Xóa khách hàng thành công"]);
    } else {
        echo json_encode(["status" => "error", "msg" => "Không thể xóa người dùng"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
