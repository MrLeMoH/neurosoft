<?php
// /controllers/AuthController.php

namespace v1\controllers;

use v1\models\User;

class AuthController
{
    public function authenticate($data)
    {
        $user = new User();
        if ($user->authenticate($data)) {
            return ['message' => 'User authenticated successfully'];
        }
        return ['error' => 'Invalid credentials'];
    }

    public function logout()
    {
        // Очистка сессии или токенов
        session_start();
        session_destroy();
        return ['message' => 'User logged out successfully'];
    }

    public function getCurrentUser()
    {
        session_start();
        if (isset($_SESSION['user_id'])) {
            $user = new User();
            $userData = $user->getUserById($_SESSION['user_id']);
            return ['username' => $userData['username']];
        }
        return ['error' => 'User not authenticated'];
    }
}


