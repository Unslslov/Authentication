<?php
$title = 'Регистрация';
$pageTitle = 'Создать аккаунт';
$pageSubtitle = 'Заполните форму для регистрации';
$isRegisterPage = true;
?>

<?php ob_start(); ?>

    <form action="/register" method="POST" data-validate>
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $field => $fieldErrors): ?>
                        <?php foreach ($fieldErrors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="bi bi-person me-1"></i>Имя
            </label>
            <input type="text"
                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                   id="name"
                   name="name"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                   required>
            <?php if (isset($errors['name'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['name'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1"></i>Email
            </label>
            <input type="email"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   id="email"
                   name="email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   required>
            <?php if (isset($errors['email'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['email'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">
                <i class="bi bi-phone me-1"></i>Телефон
            </label>
            <input type="tel"
                   class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                   id="phone"
                   name="phone"
                   value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                   placeholder="+7 (999) 123-45-67"
                   required>
            <?php if (isset($errors['phone'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['phone'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-1"></i>Пароль
            </label>
            <input type="password"
                   class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                   id="password"
                   name="password"
                   required>
            <?php if (isset($errors['password'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['password'][0]) ?></div>
            <?php endif; ?>
            <small class="text-muted">Минимум 6 символов</small>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="bi bi-lock-fill me-1"></i>Подтверждение пароля
            </label>
            <input type="password"
                   class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                   id="password_confirmation"
                   name="password_confirmation"
                   required>
            <?php if (isset($errors['password_confirmation'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['password_confirmation'][0]) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
        </button>

        <div class="text-center mt-3">
            <a href="/" class="auth-link">
                <i class="bi bi-arrow-left me-1"></i>Вернуться на главную страницу
            </a>
        </div>
    </form>

<?php $content = ob_get_clean(); ?>

<?php include dirname(__DIR__) . '/views/layouts/app.php'; ?>