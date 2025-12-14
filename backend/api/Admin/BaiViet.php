    <?php
    header("Access-Control-Allow-Origin: https://miraeshoes.shop");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(200);
        exit();
    }

    require "../../config/database.php";

    $pdo = $database->getConnection();
    $method = $_SERVER["REQUEST_METHOD"];

    $backendDir = $_SERVER["DOCUMENT_ROOT"] . "/backend/uploads/Baiviet/";
    /* ======================================================
    TOGGLE HOT / BÌNH THƯỜNG
    ====================================================== */
    if ($method === "POST" && ($_GET["action"] ?? "") === "toggle-hot") {

        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input["id_baiviet"] ?? 0;

        if (!$id) {
            echo json_encode(["success" => false, "message" => "Thiếu ID bài viết"]);
            exit;
        }

        // Lấy trạng thái hiện tại
        $stmt = $pdo->prepare("SELECT hot FROM baiviet WHERE id_baiviet = ?");
        $stmt->execute([$id]);
        $currentHot = (int)$stmt->fetchColumn();

        /* ====== NẾU ĐANG HOT → CHUYỂN VỀ BÌNH THƯỜNG ====== */
        if ($currentHot === 1) {
            $pdo->prepare("UPDATE baiviet SET hot = 0 WHERE id_baiviet = ?")
                ->execute([$id]);

            echo json_encode([
                "success" => true,
                "message" => "Đã chuyển về tin bình thường"
            ]);
            exit;
        }

        /* ====== NẾU CHƯA HOT → KIỂM TRA GIỚI HẠN ====== */
        $countHot = $pdo->query("SELECT COUNT(*) FROM baiviet WHERE hot = 1")
                        ->fetchColumn();

        if ($countHot >= 3) {
            echo json_encode([
                "success" => false,
                "message" => "Chỉ được tối đa 3 tin hot"
            ]);
            exit;
        }

        // Đẩy thành tin hot
        $pdo->prepare("UPDATE baiviet SET hot = 1 WHERE id_baiviet = ?")
            ->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "Đã đẩy thành tin hot"
        ]);
        exit;
    }
    /* ======================================================
        HÀM LƯU ẢNH BASE64 → 2 THƯ MỤC
    ====================================================== */
    function saveBase64Image($base64, $backendDir)
    {
        if (!str_contains($base64, "base64,")) return null;

        $parts = explode(";base64,", $base64);
        $mime  = $parts[0];
        $data  = base64_decode($parts[1]);

        if (!preg_match('/image\/(png|jpg|jpeg|webp|gif)/i', $mime, $m)) {
            return null;
        }

        $ext = $m[1];
        $hash = sha1($data);
        $filename = $hash . "." . $ext;

        if (!is_dir($backendDir)) {
            mkdir($backendDir, 0777, true);
        }

        $path = $backendDir . $filename;

        // ✅ CHECK TRÙNG
        if (!file_exists($path)) {
            file_put_contents($path, $data);
        }

        // URL public
        return "/backend/uploads/Baiviet/" . $filename;
    }

    /* ======================================================
        XỬ LÝ ẢNH NỘI DUNG (SEO + LƯU 2 NƠI)
    ====================================================== */
    function processContentImages($html, $backendDir, $postTitle)
    {
        preg_match_all('/<img[^>]+src="([^">]+)"/', $html, $matches);
        foreach ($matches[1] as $src) {
            if (str_contains($src, "data:image")) {
                $newUrl = saveBase64Image($src, $backendDir);
                if ($newUrl) {
                    $alt   = htmlspecialchars("Ảnh - $postTitle", ENT_QUOTES);
                    $title = htmlspecialchars("Hình ảnh trong bài viết: $postTitle", ENT_QUOTES);
                    $html = str_replace($src, $newUrl, $html);
                }
            }
        }
        return $html;
    }

    /* ======================================================
        LẤY TAGS ĐÚNG DATABASE CỦA BẠN
    ====================================================== */
    function getTags($pdo, $id)
    {
        $stmt = $pdo->prepare("
            SELECT t.id_tag, t.tag, t.slug
            FROM baiviet_tag_map m
            JOIN baiviet_tags t ON m.id_tag = t.id_tag
            WHERE m.id_baiviet = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ======================================================
        GET – LẤY DANH SÁCH BÀI VIẾT
    ====================================================== */
    if ($method === "GET") {

        $stmt = $pdo->query("
            SELECT b.*, d.tenDM
            FROM baiviet b
            LEFT JOIN baiviet_danhmuc d ON b.id_danhmuc = d.id_danhmuc
            ORDER BY b.id_baiviet DESC
        ");

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as &$item) {
            $item["tags"] = getTags($pdo, $item["id_baiviet"]);
        }

        echo json_encode($data);
        exit;
    }

    /* ======================================================
        POST – THÊM BÀI VIẾT
    ====================================================== */
    
    $input = json_decode(file_get_contents("php://input"), true);
    $hot = isset($input["hot"]) ? (int)$input["hot"] : 0;
    if ($method === "POST" && !isset($_FILES["thumbnailFile"])) {

        $content = processContentImages($input["content"], $backendDir, $input["title"]);

        $stmt = $pdo->prepare("
            INSERT INTO baiviet 
            (title, slug, thumbnail, thumbnail_alt, thumbnail_title, content, seo_title, seo_description, id_danhmuc, status, hot, creator)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $input["title"],
            $input["slug"],
            $input["thumbnail"],
            $input["thumbnail_alt"] ?: $input["title"],
            $input["thumbnail_title"] ?: $input["title"],
            $content,
            $input["seo_title"],
            $input["seo_description"],
            $input["id_danhmuc"],
            $input["status"],
            $hot,
            $input["creator"],
        ]);

        $newID = $pdo->lastInsertId();

        // Lưu tags
        if (!empty($input["tags"])) {
            foreach ($input["tags"] as $tagID) {
                $pdo->prepare("INSERT INTO baiviet_tag_map (id_baiviet, id_tag) VALUES (?, ?)")
                    ->execute([$newID, $tagID]);
            }
        }

        echo json_encode(["success" => true]);
        exit;
    }

    /* ======================================================
        UPLOAD THUMBNAIL
    ====================================================== */
    if ($method === "POST" && isset($_FILES["thumbnailFile"])) {

        if (!is_dir($frontendDir)) mkdir($frontendDir, 0777, true);
        if (!is_dir($backendDir)) mkdir($backendDir, 0777, true);

        $ext = pathinfo($_FILES["thumbnailFile"]["name"], PATHINFO_EXTENSION);
        $newName = time() . "_" . uniqid() . "." . $ext;

        $pathFrontend = $frontendDir . $newName;
        $pathBackend  = $backendDir . $newName;

        if (move_uploaded_file($_FILES["thumbnailFile"]["tmp_name"], $pathFrontend)) {

            copy($pathFrontend, $pathBackend);

            echo json_encode([
                "success" => true,
                "fileName" => $newName,
                "url" => "/src/assets/" . $newName
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Upload thất bại"]);
        }

        exit;
    }

    /* ======================================================
        PUT – SỬA BÀI VIẾT
    ====================================================== */
    if ($method === "PUT") {

        $content = processContentImages($input["content"], $frontendDir, $backendDir, $input["title"]);

        // giữ thumbnail cũ nếu không upload mới
        if (!$input["thumbnail"]) {
            $old = $pdo->prepare("SELECT thumbnail FROM baiviet WHERE id_baiviet = ?");
            $old->execute([$input["id_baiviet"]]);
            $input["thumbnail"] = $old->fetchColumn();
        }

        $stmt = $pdo->prepare("
            UPDATE baiviet
            SET title=?, slug=?, thumbnail=?, thumbnail_alt=?, thumbnail_title=?, 
                content=?, seo_title=?, seo_description=?, id_danhmuc=?, status=?, creator=?, hot=?, updated_at=NOW()
            WHERE id_baiviet=?
        ");

        $stmt->execute([
            $input["title"],
            $input["slug"],
            $input["thumbnail"],
            $input["thumbnail_alt"] ?: $input["title"],
            $input["thumbnail_title"] ?: $input["title"],
            $content,
            $input["seo_title"],
            $input["seo_description"],
            $input["id_danhmuc"],
            $input["status"],
            $input["creator"],
            $hot,
            $input["id_baiviet"]
        ]);

        // Cập nhật tags
        $pdo->prepare("DELETE FROM baiviet_tag_map WHERE id_baiviet = ?")
            ->execute([$input["id_baiviet"]]);

        if (!empty($input["tags"])) {
            foreach ($input["tags"] as $tagID) {
                $pdo->prepare("INSERT INTO baiviet_tag_map (id_baiviet, id_tag) VALUES (?, ?)")
                    ->execute([$input["id_baiviet"], $tagID]);
            }
        }

        echo json_encode(["success" => true]);
        exit;
    }
    /* ======================================================
        DELETE – XÓA BÀI VIẾT + ẢNH LIÊN QUAN
    ====================================================== */
    if ($method === "DELETE") {

        if (!isset($_GET["id"])) {
            echo json_encode(["success" => false, "msg" => "Missing ID"]);
            exit;
        }

        $id = $_GET["id"];

        /* ================================
        1️⃣ LẤY THUMBNAIL + CONTENT
        ================================= */
        $stmt = $pdo->prepare("SELECT thumbnail, content FROM baiviet WHERE id_baiviet = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {

            /* ================================
            2️⃣ XÓA THUMBNAIL NẾU CÓ
            ================================= */
            if (!empty($post["thumbnail"])) {

                $thumb = $post["thumbnail"];

                $fileFrontend = "C:/xampp/htdocs/DUAN1/frontend/src/assets/" . $thumb;
                $fileBackend  = "C:/xampp/htdocs/DUAN1/backend/uploads/Baiviet/" . $thumb;

                if (file_exists($fileFrontend)) unlink($fileFrontend);
                if (file_exists($fileBackend)) unlink($fileBackend);
            }

            /* ================================
            3️⃣ TÌM ẢNH TRONG CONTENT
            ================================= */
            preg_match_all('/src="\/backend\/uploads\/Baiviet\/([^"]+)"/', $post["content"], $matches);

            if (!empty($matches[1])) {

                foreach ($matches[1] as $img) {

                    $imgFrontend = "C:/xampp/htdocs/DUAN1/frontend/src/assets/" . $img;
                    $imgBackend  = "C:/xampp/htdocs/DUAN1/backend/uploads/Baiviet/" . $img;

                    if (file_exists($imgFrontend)) unlink($imgFrontend);
                    if (file_exists($imgBackend)) unlink($imgBackend);
                }
            }
        }

        /* ================================
        4️⃣ XÓA TAG MAP
        ================================= */
        $pdo->prepare("DELETE FROM baiviet_tag_map WHERE id_baiviet = ?")->execute([$id]);

        /* ================================
        5️⃣ XÓA BÀI VIẾT
        ================================= */
        $pdo->prepare("DELETE FROM baiviet WHERE id_baiviet = ?")->execute([$id]);

        echo json_encode(["success" => true, "message" => "Đã xóa bài viết + toàn bộ hình ảnh"]);
        exit;
    }
?>