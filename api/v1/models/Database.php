<?php
namespace v1\models;

use PDO;
use PDOException;

class Database {
    // Статическая переменная для хранения единственного экземпляра подключения
    private static $instance = null;

    // Параметры подключения к базе данных
    private static $host = 'localhost';
    private static $dbName = 'neurosoft';
    private static $user = 'root';
    private static $pass = '';

    // Приватный конструктор, чтобы предотвратить создание экземпляра класса
    private function __construct() {}

    // Приватный метод __clone, чтобы предотвратить клонирование объекта
    private function __clone() {}

    /**
     * Метод для получения подключения к базе данных
     * Если соединение уже существует, возвращает его, если нет - создает новое
     */
    public static function getConnection() {
        // Проверяем, существует ли уже подключение
        if (self::$instance === null) {
            try {
                // Если подключения нет, создаем новое подключение к базе данных
                self::$instance = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8",  // Строка подключения
                    self::$user,  // Имя пользователя
                    self::$pass   // Пароль
                );
                // Устанавливаем атрибуты для обработки ошибок
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // В случае ошибки подключения выводим сообщение
                die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }
        // Возвращаем существующее или только что созданное подключение
        return self::$instance;
    }
}

