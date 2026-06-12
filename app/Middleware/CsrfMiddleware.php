<?php

namespace App\Middleware;

/**
 * Middleware для проверки CSRF-токена в POST-запросах.
 */
class CsrfMiddleware
{
    /**
     * Генерирует новый CSRF-токен и сохраняет его в сессии.
     *
     * @return string
     */
    public static function generateToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Проверяет, совпадает ли токен из формы с токеном в сессии.
     * В случае несовпадения завершает запрос с ошибкой.
     */
    public static function validate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                http_response_code(403);
                die("CSRF token validation failed.");
            }
        }
    }

    /**
     * Возвращает HTML-поле для вставки в форму.
     *
     * @return string
     */
    public static function getTokenField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::generateToken() . '">';
    }
}