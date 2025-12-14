<?php
require_once "../../config/database.php";

/* =============================
   CORS
============================= */
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

$db = (new Database())->getConnection();

/* =============================
   XÁC ĐỊNH METHOD (FIX PUT)
============================= */
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents("php://input"), true);

if (isset($body['_method'])) {
    $method = strtoupper($body['_method']);
}

/* =============================
      GET USER BY ID
============================= */
if ($method === 'GET') {

    if (empty($_GET['id'])) {
        echo json_encode(["status" => false, "message" => "Missing user id"]);
        exit();
    }

    $id = intval($_GET['id']);

    $stmt = $db->prepare("
        SELECT id_khachhang, tenKH, email, sodienthoai, ngaysinh, gioitinh, role
        FROM khachhang
        WHERE id_khachhang = ?
    ");
    $stmt->execute([$id]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => true,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/* =============================
      UPDATE USER (POST + PUT)
============================= */
if ($method === 'PUT') {

    if (empty($_GET['id'])) {
        echo json_encode(["status" => false, "message" => "Missing user id"]);
        exit();
    }

    if (!$body) {
        echo json_encode(["status" => false, "message" => "Invalid JSON"]);
        exit();
    }

    $id = intval($_GET['id']);

    $stmt = $db->prepare("
        UPDATE khachhang 
        SET tenKH = ?, email = ?, sodienthoai = ?, ngaysinh = ?, gioitinh = ?
        WHERE id_khachhang = ?
    ");

    $success = $stmt->execute([
        $body["tenKH"] ?? "",
        $body["email"] ?? "",
        $body["sodienthoai"] ?? null,
        $body["ngaysinh"] ?? null,
        $body["gioitinh"] ?? null,
        $id
    ]);

    echo json_encode([
        "status" => $success,
        "message" => $success ? "Cập nhật thành công" : "Cập nhật thất bại"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/* =============================
      METHOD KHÔNG HỖ TRỢ
============================= */
echo json_encode([
    "status" => false,
    "message" => "Method not allowed"
]);
