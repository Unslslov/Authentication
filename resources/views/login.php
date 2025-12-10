<?php
// Устанавливаем переменные для layout
$title = 'Авторизация';
$pageTitle = 'Вход в систему';
$pageSubtitle = 'Введите ваши учетные данные';
$isLoginPage = true;
// Получаем ключ из конфигурации
$siteKey = \App\Utils\Config::get('captcha.yandex.client_key') ?? '';
?>
<?php ob_start(); ?>
<form action="/login" method="POST" id="login-form" data-validate>
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
        <label for="login" class="form-label">
            <i class="bi bi-person-circle me-1"></i>Email или телефон
        </label>
        <input type="text"
               class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>"
               id="login"
               name="login"
               value="<?= htmlspecialchars($old['login'] ?? '') ?>"
               placeholder="example@mail.ru или +79991234567"
               required>
        <?php if (isset($errors['login'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['login'][0]) ?></div>
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
    </div>
    <!-- Яндекс SmartCaptcha -->
    <div class="mb-3">
        <?php if (!empty($siteKey) && (\App\Utils\Config::get('captcha.yandex.enabled') ?? true)): ?>
            <div id="captcha-container"
                 class="smart-captcha"
                 data-sitekey="<?= htmlspecialchars($siteKey) ?>">
            </div>
            <input type="hidden" name="smart-token" id="smart-token">
            <?php if (isset($errors['smart-token'])): ?>
                <div class="alert alert-danger mt-2">
                    <?= htmlspecialchars($errors['smart-token'][0]) ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-warning">
                <small>Капча отключена в настройках</small>
            </div>
        <?php endif; ?>
    </div>
    <!-- CSRF токен -->
    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <button type="submit" class="btn-auth" id="submit-btn">
        <i class="bi bi-box-arrow-in-right me-2"></i>Войти
    </button>
    <div class="text-center mt-3">
        <a href="/register" class="auth-link">
            <i class="bi bi-person-plus me-1"></i>Нет аккаунта? Зарегистрируйтесь
        </a>
    </div>
</form>
<?php $content = ob_get_clean(); ?>
<?php include dirname(__DIR__) . '/views/layouts/auth.php'; ?>

<!-- Подключаем скрипт капчи с расширенным методом -->
<script src="https://smartcaptcha.cloud.yandex.ru/captcha.js?render=onload&onload=onloadSmartCaptcha" async defer></script>
<script>
    function onloadSmartCaptcha() {
        <?php if (!empty($siteKey) && (\App\Utils\Config::get('captcha.yandex.enabled') ?? true)): ?>
        if (window.smartCaptcha) {
            const container = document.getElementById('captcha-container');
            window.smartCaptcha.render(container, {
                sitekey: '<?= $siteKey ?>',
                hl: 'ru',
                callback: function(token) {
                    document.getElementById('smart-token').value = token;
                    document.getElementById('submit-btn').disabled = false;
                },
                'error-callback': function() {
                    console.error('Ошибка загрузки капчи');
                    document.getElementById('captcha-container').innerHTML =
                        '<div class="alert alert-danger">Не удалось загрузить капчу.</div>';
                }
            });
        } else {
            console.error('Не удалось загрузить скрипт капчи');
            document.getElementById('captcha-container').innerHTML =
                '<div class="alert alert-danger">Не удалось загрузить капчу.</div>';
        }
        <?php endif; ?>
    }

    // Валидация формы
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('login-form');
        form.addEventListener('submit', function(e) {
            const login = document.getElementById('login').value.trim();
            const password = document.getElementById('password').value.trim();
            if (!login || !password) {
                e.preventDefault();
                alert('Заполните все поля');
                return;
            }
            <?php if (!empty($siteKey) && (\App\Utils\Config::get('captcha.yandex.enabled') ?? true)): ?>
            const token = document.getElementById('smart-token').value;
            if (!token) {
                e.preventDefault();
                alert('Пройдите проверку капчи');
                return;
            }
            <?php endif; ?>
        });
    });
</script>
