<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "../../config/config.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->getConnection(); 

if (!$conn) {
    echo json_encode(["status" => "error", "msg" => "Không thể kết nối database"]);
    exit;
}

/* -----------------------------------
   NHẬN TOKEN TỪ FRONTEND
------------------------------------ */
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["credential"])) {
    echo json_encode(["status" => "error", "msg" => "Thiếu GOOGLE TOKEN"]);
    exit;
}

$token = $data["credential"];

/* -----------------------------------
   XÁC THỰC GOOGLE TOKEN
------------------------------------ */
$verifyURL = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

$googleUser = json_decode($response, true);

if (!$googleUser || !isset($googleUser["email"])) {
    echo json_encode(["status" => "error", "msg" => "Token Google không hợp lệ"]);
    exit;
}

$email = $googleUser["email"];
$name = $googleUser["name"] ?? "Người dùng Google";

/* -----------------------------------
   KIỂM TRA USER TỒN TẠI CHƯA
------------------------------------ */
$query = $conn->prepare("SELECT * FROM khachhang WHERE email = ?");
$query->execute([$email]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    // Tạo password giả cho Google account
    $fakePassword = password_hash("google_" . rand(1000, 9999), PASSWORD_DEFAULT);

    // Mặc định role = user
    $sql = "INSERT INTO khachhang (tenKH, email, password, role)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $email, $fakePassword, "user"]);

    $userId = $conn->lastInsertId();

    $newUser = [
        "id_khachhang" => $userId,
        "tenKH" => $name,
        "email" => $email,
        "role" => "user"
    ];

} else {
    // Nếu đã có tài khoản → lấy ra
    $newUser = [
        "id_khachhang" => $user["id_khachhang"],
        "tenKH" => $user["tenKH"],
        "email" => $user["email"],
        "role" => $user["role"]
    ];
}

/* -----------------------------------
   TRẢ KẾT QUẢ
------------------------------------ */
echo json_encode([
    "status" => "success",
    "msg" => "Đăng nhập Google thành công",
    "user" => $newUser
], JSON_UNESCAPED_UNICODE);

exit;
