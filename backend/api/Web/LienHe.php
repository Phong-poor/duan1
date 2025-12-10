<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Xử lý preflight OPTIONS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Include đúng file cấu hình PDO
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    echo json_encode(["status" => "error", "message" => "Không thể kết nối database"]);
    exit();
}

// Đọc JSON từ Vue gửi lên
$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data["id_khachhang"]) ||
    empty($data["ten_khachhang"]) ||
    empty($data["email"]) ||
    empty($data["chu_de"]) ||
    empty($data["noi_dung"])
) {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu"]);
    exit();
}

$id_kh    = $data["id_khachhang"];
$ten      = $data["ten_khachhang"];
$email    = $data["email"];
$chu_de   = $data["chu_de"];
$noidung  = $data["noi_dung"];

try {
    $sql = "INSERT INTO lienhe (id_khachhang, ten_khachhang, email, chu_de, noi_dung)
            VALUES (:idkh, :ten, :email, :chude, :noidung)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":idkh", $id_kh);
    $stmt->bindParam(":ten", $ten);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":chude", $chu_de);
    $stmt->bindParam(":noidung", $noidung);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Không thể thêm dữ liệu"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
