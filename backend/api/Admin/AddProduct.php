<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
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
$imageUrl = saveImageFromUrlToPublic($input["imageUrl"] ?? null);
$extraImages   = $input["extraImages"] ?? [];
$variants      = $input["variants"] ?? [];

if ($tenSP === "" || !$id_danhmuc || !$id_thuonghieu || $giaSP <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu dữ liệu bắt buộc"
    ]);
    exit;
}
function saveImageFromUrlToPublic($imageUrl)
{
    if (!$imageUrl) return null;

    // Nếu KHÔNG phải link mạng → giữ nguyên (ảnh đã có trong server)
    if (!str_starts_with($imageUrl, "http")) {
        return ltrim($imageUrl, "/");
    }

    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) return null;

    // 🔑 HASH để check trùng
    $hash = sha1($imageData);

    $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
    $ext = strtolower($ext ?: "jpg");

    $saveDir = rtrim($_SERVER['DOCUMENT_ROOT'], "/") . "/backend/uploads/Product/";
    if (!is_dir($saveDir)) mkdir($saveDir, 0777, true);

    // 🔍 CHECK TRÙNG
    foreach (["jpg","jpeg","png","webp","gif",$ext] as $e) {
        $existFile = $saveDir . $hash . "." . $e;
        if (file_exists($existFile) && filesize($existFile) > 0) {
            // Đã tồn tại → dùng lại
            return "backend/uploads/Product/" . $hash . "." . $e;
        }
    }

    // ❌ Chưa có → lưu mới
    $filename = $hash . "." . $ext;
    file_put_contents($saveDir . $filename, $imageData);

    return "backend/uploads/Product/" . $filename;
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

    $url = saveImageFromUrlToPublic($url);

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
    $img = saveImageFromUrlToPublic($v["imageUrl"] ?? null);

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
