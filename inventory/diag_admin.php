<?php
// Admin diagnostic page — shows recent app error log entries.
// Accessible only to logged-in admins (session `is_admin` must be truthy).
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    echo "403 Forbidden\n";
    exit();
}

$logFile = __DIR__ . '/logs/error.log';
echo "<pre style=\"white-space:pre-wrap;word-break:break-word;max-width:900px;margin:20px auto;padding:16px;background:#111;color:#ddd;border-radius:8px;\">";
if (!file_exists($logFile)) {
    echo "No log file found at inventory/logs/error.log\n";
} else {
    $contents = @file_get_contents($logFile);
    if ($contents === false) {
        echo "Unable to read log file. Check permissions.\n";
    } else {
        // Show last ~10000 characters to avoid huge dumps
        $len = strlen($contents);
        $start = $len > 10000 ? $len - 10000 : 0;
        echo htmlspecialchars(substr($contents, $start));
    }
}
echo "</pre>";

?>
