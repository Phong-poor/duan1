<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Content-Type: application/json");

require_once '../../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

/*
Bảng yeuthich:
id_yeuthich | id_khachhang | id_sanpham
*/

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    $user_id = $data->user_id ?? null; // id_khachhang
    $product_id = $data->product_id ?? null; // id_sanpham

    if (!$user_id || !$product_id) {
        echo json_encode(["success" => false, "message" => "Missing fields"]);
        exit;
    }

    // kiểm tra tồn tại
    $check = $conn->prepare("SELECT * FROM yeuthich WHERE id_khachhang=? AND id_sanpham=?");
    $check->execute([$user_id, $product_id]);

    if ($check->rowCount() > 0) {
        // xoá yêu thích
        $del = $conn->prepare("DELETE FROM yeuthich WHERE id_khachhang=? AND id_sanpham=?");
        $del->execute([$user_id, $product_id]);

        echo json_encode(["success" => true, "favorite" => false]);
        exit;
    }

    // thêm yêu thích
    $insert = $conn->prepare("INSERT INTO yeuthich(id_khachhang, id_sanpham) VALUES (?, ?)");
    $insert->execute([$user_id, $product_id]);

    echo json_encode(["success" => true, "favorite" => true]);
    exit;
}

if ($method === 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    // Lấy danh sách sản phẩm yêu thích + JOIN SP
    $query = $conn->prepare("
        SELECT sp.*
        FROM yeuthich y
        JOIN sanpham sp ON sp.id_sanpham = y.id_sanpham
        WHERE y.id_khachhang = ?
    ");
    $query->execute([$user_id]);

    echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
?>
