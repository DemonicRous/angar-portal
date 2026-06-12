<?php

namespace App\Middleware;

/**
 * Middleware для страниц, доступных только гостям (неавторизованным).
 */
class GuestMiddleware
{
    /**
     * Если пользователь уже авторизован – перенаправляет на главную.
     */
    public static function handle(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }
}