<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit; // xử lý preflight của CORS
}

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

/* LẤY JSON TỪ BODY */
$data = json_decode(file_get_contents("php://input"), true);

$tenDM = $data["tenDM"] ?? "";

/* VALIDATE */
if (trim($tenDM) === "") {
    echo json_encode(["status" => "error", "message" => "Tên danh mục không được để trống"]);
    exit;
}

/* TẠO maDM TỰ ĐỘNG */
$stmt = $pdo->query("SELECT maDM FROM danhmuc ORDER BY id_danhmuc DESC LIMIT 1");
$last = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$last) {
    $maDM = "DM001";
} else {
    $num = (int) filter_var($last["maDM"], FILTER_SANITIZE_NUMBER_INT);
    $num++;
    $maDM = "DM" . str_pad($num, 3, "0", STR_PAD_LEFT);
}

/* INSERT CHUẨN */
$stmt = $pdo->prepare("
    INSERT INTO danhmuc (maDM, tenDM)
    VALUES (?, ?)
");

$stmt->execute([$maDM, $tenDM]);

echo json_encode(["status" => "success", "message" => "Thêm danh mục thành công"]);
exit;
?>