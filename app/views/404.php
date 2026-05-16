<?php
ob_start();
?>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-page">
                <i class="fas fa-search fa-5x mb-4 text-muted"></i>
                <h1 class="display-4 mb-3">404</h1>
                <h2 class="mb-3">Page Not Found</h2>
                <p class="lead mb-4"><?= htmlspecialchars($message ?? 'The page you are looking for might have been removed or is temporarily unavailable.') ?></p>
                <div class="action-buttons">
                    <a href="<?= BASE_URL ?>" class="btn btn-gold me-3">
                        <i class="fas fa-home"></i> Go Home
                    </a>
                    <a href="<?= BASE_URL ?>/products" class="btn btn-outline-gold">
                        <i class="fas fa-shopping-bag"></i> Browse Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$additionalCSS = '
<style>
    .error-page {
        padding: 3rem 0;
    }
    .error-page i {
        color: #d4af37;
    }
    .error-page h1 {
        font-size: 6rem;
        font-weight: bold;
        color: #d4af37;
    }
    .error-page h2 {
        color: #333;
    }
    .action-buttons {
        margin-top: 2rem;
    }
</style>
';

include __DIR__ . '/layouts/main.php';
?>
