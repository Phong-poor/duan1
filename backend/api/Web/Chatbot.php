<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/../../vendor/autoload.php';
require_once "../../config/database.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$GEMINI_API_KEY = $_ENV['GEMINI_API_KEY'];
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent');

// Database
$db = (new Database())->getConnection();

if (!$db) {
    echo json_encode(['error' => 'Không kết nối được database'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$userMessage = trim($data['message'] ?? '');

if (!$userMessage) {
    die(json_encode(['error' => 'Message rỗng'], JSON_UNESCAPED_UNICODE));
}


function searchProducts(PDO $db, $text)
{
    $text = strtolower($text);

    // ======================
    //  PRICE EXTRACTION FIX
    // ======================
    $minPrice = null;
    $maxPrice = null;

    // Chuẩn hóa text
    $clean = str_replace(['.', ',', 'vnđ', 'đ', '₫'], '', strtolower($text));

    /* 1. 700k - 900k hoặc 700 - 900k */
    if (preg_match('/(\d+)\s*(k)?\s*[-–]\s*(\d+)\s*(k)?/', $clean, $m)) {
        $minPrice = intval($m[1]) * (isset($m[2]) ? 1000 : 1);
        $maxPrice = intval($m[3]) * (isset($m[4]) ? 1000 : 1);
    }

    /* 2. dưới 900k / dưới 900 */
    elseif (preg_match('/(dưới|nhỏ hơn|below|less than)\s*(\d+)\s*(k)?/', $clean, $m)) {
        $maxPrice = intval($m[2]) * (isset($m[3]) ? 1000 : 1);
    }

    /* 3. trên 900k / trên 900 */
    elseif (preg_match('/(trên|lớn hơn|above|more than)\s*(\d+)\s*(k)?/', $clean, $m)) {
        $minPrice = intval($m[2]) * (isset($m[3]) ? 1000 : 1);
    }

    /* 4. từ 500k đến 900k */
    elseif (preg_match('/từ\s*(\d+)\s*(k)?\s*(đến|tới|-)\s*(\d+)\s*(k)?/', $clean, $m)) {
        $minPrice = intval($m[1]) * (isset($m[2]) ? 1000 : 1);
        $maxPrice = intval($m[4]) * (isset($m[5]) ? 1000 : 1);
    }


    // ===== COLORS =====
    $colors = [];
    $stmt = $db->query("SELECT LOWER(mausac) AS mausac FROM bienthemausac");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $colors[] = $row['mausac'];
    }

    $detectedColor = null;
    foreach ($colors as $c) {
        if (str_contains($text, $c)) {
            $detectedColor = $c;
            break;
        }
    }

    // ===== SIZES =====
    $sizes = [];
    $stmt = $db->query("SELECT LOWER(size) AS size FROM bienthesize");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sizes[] = $row['size'];
    }

    $detectedSize = null;
    foreach ($sizes as $s) {
        if (str_contains($text, $s)) {
            $detectedSize = $s;
            break;
        }
    }

    // ===== KEYWORDS =====
    $stopWords = [
        'tôi','toi','muốn','những','sản','phẩm','của','cho','tìm','mua',
        'cần','và','màu','size','giày','khoảng','tầm','từ','-', 'có', 'co',
        'giá', 'gia', 'dưới','trên'
    ];

    $words = preg_split('/\s+/', $text);

    $keywords = array_filter($words, function($w) use ($stopWords, $colors, $sizes) {

        // loại bỏ giá tiền để không ảnh hưởng tìm kiếm tên
        if (preg_match('/^\d+[kK]?$/', $w)) return false;

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

    foreach ($keywords as $k) {
        $k = str_replace(["%", "_"], ["\%", "\_"], $k);
        $sql .= " AND (LOWER(sp.tenSP) LIKE '%$k%' OR LOWER(dm.tenDM) LIKE '%$k%')";
    }

    if ($minPrice !== null) $sql .= " AND sp.giaSP >= $minPrice";
    if ($maxPrice !== null) $sql .= " AND sp.giaSP <= $maxPrice";

    if ($detectedColor) $sql .= " AND LOWER(btm.mausac) = '$detectedColor'";
    if ($detectedSize) $sql .= " AND bts.size = '$detectedSize'";

    $sql .= " GROUP BY sp.id_sanpham LIMIT 20";

    $res = $db->query($sql);
    $data = [];

    $baseUrl = "http://miraeshoes.shop/backend/";

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $img = $row['hinhAnhgoc'];
        if (!str_contains($img, "uploads/Product/")) {
            $img = "uploads/Product/" . $img;
        }

        $data[] = [
            "id" => $row['id_sanpham'],
            "name" => $row['tenSP'],
            "price" => $row['giaSP'],
            "image" => $baseUrl . $img,
            "category" => $row['tenDM'],
            "color" => $row['mausac'],
            "size" => $row['size']
        ];
    }

    return $data;
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
    if (ob_get_length()) ob_clean(); // ⭐ TRƯỚC ECHO

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

