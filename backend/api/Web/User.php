<?php
require_once "../../config/database.php";

// CORS
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

$db = (new Database())->getConnection();

/* =============================
      GET USER BY ID
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (empty($_GET['id'])) {
        echo json_encode(["status" => false, "message" => "Missing user id"]);
        exit();
    }

    $id = intval($_GET['id']);

    $stmt = $db->prepare("SELECT * FROM khachhang WHERE id_khachhang = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}


/* =============================
      UPDATE USER (PUT)
============================= */
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (empty($_GET['id'])) {
        echo json_encode(["status" => false, "message" => "Missing user id"]);
        exit();
    }

    $id = intval($_GET['id']);
    $body = json_decode(file_get_contents("php://input"), true);

    if (!$body) {
        echo json_encode(["status" => false, "message" => "Invalid JSON"]);
        exit();
    }

    $stmt = $db->prepare("
        UPDATE khachhang 
        SET tenKH = ?, email = ?, sodienthoai = ?, ngaysinh = ?, gioitinh = ?
        WHERE id_khachhang = ?
    ");

    $success = $stmt->execute([
        $body["tenKH"],
        $body["email"],
        $body["sodienthoai"],
        $body["ngaysinh"],
        $body["gioitinh"],
        $id
    ]);

    echo json_encode([
        "status" => $success,
        "message" => $success ? "Cập nhật thành công" : "Cập nhật thất bại"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

echo json_encode(["status" => false, "message" => "Method not allowed"]);
