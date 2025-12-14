<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

require "../../config/database.php";
$pdo = $database->getConnection();

if (!isset($_GET['slug'])) {
    echo json_encode(["error" => "Missing slug"]);
    exit;
}

$slug = $_GET['slug'];

/* ===== LẤY BÀI VIẾT + DANH MỤC ===== */
$stmt = $pdo->prepare("
    SELECT 
        b.*,
        d.tenDM,
        d.slug AS slug_danhmuc
    FROM baiviet b
    LEFT JOIN baiviet_danhmuc d ON b.id_danhmuc = d.id_danhmuc
    WHERE b.slug = ? AND b.status = 'public'
    LIMIT 1
");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo json_encode(["error" => "Post not found"]);
    exit;
}

/* ===== LẤY TAGS ===== */
$stmtTags = $pdo->prepare("
    SELECT t.id_tag, t.tag, t.slug
    FROM baiviet_tag_map m
    JOIN baiviet_tags t ON m.id_tag = t.id_tag
    WHERE m.id_baiviet = ?
");
$stmtTags->execute([$post['id_baiviet']]);
$post['tags'] = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($post);
