<?php

namespace App\Http\Controllers;

use App\Utils\BaseController;
use App\Models\User;

class HomeController extends BaseController
{
    /**
     * Главная страница
     */
    public function index()
    {
        $userData = null;
        if ($this->getCurrentUserId()) {
            $userModel = new User();
            $userData = $userModel->findById($this->getCurrentUserId());
        }

        $this->view('home', [
            'title' => 'Главная страница',
            'user' => $userData,
            'isAuthenticated' => $this->getCurrentUserId() !== null,
            'currentYear' => date('Y')
        ]);
    }
}