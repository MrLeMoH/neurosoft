<?php
namespace v1\models;

use PDO;

class User
{
    private $db;

    // Конструктор класса User, устанавливающий соединение с базой данных
    public function __construct() {
        // Получаем соединение с базой данных через статический метод getConnection
        $this->db = Database::getConnection();
    }

    // Метод для создания нового пользователя
    public function create($data)
    {
        // Подготавливаем SQL запрос для вставки нового пользователя в таблицу
        $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

        // Привязываем параметры (имя пользователя и хешированный пароль) к запросу
        $stmt->bind_param("ss", $data['username'], password_hash($data['password'], PASSWORD_BCRYPT));

        // Выполняем запрос и возвращаем результат
        return $stmt->execute();
    }

    // Метод для аутентификации пользователя
    public function authenticate($data)
    {
        // Подготавливаем запрос для получения пользователя по логину, исключая заблокированных
        $stmt = $this->db->prepare("SELECT * FROM users WHERE login = :login AND status != 0");

        // Привязываем параметр логина к запросу
        $stmt->bindValue(':login', $data['login'], PDO::PARAM_STR);

        // Выполняем запрос
        $stmt->execute();

        // Получаем данные пользователя
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверяем правильность пароля (хотя для безопасности лучше использовать password_verify)
        if ($user && ($data['password'] === $user['password'])) {
            // Запускаем сессию, если пароль верен
            session_start();
            // Сохраняем ID пользователя в сессии
            $_SESSION['user_id'] = $user['id'];
            return true;
        }

        // Возвращаем false, если аутентификация не прошла
        return false;
    }

    // Метод для получения информации о пользователе по его ID
    public function getUserById($userId)
    {
        // Подготавливаем запрос для получения пользователя по ID
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");

        // Привязываем ID пользователя к запросу
        $stmt->bindValue(':id', $userId, \PDO::PARAM_INT);

        // Выполняем запрос
        $stmt->execute();

        // Возвращаем результат запроса в виде ассоциативного массива
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Метод для проверки, авторизован ли пользователь
    public function isAuth(): bool
    {
        // Запускаем сессию
        session_start();
        // Проверяем, есть ли ID пользователя в сессии
        if (isset($_SESSION['user_id'])) {
            return true;  // Пользователь авторизован
        }
        return false; // Если ID нет — пользователь не авторизован
    }
}
