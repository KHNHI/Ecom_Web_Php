<?php
// Check file encoding and BOM
function checkFileEncoding($filepath) {
    if (!file_exists($filepath)) {
        return "File not found";
    }
    
    $content = file_get_contents($filepath);
    $encoding = mb_detect_encoding($content, ['UTF-8', 'ASCII', 'Windows-1252', 'ISO-8859-1'], true);
    
    // Check for BOM
    $bom = substr($content, 0, 3);
    $hasBOM = ($bom === "\xEF\xBB\xBF");
    
    return [
        'encoding' => $encoding,
        'has_bom' => $hasBOM,
        'file_size' => strlen($content),
        'first_50_chars' => bin2hex(substr($content, 0, 50))
    ];
}

header('Content-Type: application/json; charset=utf-8');

$files_to_check = [
    'AuthController.php' => __DIR__ . '/app/controllers/AuthController.php',
    'BaseController.php' => __DIR__ . '/core/BaseController.php',
    'signin.php' => __DIR__ . '/app/views/auth/signin.php',
    'url_helper.php' => __DIR__ . '/helpers/url_helper.php'
];

$results = [];
foreach ($files_to_check as $name => $path) {
    $results[$name] = checkFileEncoding($path);
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>