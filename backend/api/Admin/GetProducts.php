<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

/* 
---------------------------------------------
 TỰ ĐỘNG RESET GIẢM GIÁ KHI HẾT THỜI GIAN
---------------------------------------------
*/
$pdo->query("
    UPDATE sanpham
    SET 
        giamgiaSP = 0,
        giamgia_start = NULL,
        giamgia_end = NULL
    WHERE giamgia_end IS NOT NULL
      AND NOW() > giamgia_end
");

/* Lấy danh sách sản phẩm */
$sql = "
SELECT 
    sp.id_sanpham AS id,
    sp.maSP,
    sp.tenSP,
    sp.giaSP,
    sp.giamgiaSP,
    sp.giamgia_start,
    sp.giamgia_end,
    sp.hinhAnhGoc,
    dm.tenDM AS category,
    th.tenTH AS brand
FROM sanpham sp
LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
LEFT JOIN thuonghieu th ON sp.id_thuonghieu = th.id_thuonghieu
ORDER BY sp.id_sanpham DESC
";

$stmt = $pdo->query($sql);

echo json_encode(["products" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
exit;
