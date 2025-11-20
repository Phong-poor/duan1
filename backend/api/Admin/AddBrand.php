<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$response = ["status" => "error", "message" => ""];

$tenTH = $_POST["tenTH"] ?? "";
$maTH = $_POST["maTH"] ?? "";

if ($tenTH === "" || $maTH === "") {
    $response["message"] = "Thiếu dữ liệu!";
    echo json_encode($response);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO thuonghieu (maTH, tenTH) VALUES (?, ?)");
$stmt->execute([$maTH, $tenTH]);

$response["status"] = "success";
$response["message"] = "Thêm thương hiệu thành công!";
$response["data"] = [
    "id_thuonghieu" => $pdo->lastInsertId(),
    "maTH" => $maTH,
    "tenTH" => $tenTH
];

echo json_encode($response);
?>
