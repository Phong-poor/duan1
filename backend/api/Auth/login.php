<?php
session_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once "../../config/config.php";
require_once "../../config/db_utils.php";

$db_util = new DB_UTILS();

// ===== CORS =====
$allowedOrigins = ['http://localhost:5173']; // origin dev server Vue
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Chỉ POST
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(["status" => "error", "msg" => "Phương thức không hợp lệ"]);
    exit();
}

// Nhận JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["status" => "error", "msg" => "Không nhận được dữ liệu JSON"]);
    exit();
}

// Lấy email + password
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(["status" => "error", "msg" => "Vui lòng nhập đủ thông tin"]);
    exit();
}

// Lấy user trong DB
$user = $db_util->getOne("SELECT id_khachhang, tenKH, email, password, role FROM khachhang WHERE email = ?", [$email]);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
        "id_khachhang" => $user['id_khachhang'],
        "tenKH" => $user['tenKH'],
        "email" => $user['email'],
        "role" => $user['role'] ?? 'user'
    ];
    echo json_encode([
        "status" => "success",
        "msg" => "Đăng nhập thành công",
        "user" => $_SESSION['user']
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "msg" => "Email hoặc mật khẩu không đúng"
    ]);
}
