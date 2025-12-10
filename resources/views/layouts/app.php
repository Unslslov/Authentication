<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Пример авторизации') ?></title>

    <!-- Bootstrap 5 CSS (опционально) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Иконки Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">


    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: #4a76a8;
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.2rem 1.5rem;
        }

        .btn-primary {
            background-color: #4a76a8;
            border-color: #4a76a8;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary:hover {
            background-color: #3a5a80;
            border-color: #3a5a80;
        }

        .alert {
            border-radius: 8px;
            border: none;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #4a76a8;
            box-shadow: 0 0 0 0.2rem rgba(74, 118, 168, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: #4a76a8;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #3a5a80;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4a76a8;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .smart-captcha {
            margin: 1rem 0;
        }
    </style>
</head>
<body>
<!-- Навигация -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/">
            <i class="bi bi-shield-lock me-2"></i>MyAuth
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                <?= mb_substr($user['name'] ?? 'U', 0, 1) ?>
                            </div>
                            <span><?= htmlspecialchars($user['name'] ?? 'Пользователь') ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person-circle me-2"></i>Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login"><i class="bi bi-box-arrow-in-right me-1"></i>Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register"><i class="bi bi-person-plus me-1"></i>Регистрация</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash сообщения -->
<?php if (isset($messages) && !empty($messages)): ?>
    <div class="container mt-3">
        <?php foreach ($messages as $type => $typeMessages): ?>
            <?php foreach ($typeMessages as $message): ?>
                <div class="alert alert-<?= $type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Контент -->
<main class="py-4">
    <?= $content ?>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    // Автоматическое скрытие alert через 5 секунд
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Валидация форм на клиенте
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[data-validate]');

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const inputs = form.querySelectorAll('input[required]');

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('is-invalid');

                        if (!input.nextElementSibling?.classList.contains('error-message')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message';
                            errorDiv.textContent = 'Это поле обязательно';
                            input.parentNode.appendChild(errorDiv);
                        }
                    } else {
                        input.classList.remove('is-invalid');
                        const errorDiv = input.parentNode.querySelector('.error-message');
                        if (errorDiv) errorDiv.remove();
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
</body>
</html>