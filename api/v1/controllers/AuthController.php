<?php
namespace Controllers;

use Models\User;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function authenticate() {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $this->userModel->authenticate($data['login'], $data['password']);
        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['message' => 'Authenticated successfully']);
        } else {
            echo json_encode(['error' => 'Invalid login or password']);
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(['message' => 'Logged out successfully']);
    }

    public function getCurrentUser() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            $user = $this->userModel->getById($_SESSION['user_id']);
            echo json_encode(['user' => $user]);
        } else {
            echo json_encode(['error' => 'No user logged in']);
        }
    }
}
