<?php
namespace v1\models;

use PDO;

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getProducts($isAuthorized) {
        $query = "
            SELECT p.id, p.name, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
        ";
        if (!$isAuthorized) {
            $query .= " WHERE p.status = 1 AND c.status = 1";
        }
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function getAll()
    {
    }
}
