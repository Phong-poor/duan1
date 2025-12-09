<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Kết nối với cơ sở dữ liệu bằng lớp DB_UTILS
require_once __DIR__ . "/../../config/db_utils.php";

// Khởi tạo đối tượng DB_UTILS
$dbUtils = new DB_UTILS();
$conn = $dbUtils->connection; // Kết nối sẽ được lấy từ đối tượng DB_UTILS

// Lấy dữ liệu từ yêu cầu POST
$data = json_decode(file_get_contents("php://input"));
$id_binhluan = $data->id_binhluan;  // ID của bình luận

// Kiểm tra xem bình luận có tồn tại không
$query = "SELECT * FROM binhluan WHERE id_binhluan = ?";
$stmt = $conn->prepare($query);
$stmt->bindValue(1, $id_binhluan, PDO::PARAM_INT);  // Thay bind_param() bằng bindValue() trong PDO
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);  // PDO sử dụng fetch() thay vì get_result()

if ($result) {
    // Cập nhật trạng thái báo cáo
    $updateQuery = "UPDATE binhluan SET report_status = 'Đã báo cáo' WHERE id_binhluan = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindValue(1, $id_binhluan, PDO::PARAM_INT);  // Cũng dùng bindValue() ở đây
    $updateStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Bình luận đã được báo cáo.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy bình luận.']);
}
?>
