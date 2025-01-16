<?php
namespace v1\models;

use PDO;

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($body)
    {
        // Получаем данные из тела запроса
        $name = $body['name'];
        $status = isset($body['status']) ? $body['status'] : 1; // По умолчанию статус 1

        // Генерация SQL-запроса
        $query = "INSERT INTO categories (name, status) VALUES (:name, :status)";

        // Подготовка и выполнение запроса с параметрами
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAll()
    {
        $query = "SELECT * FROM categories WHERE status = 1";
        $stmt = $this->db->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $id = intval($id);
        $query = "SELECT * FROM categories WHERE status = 1 AND id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $query = "UPDATE categories SET ";
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $query .= implode(", ", $fields);
        $query .= " WHERE id = ?";

        // Добавляем id в параметры запроса
        $values[] = $id;

        // Подготавливаем и выполняем запрос
        $stmt = $this->db->prepare($query);

        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $id = intval($id);

        // Проверяем, есть ли связанные продукты
        $checkQuery = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([$id]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            return false; // Нельзя удалить категорию, у которой есть продукты
        }

        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->db->prepare($query);

        return $stmt->execute([$id]);
    }
}
