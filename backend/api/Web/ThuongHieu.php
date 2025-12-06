<?php
require_once '../../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");


$database = new Database();
$db = $database->getConnection();

$query = "SELECT id_thuonghieu, tenTH FROM thuonghieu";
$stmt = $db->prepare($query);
$stmt->execute();
$brands = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data' => $brands
], JSON_UNESCAPED_UNICODE);
?>
