<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$sql = "SELECT id_mausac, mausac FROM bienthemausac ORDER BY id_mausac DESC";
$stmt = $pdo->query($sql);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;
?>
