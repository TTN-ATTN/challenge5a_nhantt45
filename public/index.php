<?php
require_once __DIR__ . '/../autoload.php';

use App\Core\Session;
use App\Controllers\ErrorController;
use App\Controllers\FileController;

Session::start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (preg_match('#^/api/files/(.+)$#', $requestUri, $matches)) {
    $filename = $matches[1];
    $fileController = new FileController();
    $fileController->serveAvatar($filename);
    exit;
}

// Mảng 2 chiều map các route và controller 
$routes = [
    'GET' => [
        '/' => ['controller' => 'App\Controllers\HomeController', 'method' => 'index'],
        '/profile' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'showProfile'],
        '/login' => ['controller' => 'App\Controllers\AuthController', 'method' => 'showLogin'],
        '/logout' => ['controller' => 'App\Controllers\AuthController', 'method' => 'logout'],
    ],
    'POST' => [
        '/login' => ['controller' => 'App\Controllers\AuthController', 'method' => 'login'],
        '/profile' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'updateProfile'],
        '/delete-student' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'deleteStudent'],
        '/create-student' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'createStudent']
    ]
];
try {
    if (isset($routes[$requestMethod][$requestUri])) {
        $target = $routes[$requestMethod][$requestUri];
        $className = $target['controller'];
        $methodName = $target['method'];

        $controller = new $className();
        $controller->$methodName();
    } else {
        ErrorController::notFound();
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    ErrorController::serverError();
}
