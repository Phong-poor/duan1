<?php
header("Access-Control-Allow-Origin: https://miraeshoes.shop");
header("Content-Type: application/json; charset=UTF-8");

require "../../config/database.php";
$pdo = $database->getConnection();

$stmt = $pdo->query("
  SELECT 
    d.id_danhmuc,
    d.tenDM,
    d.slug,
    COUNT(b.id_baiviet) AS total
  FROM baiviet_danhmuc d
  LEFT JOIN baiviet b 
    ON b.id_danhmuc = d.id_danhmuc 
    AND b.status = 'public'
  GROUP BY d.id_danhmuc
  ORDER BY total DESC
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
