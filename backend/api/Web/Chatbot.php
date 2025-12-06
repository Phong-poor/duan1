<?php
ob_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$GEMINI_API_KEY = $_ENV['GEMINI_API_KEY'];
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent');

// Database
$db = new mysqli('localhost', 'root', '', 'duan1');
$db->set_charset('utf8mb4');

if ($db->connect_error) {
    die(json_encode(['error' => 'DB lỗi'], JSON_UNESCAPED_UNICODE));
}

// Input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$userMessage = trim($data['message'] ?? '');

if (!$userMessage) {
    die(json_encode(['error' => 'Message rỗng'], JSON_UNESCAPED_UNICODE));
}


function searchProducts($db, $text)
{
    $text = strtolower($text);

    // ===== LẤY TẤT CẢ MÀU TỪ DB =====
    $colors = [];
    $r1 = $db->query("SELECT LOWER(mausac) AS mausac FROM bienthemausac");
    while ($row = $r1->fetch_assoc()) {
        $colors[] = $row['mausac'];
    }

    // ===== LẤY TẤT CẢ SIZE TỪ DB =====
    $sizes = [];
    $r2 = $db->query("SELECT LOWER(size) AS size FROM bienthesize");
    while ($row = $r2->fetch_assoc()) {
        $sizes[] = $row['size'];
    }

    // ===== TÁCH MÀU =====
    $detectedColor = null;
    foreach ($colors as $c) {
        if (str_contains($text, $c)) {
            $detectedColor = $c;
            break;
        }
    }

    // ===== TÁCH SIZE =====
    $detectedSize = null;
    foreach ($sizes as $s) {
        if (str_contains($text, $s)) {
            $detectedSize = $s;
            break;
        }
    }

    // ===== LỌC TỪ KHÓA TÌM TÊN SẢN PHẨM / BRAND =====
    $stopWords = ['tôi','toi','muốn','muon','những','nhung','sản','san','phẩm','pham','của','cua','cho','tìm','tim','mua','cần','can','và','va','màu','mau','size', 'giày', 'giay'];

    $words = explode(" ", $text);

    $keywords = array_filter($words, function($w) use ($stopWords, $colors, $sizes) {
        return !in_array($w, $stopWords)
            && !in_array($w, $colors)
            && !in_array($w, $sizes)
            && strlen($w) > 1;
    });

    // ===== SQL =====
    $sql = "
        SELECT 
            sp.id_sanpham,
            sp.tenSP,
            sp.giaSP,
            sp.hinhAnhgoc,
            dm.tenDM,
            btm.mausac,
            bts.size
        FROM sanpham sp
        LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
        LEFT JOIN bienthe bt ON bt.id_sanpham = sp.id_sanpham
        LEFT JOIN bienthemausac btm ON btm.id_mausac = bt.id_mausac
        LEFT JOIN bienthesize bts ON bts.id_size = bt.id_size
        WHERE 1=1
    ";

    // ===== TỪ KHÓA TÊN, BRAND =====
    foreach ($keywords as $k) {
        $k = $db->real_escape_string($k);
        $sql .= " AND (LOWER(sp.tenSP) LIKE '%$k%' OR LOWER(dm.tenDM) LIKE '%$k%')";
    }

    // ===== LỌC MÀU =====
    if ($detectedColor) {
        $c = $db->real_escape_string($detectedColor);
        $sql .= " AND LOWER(btm.mausac) = '$c'";
    }

    // ===== LỌC SIZE (bỏ LOWER vì size = INT) =====
    if ($detectedSize) {
        $s = $db->real_escape_string($detectedSize);
        $sql .= " AND bts.size = '$s'";
    }

    // ===== TRÁNH LẶP SẢN PHẨM =====
    $sql .= " GROUP BY sp.id_sanpham LIMIT 20";

    // ===== RUN =====
    $res = $db->query($sql);
    $arr = [];

    $baseUrl = "http://localhost/duan1/backend/";

    while ($row = $res->fetch_assoc()) {

        // FIX ẢNH
        $img = $row['hinhAnhgoc'];
        if (!str_contains($img, "uploads/Product/")) {
            $img = "uploads/Product/" . $img;
        }

        $arr[] = [
            "id" => $row['id_sanpham'],
            "name" => $row['tenSP'],
            "price" => $row['giaSP'],
            "image" => $baseUrl . $img,
            "category" => $row['tenDM'],
            "color" => $row['mausac'],
            "size" => $row['size']
        ];
    }

    return $arr;
}


$products = searchProducts($db, $userMessage);


// =============================
//  NHỒI CONTEXT CHO GEMINI
// =============================
$context = "Bạn là trợ lý bán giày. Hãy trả lời ngắn gọn và thân thiện.";

if ($products) {
    $context .= "\nDưới đây là sản phẩm tìm được:\n";
    foreach ($products as $p) {
        $context .= "- {$p['name']} | Giá: {$p['price']} | [Xem chi tiết](/ChiTiet?id={$p['id']})\n";
    }
} else {
    $context .= "\nKhông tìm thấy sản phẩm.";
}


// =============================
//  GỌI GEMINI (format ĐÚNG 100% CỦA BẠN ĐANG DÙNG)
// =============================
$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $context . "\n\nCâu hỏi của khách: " . $userMessage]
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, GEMINI_API_URL . '?key=' . $GEMINI_API_KEY);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //  XAMPP không có SSL CA

$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


// =============================
// 📌 XỬ LÝ TRẢ VỀ
// =============================
if ($http !== 200) {
    echo json_encode([
        'success' => true,
        'message' => "Mình tìm thấy ".count($products)." sản phẩm như dưới đây nè!",
        'products' => $products
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$json = json_decode($res, true);

$aiMessage =
    $json['candidates'][0]['content']['parts'][0]['text']
        ?? "Không đọc được phản hồi từ AI.";


echo json_encode([
    'success' => true,
    'message' => $aiMessage,
    'products' => $products
], JSON_UNESCAPED_UNICODE);

