<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit();

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input) {
    echo json_encode([
        "status" => "error",
        "message" => "Không nhận được JSON"
    ]);
    exit;
}

$tenSP         = $input["tenSP"] ?? "";
$id_danhmuc    = $input["id_danhmuc"] ?? null;
$id_thuonghieu = $input["id_thuonghieu"] ?? null;
$giaSP         = $input["giaSP"] ?? 0;
$imageUrl      = $input["imageUrl"] ?? null;
$extraImages   = $input["extraImages"] ?? [];
$variants      = $input["variants"] ?? [];

if ($tenSP === "" || !$id_danhmuc || !$id_thuonghieu || $giaSP <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu bắt buộc"
    ]);
    exit;
}

function generateMaSP($pdo)
{
    $stmt = $pdo->query("SELECT maSP FROM sanpham ORDER BY id_sanpham DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$last) return "SP001";

    $num = preg_replace('/[^0-9]/', '', $last["maSP"]);
    return "SP" . str_pad($num + 1, 3, "0", STR_PAD_LEFT);
}

$maSP = generateMaSP($pdo);

$stmt = $pdo->prepare("
    INSERT INTO sanpham (maSP, tenSP, giaSP, hinhAnhGoc, id_danhmuc, id_thuonghieu)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $maSP,
    $tenSP,
    $giaSP,
    $imageUrl,
    $id_danhmuc,
    $id_thuonghieu
]);

$idSP = $pdo->lastInsertId();

/* ẢNH PHỤ */
foreach ($extraImages as $i => $url) {
    if (!$url) continue;

    $stmt = $pdo->prepare("
        INSERT INTO sanpham_hinhanhphu (id_sanpham, link_anh, thu_tu)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$idSP, $url, $i + 1]);
}

/* BIẾN THỂ */
foreach ($variants as $v) {
    $color = $v["color"] ?? null;
    $size  = $v["size"] ?? null;
    $qty   = $v["quantity"] ?? 0;
    $img   = $v["imageUrl"] ?? null;

    if (!$color || !$size || $qty <= 0) continue;

    $stmt = $pdo->prepare("
        INSERT INTO bienthe (id_sanpham, id_mausac, id_size, so_luong, anh_bienthe)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$idSP, $color, $size, $qty, $img]);
}

echo json_encode([
    "status" => "success",
    "message" => "Thêm sản phẩm thành công!",
    "product_id" => $idSP
]);
