<?php

namespace App\Http\Controllers;

use App\Utils\BaseController;
use App\Models\User;

class ProfileController extends BaseController
{
    public function show()
    {
        $userModel = new User();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION['user_id'];

        $user = $userModel->findById($id);

        if (!$user) {
            $this->redirect('/404');
        }

        $this->view('profile', ['user' => $user]);
    }
}