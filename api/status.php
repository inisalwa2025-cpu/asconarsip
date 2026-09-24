<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "koneksi.php";

$result = $conn->query("SELECT DATABASE() AS database_name, VERSION() AS server_version");

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Koneksi berhasil, tetapi pemeriksaan database gagal.",
        "details" => $conn->error,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$status = $result->fetch_assoc();
echo json_encode([
    "success" => true,
    "message" => "API terhubung ke TiDB.",
    "database" => $status["database_name"] ?? null,
    "server_version" => $status["server_version"] ?? null,
    "host" => $dbHost,
    "port" => $dbPort,
    "tls" => $dbSslCa !== "",
], JSON_UNESCAPED_UNICODE);