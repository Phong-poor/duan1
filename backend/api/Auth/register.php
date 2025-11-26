<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Nếu là OPTIONS request thì dừng ở đây
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

session_start();

// Tắt lỗi hiển thị trước khi gửi JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once "../../config/MailService.php";
require_once "../../config/config.php";
require_once "../../config/db_utils.php";

$db_util = new DB_UTILS();

// OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo json_encode(["status" => "error", "msg" => "Phương thức không hợp lệ"]);
    exit();
}

// Nhận dữ liệu JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["status" => "error", "msg" => "Không nhận được dữ liệu JSON"]);
    exit();
}

/* ==========================
   REGISTER
========================== */
$tenKH = trim($data['tenKH'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$gender = trim($data['gender'] ?? '');
$password = $data['password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';
$ngaysinh = trim($data['ngaysinh'] ?? null);

// Kiểm tra dữ liệu bắt buộc
if ($tenKH === '' || $email === '' || $phone === '' || $gender === '' || $password === '' || $confirm_password === '') {
    echo json_encode(["status" => "error", "msg" => "Dữ liệu không được để trống"]);
    exit();
}

// Email @gmail.com
if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
    echo json_encode(["status" => "error", "msg" => "Email phải kết thúc bằng @gmail.com"]);
    exit();
}

// Phone 10 số
if (!preg_match("/^\d{10}$/", $phone)) {
    echo json_encode(["status" => "error", "msg" => "Số điện thoại phải đủ 10 số"]);
    exit();
}

// Password >=6 ký tự
if (strlen($password) < 6) {
    echo json_encode(["status" => "error", "msg" => "Mật khẩu phải ít nhất 6 ký tự"]);
    exit();
}

// Confirm password
if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "msg" => "Mật khẩu xác nhận không khớp"]);
    exit();
}

// Check email trùng
$check_email = $db_util->getOne("SELECT * FROM khachhang WHERE email = ?", [$email]);
if ($check_email) {
    echo json_encode(["status" => "error", "msg" => "Email đã tồn tại"]);
    exit();
}

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Chuyển ngày sinh sang YYYY-MM-DD
$birth_mysql = null;
if ($ngaysinh) {
    $d = DateTime::createFromFormat('Y-m-d', $ngaysinh);
    if ($d) $birth_mysql = $d->format('Y-m-d');
}

// Thêm user mới
$insert_user = "INSERT INTO khachhang (tenKH, email, sodienthoai, gioitinh, password, role, ngaysinh)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
$insert_result = $db_util->execute($insert_user, [
    $tenKH,
    $email,
    $phone,
    $gender,
    $password_hash,
    'user',
    $birth_mysql
]);

if ($insert_result) {
    // Lấy ID vừa insert
    $newUserId = $db_util->getLastInsertId(); // DB_UTILS phải có hàm lastInsertId()
    if (!$newUserId) {
        echo json_encode([
            "status" => "error",
            "msg" => "Đăng ký thất bại. Không thể lấy ID user mới."
        ]);
        exit();
    }

    // Lấy thông tin user theo ID
    $newUser = $db_util->getOne("SELECT id, tenKH, email, role FROM khachhang WHERE id = ?", [$newUserId]);
    if (!$newUser) {
        echo json_encode([
            "status" => "error",
            "msg" => "Đăng ký thất bại. Không thể lấy thông tin user."
        ]);
        exit();
    }

    // Lưu session
    $_SESSION['user'] = $newUser;

    // Gửi mail
    $subject = "Thông báo đăng ký thành công";
    $body = "Cảm ơn bạn đã đăng ký tài khoản với email: $email";
    MailService::send($email, USERNAME_EMAIL, $subject, $body);

    echo json_encode([
        "status" => "success",
        "msg" => "Đăng ký thành công!",
        "user" => $_SESSION['user']
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "msg" => "Đăng ký thất bại. Vui lòng thử lại."
    ]);
}
