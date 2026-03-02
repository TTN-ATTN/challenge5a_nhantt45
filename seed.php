<?php
require_once __DIR__ . '/autoload.php';

$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'app_db';
$rootPass = getenv('MYSQL_ROOT_PASSWORD');

if (!$rootPass) {
    die("Error. Can't find MYSQL_ROOT_PASSWORD environment variable.\n");
}

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", 'root', $rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false 
    ]);

    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");

    $schemaPath = __DIR__ . '/database/schema.sql';
    $schemaSql = file_get_contents($schemaPath);
    $pdo->exec($schemaSql);

    $hashedPassword = password_hash('test', PASSWORD_BCRYPT);

    $users = [
        ['ttn', $hashedPassword, 'Trần Trung Nhân', 'ttn@email.com', '0900000000', 'teacher'],
        ['teacher1', $hashedPassword, 'Giáo viên 1', 'teacher1@email.com', '0900000001', 'teacher'],
        ['teacher2', $hashedPassword, 'Giáo viên 2', 'teacher2@email.com', '0900000002', 'teacher'],
        ['student1', $hashedPassword, 'Sinh viên 1', 'student1@email.com', '0900000003', 'student'],
        ['student2', $hashedPassword, 'Sinh viên 2', 'student2@email.com', '0900000004', 'student']
    ];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, phone_number, role) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($users as $user) {
        $stmt->execute($user);
    }

    echo "Finish!\n";

} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}