<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$id = $_GET["id"] ?? "";

$stmt = $pdo->prepare("DELETE FROM thuonghieu WHERE id_thuonghieu = ?");
$stmt->execute([$id]);

/* LẤY LẠI LIST */
$list = $pdo->query("SELECT id_thuonghieu FROM thuonghieu ORDER BY id_thuonghieu ASC")
            ->fetchAll(PDO::FETCH_ASSOC);

/* ĐÁNH LẠI maTH */
$count = 1;
foreach ($list as $row) {
    $newCode = "TH" . str_pad($count, 3, "0", STR_PAD_LEFT);
    $up = $pdo->prepare("UPDATE thuonghieu SET maTH = ? WHERE id_thuonghieu = ?");
    $up->execute([$newCode, $row["id_thuonghieu"]]);
    $count++;
}

echo json_encode(["status" => "success", "message" => "Xóa và cập nhật lại mã thương hiệu"]);
exit;
?>
