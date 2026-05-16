/**
 * Admin Session Management
 * Handles displaying admin user information in the header
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get admin session data from PHP
    const adminName = '<?php echo isset($_SESSION["admin_name"]) ? htmlspecialchars($_SESSION["admin_name"]) : "Admin"; ?>';
    const adminEmail = '<?php echo isset($_SESSION["admin_email"]) ? htmlspecialchars($_SESSION["admin_email"]) : ""; ?>';
    
    // Update header elements if they exist
    const nameElement = document.getElementById('admin-name');
    const nameMenuElement = document.getElementById('admin-name-menu');
    const emailElement = document.getElementById('admin-email');
    
    if (nameElement && adminName) {
        nameElement.textContent = adminName;
    }
    
    if (nameMenuElement && adminName) {
        nameMenuElement.textContent = adminName;
    }
    
    if (emailElement && adminEmail) {
        emailElement.textContent = adminEmail;
    }
});

// Session timeout warning (optional)
let sessionTimeout = 8 * 60 * 60 * 1000; // 8 hours in milliseconds
let warningTime = sessionTimeout - (30 * 60 * 1000); // Warn 30 minutes before

setTimeout(function() {
    if (confirm('Phiên đăng nhập sắp hết hạn. Bạn có muốn tiếp tục?')) {
        // Refresh session by making a simple request
        const baseUrl = window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : '';
        fetch(`${baseUrl}/admin/dashboard`, {
            method: 'HEAD',
            credentials: 'same-origin'
        });
    }
}, warningTime);
