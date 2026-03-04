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
                $sessionToken = bin2hex(random_bytes(32));
                $userModel->updateSessionToken($user['id'], $sessionToken);
                Session::set('user_id', $user['id']);
                Session::set('role', $user['role']);
                Session::set('username', $user['username']);
                Session::set('full_name', $user['full_name']);
                Session::set('session_token', $sessionToken);
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
        $error = $_GET['error'] ?? null;
         if($error === 'concurrent'){
            $error = "Tài khoản của bạn đã được đăng nhập ở nơi khác. Vui lòng đăng nhập lại.";
        }
        require_once __DIR__ . '/../Views/login.php';
    }

    public function logout(){
        Session::destroy();
        header('Location: /login');
        exit;
    }
}

?>