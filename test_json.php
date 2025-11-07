<?php
// Simple JSON test endpoint
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Set encoding
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

$response = [
    'success' => true,
    'message' => 'Test JSON response with Vietnamese: Đây là test tiếng Việt',
    'data' => [
        'encoding' => mb_internal_encoding(),
        'timestamp' => date('Y-m-d H:i:s'),
        'vietnamese_text' => 'Chào mừng bạn đến với hệ thống!'
    ]
];

$json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON encoding failed: ' . json_last_error_msg()]);
} else {
    echo $json;
}
?>