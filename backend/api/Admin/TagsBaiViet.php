<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// Cho phép preflight CORS
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once "../../config/database.php";

// Lấy kết nối PDO từ database.php
$pdo = $database->getConnection();
$method = $_SERVER["REQUEST_METHOD"];

/* ---------------------------------------------------
   LẤY DANH SÁCH TAG (GET)
---------------------------------------------------- */
if ($method === "GET") {

    $sql = "SELECT * FROM baiviet_tags ORDER BY id_tag DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

/* ---------------------------------------------------
   THÊM TAG (POST)
---------------------------------------------------- */
/* ---------------------------------------------------
   THÊM TAG (POST)
---------------------------------------------------- */
if ($method === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);
    $created = date("Y-m-d H:i:s");

    // CHECK slug trùng
    $check = $pdo->prepare("SELECT COUNT(*) FROM baiviet_tags WHERE slug = ?");
    $check->execute([$data["slug"]]);

    if ($check->fetchColumn() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Slug đã tồn tại, vui lòng đổi tên tag"
        ]);
        exit();
    }

    $stmt = $pdo->prepare("
        INSERT INTO baiviet_tags (tag, slug, created_at) 
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $data["tag"],
        $data["slug"],
        $created
    ]);

    echo json_encode(["success" => true]);
    exit();
}


/* ---------------------------------------------------
   SỬA TAG (PUT)
---------------------------------------------------- */
if ($method === "PUT") {

    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data["id_tag"] ?? null;

    if (!$id) {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu ID tag"
        ]);
        exit();
    }

    // ✅ CHECK slug trùng nhưng loại trừ chính nó
    $check = $pdo->prepare("
        SELECT COUNT(*) 
        FROM baiviet_tags 
        WHERE slug = ? AND id_tag != ?
    ");
    $check->execute([
        $data["slug"],
        $id
    ]);

    if ($check->fetchColumn() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Slug đã tồn tại, vui lòng đổi tên tag"
        ]);
        exit();
    }

    // ✅ UPDATE
    $stmt = $pdo->prepare("
        UPDATE baiviet_tags 
        SET tag=?, slug=? 
        WHERE id_tag=?
    ");

    $stmt->execute([
        $data["tag"],
        $data["slug"],
        $id
    ]);

    echo json_encode(["success" => true]);
    exit();
}


/* ---------------------------------------------------
   XÓA TAG (DELETE)
---------------------------------------------------- */
if ($method === "DELETE") {

    if (!isset($_GET["id"])) {
        echo json_encode(["success" => false, "message" => "Thiếu ID"]);
        exit();
    }

    $id = $_GET["id"];

    $sql = "DELETE FROM baiviet_tags WHERE id_tag=?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$id]);

    echo json_encode(["success" => $ok]);
    exit();
}

