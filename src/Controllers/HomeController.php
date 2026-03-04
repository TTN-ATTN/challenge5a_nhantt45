<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;
use App\Controllers\AuthController;

class HomeController{
    public function index(){
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        $userModel = new User();
        $currentUser = $userModel->getUserById(Session::get('user_id'));
        $allUsers = $userModel->getAllUsers();
        require_once __DIR__ . '/../Views/home.php';
    }
}
