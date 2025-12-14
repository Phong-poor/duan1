
<?php
$currentDir = getcwd();
chdir('../../config');
require_once 'database.php';
chdir($currentDir);

error_reporting(0);
ini_set('display_errors', '0');

header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

global $pdo;
if (isset($pdo)) {
    $db = $pdo;
} else {
    $database = new Database();
    $db = $database->getConnection();
}

if ($db === null) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối cơ sở dữ liệu']);
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
    exit;
}

$query = "SELECT 
            sp.id_sanpham, sp.maSP, sp.hinhAnhgoc, sp.tenSP, sp.giaSP, sp.giamgiaSP,
            sp.giamgia_start, sp.giamgia_end, sp.id_danhmuc, sp.id_thuonghieu,
            dm.tenDM, th.tenTH,
            CASE 
                WHEN sp.giamgiaSP IS NOT NULL AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
                THEN sp.giamgiaSP ELSE sp.giaSP
            END as giaSauGiam,
            CASE 
                WHEN sp.giamgiaSP IS NOT NULL AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
                THEN 1 ELSE 0
            END as coGiamGia
        FROM sanpham sp
        LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
        LEFT JOIN thuonghieu th ON sp.id_thuonghieu = th.id_thuonghieu
        WHERE sp.id_sanpham = :id
        LIMIT 1";

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    $imgQuery = "SELECT link_anh FROM sanpham_hinhanhphu WHERE id_sanpham = :id ORDER BY thu_tu ASC";
    $imgStmt = $db->prepare($imgQuery);
    $imgStmt->bindParam(':id', $id);
    $imgStmt->execute();
    $product['gallery'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

    $variantQuery = "SELECT bt.id_bienthe, bt.so_luong, ms.id_mausac, ms.mausac, sz.id_size, sz.size 
                    FROM bienthe bt 
                    INNER JOIN bienthemausac ms ON bt.id_mausac = ms.id_mausac 
                    INNER JOIN bienthesize sz ON bt.id_size = sz.id_size 
                    WHERE bt.id_sanpham = :id AND bt.so_luong > 0";
    
    $variantStmt = $db->prepare($variantQuery);
    $variantStmt->bindParam(':id', $id);
    $variantStmt->execute();
    $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

    $relatedQuery = "SELECT sp.id_sanpham, sp.maSP, sp.hinhAnhgoc, sp.tenSP, sp.giaSP, sp.giamgiaSP,
                        CASE 
                            WHEN sp.giamgiaSP IS NOT NULL AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
                            THEN sp.giamgiaSP ELSE sp.giaSP
                        END as giaSauGiam,
                        CASE 
                            WHEN sp.giamgiaSP IS NOT NULL AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
                            THEN 1 ELSE 0
                        END as coGiamGia
                    FROM sanpham sp
                    WHERE sp.id_thuonghieu = :id_thuonghieu AND sp.id_sanpham != :id
                    ORDER BY RAND()
                    LIMIT 8";
    
    $relatedStmt = $db->prepare($relatedQuery);
    $relatedStmt->bindParam(':id_thuonghieu', $product['id_thuonghieu']);
    $relatedStmt->bindParam(':id', $id);
    $relatedStmt->execute();
    $product['relatedProducts'] = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $product], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
}
?>
