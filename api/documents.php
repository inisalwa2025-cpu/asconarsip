<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";
header("Content-Type: application/json; charset=utf-8");

$isTrash = isset($_GET["trash"]) && $_GET["trash"] === "1";
$deleted = $isTrash ? 1 : 0;
$scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$apiBaseUrl = $scheme . "://" . $_SERVER["HTTP_HOST"] . rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\");
$fileEndpoint = $apiBaseUrl . "/file.php?id=";

$stmt = $conn->prepare("SELECT id,title,document_number,category,file_type,file_size,document_date,source,file_name,is_deleted,created_at,updated_at FROM documents WHERE is_deleted = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $deleted);
$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $type = strtolower($row["file_type"] ?? "");
    $type = in_array($type, ["jpg","jpeg","png","gif","webp"]) ? "img" : (in_array($type, ["mp4","webm","mov","avi","mkv"]) ? "video" : (in_array($type,["doc","docx"]) ? "doc" : "pdf"));
    $data[] = [
        "id" => (int)$row["id"],
        "title" => $row["title"],
        "no" => $row["document_number"] ?? "",
        "cat" => $row["category"],
        "type" => $type,
        "size" => $row["file_size"] ?? "-",
        "date" => $row["document_date"] ?? date("Y-m-d"),
        "from" => $row["source"] ?? "-",
        "fileName" => $row["file_name"],
        "fileUrl" => $row["file_name"] ? $fileEndpoint . (int)$row["id"] : null
    ];
}

echo json_encode(["success"=>true,"data"=>$data], JSON_UNESCAPED_UNICODE);
