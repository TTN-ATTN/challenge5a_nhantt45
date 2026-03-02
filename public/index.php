<?php
require_once __DIR__ . '/../autoload.php';

use App\Core\Session;
use App\Controllers\AuthController;

Session::start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($requestUri) {
    case '/':
        if (!Session::get('user_id')) {
            header('Location: /login');
            exit;
        }
        $fullName = Session::get('full_name', 'User');
        $role = Session::get('role', 'student');
        require_once __DIR__ . '/../src/Views/home.php';
        break;
    case '/login':
        $authController = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $authController->showLogin();
        } else {
            $authController->login();
        }
        break;
    case '/logout':
        $authController = new AuthController();
        $authController->logout();
        break;
    default:
        http_response_code(404);
        echo "Page not found";
        echo "Route: " . htmlspecialchars($requestUri);
        break;
}

?>
