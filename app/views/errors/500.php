<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Có lỗi xảy ra</title>
    <link href="/public/assets/css/css.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <h1>Oops! Có lỗi xảy ra</h1>
            <div class="error-message">
                <?php if (isset($message)): ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php else: ?>
                    <p>Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau.</p>
                <?php endif; ?>
            </div>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">Về trang chủ</a>
                <button onclick="history.back()" class="btn btn-secondary">Quay lại</button>
            </div>
        </div>
    </div>

    <style>
        .error-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .error-content {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        
        .error-content h1 {
            color: #dc3545;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .error-message {
            margin: 20px 0;
            color: #666;
        }
        
        .error-actions {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</body>
</html>
