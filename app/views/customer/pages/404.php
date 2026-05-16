<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Trang không tìm thấy</title>
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    <style>
        .error-404-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .error-content {
            text-align: center;
            background: white;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
        }
        
        .error-404 {
            font-size: 8rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .error-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 15px;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .error-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108,117,125,0.3);
        }
        
        .btn-outline {
            background: transparent;
            color: #007bff;
            border: 2px solid #007bff;
        }
        
        .btn-outline:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-404-container">
        <div class="error-content">
            <div class="error-404">404</div>
            <h1 class="error-title">Trang không tìm thấy</h1>
            <p class="error-message">
                Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển. 
                <br>Hãy kiểm tra lại URL hoặc quay về trang chủ để tiếp tục mua sắm.
            </p>
            <div class="error-buttons">
                <a href="<?= url('') ?>" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Về trang chủ
                </a>
                <a href="<?= route('products') ?>" class="btn btn-outline">
                    <i class="fas fa-shopping-bag me-2"></i>Xem sản phẩm
                </a>
                <button onclick="history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </button>
            </div>
        </div>
    </div>
    
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
