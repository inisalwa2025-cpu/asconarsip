<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";
header("Content-Type: application/json; charset=utf-8");
$id = (int)($_POST["id"] ?? 0);
if ($id <= 0) { echo json_encode(["success"=>false,"message"=>"ID dokumen wajib diisi"]); exit; }
$stmt = $conn->prepare("UPDATE documents SET is_deleted=0 WHERE id=? AND is_deleted=1");
$stmt->bind_param("i", $id);
if (!$stmt->execute() || $stmt->affected_rows < 1) { echo json_encode(["success"=>false,"message"=>"Dokumen tidak ditemukan di sampah"]); exit; }
echo json_encode(["success"=>true,"message"=>"Dokumen berhasil dipulihkan"]);
