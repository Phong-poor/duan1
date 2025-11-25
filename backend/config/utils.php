<?php

function getRandom($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = random_int(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}

function getRandomOTP($n) {
    $characters = '0123456789';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = random_int(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}
function saveOTPToDatabase($email, $otp) {
    require_once "database.php";

    $db = new Database();
    $conn = $db->getConnection();

    $expiry = date('Y-m-d H:i:s', strtotime('+90 seconds'));

    $sql = "UPDATE khachhang SET otp = :otp, otp_hethan = :otp_hethan WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':otp', $otp);
    $stmt->bindParam(':otp_hethan', $expiry);
    $stmt->bindParam(':email', $email);

    return $stmt->execute();
}
function verifyOTP($email, $enteredOtp) {
    require_once "database.php";

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT otp, otp_hethan FROM khachhang WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || empty($result['otp']) || empty($result['otp_hethan'])) {
        return false;
    }

    $enteredOtp = trim(strval($enteredOtp));
    $otpInDb = trim(strval($result['otp']));
    $now = time();
    $expiry = strtotime($result['otp_hethan']);

    if ($enteredOtp === $otpInDb && $now <= $expiry) {
        // Xoá OTP sau khi dùng
        $stmt = $conn->prepare("UPDATE khachhang SET otp = NULL, otp_hethan = NULL WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return true;
    }

    return false;
}
?>