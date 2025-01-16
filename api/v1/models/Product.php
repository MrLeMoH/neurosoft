<?php
namespace v1\models;

use PDO;

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
    public function create($body)
    {
        // Получаем данные из тела запроса
        $name = $body['name'];
        $categoryId = $body['categoryId'];
        $status = isset($body['status']) ? $body['status'] : 1;  // Если статус не передан, по умолчанию 1

        // Генерация SQL-запроса
        $query = "INSERT INTO products (name, category_id, status) VALUES (:name, :category_id, :status)";

        // Подготовка и выполнение запроса с параметрами
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getByCategory($categoryId = null)
    {
        $query = "SELECT * FROM products WHERE status = 1 ";
        if($categoryId)
        {
            $query .= ' AND category_id = ' . $categoryId;
        }
        $stmt = $this->db->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id)
    {
        $query = "SELECT * FROM products WHERE status = 1  AND id = " . $id;
        $stmt = $this->db->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $query = "UPDATE products SET ";
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

        if ($stmt->execute($values)) {
            return true; // Успех
        } else {
            return false; // Ошибка
        }
    }

    public function delete($id)
    {
        $id = intval($id);
        $query = "DELETE FROM products WHERE id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute([$id])) {
            echo "Delete successful"; // Отладочное сообщение
            return true;
        } else {
            var_dump($stmt->errorInfo()); // Показывает информацию об ошибке PDO
            return false;
        }
    }




}
