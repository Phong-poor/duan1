<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

session_start();

// Tắt lỗi để tránh phá JSON trả về
error_reporting(0);
ini_set('display_errors', 0);

require_once "../../config/MailService.php";
require_once "../../config/config.php";
require_once "../../config/db_utils.php";

$db_util = new DB_UTILS();

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode([
        "status" => "error",
        "msg" => "Phương thức không hợp lệ"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Nhận JSON từ client
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["status" => "error", "msg" => "Không nhận được dữ liệu JSON"]);
    exit();
}

/* ==========================
   REGISTER (FORM RÚT GỌN)
========================== */

$tenKH = trim($data['tenKH'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';

// Kiểm tra dữ liệu bắt buộc
if ($tenKH === '' || $email === '' || $password === '' || $confirm_password === '') {
    echo json_encode(["status" => "error", "msg" => "Dữ liệu không được để trống"]);
    exit();
}

// Check email @gmail.com
if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
    echo json_encode(["status" => "error", "msg" => "Email phải kết thúc bằng @gmail.com"]);
    exit();
}

// Password >= 6 ký tự
if (strlen($password) < 6) {
    echo json_encode(["status" => "error", "msg" => "Mật khẩu phải ít nhất 6 ký tự"]);
    exit();
}

// Confirm password
if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "msg" => "Mật khẩu xác nhận không khớp"]);
    exit();
}

// Kiểm tra email tồn tại
$check_email = $db_util->getOne("SELECT * FROM khachhang WHERE email = ?", [$email]);
if ($check_email) {
    echo json_encode(["status" => "error", "msg" => "Email đã tồn tại"]);
    exit();
}

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Thêm user mới
$insert_user = "INSERT INTO khachhang (tenKH, email, password, role)
                VALUES (?, ?, ?, ?)";

$insert_result = $db_util->execute($insert_user, [
    $tenKH,
    $email,
    $password_hash,
    'user'
]);

if ($insert_result) {

    // Lấy ID user mới
    $newUserId = $db_util->getLastInsertId();

    // Lấy thông tin user
    $newUser = $db_util->getOne("SELECT id_khachhang, tenKH, email, role FROM khachhang WHERE id_khachhang = ?", [$newUserId]);

    // Lưu session
    $_SESSION['user'] = $newUser;

    // Gửi mail thông báo đăng ký thành công
    $subject = "Thông báo đăng ký thành công";
    $body = "Cảm ơn bạn đã đăng ký tài khoản với email: $email";
    MailService::send($email, USERNAME_EMAIL, $subject, $body);
    echo json_encode([
        "status" => "success",
        "msg" => "Đăng ký thành công!",
        "user" => $newUser
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "msg" => "Đăng ký thất bại. Vui lòng thử lại."
    ]);
}
