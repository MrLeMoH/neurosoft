<?php

namespace v1\models;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function isAuthorized($userId)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id AND status = 1");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch() !== false;
    }
}
