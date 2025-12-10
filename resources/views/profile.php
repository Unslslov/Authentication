<?php ob_start(); ?>
<div class="container">
    <h1>Profile</h1>
    <div class="profile-info">
        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'] ?? 'Unknown'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'Unknown'); ?></p>
            <p><strong>Registered:</strong> <?php echo htmlspecialchars($user['created_at'] ?? 'Unknown'); ?></p>
        </div>
    </div>
    <form action="/logout" method="POST" id="login-form" data-validate>
        <button type="submit" class="btn-auth" id="submit-btn">
            <i class="bi bi-box-arrow-in-right me-2"></i>Выйти из аккаунта
        </button>
    </form>
</div>
<?php $content = ob_get_clean(); ?>

<?php include dirname(__DIR__) . '/views/layouts/app.php'; ?>