<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/MailService.php';

$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    echo json_encode(["status" => "error", "msg" => "Không thể kết nối database"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$id_lienhe = $data["id_lienhe"] ?? null;
$tra_loi   = $data["tra_loi"] ?? null;

if (!$id_lienhe || !$tra_loi) {
    echo json_encode(["status" => "error", "msg" => "Thiếu dữ liệu gửi lên"]);
    exit();
}

try {
    // Lấy thông tin KH
    $sqlGet = "SELECT email, ten_khachhang FROM lienhe WHERE id_lienhe = :id";
    $stmt = $pdo->prepare($sqlGet);
    $stmt->bindParam(":id", $id_lienhe);
    $stmt->execute();

    $info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$info) {
        echo json_encode(["status" => "error", "msg" => "Không tìm thấy liên hệ"]);
        exit();
    }

    $email_kh = $info["email"];
    $ten_kh   = $info["ten_khachhang"];

    // Cập nhật phản hồi vào database
    $sqlUpdate = "UPDATE lienhe 
                  SET tra_loi = :traloi,
                      trang_thai = 'Đã phản hồi',
                      ngay_phan_hoi = NOW()
                  WHERE id_lienhe = :id";

    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(":traloi", $tra_loi);
    $stmtUpdate->bindParam(":id", $id_lienhe);
    $stmtUpdate->execute();

    // -----------------------------
    // GỬI EMAIL BẰNG MAILSERVICE
    // -----------------------------
    $subject = "Phản hồi từ Mirae về yêu cầu liên hệ #$id_lienhe";

    $content = "
        <div style='font-family: Arial; padding: 15px;'>
            <h2 style='color: #0a84ff;'>MIRAE - Hỗ trợ khách hàng</h2>

            <p>Xin chào <b>$ten_kh</b>,</p>

            <p>Cảm ơn bạn đã liên hệ với Mirae. Chúng tôi đã xem xét yêu cầu của bạn và phản hồi như sau:</p>

            <div style='background: #f1f1f1; padding: 12px; border-radius: 6px;'>
                $tra_loi
            </div>

            <br>

            <p>
                Nếu bạn còn thắc mắc, vui lòng phản hồi lại email này hoặc liên hệ qua hotline:
                <b>1900 88 66 92</b>.
            </p>

            <p>Trân trọng,<br>MIRAE Support Team</p>
        </div>
    ";

    $send = MailService::send($email_kh, $subject, $content);

    if (!$send) {
        echo json_encode(["status" => "error", "msg" => "Đã phản hồi nhưng email KHÔNG gửi được"]);
        exit();
    }

    echo json_encode(["status" => "success", "msg" => "Đã phản hồi và email đã gửi thành công!"]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
