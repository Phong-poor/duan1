<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

if (!isset($_FILES["file"])) {
    echo json_encode(["success" => false, "message" => "Không có file upload"]);
    exit;
}

/* =====================================================
   2️⃣ Thêm thư mục mới lưu vào backend
===================================================== */
$backendDir = $_SERVER["DOCUMENT_ROOT"] . "/backend/uploads/Baiviet/";

// Tạo thư mục nếu chưa có
if (!is_dir($backendDir)) mkdir($backendDir, 0777, true);

$fileTmp = $_FILES["file"]["tmp_name"];
$ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

$allowed = ["jpg","jpeg","png","webp","gif"];
if (!in_array($ext, $allowed)) {
    echo json_encode(["success" => false, "message" => "File không hợp lệ"]);
    exit;
}

$hash = sha1_file($fileTmp);
$fileName = $hash . "." . $ext;
$path = $backendDir . $fileName;

if (!file_exists($path)) {
    move_uploaded_file($fileTmp, $path);
}

echo json_encode([
    "success" => true,
    "fileName" => $fileName,
    "url" => "/backend/uploads/Baiviet/" . $fileName
]);
?>
