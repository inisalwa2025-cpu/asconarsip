<?php

header("Content-Type: application/json; charset=utf-8");

// Adjust this value to match the hosting plan quota.
$storageLimitBytes = 10 * 1024 * 1024 * 1024;
$result = $conn->query("SELECT COALESCE(SUM(OCTET_LENGTH(file_data)), 0) AS used_bytes, COUNT(file_data) AS file_count FROM documents WHERE file_data IS NOT NULL");
$storage = $result ? $result->fetch_assoc() : ["used_bytes" => 0, "file_count" => 0];
$usedBytes = (int)$storage["used_bytes"];
$fileCount = (int)$storage["file_count"];

$percent = $storageLimitBytes > 0
    ? min(100, round(($usedBytes / $storageLimitBytes) * 100, 2))
    : 0;

$availableBytes = max(0, $storageLimitBytes - $usedBytes);

echo json_encode([
    "success" => true,
    "usedBytes" => $usedBytes,
    "limitBytes" => $storageLimitBytes,
    "availableBytes" => $availableBytes,
    "percent" => $percent,
    "fileCount" => $fileCount,
    "used" => formatStorage($usedBytes),
    "limit" => formatStorage($storageLimitBytes),
    "available" => formatStorage($availableBytes)
], JSON_UNESCAPED_UNICODE);

function formatStorage($bytes) {
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . " KB";
    }
    if ($bytes < 1024 * 1024 * 1024) {
        return round($bytes / (1024 * 1024), 1) . " MB";
    }
    return round($bytes / (1024 * 1024 * 1024), 2) . " GB";
}
