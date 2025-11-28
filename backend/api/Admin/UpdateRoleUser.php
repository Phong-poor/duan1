<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

require_once "../../config/config.php";
require_once "../../config/db_utils.php";

$db = new DB_UTILS();
$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? 0;
$role = $data["role"] ?? "";

// Kiểm tra session
if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "msg" => "Không có quyền thực hiện thao tác này (chưa đăng nhập)"]);
    exit();
}

$currentUserEmail = $_SESSION['user']['email'];
$currentUserRole = $_SESSION['user']['role'];

if (!$id || $role == "") {
    echo json_encode(["status" => "error", "msg" => "Thiếu dữ liệu"]);
    exit;
}

// Lấy thông tin user đang bị chỉnh sửa
$user = $db->getOne("SELECT email FROM khachhang WHERE id_khachhang = ?", [$id]);

if (!$user) {
    echo json_encode(["status" => "error", "msg" => "Người dùng không tồn tại"]);
    exit;
}

// SUPER ADMIN
$superAdmin = "phongtqpk04300@gmail.com";

// ❌ Chặn chỉnh sửa super admin
if ($user["email"] === $superAdmin) {
    echo json_encode(["status" => "error", "msg" => "Không được phép thay đổi quyền của tài khoản SUPER ADMIN"]);
    exit;
}

// ❌ Chặn chỉnh sửa chính mình
if ($user["email"] === $currentUserEmail) {
    echo json_encode(["status" => "error", "msg" => "Bạn không thể thay đổi quyền tài khoản của chính bạn"]);
    exit;
}

// ❌ Nếu không phải Admin → chặn luôn
if ($currentUserRole !== "admin") {
    echo json_encode(["status" => "error", "msg" => "Bạn không có quyền thay đổi quyền người khác"]);
    exit;
}

// Nếu vượt qua hết điều kiện → cập nhật quyền
$db->execute("UPDATE khachhang SET role = ? WHERE id_khachhang = ?", [$role, $id]);

echo json_encode(["status" => "success", "msg" => "Cập nhật thành công"]);
exit;
