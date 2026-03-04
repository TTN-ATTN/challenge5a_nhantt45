<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class AuthController{
    public function login(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error= "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu";
                return;
            }

            $userModel = new User();
            $user = $userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                Session::set('user_id', $user['id']);
                Session::set('role', $user['role']);
                Session::set('username', $user['username']);
                Session::set('full_name', $user['full_name']);
                header('Location: /');
                exit;
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng";
                require_once __DIR__ . '/../Views/login.php';
            }
        } else {
            require_once __DIR__ . '/../Views/login.php';
        }
    }

    public function showLogin(){
        require_once __DIR__ . '/../Views/login.php';
    }

    public function logout(){
        Session::destroy();
        header('Location: /login');
        exit;
    }
}

?>