<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../../config/database.php";

$db = new Database();
$pdo = $db->getConnection();

$response = ["status" => "error", "message" => ""];

/* -----------------------------------------
   HÀM TẠO maSP TỰ ĐỘNG (SP001, SP002...)
------------------------------------------*/
function generateMaSP($pdo)
{
    $stmt = $pdo->query("SELECT maSP FROM sanpham ORDER BY id_sanpham DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$last) return "SP001";

    $num = (int) filter_var($last["maSP"], FILTER_SANITIZE_NUMBER_INT);
    $num++;

    return "SP" . str_pad($num, 3, "0", STR_PAD_LEFT);
}

/* -----------------------------------------
   NHẬN DỮ LIỆU TỪ VUE
------------------------------------------*/
$tenSP = $_POST["tenSP"] ?? "";
$id_danhmuc = $_POST["id_danhmuc"] ?? "";
$id_thuonghieu = $_POST["id_thuonghieu"] ?? "";
$giaSP = $_POST["giaSP"] ?? 0;
$mota = $_POST["mota"] ?? "";
$variants = json_decode($_POST["variants"], true) ?? [];

$maSP = generateMaSP($pdo);

/* -----------------------------------------
   UPLOAD ẢNH CHÍNH
------------------------------------------*/
$mainImagePath = null;

if (!empty($_FILES["mainImage"]["name"])) {
    $filename = time() . "_" . $_FILES["mainImage"]["name"];
    $path = "../../uploads/Product/" . $filename;

    if (move_uploaded_file($_FILES["mainImage"]["tmp_name"], $path)) {
        $mainImagePath = "uploads/Product/" . $filename;
    }
}

/* -----------------------------------------
   INSERT SẢN PHẨM
------------------------------------------*/
$stmt = $pdo->prepare("
    INSERT INTO sanpham (maSP, tenSP, giaSP, mota, hinhAnhGoc, id_danhmuc, id_thuonghieu)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$maSP, $tenSP, $giaSP, $mota, $mainImagePath, $id_danhmuc, $id_thuonghieu]);

$idSP = $pdo->lastInsertId();

/* -----------------------------------------
   UPLOAD ẢNH PHỤ
------------------------------------------*/
if (!empty($_FILES["extraImages"])) {
    foreach ($_FILES["extraImages"]["tmp_name"] as $key => $tmpName) {

        $fileName = time() . "_extra_" . $key . "_" . $_FILES["extraImages"]["name"][$key];
        $savePath = "../../uploads/Product/" . $fileName;

        if (move_uploaded_file($tmpName, $savePath)) {
            $url = "uploads/Product/" . $fileName;

            // lưu DB
            $stmt = $pdo->prepare("INSERT INTO sanpham_hinhanhphu (id_sanpham, link_anh, thu_tu) VALUES (?, ?, ?)");
            $stmt->execute([$idSP, $url, $key + 1]);
        }
    }
}

/* -----------------------------------------
   INSERT BIẾN THỂ
------------------------------------------*/
foreach ($variants as $i => $var) {

    // Upload ảnh biến thể
    $varImage = null;

    if (!empty($_FILES["variantImages"]["name"][$i])) {
        $fileName = time() . "_variant_" . $i . "_" . $_FILES["variantImages"]["name"][$i];
        $savePath = "../../uploads/Product/" . $fileName;

        if (move_uploaded_file($_FILES["variantImages"]["tmp_name"][$i], $savePath)) {
            $varImage = "uploads/Product/" . $fileName;
        }
    }

    // Lưu biến thể vào DB
    $stmt = $pdo->prepare("
        INSERT INTO bienthesanpham (id_sanpham, id_mausac, id_size, soLuong, anh_bienthe)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([$idSP, $var["color"], $var["size"], $var["quantity"], $varImage]);
}


/* -----------------------------------------
   TRẢ VỀ JSON CHO FRONTEND
------------------------------------------*/
$response["status"] = "success";
$response["message"] = "Thêm sản phẩm thành công!";
$response["product_id"] = $idSP;

echo json_encode($response);
exit;
?>
