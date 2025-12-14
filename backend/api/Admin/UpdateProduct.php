<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit();

require_once "../../config/database.php";

$db  = new Database();
$pdo = $db->getConnection();
/* ================== IMAGE HELPERS ================== */
function normalizeProductPath($path) {
    if (!$path) return $path;

    if (str_starts_with($path, "http")) return $path;

    $path = str_replace("\\", "/", $path);

    if (str_contains($path, "uploads/Product/")) {
        $path = preg_replace('#^/?uploads/Product/#', 'backend/uploads/Product/', $path);
    }

    return ltrim($path, "/");
}

function saveImageFromUrlToPublicDedup($imageUrl) {
    if (!$imageUrl) return null;

    // nếu không phải link mạng → chỉ normalize path
    if (!str_starts_with($imageUrl, "http")) {
        return normalizeProductPath($imageUrl);
    }

    $data = @file_get_contents($imageUrl);
    if ($data === false) return null;

    $hash = sha1($data);

    $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
    $ext = strtolower($ext ?: "jpg");

    $dir = rtrim($_SERVER["DOCUMENT_ROOT"], "/") . "/backend/uploads/Product/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // check trùng ảnh
    foreach (["jpg","jpeg","png","webp","gif",$ext] as $e) {
        $exist = $dir . $hash . "." . $e;
        if (file_exists($exist) && filesize($exist) > 0) {
            return "backend/uploads/Product/" . $hash . "." . $e;
        }
    }

    $filename = $hash . "." . $ext;
    file_put_contents($dir . $filename, $data);

    return "backend/uploads/Product/" . $filename;
}
/* Nhận JSON */
$raw   = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!$input || !is_array($input)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Không nhận được JSON hợp lệ"
    ]);
    exit;
}

$idSP          = $input["id_sanpham"]   ?? null;
$tenSP         = trim($input["tenSP"] ?? "");
$id_danhmuc    = $input["id_danhmuc"]   ?? null;
$id_thuonghieu = $input["id_thuonghieu"] ?? null;
$giaSP         = $input["giaSP"]        ?? 0;

$imageUrl = saveImageFromUrlToPublicDedup($input["imageUrl"] ?? null); // chỉ ghi lại giá trị cũ (không bắt buộc sửa)
$extraImages   = $input["extraImages"]  ?? [];
$variants      = $input["variants"]     ?? [];

/* Validate cơ bản */
if (!$idSP || $tenSP === "" || !$id_danhmuc || !$id_thuonghieu || $giaSP <= 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "Thiếu dữ liệu bắt buộc"
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    /* UPDATE bảng sanpham */
    $stmt = $pdo->prepare("
        UPDATE sanpham
        SET tenSP = ?, giaSP = ?, hinhAnhGoc = ?, id_danhmuc = ?, id_thuonghieu = ?
        WHERE id_sanpham = ?
    ");
    $stmt->execute([
        $tenSP,
        $giaSP,
        $imageUrl,
        $id_danhmuc,
        $id_thuonghieu,
        $idSP
    ]);

    /* XÓA toàn bộ ảnh phụ cũ và insert lại */
    $stmt = $pdo->prepare("DELETE FROM sanpham_hinhanhphu WHERE id_sanpham = ?");
    $stmt->execute([$idSP]);

    if (is_array($extraImages)) {
        foreach ($extraImages as $idx => $url) {
            if (!$url) continue;
            $url = saveImageFromUrlToPublicDedup($url);
            $stmt = $pdo->prepare("
                INSERT INTO sanpham_hinhanhphu (id_sanpham, link_anh, thu_tu)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$idSP, $url, $idx + 1]);
        }
    }

    /* XÓA toàn bộ biến thể cũ và insert lại */
    $stmt = $pdo->prepare("DELETE FROM bienthe WHERE id_sanpham = ?");
    $stmt->execute([$idSP]);

    if (is_array($variants)) {
        foreach ($variants as $v) {
            $color = $v["color"]    ?? null;
            $size  = $v["size"]     ?? null;
            $qty   = $v["quantity"] ?? 0;
            $imgBT = saveImageFromUrlToPublicDedup($v["imageUrl"] ?? null);

            if (!$color || !$size || $qty <= 0) continue;

            $stmt = $pdo->prepare("
                INSERT INTO bienthe (id_sanpham, id_mausac, id_size, so_luong, anh_bienthe)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$idSP, $color, $size, $qty, $imgBT]);
        }
    }

    $pdo->commit();

    echo json_encode([
        "status"      => "success",
        "message"     => "Cập nhật sản phẩm thành công!",
        "product_id"  => $idSP
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        "status"  => "error",
        "message" => "Lỗi cập nhật: " . $e->getMessage()
    ]);
}
exit;
