<?php
require_once __DIR__ . '/../configs/email.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    
    /**
     * Get base URL dynamically based on current environment
     */
    private static function getBaseUrl() {
        // Check if we're in HTTPS
        $serverPort = $_SERVER['SERVER_PORT'] ?? 80;
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                   $serverPort == 443 ? 'https://' : 'http://';
        
        // Get host
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        
        // Detect project path
        $projectPath = '';
        
        // Check different sources for project path detection
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $phpSelf = $_SERVER['PHP_SELF'] ?? '';
        
        // Method 1: From REQUEST_URI (most reliable)
        if ($requestUri && strpos($requestUri, '/Ecom_website/') !== false) {
            $projectPath = '/Ecom_website';
        }
        // Method 2: From SCRIPT_NAME
        elseif ($scriptName && strpos($scriptName, '/Ecom_website/') !== false) {
            $projectPath = '/Ecom_website';
        }
        // Method 3: From PHP_SELF
        elseif ($phpSelf && strpos($phpSelf, '/Ecom_website/') !== false) {
            $projectPath = '/Ecom_website';
        }
        // Method 4: Check current directory structure
        elseif (defined('BASE_URL')) {
            $projectPath = BASE_URL;
        }
        // Method 5: Default for localhost
        elseif ($host === 'localhost' || strpos($host, 'localhost') !== false) {
            $projectPath = '/Ecom_website';
        }
        
        return $protocol . $host . $projectPath;
    }
    
    /**
     * Gửi email sử dụng PHPMailer với SMTP
     */
    public static function sendEmail($to, $subject, $message) {
        // Log email attempt
        $logsDir = __DIR__ . '/../logs';
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }
        
        $emailLog = $logsDir . '/emails.log';
        $logEntry = date('Y-m-d H:i:s') . " - SENDING TO: $to - SUBJECT: $subject\n";
        @file_put_contents($emailLog, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Use PHPMailer
        $result = self::sendWithPHPMailer($to, $subject, $message);
        
        // Log result
        $resultLog = $result ? "SUCCESS" : "FAILED";
        $logEntry = date('Y-m-d H:i:s') . " - RESULT: $resultLog for $to\n\n";
        @file_put_contents($emailLog, $logEntry, FILE_APPEND | LOCK_EX);
        
        return $result;
    }
    
    /**
     * Send email using PHPMailer
     */
    private static function sendWithPHPMailer($to, $subject, $message) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';
            
            // Recipients
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Gửi email xác thực đăng ký với link verification
     */
    public static function sendVerificationEmail($email, $token) {
        $subject = "Xác thực tài khoản của bạn";
        
        // Dynamic URL verification link
        $baseUrl = self::getBaseUrl();
        $verificationLink = $baseUrl . "/auth/verify?token=" . $token;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                .content { background: white; padding: 30px; border-radius: 10px; }
                .button { display: inline-block; padding: 15px 30px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { font-size: 12px; color: #666; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='content'>
                    <h2 style='color: #d4af37;'>Chào mừng bạn đến với Jewelry Store!</h2>
                    <p>Cảm ơn bạn đã đăng ký tài khoản. Vui lòng nhấn vào nút bên dưới để xác thực email:</p>
                    <a href='$verificationLink' class='button'>Xác thực tài khoản</a>
                    <p>Hoặc copy link sau vào trình duyệt:</p>
                    <p style='word-break: break-all; background: #f5f5f5; padding: 10px;'>$verificationLink</p>
                    <p><strong>Link này sẽ hết hạn sau 24 giờ.</strong></p>
                </div>
                <div class='footer'>
                    <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return self::sendEmail($email, $subject, $message);
    }
    

    
    /**
     * Tạo mã xác thực 6 số
     */
    public static function generateVerificationCode() {
        return sprintf("%06d", mt_rand(0, 999999));
    }
    
    /**
     * Send reset password email
     */
    public static function sendResetPasswordEmail($email, $token) {
        $subject = "Đặt lại mật khẩu - " . SMTP_FROM_NAME;
        
        // Dynamic reset password link
        $baseUrl = self::getBaseUrl();
        $resetLink = $baseUrl . "/reset-password?token=" . $token;
        
        $message = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Đặt Lại Mật Khẩu</h1>
                </div>
                <div class='content'>
                    <p>Xin chào,</p>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại <strong>" . SMTP_FROM_NAME . "</strong>.</p>
                    <p>Nếu bạn đã yêu cầu đặt lại mật khẩu, vui lòng nhấp vào nút bên dưới để tiến hành:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$resetLink' class='button'>Đặt Lại Mật Khẩu</a>
                    </div>
                    
                    <p><strong>Lưu ý:</strong></p>
                    <ul>
                        <li>Link này sẽ hết hạn sau " . RESET_EXPIRE_MINUTES . " phút</li>
                        <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                        <li>Vì lý do bảo mật, không chia sẻ link này với bất kỳ ai</li>
                    </ul>
                    
                    <p>Nếu nút không hoạt động, bạn có thể sao chép và dán link sau vào trình duyệt:</p>
                    <p style='background: #e9e9e9; padding: 10px; word-break: break-all; font-size: 12px;'>$resetLink</p>
                </div>
                <div class='footer'>
                    <p>Email này được gửi tự động, vui lòng không phản hồi.<br>
                    © " . date('Y') . " " . SMTP_FROM_NAME . ". All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        return self::sendEmail($email, $subject, $message);
    }

    /**
     * Gửi email xác nhận thanh toán (dùng khi admin mark payment_status = paid)
     * @param object|array $order Order object or associative array containing at least order_id, customer_name/email, total_amount
     * @return bool
     */
    public static function sendPaymentConfirmationEmail($order) {
        $to = $order->customer_email ?? $order['customer_email'] ?? $order->email ?? null;
        $name = $order->customer_name ?? $order['customer_name'] ?? $order->full_name ?? 'Khách hàng';
        $orderId = $order->order_id ?? $order['order_id'] ?? null;
        $total = $order->total_amount ?? $order['total_amount'] ?? 0;

        if (!$to || !$orderId) {
            error_log('sendPaymentConfirmationEmail: missing recipient or order id');
            return false;
        }

        $subject = "Xác nhận thanh toán cho đơn hàng #$orderId";

        $message = "
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: Arial, sans-serif; color:#333;'>
            <div style='max-width:600px;margin:0 auto;padding:20px;background:#f9f9f9;'>
                <div style='background:#fff;padding:20px;border-radius:8px;'>
                    <h2 style='color:#28a745;'>Thanh toán đơn hàng thành công</h2>
                    <p>Xin chào <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    <p>Chúng tôi đã ghi nhận thanh toán cho đơn hàng <strong>#$orderId</strong>.</p>
                    <p>Tổng thanh toán: <strong>" . number_format($total, 0, ',', '.') . " VND</strong></p>
                    <p>Chúng tôi sẽ xử lý đơn hàng và gửi thông tin vận chuyển sớm nhất có thể.</p>
                    <p>Xin cảm ơn bạn đã mua hàng!</p>
                    <hr>
                    <small>Nếu bạn không thực hiện giao dịch này, vui lòng liên hệ ngay với bộ phận hỗ trợ.</small>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::sendEmail($to, $subject, $message);
    }
    
    /**
     * Tạo token reset password
     */
    public static function generateResetToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Test method to check what base URL is being generated
     * Remove this after deployment
     */
    public static function testBaseUrl() {
        return self::getBaseUrl();
    }
}
?>