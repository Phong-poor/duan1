<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: DELETE");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$id = $_GET["id"] ?? "";

/* --- 1. XÓA DANH MỤC --- */
$stmt = $pdo->prepare("DELETE FROM danhmuc WHERE id_danhmuc = ?");
$stmt->execute([$id]);

/* --- 2. LẤY LẠI DANH MỤC CÒN LẠI --- */
$list = $pdo->query("SELECT id_danhmuc FROM danhmuc ORDER BY id_danhmuc ASC")
            ->fetchAll(PDO::FETCH_ASSOC);

/* --- 3. ĐÁNH LẠI MÃ DANH MỤC: DM001, DM002... --- */
$count = 1;
foreach ($list as $row) {
    $newCode = "DM" . str_pad($count, 3, "0", STR_PAD_LEFT);
    $up = $pdo->prepare("UPDATE danhmuc SET maDM = ? WHERE id_danhmuc = ?");
    $up->execute([$newCode, $row["id_danhmuc"]]);

    $count++;
}

/* --- RESPONSE --- */
echo json_encode([
    "status" => "success",
    "message" => "Đã xóa và cập nhật lại mã danh mục!"
]);
?>
