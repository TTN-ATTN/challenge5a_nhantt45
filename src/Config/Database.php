<?php
namespace App\Config;

use PDO;
use PDOException;

// Design pattern: Singleton 
class Database {
    private static $instance = null;
    private $connection;
    private function __construct() {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false, // Bắt buộc sử dụng prepared statements thực sự, không phải emulation
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage()); 
            die(json_encode(["error" => "Internal Server Error"])); 
        }
    }

    // Chống clone và unserialize để đảm bảo chỉ có một instance duy nhất
    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}