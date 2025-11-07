<?php
// Test base URL detection
require_once 'helpers/email_helper.php';

echo "Testing Base URL Detection:\n";
echo "========================\n";

// Simulate localhost environment
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/Ecom_website/auth/signup';
$_SERVER['SCRIPT_NAME'] = '/Ecom_website/index.php';
echo "Localhost: " . EmailHelper::testBaseUrl() . "\n";

// Simulate production environment
$_SERVER['HTTP_HOST'] = 'trangsucse0001ueh.id.vn';
$_SERVER['REQUEST_URI'] = '/Ecom_website/auth/signup';
$_SERVER['SCRIPT_NAME'] = '/Ecom_website/index.php';
$_SERVER['HTTPS'] = 'on';
echo "Production: " . EmailHelper::testBaseUrl() . "\n";

// Test without /Ecom_website path (root domain)
$_SERVER['HTTP_HOST'] = 'example.com';
$_SERVER['REQUEST_URI'] = '/auth/signup';
$_SERVER['SCRIPT_NAME'] = '/index.php';
unset($_SERVER['HTTPS']);
echo "Root domain: " . EmailHelper::testBaseUrl() . "\n";
?>