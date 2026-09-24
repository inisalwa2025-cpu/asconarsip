<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";
header("Content-Type: application/json; charset=utf-8");

$id = (int)($_POST["id"] ?? 0);
$title = trim($_POST["title"] ?? "");
$number = trim($_POST["number"] ?? "");
$category = trim($_POST["category"] ?? "");
$source = trim($_POST["source"] ?? "");
$allowed = ["masuk","keluar","nota","kontrak","sk","foto","video"];

if ($id <= 0 || $title === "" || !in_array($category, $allowed, true)) {
    echo json_encode(["success"=>false,"message"=>"Data edit tidak lengkap"]);
    exit;
}
$stmt = $conn->prepare("UPDATE documents SET title=?, document_number=?, category=?, source=? WHERE id=?");
$stmt->bind_param("ssssi", $title, $number, $category, $source, $id);
if (!$stmt->execute()) {
    echo json_encode(["success"=>false,"message"=>"Gagal memperbarui dokumen: " . $stmt->error]);
    exit;
}
echo json_encode(["success"=>true,"message"=>"Dokumen berhasil diperbarui"]);
