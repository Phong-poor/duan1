<?php
require_once "../../config/database.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST");

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';

switch ($action) {

    // ==============================
    // 1) THÊM SẢN PHẨM VÀO GIỎ HÀNG
    // ==============================
    case "add":
        $data = json_decode(file_get_contents("php://input"), true);

        $id_khachhang = $data['id_khachhang'] ?? null;
        $id_bienthe   = $data['id_bienthe'] ?? null;
        $so_luong     = $data['so_luong'] ?? 1;

        if (!$id_khachhang || !$id_bienthe) {
            echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
            exit;
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $check = $db->prepare("
            SELECT id_giohang, so_luong 
            FROM giohang 
            WHERE id_khachhang = ? AND id_bienthe = ?
        ");
        $check->execute([$id_khachhang, $id_bienthe]);
        $exist = $check->fetch(PDO::FETCH_ASSOC);

        if ($exist) {
            // Cộng dồn số lượng
            $newQty = $exist['so_luong'] + $so_luong;

            $update = $db->prepare("
                UPDATE giohang SET so_luong = ? 
                WHERE id_giohang = ?
            ");
            $update->execute([$newQty, $exist['id_giohang']]);

            echo json_encode(["success" => true, "message" => "Cập nhật số lượng"]);
        } else {
            // Thêm mới vào giỏ
            $insert = $db->prepare("
                INSERT INTO giohang(id_khachhang, id_bienthe, so_luong)
                VALUES (?, ?, ?)
            ");
            $insert->execute([$id_khachhang, $id_bienthe, $so_luong]);

            echo json_encode(["success" => true, "message" => "Đã thêm vào giỏ"]);
        }
        break;

    // ==============================
    // 2) LẤY DANH SÁCH GIỎ HÀNG
    // ==============================
    case "list":
        $id_khachhang = $_GET['id_khachhang'] ?? null;

        if (!$id_khachhang) {
            echo json_encode(["success" => false, "message" => "Thiếu ID khách hàng"]);
            exit;
        }

        $query = $db->prepare("
            SELECT 
                gh.id_giohang,
                gh.so_luong,
                bt.id_bienthe,
                bt.so_luong AS tonkho,
                bt.id_size,
                sz.size,
                bt.id_mausac,
                ms.mausac,
                sp.id_sanpham,
                sp.tenSP,
                sp.giaSP,
                sp.giamgiaSP,
                sp.hinhAnhgoc
            FROM giohang gh
            JOIN bienthe bt     ON gh.id_bienthe = bt.id_bienthe
            JOIN bienthesize sz ON bt.id_size = sz.id_size
            JOIN bienthemausac ms ON bt.id_mausac = ms.id_mausac
            JOIN sanpham sp     ON bt.id_sanpham = sp.id_sanpham
            WHERE gh.id_khachhang = ?
            ORDER BY gh.id_giohang DESC
        ");

        $query->execute([$id_khachhang]);
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $rows]);
        break;

    // ==============================
    // 3) XOÁ SẢN PHẨM TRONG GIỎ
    // ==============================
    case "delete":
        $data = json_decode(file_get_contents("php://input"), true);
        $id_giohang = $data['id_giohang'] ?? null;

        if (!$id_giohang) {
            echo json_encode(["success" => false, "message" => "Thiếu ID giỏ hàng"]);
            exit;
        }

        $del = $db->prepare("DELETE FROM giohang WHERE id_giohang = ?");
        $del->execute([$id_giohang]);

        echo json_encode(["success" => true, "message" => "Đã xoá sản phẩm"]);
        break;

    // ==============================
    // 4) UPDATE SỐ LƯỢNG TRONG GIỎ
    // ==============================
    case "update":
        $data = json_decode(file_get_contents("php://input"), true);

        $id_giohang = $data['id_giohang'] ?? null;
        $so_luong   = $data['so_luong'] ?? null;

        if (!$id_giohang || !$so_luong) {
            echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
            exit;
        }

        $update = $db->prepare("
            UPDATE giohang 
            SET so_luong = ? 
            WHERE id_giohang = ?
        ");
        $update->execute([$so_luong, $id_giohang]);

        echo json_encode(["success" => true, "message" => "Cập nhật thành công"]);
        break;

    default:
        echo json_encode(["success" => false, "message" => "Action không hợp lệ"]);
}
?>
