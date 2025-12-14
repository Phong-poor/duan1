<?php
session_start();

header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}
require_once "../../config/config.php";
require_once "../../config/db_utils.php";

$db = new DB_UTILS();

// Chỉ cho Admin xem danh sách user
if (!isset($_SESSION['user'])) {
    echo json_encode([
        "status" => "error",
        "msg" => "Bạn chưa đăng nhập!"
    ]);
    exit();
}

if ($_SESSION['user']['role'] !== "admin") {
    echo json_encode([
        "status" => "error",
        "msg" => "Bạn không có quyền xem danh sách người dùng!"
    ]);
    exit();
}

$data = $db->getAll("
    SELECT id_khachhang, tenKH, email, sodienthoai, role, gioitinh, ngaysinh
    FROM khachhang
");

echo json_encode([
    "status" => "success",
    "data" => $data
]);
