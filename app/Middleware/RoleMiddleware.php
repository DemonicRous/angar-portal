<?php

namespace App\Middleware;

/**
 * Middleware для проверки прав доступа по роли.
 */
class RoleMiddleware
{
    /**
     * Проверяет, имеет ли текущий пользователь одну из разрешённых ролей.
     *
     * @param array $allowedRoles Массив имён ролей (например, ['admin', 'dispatcher'])
     * @return bool
     */
    public static function handle(array $allowedRoles): bool
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_name'])) {
            header('Location: /login');
            exit;
        }

        if (!in_array($_SESSION['role_name'], $allowedRoles)) {
            http_response_code(403);
            echo "Доступ запрещён. Недостаточно прав.";
            exit;
        }
        return true;
    }
}