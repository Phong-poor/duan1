<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require "../../config/database.php";
$pdo = $database->getConnection();

/* ================= TAGS ================= */
function getTags($pdo, $id)
{
    $stmt = $pdo->prepare("
        SELECT t.id_tag, t.tag, t.slug
        FROM baiviet_tag_map m
        JOIN baiviet_tags t ON m.id_tag = t.id_tag
        WHERE m.id_baiviet = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ================= GET ================= */
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    /* ===== TIN HOT ===== */
    $stmtHot = $pdo->query("
        SELECT 
            b.*,
            d.tenDM,
            d.slug AS slug_danhmuc
        FROM baiviet b
        LEFT JOIN baiviet_danhmuc d ON b.id_danhmuc = d.id_danhmuc
        WHERE b.status = 'public' AND b.hot = 1
        ORDER BY b.updated_at DESC
        LIMIT 3
    ");

    $hot = $stmtHot->fetchAll(PDO::FETCH_ASSOC);
    foreach ($hot as &$h) {
        $h['tags'] = getTags($pdo, $h['id_baiviet']);
    }

    /* ===== TIN THƯỜNG ===== */
    $stmtNormal = $pdo->query("
        SELECT 
            b.*,
            d.tenDM,
            d.slug AS slug_danhmuc
        FROM baiviet b
        LEFT JOIN baiviet_danhmuc d ON b.id_danhmuc = d.id_danhmuc
        WHERE b.status = 'public' AND b.hot = 0
        ORDER BY b.created_at DESC
    ");

    $normal = $stmtNormal->fetchAll(PDO::FETCH_ASSOC);
    foreach ($normal as &$n) {
        $n['tags'] = getTags($pdo, $n['id_baiviet']);
    }

    echo json_encode([
        "hot" => $hot,
        "normal" => $normal
    ]);
    exit;
}
