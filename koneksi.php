<?php
$possibleConfigFiles = [
    __DIR__ . DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "config.php",
    __DIR__ . DIRECTORY_SEPARATOR . "config.php"
];

foreach ($possibleConfigFiles as $configFile) {
    if (file_exists($configFile)) {
        require_once $configFile;
        break;
    }
}

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if (($_SERVER["REQUEST_METHOD"] ?? "GET") === "OPTIONS") {
    http_response_code(204);
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();

if ($dbSslCa !== '') {
    if (!is_file($dbSslCa)) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "File CA database tidak ditemukan.",
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
    $conn->ssl_set(null, null, $dbSslCa, null, null);
    @$conn->real_connect(
        $dbHost,
        $dbUser,
        $dbPassword,
        $dbName,
        $dbPort,
        null,
        MYSQLI_CLIENT_SSL
    );
} else {
    @$conn->real_connect($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
}

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Koneksi database gagal. Periksa kredensial dan nama database hosting.",
        "details" => $conn->connect_error,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$conn->set_charset("utf8mb4");

$columns = $conn->query("SHOW COLUMNS FROM documents");
if ($columns) {
    $exists = false;
    while ($row = $columns->fetch_assoc()) {
        if (($row["Field"] ?? "") === "file_data") {
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        $conn->query("ALTER TABLE documents ADD COLUMN file_data LONGBLOB NULL AFTER file_name");
    }
}
