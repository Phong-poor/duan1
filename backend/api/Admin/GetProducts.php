<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

/* Lấy danh sách sản phẩm JOIN danh mục + thương hiệu */
$sql = "
SELECT 
    sp.id_sanpham AS id,
    sp.maSP,
    sp.tenSP,
    sp.giaSP,
    sp.hinhAnhGoc,
    dm.ten_danhmuc AS category,
    th.ten_thuonghieu AS brand
FROM sanpham sp
LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
LEFT JOIN thuonghieu th ON sp.id_thuonghieu = th.id_thuonghieu
ORDER BY sp.id_sanpham DESC
";

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["products" => $products]);
?>
