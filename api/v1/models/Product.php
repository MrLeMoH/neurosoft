<?php
namespace v1\models;

use PDO;

class Product {
    private $db;

    // Конструктор класса Product, устанавливающий соединение с базой данных
    public function __construct() {
        // Получаем соединение с базой данных через метод getConnection
        $this->db = Database::getConnection();
    }

    // Метод для создания нового продукта
    public function create($body)
    {
        // Получаем данные из тела запроса
        $name = $body['name'];
        $categoryId = $body['categoryId'];
        $status = isset($body['status']) ? $body['status'] : 1;  // Если статус не передан, по умолчанию 1

        // Генерация SQL-запроса для вставки нового продукта
        $query = "INSERT INTO products (name, category_id, status) VALUES (:name, :category_id, :status)";

        // Подготовка и выполнение запроса с параметрами
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();

        // Возвращаем true, если строка была вставлена
        return $stmt->rowCount() > 0;
    }

    // Метод для получения продуктов по категории
    public function getByCategory($categoryId = null)
    {
        // Начальный запрос для получения всех продуктов с активным статусом
        $query = "SELECT * FROM products ";

        // Создаем объект User для проверки авторизации
        $user = new User();

        // Если пользователь не авторизован
        if (!$user->isAuth()) {
            // Если передан categoryId, добавляем его в запрос
            if ($categoryId) {
                $query .= 'WHERE status = 1  AND category_id = ' . $categoryId;
            }
        } else {
            // Если пользователь авторизован, просто добавляем проверку по категории, если она передана
            if ($categoryId) {
                $query .= ' WHERE category_id = ' . $categoryId;
            }
        }

        // Выполняем запрос
        $stmt = $this->db->query($query);

        // Возвращаем все найденные продукты
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Метод для получения продукта по его ID
    public function getById($id)
    {
        // Генерируем запрос для получения продукта по его ID
        $query = "SELECT * FROM products WHERE id = " . $id;

        // Проверяем авторизацию пользователя
        $user = new User();
        if (!$user->isAuth()) {
            // Если пользователь не авторизован, добавляем фильтр по статусу
            $query .= " AND status = 1";
        }

        // Выполняем запрос
        $stmt = $this->db->query($query);

        // Возвращаем данные продукта
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Метод для обновления данных продукта
    public function update($id, $data)
    {
        // Генерация запроса для обновления продукта
        $query = "UPDATE products SET ";
        $fields = [];
        $values = [];

        // Добавляем поля и их значения для обновления
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        // Заканчиваем запрос и добавляем ID продукта
        $query .= implode(", ", $fields);
        $query .= " WHERE id = ?";

        // Добавляем ID в параметры запроса
        $values[] = $id;

        // Подготавливаем и выполняем запрос
        $stmt = $this->db->prepare($query);

        // Возвращаем true, если обновление прошло успешно, иначе false
        if ($stmt->execute($values)) {
            return true; // Успех
        } else {
            return false; // Ошибка
        }
    }

    // Метод для удаления продукта
    public function delete($id)
    {
        // Приводим ID к целому числу
        $id = intval($id);

        // Генерация запроса для удаления продукта
        $query = "DELETE FROM products WHERE id = ?";

        // Подготавливаем запрос
        $stmt = $this->db->prepare($query);

        // Выполняем запрос и проверяем результат
        if ($stmt->execute([$id])) {
            echo "Delete successful"; // Отладочное сообщение
            return true;
        } else {
            // Показываем информацию об ошибке PDO в случае неудачи
            var_dump($stmt->errorInfo());
            return false;
        }
    }
}
