<?php

namespace App\Http\Controllers;

use App\Utils\BaseController;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Utils\Config;

class AuthController extends BaseController
{
    public function showRegisterForm()
    {
        $userData = null;
        if ($this->getCurrentUserId()) {
            $userModel = new User();
            $userData = $userModel->findById($this->getCurrentUserId());
        }

        $this->view('register', [
            'title' => 'Cтраница регистрации',
            'user' => $userData,
            'isAuthenticated' => $this->getCurrentUserId() !== null,
            'currentYear' => date('Y')
        ]);
    }

    /**
     * Регистрация
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = new RegisterRequest();

            if (!$request->validate()) {
                // Возвращаем форму с ошибками
                $this->view('register', [
                    'errors' => $request->errors(),
                    'old' => $request->all()
                ]);
            }

            $userModel = new User();
            $userId = $userModel->create($request->validated());

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Авторизуем
            $_SESSION['user_id'] = $userId;

            // Редирект
            $this->addFlash('success', 'Регистрация успешна!');
            $this->redirect('/profile');

            return;
        }

        $this->view('register');
    }

    public function showLoginForm()
    {
        $this->view('login', [
            'title' => 'Авторизация',
            'pageTitle' => 'Вход в систему',
            'pageSubtitle' => 'Введите ваши учетные данные',
            'isLoginPage' => true,
            'errors' => $_SESSION['errors'] ?? [],
            'old' => $_SESSION['old'] ?? [],
            'captchaEnabled' => Config::get('captcha.yandex.enabled') ?? true,
            'siteKey' => Config::get('captcha.yandex.client_key') ?? ''
        ]);

        unset($_SESSION['errors'], $_SESSION['old']);
    }

    public function login()
    {
        $request = new LoginRequest();

        if (!$request->validate()) {
            $_SESSION['errors'] = $request->errors();
            $_SESSION['old'] = $request->all();

            $this->redirect('/login');
            return;
        }

        // Поиск пользователя по email или телефону
        $userModel = new User();
        $loginValue = $request->input('login');
        $user = $userModel;
        if (filter_var($loginValue, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->findByEmail($loginValue);
        } else {
            $user = $userModel->findByPhone($loginValue);
        }

        // Проверка пароля
        if (!$user || !password_verify($request->input('password'), $user['password_hash'])) {
            $_SESSION['errors'] = ['login' => ['Неверный логин или пароль']];
            $_SESSION['old'] = $request->all();

            $this->redirect('/login');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Авторизация
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;

        // Редирект на страницу профиля
        $this->addFlash('success', 'Добро пожаловать!');
        $this->redirect('/profile');
    }

    function logout() {
        session_destroy();
        session_start();
        $this->redirect('/login');
    }
}