<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";
header("Content-Type: application/json; charset=utf-8");

$title = trim($_POST["title"] ?? "");
$number = trim($_POST["number"] ?? "");
$category = trim($_POST["category"] ?? "");
$source = trim($_POST["source"] ?? "");

$allowed = ["masuk","keluar","nota","kontrak","sk","foto","video"];
if ($title === "" || !in_array($category, $allowed, true)) {
    respond(false, "Judul dan kategori wajib diisi", 422);
}
$uploadError = $_FILES["file"]["error"] ?? UPLOAD_ERR_NO_FILE;
if ($uploadError !== UPLOAD_ERR_OK) {
    $messages = [
        UPLOAD_ERR_INI_SIZE => "Ukuran file melebihi batas server",
        UPLOAD_ERR_FORM_SIZE => "Ukuran file melebihi batas formulir",
        UPLOAD_ERR_PARTIAL => "File hanya terunggah sebagian",
        UPLOAD_ERR_NO_FILE => "File wajib dipilih",
        UPLOAD_ERR_NO_TMP_DIR => "Folder sementara server tidak tersedia",
        UPLOAD_ERR_CANT_WRITE => "Server tidak dapat menulis file",
        UPLOAD_ERR_EXTENSION => "Upload dihentikan oleh ekstensi PHP"
    ];
    respond(false, $messages[$uploadError] ?? "Upload file gagal", 422);
}

$file = $_FILES["file"];
$maxSize = 500 * 1024 * 1024;
if ($file["size"] > $maxSize) {
    respond(false, "Ukuran file maksimal 500 MB", 422);
}

$original = basename($file["name"]);
$ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
$allowedExt = ["pdf","doc","docx","xls","xlsx","ppt","pptx","txt","csv","json","xml","jpg","jpeg","png","gif","webp","mp4","webm","mov","avi","mkv"];
if (!in_array($ext, $allowedExt, true)) {
    respond(false, "Format file tidak diizinkan", 422);
}

$size = formatSize($file["size"]);
$date = date("Y-m-d");
$stmt = $conn->prepare("INSERT INTO documents (title, document_number, category, file_type, file_size, document_date, source, file_name, file_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    respond(false, "Query database tidak dapat disiapkan: " . $conn->error, 500);
}
$fileData = null;
$stmt->bind_param("ssssssssb", $title, $number, $category, $ext, $size, $date, $source, $original, $fileData);

$handle = fopen($file["tmp_name"], "rb");
if (!$handle) {
    respond(false, "File sementara tidak dapat dibaca", 500);
}
while (!feof($handle)) {
    $chunk = fread($handle, 1024 * 1024);
    if ($chunk === false) {
        fclose($handle);
        respond(false, "File gagal dibaca", 500);
    }
    $stmt->send_long_data(8, $chunk);
}
fclose($handle);

if (!$stmt->execute()) {
    respond(false, "Gagal menyimpan data ke database: " . $stmt->error, 500);
}

echo json_encode(["success"=>true,"message"=>"Dokumen berhasil disimpan","id"=>$stmt->insert_id]);

function respond($success, $message, $status = 200) {
    http_response_code($status);
    echo json_encode(["success" => $success, "message" => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function formatSize($bytes) {
    if ($bytes < 1024) return $bytes . " B";
    if ($bytes < 1024*1024) return round($bytes/1024) . " KB";
    return number_format($bytes/(1024*1024), 1) . " MB";
}
