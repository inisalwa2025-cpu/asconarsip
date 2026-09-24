<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { http_response_code(400); exit("ID tidak valid"); }

$stmt = $conn->prepare("SELECT file_name,file_type,file_data FROM documents WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row || $row["file_data"] === null) { http_response_code(404); exit("File tidak ditemukan"); }

$mime = function_exists("finfo_open") ? (function () use ($row) {
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$type = finfo_buffer($finfo, $row["file_data"]);
	finfo_close($finfo);
	return $type ?: "application/octet-stream";
})() : "application/octet-stream";
$downloadName = str_replace(["\"", "\\"], "", basename($row["file_name"]));
header("Content-Type: " . $mime);
header("Content-Length: " . strlen($row["file_data"]));
$disposition = isset($_GET["preview"]) && $_GET["preview"] === "1" ? "inline" : "attachment";
header('Content-Disposition: ' . $disposition . '; filename="' . $downloadName . '"');
echo $row["file_data"];
