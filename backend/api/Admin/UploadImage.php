<?php
header("Access-Control-Allow-Origin: *");
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
   1️⃣ Thư mục lưu vào frontend (giữ nguyên như bạn đang dùng)
===================================================== */
$frontendDir = "C:/xampp/htdocs/DUAN1/frontend/src/assets/";

/* =====================================================
   2️⃣ Thêm thư mục mới lưu vào backend
===================================================== */
$backendDir = "C:/xampp/htdocs/DUAN1/backend/uploads/Baiviet/";

// Tạo thư mục nếu chưa có
if (!is_dir($frontendDir)) mkdir($frontendDir, 0777, true);
if (!is_dir($backendDir)) mkdir($backendDir, 0777, true);

$fileTmp = $_FILES["file"]["tmp_name"];
$fileNameOriginal = $_FILES["file"]["name"];

$ext = strtolower(pathinfo($fileNameOriginal, PATHINFO_EXTENSION));

$allowed = ["jpg", "jpeg", "png", "webp", "gif"];
if (!in_array($ext, $allowed)) {
    echo json_encode(["success" => false, "message" => "Chỉ cho phép file ảnh"]);
    exit;
}

$fileName = time() . "_" . uniqid() . "." . $ext;

$pathFrontend = $frontendDir . $fileName;
$pathBackend = $backendDir . $fileName;

/* =====================================================
   3️⃣ Upload vào frontend
===================================================== */
if (move_uploaded_file($fileTmp, $pathFrontend)) {

    // Sao chép sang backend
    copy($pathFrontend, $pathBackend);

    echo json_encode([
        "success" => true,
        "fileName" => $fileName,
        "url" => "/src/assets/" . $fileName   // Giữ nguyên URL cũ
    ]);

} else {
    echo json_encode(["success" => false, "message" => "Upload thất bại"]);
}
?>
