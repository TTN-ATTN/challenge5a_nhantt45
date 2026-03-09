<?php
require_once __DIR__ . '/../autoload.php';

use App\Core\Session;
use App\Controllers\ErrorController;
use App\Controllers\FileController;

Session::start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

if (preg_match('#^/api/avatars/(.+)$#', $requestUri, $matches)) {
    $filename = $matches[1];
    $fileController = new FileController();
    $fileController->serveAvatar($filename);
    exit;
}

if (preg_match('#^/api/assignments/(.+)$#', $requestUri, $matches)) {
    $filename = $matches[1];
    $fileController = new FileController();
    $fileController->serveAssignmentFile($filename);
    exit;
}

// Mảng 2 chiều map các route và controller 
$routes = [
    'GET' => [
        '/' => ['controller' => 'App\Controllers\HomeController', 'method' => 'index'],
        '/profile' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'showProfile'],
        '/login' => ['controller' => 'App\Controllers\AuthController', 'method' => 'showLogin'],
        '/logout' => ['controller' => 'App\Controllers\AuthController', 'method' => 'logout'],
        '/create-student' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'showCreateStudentForm'],
        '/assignments' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'index']
    ],
    'POST' => [
        '/login' => ['controller' => 'App\Controllers\AuthController', 'method' => 'login'],
        '/profile' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'updateProfile'],
        '/delete-student' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'deleteStudent'],
        '/create-student' => ['controller' => 'App\Controllers\ProfileController', 'method' => 'createStudent'],
        '/send-message' => ['controller' => 'App\Controllers\MessageController', 'method' => 'store'],
        '/edit-message' => ['controller' => 'App\Controllers\MessageController', 'method' => 'update'],
        '/delete-message' => ['controller' => 'App\Controllers\MessageController', 'method' => 'delete'],
        '/assignments/create' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'create'],
        '/assignments/submit' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'submit'],
        '/assignments/grade' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'grade'],
        '/assignments/delete' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'deleteAssignment'],
        '/assignments/unsubmit' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'unsubmit']
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
    // echo $e->getMessage();
    error_log($e->getMessage());
    ErrorController::serverError();
}
