<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../../config/db_utils.php";
require_once "../../config/utils.php";
require_once "../../config/MailService.php";

$db = new DB_UTILS();

// Lấy action từ client
$action = $_GET['action'] ?? null;

if (!$action) {
    echo json_encode(["status" => "error", "msg" => "Thiếu action"]);
    exit;
}

// ============================
// 📌 1. GỬI OTP (sendOTP)
// ============================
if ($action === "sendOTP") {

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";

    if (!$email) {
        echo json_encode(["status" => "error", "msg" => "Email không được trống"]);
        exit;
    }

    // Kiểm tra email tồn tại
    $user = $db->getOne("SELECT * FROM khachhang WHERE email = ?", [$email]);

    if (!$user) {
        echo json_encode(["status" => "error", "msg" => "Email không tồn tại"]);
        exit;
    }

    // Tạo OTP
    $otp = getRandomOTP(6);
    $expire = date("Y-m-d H:i:s", strtotime("+90 seconds"));
    $wait   = date("Y-m-d H:i:s", strtotime("+90 seconds"));

    // Lưu vào DB
    $db->execute(
        "UPDATE khachhang SET otp = ?, otp_hethan = ?, otp_cho = ? WHERE email = ?",
        [$otp, $expire, $wait, $email]
    );

    // Gửi mail
    $subject = "Mã OTP khôi phục mật khẩu";
    $body = "Mã OTP của bạn là: $otp. Có hiệu lực trong 90 giây.";

    MailService::send($email, $subject, $body);

    echo json_encode(["status" => "success", "msg" => "OTP đã được gửi"]);
    exit;
}

// ============================
// 📌 2. XÁC THỰC OTP (verifyOTP)
// ============================
if ($action === "verifyOTP") {

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";
    $otp = $data["otp"] ?? "";

    if (!$email || !$otp) {
        echo json_encode(["status" => "error", "msg" => "Thiếu email hoặc OTP"]);
        exit;
    }

    $user = $db->getOne("SELECT * FROM khachhang WHERE email = ?", [$email]);

    if (!$user) {
        echo json_encode(["status" => "error", "msg" => "Email không tồn tại"]);
        exit;
    }

    if ($user["otp"] != $otp) {
        echo json_encode(["status" => "error", "msg" => "OTP không đúng"]);
        exit;
    }

    if (strtotime($user["otp_hethan"]) < time()) {
        echo json_encode(["status" => "error", "msg" => "OTP đã hết hạn"]);
        exit;
    }

    echo json_encode(["status" => "success", "msg" => "OTP hợp lệ"]);
    exit;
}

// ============================
// 📌 3. GỬI LẠI OTP (resendOTP)
// ============================
if ($action === "resendOTP") {

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";

    if (!$email) {
        echo json_encode(["status" => "error", "msg" => "Thiếu email"]);
        exit;
    }

    $user = $db->getOne("SELECT * FROM khachhang WHERE email = ?", [$email]);

    if (!$user) {
        echo json_encode(["status" => "error", "msg" => "Email không tồn tại"]);
        exit;
    }

    $now = time();
    $waitTime = strtotime($user["otp_cho"]);

    if ($now < $waitTime) {
        $delay = $waitTime - $now;
        echo json_encode(["status" => "error", "msg" => "Vui lòng đợi $delay giây để gửi lại OTP"]);
        exit;
    }

    // Tạo OTP mới
    $otp = getRandomOTP(6);
    $expire = date("Y-m-d H:i:s", strtotime("+90 seconds"));
    $wait   = date("Y-m-d H:i:s", strtotime("+90 seconds"));

    $db->execute(
        "UPDATE khachhang SET otp = ?, otp_hethan = ?, otp_cho = ? WHERE email = ?",
        [$otp, $expire, $wait, $email]
    );

    MailService::send($email, "Mã OTP mới", "OTP của bạn: $otp");

    echo json_encode(["status" => "success", "msg" => "OTP mới đã được gửi"]);
    exit;
}

// ============================
// 📌 4. RESET PASSWORD (resetPassword)
// ============================
if ($action === "resetPassword") {

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";
    $pass  = $data["password"] ?? "";
    $confirm = $data["confirm_password"] ?? "";

    if (!$email || !$pass || !$confirm) {
        echo json_encode(["status" => "error", "msg" => "Thiếu dữ liệu"]);
        exit;
    }
    if ($pass !== $confirm) {
        echo json_encode(["status" => "error", "msg" => "Mật khẩu không trùng khớp"]);
        exit;
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    $db->execute(
        "UPDATE khachhang SET password = ?, otp = NULL, otp_hethan = NULL WHERE email = ?",
        [$hashed, $email]
    );

    echo json_encode(["status" => "success", "msg" => "Đặt lại mật khẩu thành công"]);
    exit;
}

// Nếu action sai
echo json_encode(["status" => "error", "msg" => "Action không hợp lệ"]);
exit;
?>
