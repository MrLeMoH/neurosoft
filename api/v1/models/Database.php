<?php
namespace v1\models;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private static $host = 'localhost';
    private static $dbName = 'neurosoft';
    private static $user = 'root';
    private static $pass = '';

    private function __construct() {}
    private function __clone() {}

    public static function getConnection() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8",
                    self::$user,
                    self::$pass
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }
        return self::$instance;
    }
}
