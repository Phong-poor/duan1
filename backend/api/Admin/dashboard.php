<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../config/db_utils.php";
$db = new DB_UTILS();

// =====================
// 1. THỐNG KÊ DOANH THU
// =====================

// Tổng doanh thu
$total_revenue = $db->getValue("
    SELECT SUM(tongtien)
    FROM donhang
    WHERE trangthai = 'Thành công'
");

// Doanh thu năm nay
$year_revenue = $db->getValue("
    SELECT SUM(tongtien)
    FROM donhang
    WHERE trangthai = 'Thành công' 
      AND YEAR(thoigiantao) = YEAR(NOW())
");

// Doanh thu tháng này
$month_revenue = $db->getValue("
    SELECT SUM(tongtien)
    FROM donhang
    WHERE trangthai = 'Thành công' 
      AND MONTH(thoigiantao) = MONTH(NOW())
      AND YEAR(thoigiantao) = YEAR(NOW())
");

// Doanh thu tuần này
$week_revenue = $db->getValue("
    SELECT SUM(tongtien)
    FROM donhang
    WHERE trangthai = 'Thành công'
      AND YEARWEEK(thoigiantao, 1) = YEARWEEK(NOW(), 1)
");

// ================================
// 2. BIỂU ĐỒ: DOANH THU THEO THÁNG
// ================================
$chart_money = $db->getAll("
    SELECT MONTH(thoigiantao) AS thang, SUM(tongtien) AS total
    FROM donhang
    WHERE trangthai = 'Thành công'
    GROUP BY MONTH(thoigiantao)
");

// =====================================
// 3. BIỂU ĐỒ: SỐ LƯỢNG SẢN PHẨM THEO THÁNG
// =====================================
$chart_qty = $db->getAll("
    SELECT MONTH(d.thoigiantao) AS thang, SUM(c.soLuongMua) AS qty
    FROM hoadonchitiet c
    JOIN donhang d ON c.id_donhang = d.id_donhang
    WHERE d.trangthai = 'Thành công'
    GROUP BY MONTH(d.thoigiantao)
");

// =============================
// 4. SẢN PHẨM SẮP HẾT HÀNG (<10)
// =============================
$low_stock = $db->getAll("
    SELECT b.id_bienthe, s.tenSP, m.mausac, z.size, b.so_luong
    FROM bienthe b
    JOIN sanpham s ON b.id_sanpham = s.id_sanpham
    JOIN bienthemausac m ON b.id_mausac = m.id_mausac
    JOIN bienthesize z ON b.id_size = z.id_size
    WHERE b.so_luong < 10
");

// ========================================
// 5. TOP 5 SẢN PHẨM BÁN CHẠY NHẤT
// ========================================
$top_products = $db->getAll("
    SELECT s.id_sanpham, s.tenSP, s.hinhAnhgoc,
           SUM(c.soLuongMua) AS total_qty
    FROM hoadonchitiet c
    JOIN sanpham s ON c.id_sanpham = s.id_sanpham
    GROUP BY s.id_sanpham
    ORDER BY total_qty DESC
    LIMIT 5
");

// Final JSON output
echo json_encode([
    "status" => true,
    "summary" => [
        "total" => intval($total_revenue),
        "year" => intval($year_revenue),
        "month" => intval($month_revenue),
        "week" => intval($week_revenue)
    ],
    "chart_money" => $chart_money,
    "chart_qty" => $chart_qty,
    "low_stock" => $low_stock,
    "top_products" => $top_products
], JSON_UNESCAPED_UNICODE);
$filter = $_GET['filter'] ?? 'today';
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

switch ($filter) {
    case "today":
        $condition = "DATE(thoigiantao) = CURDATE()";
        break;

    case "yesterday":
        $condition = "DATE(thoigiantao) = CURDATE() - INTERVAL 1 DAY";
        break;

    case "week":
        $condition = "YEARWEEK(thoigiantao, 1) = YEARWEEK(CURDATE(), 1)";
        break;

    case "month":
        $condition = "MONTH(thoigiantao)=MONTH(CURDATE())
                      AND YEAR(thoigiantao)=YEAR(CURDATE())";
        break;

    case "year":
        $condition = "YEAR(thoigiantao)=YEAR(CURDATE())";
        break;

    case "custom":
        if ($start && $end) {
            $condition = "DATE(thoigiantao) BETWEEN '$start' AND '$end'";
        }
        break;

    default:
        $condition = "DATE(thoigiantao) = CURDATE()";
}