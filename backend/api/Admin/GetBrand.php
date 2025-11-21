<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$sql = "SELECT id_thuonghieu, maTH, tenTH FROM thuonghieu ORDER BY id_thuonghieu DESC";
$stmt = $pdo->query($sql);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;
?>
