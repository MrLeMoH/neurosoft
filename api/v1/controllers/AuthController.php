<?php
namespace v1\controllers;

use v1\models\User;

class AuthController
{
    /**
     * Аутентификация пользователя.
     * Проверяет правильность введенных данных и устанавливает сессию, если данные верны.
     *
     * @param array $data - Данные для аутентификации (например, логин и пароль)
     * @return array - Возвращает сообщение о результатах аутентификации
     */
    public function authenticate($data)
    {
        // Создаем объект модели User для аутентификации
        $user = new User();

        // Если аутентификация прошла успешно
        if ($user->authenticate($data)) {
            return ['message' => 'User authenticated successfully'];
        }

        // Если аутентификация не удалась
        return ['error' => 'Invalid credentials'];
    }

    /**
     * Выход пользователя.
     * Очищает текущую сессию и удаляет токены (если используются).
     *
     * @return array - Сообщение о том, что пользователь успешно вышел
     */
    public function logout()
    {
        // Запускаем сессию и уничтожаем её
        session_start();
        session_destroy();

        // Возвращаем сообщение о завершении выхода
        return ['message' => 'User logged out successfully'];
    }

    /**
     * Получение данных текущего пользователя.
     * Проверяет, аутентифицирован ли пользователь и возвращает его данные.
     *
     * @return array - Возвращает данные пользователя или сообщение об ошибке, если пользователь не аутентифицирован
     */
    public function getCurrentUser()
    {
        // Запускаем сессию
        session_start();

        // Если пользователь аутентифицирован (есть ID в сессии)
        if (isset($_SESSION['user_id'])) {
            // Создаем объект модели User и получаем данные пользователя
            $user = new User();
            $userData = $user->getUserById($_SESSION['user_id']);

            // Возвращаем логин пользователя
            return ['login' => $userData['login']];
        }

        // Если пользователь не аутентифицирован
        return ['error' => 'User not authenticated'];
    }
}

