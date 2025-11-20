<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

require_once "../../config/database.php";

try {
    $sql = "SELECT id_thuonghieu AS id, tenTH AS ten FROM thuonghieu";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
} catch(Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}