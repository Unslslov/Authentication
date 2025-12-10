<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Авторизация') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #4a76a8 0%, #2c3e50 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .auth-header {
            background: #4a76a8;
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .auth-body {
            padding: 2rem;
        }

        .auth-logo {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #4a76a8;
            box-shadow: 0 0 0 0.2rem rgba(74, 118, 168, 0.25);
        }

        .btn-auth {
            background: #4a76a8;
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-auth:hover {
            background: #3a5a80;
            transform: translateY(-2px);
        }

        .auth-link {
            color: #4a76a8;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link:hover {
            color: #3a5a80;
            text-decoration: underline;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px 0 0 8px;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1 class="auth-title"><?= htmlspecialchars($pageTitle ?? 'MyAuth') ?></h1>
            <p class="auth-subtitle"><?= htmlspecialchars($pageSubtitle ?? 'Безопасная аутентификация') ?></p>
        </div>

        <div class="auth-body">
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>

    <div class="text-center mt-4">
        <?php if (isset($isLoginPage) && $isLoginPage): ?>
            <p class="text-white">Нет аккаунта? <a href="/register" class="auth-link">Зарегистрируйтесь</a></p>
        <?php elseif (isset($isRegisterPage) && $isRegisterPage): ?>
            <p class="text-white">Уже есть аккаунт? <a href="/login" class="auth-link">Войдите</a></p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>