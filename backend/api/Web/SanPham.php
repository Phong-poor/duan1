<?php
require_once '../../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");


$database = new Database();
$db = $database->getConnection();

$id_danhmuc = isset($_GET['id_danhmuc']) ? $_GET['id_danhmuc'] : null;
$id_thuonghieu = isset($_GET['id_thuonghieu']) ? $_GET['id_thuonghieu'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;
$size = isset($_GET['size']) ? $_GET['size'] : null;
$giamgia = isset($_GET['giamgia']) ? $_GET['giamgia'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'moi_nhat';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$query = "SELECT 
            sp.id_sanpham,
            sp.maSP,
            sp.hinhAnhgoc,
            sp.tenSP,
            sp.giaSP,
            sp.giamgiaSP,
            sp.giamgia_start,
            sp.giamgia_end,
            dm.tenDM,
            dm.id_danhmuc,
            th.tenTH,
            th.id_thuonghieu,
            CASE 
                WHEN sp.giamgiaSP IS NOT NULL 
                AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
                THEN sp.giamgiaSP
                ELSE sp.giaSP
            END as giaSauGiam,
            CASE 
                WHEN sp.giamgiaSP IS NOT NULL 
                AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end 
                THEN 1
                ELSE 0
            END as coGiamGia
        FROM sanpham sp
        LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
        LEFT JOIN thuonghieu th ON sp.id_thuonghieu = th.id_thuonghieu";

if ($size) {
    $query .= " INNER JOIN bienthe bt ON sp.id_sanpham = bt.id_sanpham
                INNER JOIN bienthesize sz ON bt.id_size = sz.id_size";
}

$query .= " WHERE 1=1";

$params = [];

if ($id_danhmuc) {
    $query .= " AND sp.id_danhmuc = :id_danhmuc";
    $params[':id_danhmuc'] = $id_danhmuc;
}

if ($id_thuonghieu) {
    $query .= " AND sp.id_thuonghieu = :id_thuonghieu";
    $params[':id_thuonghieu'] = $id_thuonghieu;
}

if ($search) {
    $query .= " AND (sp.tenSP LIKE :search OR sp.maSP LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($size) {
    $query .= " AND sz.size = :size";
    $params[':size'] = $size;
}

if ($giamgia == '1') {
    $query .= " AND sp.giamgiaSP IS NOT NULL 
                AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end";
}

if ($size) {
    $query .= " GROUP BY sp.id_sanpham";
}

switch ($sort) {
    case 'gia_tang':
        $query .= " ORDER BY giaSauGiam ASC";
        break;
    case 'gia_giam':
        $query .= " ORDER BY giaSauGiam DESC";
        break;
    case 'moi_nhat':
    default:
        $query .= " ORDER BY sp.id_sanpham DESC";
        break;
}

$query .= " LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$products = $stmt->fetchAll();

$countQuery = "SELECT COUNT(DISTINCT sp.id_sanpham) as total FROM sanpham sp 
               LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
               LEFT JOIN thuonghieu th ON sp.id_thuonghieu = th.id_thuonghieu";

if ($size) {
    $countQuery .= " INNER JOIN bienthe bt ON sp.id_sanpham = bt.id_sanpham
                    INNER JOIN bienthesize sz ON bt.id_size = sz.id_size";
}

$countQuery .= " WHERE 1=1";

if ($id_danhmuc) {
    $countQuery .= " AND sp.id_danhmuc = :id_danhmuc";
}
if ($id_thuonghieu) {
    $countQuery .= " AND sp.id_thuonghieu = :id_thuonghieu";
}
if ($search) {
    $countQuery .= " AND (sp.tenSP LIKE :search OR sp.maSP LIKE :search)";
}
if ($size) {
    $countQuery .= " AND sz.size = :size";
}
if ($giamgia == '1') {
    $countQuery .= " AND sp.giamgiaSP IS NOT NULL 
                    AND NOW() BETWEEN sp.giamgia_start AND sp.giamgia_end";
}

$countStmt = $db->prepare($countQuery);
foreach ($params as $key => $value) {
    if ($key !== ':limit' && $key !== ':offset') {
        $countStmt->bindValue($key, $value);
    }
}
$countStmt->execute();
$total = $countStmt->fetch()['total'];

echo json_encode([
    'success' => true,
    'data' => $products,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset
], JSON_UNESCAPED_UNICODE);
?>
