<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit();

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id_sanpham"] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu id_sanpham"
    ]);
    exit;
}

/* Xóa ảnh phụ */
$pdo->prepare("DELETE FROM sanpham_hinhanhphu WHERE id_sanpham = ?")
    ->execute([$id]);

/* Xóa biến thể */
$pdo->prepare("DELETE FROM bienthe WHERE id_sanpham = ?")
    ->execute([$id]);

/* Xóa sản phẩm */
$pdo->prepare("DELETE FROM sanpham WHERE id_sanpham = ?")
    ->execute([$id]);


/* ===========================
   CẬP NHẬT LẠI MÃ SẢN PHẨM
   =========================== */
$stmt = $pdo->query("
    SELECT id_sanpham 
    FROM sanpham 
    ORDER BY id_sanpham ASC
");

$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counter = 1;

foreach ($all as $row) {
    $newMa = "SP" . str_pad($counter, 3, "0", STR_PAD_LEFT);

    $pdo->prepare("UPDATE sanpham SET maSP = ? WHERE id_sanpham = ?")
        ->execute([$newMa, $row["id_sanpham"]]);

    $counter++;
}

echo json_encode([
    "status" => "success",
    "message" => "Xóa sản phẩm và cập nhật mã SP thành công!"
]);
exit;
?>
