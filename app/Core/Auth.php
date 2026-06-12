<?php

namespace App\Core;

use App\Models\User;

/**
 * Класс для управления аутентификацией.
 */
class Auth
{
    /**
     * Проверяет, авторизован ли пользователь.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Возвращает ID текущего пользователя.
     *
     * @return int|null
     */
    public static function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Возвращает роль текущего пользователя (название).
     *
     * @return string|null
     */
    public static function userRole(): ?string
    {
        return $_SESSION['role_name'] ?? null;
    }

    /**
     * Проверяет, является ли текущий пользователь администратором.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::userRole() === 'admin';
    }

    /**
     * Проверяет, является ли текущий пользователь диспетчером.
     *
     * @return bool
     */
    public static function isDispatcher(): bool
    {
        return self::userRole() === 'dispatcher';
    }

    /**
     * Проверяет, является ли текущий пользователь водителем.
     *
     * @return bool
     */
    public static function isDriver(): bool
    {
        return self::userRole() === 'driver';
    }

    /**
     * Выполняет вход пользователя.
     *
     * @param int $userId
     * @param string $userName
     * @param string $roleName
     */
    public static function login(int $userId, string $userName, string $roleName): void
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['role_name'] = $roleName;
        session_regenerate_id(true);
    }

    /**
     * Выполняет выход пользователя.
     */
    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Проверяет, имеет ли текущий пользователь доступ к действию.
     *
     * @param string $permission Название права (например, 'trips.edit')
     * @return bool
     */
    public static function can(string $permission): bool
    {
        if (self::isAdmin()) {
            return true;
        }
        
        $role = self::userRole();
        $permissions = require CONFIG_PATH . '/auth.php';
        $rolePermissions = $permissions['permissions'][$role] ?? [];
        
        return in_array($permission, $rolePermissions) || in_array('*', $rolePermissions);
    }
}