<?php

const _HOST = 'localhost';
const _DB = 'db_ecom';
const _USER = 'root';
const _PASSWORD = '';
const _DRIVER = 'mysql';

// Base URL configuration
// Dynamic Base URL configuration - supports cloning into any folder
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// If we're running from admin/index.php, strip the /admin part
// so BASE_URL represents the project root, not the admin subdirectory
if (preg_match('#/admin$#', $scriptDir)) {
    $scriptDir = substr($scriptDir, 0, -6); // Remove trailing '/admin'
}

define('BASE_URL', rtrim($scriptDir === '/' ? '' : $scriptDir, '/'));
define('FULL_BASE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL);

// Define root paths
define('ROOT', __DIR__ . '/../app/views/admin/');
define('ADMIN_PATH', __DIR__ . '/../app/views/admin/');
define('PUBLIC_PATH', __DIR__ . '/../public/');

// Email verification settings (moved to email.php)

// Include URL helper functions
require_once __DIR__ . '/../helpers/url_helper.php';