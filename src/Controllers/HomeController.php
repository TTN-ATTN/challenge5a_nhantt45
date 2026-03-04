<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class HomeController
{
    public function index()
    {
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        $userModel = new User();
        $currentUser = $userModel->getUserById(Session::get('user_id'));
        if (!$currentUser) {
            Session::destroy();
            header('Location: /login');
            exit;
        }
        if ($currentUser['session_token'] !== Session::get('session_token')) {
            Session::destroy();
            header('Location: /login?error=concurrent');
            exit;
        }

        // Lấy và xóa Toast từ Session để truyền sang home.php
        $toastError = Session::get('toast_error');
        $toastSuccess = Session::get('toast_success');
        Session::set('toast_error', null);
        Session::set('toast_success', null);

        $allUsers = $userModel->getAllUsers();
        require_once __DIR__ . '/../Views/home.php';
    }
}
