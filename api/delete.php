<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";
header("Content-Type: application/json; charset=utf-8");

$id = (int)($_POST["id"] ?? 0);
$permanent = ($_POST["permanent"] ?? "0") === "1";
if ($id <= 0) {
    echo json_encode(["success"=>false,"message"=>"ID dokumen wajib diisi"]);
    exit;
}

if ($permanent) {
    $stmt = $conn->prepare("SELECT id FROM documents WHERE id=? AND is_deleted=1");
    $stmt->bind_param("i", $id); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
    if (!$row) { echo json_encode(["success"=>false,"message"=>"Dokumen tidak ditemukan di sampah"]); exit; }
    $stmt = $conn->prepare("DELETE FROM documents WHERE id=? AND is_deleted=1");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) { echo json_encode(["success"=>false,"message"=>"Gagal menghapus permanen"]); exit; }
    echo json_encode(["success"=>true,"message"=>"Dokumen dihapus permanen"]); exit;
}

$stmt = $conn->prepare("UPDATE documents SET is_deleted=1 WHERE id=? AND is_deleted=0");
$stmt->bind_param("i", $id);
if (!$stmt->execute() || $stmt->affected_rows < 1) { echo json_encode(["success"=>false,"message"=>"Dokumen tidak ditemukan"]); exit; }
echo json_encode(["success"=>true,"message"=>"Dokumen dipindahkan ke sampah"]);
