<?php
require_once __DIR__ . '/../autoload.php';

use App\Core\Session;
use App\Controllers\ErrorController;
use App\Controllers\FileController;

Session::start();

try {
    $envPath = __DIR__ . '/../.env.example';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; // Bỏ qua comment
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                putenv(trim($name) . '=' . trim($value));
            }
        }
    }
    // var_dump(getenv());
} catch (Exception $e) {
    echo $e->getMessage();
    ErrorController::serverError();
}

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
        '/assignments' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'index'],
        '/challenges' => ['controller' => 'App\Controllers\ChallengeController', 'method' => 'index']
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
        '/assignments/unsubmit' => ['controller' => 'App\Controllers\AssignmentController', 'method' => 'unsubmit'],
        '/challenges/create' => ['controller' => 'App\Controllers\ChallengeController', 'method' => 'create'],
        '/challenges/solve' => ['controller' => 'App\Controllers\ChallengeController', 'method' => 'solve'],
        '/challenges/edit' => ['controller' => 'App\Controllers\ChallengeController', 'method' => 'edit'],
        '/challenges/delete' => ['controller' => 'App\Controllers\ChallengeController', 'method' => 'delete']
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
