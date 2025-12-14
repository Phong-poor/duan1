<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Content-Type: application/json; charset=UTF-8");

// Include config PDO
require_once '../../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    echo json_encode(["status" => "error", "message" => "Không thể kết nối database"]);
    exit();
}

try {
    $sql = "SELECT 
                id_lienhe,
                id_khachhang,
                ten_khachhang,
                email,
                chu_de,
                noi_dung,
                tra_loi,
                trang_thai,
                ngay_tao,
                ngay_phan_hoi
            FROM lienhe
            ORDER BY id_lienhe DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
