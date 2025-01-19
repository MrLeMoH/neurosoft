<?php
namespace v1\models;

use PDO;

class Category {
    private $db;

    // Конструктор класса, получает соединение с базой данных
    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Создание новой категории
     *
     * @param array $body - Данные категории (name, status)
     * @return bool - Возвращает true, если категория была создана, иначе false
     */
    public function create($body)
    {
        // Получаем данные из тела запроса
        $name = $body['name'];
        $status = isset($body['status']) ? $body['status'] : 1; // По умолчанию статус 1

        // Генерация SQL-запроса для вставки новой категории
        $query = "INSERT INTO categories (name, status) VALUES (:name, :status)";

        // Подготовка и выполнение запроса с параметрами
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0; // Возвращаем true, если добавлена хотя бы одна строка
    }

    /**
     * Получить все категории
     *
     * @return array - Массив всех категорий
     */
    public function getAll()
    {
        $query = "SELECT * FROM categories ";

        // Создаем объект пользователя, чтобы проверить, авторизован ли пользователь
        $user = new User();
        if (!$user->isAuth()) {
            // Если пользователь не авторизован, показываем только активные категории
            $query .= " WHERE status = 1";
        }

        // Выполняем запрос и возвращаем результат
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Получить категорию по ID
     *
     * @param int $id - ID категории
     * @return array - Категория по указанному ID
     */
    public function getById($id)
    {
        $id = intval($id); // Преобразуем ID в целое число для безопасности

        // Генерация запроса для получения категории по ID
        $query = "SELECT * FROM categories WHERE id = :id";

        // Создаем объект пользователя, чтобы проверить, авторизован ли пользователь
        $user = new User();
        if (!$user->isAuth()) {
            // Если пользователь не авторизован, показываем только активные категории
            $query .= " AND status = 1";
        }

        // Подготавливаем и выполняем запрос
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Возвращаем результат запроса
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Обновить данные категории
     *
     * @param int $id - ID категории
     * @param array $data - Данные для обновления
     * @return bool - Возвращает true, если обновление прошло успешно, иначе false
     */
    public function update($id, $data)
    {
        // Генерация запроса для обновления категории
        $query = "UPDATE categories SET ";
        $fields = [];
        $values = [];

        // Формируем поля и значения для обновления
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        // Строим финальный запрос
        $query .= implode(", ", $fields);
        $query .= " WHERE id = ?";

        // Добавляем ID в параметры запроса
        $values[] = $id;

        // Подготавливаем и выполняем запрос
        $stmt = $this->db->prepare($query);

        return $stmt->execute($values); // Возвращаем результат выполнения запроса
    }

    /**
     * Удалить категорию
     *
     * @param int $id - ID категории для удаления
     * @return bool - Возвращает true, если категория была удалена, иначе false
     */
    public function delete($id)
    {
        $id = intval($id); // Преобразуем ID в целое число для безопасности

        // Проверяем, есть ли связанные с категорией продукты
        $checkQuery = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Если есть связанные продукты, удаление невозможно
        if ($result['count'] > 0) {
            return false; // Нельзя удалить категорию, у которой есть продукты
        }

        // Генерация запроса для удаления категории
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($query);

        // Выполняем запрос на удаление
        return $stmt->execute([$id]); // Возвращаем true, если категория была удалена
    }
}

