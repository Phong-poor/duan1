<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// OPTIONS request (CORS preflight)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once "../../config/database.php";

// Lấy kết nối PDO từ file database.php
$pdo = $database->getConnection();
$method = $_SERVER["REQUEST_METHOD"];

/* -------------------------------------
   LẤY DANH MỤC (GET)
-------------------------------------- */
if ($method === "GET") {

    $sql = "SELECT * FROM baiviet_danhmuc ORDER BY id_danhmuc DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

/* -------------------------------------
   THÊM DANH MỤC (POST)
-------------------------------------- */
if ($method === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);
    $created = date("Y-m-d H:i:s");

    $sql = "INSERT INTO baiviet_danhmuc (tenDM, slug, mota, created_at) 
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    $ok = $stmt->execute([
        $data["tenDM"],
        $data["slug"],
        $data["mota"],
        $created
    ]);

    echo json_encode(["success" => $ok]);
    exit();
}

/* -------------------------------------
   SỬA DANH MỤC (PUT)
-------------------------------------- */
if ($method === "PUT") {

    $data = json_decode(file_get_contents("php://input"), true);

    $sql = "UPDATE baiviet_danhmuc 
            SET tenDM=?, slug=?, mota=? 
            WHERE id_danhmuc=?";
    $stmt = $pdo->prepare($sql);

    $ok = $stmt->execute([
        $data["tenDM"],
        $data["slug"],
        $data["mota"],
        $data["id"]
    ]);

    echo json_encode(["success" => $ok]);
    exit();
}

/* -------------------------------------
   XÓA DANH MỤC (DELETE)
-------------------------------------- */
if ($method === "DELETE") {

    if (!isset($_GET["id"])) {
        echo json_encode(["success" => false, "message" => "Thiếu ID"]);
        exit();
    }

    $id = $_GET["id"];

    $sql = "DELETE FROM baiviet_danhmuc WHERE id_danhmuc=?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$id]);

    echo json_encode(["success" => $ok]);
    exit();
}

