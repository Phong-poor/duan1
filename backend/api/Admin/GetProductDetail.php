<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit();

require_once "../../config/database.php";

$db  = new Database();
$pdo = $db->getConnection();

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Thiếu id sản phẩm"
    ]);
    exit;
}

/* Lấy thông tin sản phẩm */
$sqlProduct = "
    SELECT 
        sp.id_sanpham,
        sp.maSP,
        sp.tenSP,
        sp.giaSP,
        sp.hinhAnhGoc,
        sp.id_danhmuc,
        sp.id_thuonghieu,
        dm.tenDM  AS categoryName,
        th.tenTH  AS brandName
    FROM sanpham sp
    LEFT JOIN danhmuc dm      ON sp.id_danhmuc    = dm.id_danhmuc
    LEFT JOIN thuonghieu th   ON sp.id_thuonghieu = th.id_thuonghieu
    WHERE sp.id_sanpham = ?
";
$stmt = $pdo->prepare($sqlProduct);
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode([
        "status"  => "error",
        "message" => "Không tìm thấy sản phẩm"
    ]);
    exit;
}

/* Lấy ảnh phụ */
$sqlExtra = "
    SELECT 
        id_hinhanh AS id,
        link_anh  AS url,
        thu_tu    AS ord
    FROM sanpham_hinhanhphu
    WHERE id_sanpham = ?
    ORDER BY thu_tu ASC, id_hinhanh ASC
";
$stmt = $pdo->prepare($sqlExtra);
$stmt->execute([$id]);
$extraImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Lấy biến thể */
$sqlVariant = "
    SELECT
        bt.id_bienthe         AS id,
        bt.id_mausac          AS color,
        bt.id_size            AS size,
        bt.so_luong            AS quantity,
        bt.anh_bienthe        AS imageUrl
    FROM bienthe bt
    WHERE bt.id_sanpham = ?
";
$stmt = $pdo->prepare($sqlVariant);
$stmt->execute([$id]);
$variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status"       => "success",
    "product"      => $product,
    "extraImages"  => $extraImages,
    "variants"     => $variants
]);
exit;
