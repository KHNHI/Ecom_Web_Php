<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực email - JEWELRY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #d4af37;
            --dark-gold: #b8941f;
            --light-gold: #f0e68c;
            --cream: #f8f6f0;
            --dark-brown: #3a2f28;
        }

        body {
            background: var(--cream);
            background-image: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(244, 228, 188, 0.1) 100%);
            min-height: 100vh;
            font-family: "Inter", "Playfair Display", serif;
            color: var(--dark-brown);
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4af37' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
            opacity: 0.5;
        }
        
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .verification-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.15);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .verification-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 6px rgba(212, 175, 55, 0.2));
        }
        
        .verification-icon.success {
            color: var(--gold);
        }
        
        .verification-icon.error {
            color: var(--dark-gold);
        }
        
        .verification-title {
            font-size: 2rem;
            font-family: "Playfair Display", serif;
            font-weight: 400;
            margin-bottom: 1rem;
            color: var(--dark-brown);
            letter-spacing: 0.5px;
        }
        
        .verification-message {
            font-size: 1.1rem;
            color: var(--dark-brown);
            opacity: 0.8;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
            border: none;
            border-radius: 4px;
            padding: 12px 30px;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 10px 10px 0;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            transition: 0.5s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-gold) 0%, var(--gold) 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
        }

        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-outline-primary {
            border: 2px solid var(--gold);
            color: var(--dark-brown);
            border-radius: 4px;
            padding: 12px 30px;
            font-weight: 500;
            font-size: 0.95rem;
            margin: 0 10px 10px 0;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: transparent;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.2) 50%, transparent 100%);
            transition: 0.5s;
        }
        
        .btn-outline-primary:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.15);
        }

        .btn-outline-primary:hover::before {
            left: 100%;
        }
        
        @media (max-width: 768px) {
            .verification-card {
                padding: 40px 20px;
                margin: 20px;
            }
            
            .verification-title {
                font-size: 1.5rem;
            }
            
            .verification-icon {
                font-size: 3rem;
            }

            .verification-message {
                font-size: 1rem;
            }

            .btn-primary,
            .btn-outline-primary {
                width: 100%;
                margin: 10px 0;
            }
        }

        small {
            color: var(--dark-brown);
            opacity: 0.7;
        }

        small a {
            color: var(--gold);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        small a:hover {
            color: var(--dark-gold);
            text-decoration: underline;
        }

        .text-success {
            color: var(--gold) !important;
        }

        .text-danger {
            color: var(--dark-gold) !important;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <?php if (isset($success) && $success): ?>
                <div class="verification-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="verification-title text-success">Xác thực thành công!</h1>
                <p class="verification-message"><?php echo htmlspecialchars($message); ?></p>
                <div class="mt-4">
                    <a href="/Ecom_website/signin" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                    </a>
                    <a href="/Ecom_website/" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </div>
            <?php else: ?>
                <div class="verification-icon error">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1 class="verification-title text-danger">Xác thực thất bại</h1>
                <p class="verification-message"><?php echo htmlspecialchars($message ?? 'Có lỗi xảy ra trong quá trình xác thực email.'); ?></p>
                <div class="mt-4">
                    <a href="/Ecom_website/signup" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Đăng ký lại
                    </a>
                    <a href="/Ecom_website/" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        Gặp vấn đề? <a href="mailto:support@jewelry.com" class="text-decoration-none">Liên hệ hỗ trợ</a>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>