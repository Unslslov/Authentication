<?php
$title = 'Главная страница';
?>

<?php ob_start(); ?>

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Добро пожаловать в <span class="text-primary">MyAuth</span>
                </h1>
                <p class="lead mb-4">
                    Безопасная система аутентификации и управления профилем.
                    Регистрируйтесь, входите и управляйте своими данными в одном месте.
                </p>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="d-flex gap-3">
                        <a href="/register" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-person-plus me-2"></i>Начать регистрацию
                        </a>
                        <a href="/login" class="btn btn-outline-primary btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-3">
                        <a href="/profile" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-person-circle me-2"></i>Мой профиль
                        </a>
                        <a href="/logout" class="btn btn-outline-danger btn-lg px-4">
                            <i class="bi bi-box-arrow-right me-2"></i>Выйти
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock text-primary" style="font-size: 4rem;"></i>
                        </div>

                        <h3 class="text-center mb-4">Возможности системы</h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                                    <div>
                                        <h6>Безопасная регистрация</h6>
                                        <p class="text-muted small">Защита данных и паролей</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                                    <div>
                                        <h6>Защита от ботов</h6>
                                        <p class="text-muted small">Yandex SmartCaptcha</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                                    <div>
                                        <h6>Безопасный вход</h6>
                                        <p class="text-muted small">По email или телефону</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--        --><?php //if (isset($_SESSION['user_id'])): ?>
<!--            <div class="row mt-5">-->
<!--                <div class="col-12">-->
<!--                    <div class="card border-primary">-->
<!--                        <div class="card-header bg-primary text-white">-->
<!--                            <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Статистика аккаунта</h5>-->
<!--                        </div>-->
<!--                        <div class="card-body">-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-4 text-center">-->
<!--                                    <div class="display-6 fw-bold text-primary">1</div>-->
<!--                                    <p class="text-muted">Аккаунт</p>-->
<!--                                </div>-->
<!--                                <div class="col-md-4 text-center">-->
<!--                                    <div class="display-6 fw-bold text-success">-->
<!--                                        --><?php //= date('d') ?>
<!--                                    </div>-->
<!--                                    <p class="text-muted">Дней в системе</p>-->
<!--                                </div>-->
<!--                                <div class="col-md-4 text-center">-->
<!--                                    <div class="display-6 fw-bold text-info">-->
<!--                                        --><?php //= isset($user) ? substr($user['email'] ?? '', 0, 1) : 'U' ?>
<!--                                    </div>-->
<!--                                    <p class="text-muted">Первая буква email</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        --><?php //endif; ?>
<!--    </div>-->

<?php $content = ob_get_clean(); ?>

<?php include dirname(__DIR__) . '/views/layouts/app.php'; ?>