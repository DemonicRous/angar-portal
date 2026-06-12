<?php

namespace App\Middleware;

/**
 * Middleware для проверки аутентификации пользователя.
 */
class AuthMiddleware
{
    /**
     * Проверяет, авторизован ли пользователь.
     * Если нет – перенаправляет на страницу входа.
     */
    public static function handle(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        return true;
    }
}