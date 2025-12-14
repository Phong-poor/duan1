<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$tenTH = $data["tenTH"] ?? "";

/* VALIDATE */
if (trim($tenTH) === "") {
    echo json_encode(["status" => "error", "message" => "Tên thương hiệu không được để trống"]);
    exit;
}

/* TẠO maTH TỰ ĐỘNG */
$stmt = $pdo->query("SELECT maTH FROM thuonghieu ORDER BY id_thuonghieu DESC LIMIT 1");
$last = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$last) {
    $maTH = "TH001";
} else {
    $num = (int) filter_var($last["maTH"], FILTER_SANITIZE_NUMBER_INT);
    $num++;
    $maTH = "TH" . str_pad($num, 3, "0", STR_PAD_LEFT);
}

/* INSERT */
$stmt = $pdo->prepare("INSERT INTO thuonghieu (maTH, tenTH) VALUES (?, ?)");
$stmt->execute([$maTH, $tenTH]);

echo json_encode(["status" => "success", "message" => "Thêm thương hiệu thành công"]);
exit;
?>
