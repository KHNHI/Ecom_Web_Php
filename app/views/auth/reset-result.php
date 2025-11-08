<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Thành công' : 'Lỗi'; ?> - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        .result-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .result-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 20px 40px rgba(212, 175, 55, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: 20px;
            text-align: center;
            border: 1px solid rgba(212, 175, 55, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .result-header {
            background: <?php echo $success ? 'linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%)' : 'linear-gradient(135deg, var(--dark-gold) 0%, var(--gold) 100%)'; ?>;
            color: white;
            padding: 50px 30px;
            position: relative;
            overflow: hidden;
        }

        .result-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            transition: 0.5s;
            animation: shimmer 3s infinite linear;
        }
        
        .result-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.2));
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        .result-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 400;
            font-family: "Playfair Display", serif;
            letter-spacing: 0.5px;
            position: relative;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .result-body {
            padding: 40px 30px;
            position: relative;
            z-index: 1;
        }
        
        .result-message {
            font-size: 1.1rem;
            line-height: 1.6;
            color: var(--dark-brown);
            opacity: 0.8;
            margin-bottom: 30px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--dark-gold) 100%);
            border: none;
            border-radius: 4px;
            padding: 12px 30px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: inline-block;
            margin: 0 10px;
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
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
            color: white;
        }

        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid var(--gold);
            border-radius: 4px;
            padding: 12px 30px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark-brown);
            display: inline-block;
            margin: 0 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.2) 50%, transparent 100%);
            transition: 0.5s;
        }
        
        .btn-secondary:hover {
            background: var(--gold);
            border-color: var(--gold);
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.15);
        }

        .btn-secondary:hover::before {
            left: 100%;
        }
        
        .action-buttons {
            margin-top: 20px;
        }

        .mt-4 a {
            color: var(--dark-brown);
            opacity: 0.7;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .mt-4 a:hover {
            color: var(--gold);
            opacity: 1;
        }
        
        @media (max-width: 576px) {
            .btn-primary, .btn-secondary {
                width: 100%;
                margin: 8px 0;
            }

            .result-card {
                margin: 15px;
            }

            .result-header {
                padding: 35px 20px;
            }

            .result-icon {
                font-size: 3rem;
            }

            .result-header h1 {
                font-size: 1.5rem;
            }

            .result-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="result-container">
        <div class="result-card">
            <div class="result-header">
                <div class="result-icon">
                    <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                </div>
                <h1><?php echo $success ? 'Thành công!' : 'Có lỗi xảy ra'; ?></h1>
            </div>
            
            <div class="result-body">
                <div class="result-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                
                <div class="action-buttons">
                    <?php if ($success): ?>
                        <a href="<?php echo url('signin'); ?>" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('forgot-password'); ?>" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Thử lại
                        </a>
                        <a href="<?php echo url('signin'); ?>" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <a href="<?php echo url(''); ?>" style="color: #6c757d; text-decoration: none;">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>