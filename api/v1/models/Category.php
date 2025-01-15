<?php

namespace v1\models;

use Models\PDO;

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getActiveCategories()
    {
        $stmt = $this->db->query("SELECT id, name FROM categories WHERE status = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
