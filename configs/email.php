<?php
// Load environment variables
require_once __DIR__ . '/env.php';
loadEnv();

// Cấu hình email từ environment variables
define('SMTP_HOST', env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', env('SMTP_PORT', 587));
define('SMTP_USERNAME', env('SMTP_USER', ''));
define('SMTP_PASSWORD', env('SMTP_PASS', ''));
define('SMTP_FROM', env('SMTP_USER', 'nhithieu03@gmail.com'));
define('SMTP_FROM_NAME', env('SMTP_FROM_NAME', 'Jewelry Store'));

// Thời gian hết hạn
define('VERIFICATION_EXPIRE_MINUTES', 1440); // 24 giờ
define('RESET_EXPIRE_MINUTES', 30); // 30 phút cho reset password
?>