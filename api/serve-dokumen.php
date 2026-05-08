<?php
// api/serve-dokumen.php
// Serve dokumen dengan path yang aman

if (!isset($_GET['file'])) {
    http_response_code(400);
    die('File not specified');
}

$filename = basename($_GET['file']); // Prevent path traversal
$filepath = realpath(__DIR__ . '/../uploads/dokumen/' . $filename);

// Validasi path
if (!$filepath || strpos($filepath, realpath(__DIR__ . '/../uploads/dokumen/')) !== 0) {
    http_response_code(403);
    die('Access denied');
}

// Cek file ada
if (!file_exists($filepath)) {
    http_response_code(404);
    die('File not found');
}

// Serve file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filepath);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
?>
