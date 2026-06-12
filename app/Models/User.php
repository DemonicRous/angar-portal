<?php

namespace App\Models;

use App\Core\Model;
use App\Models\Role;

/**
 * Модель пользователя.
 * Связана с таблицей users.
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['name', 'email', 'password', 'role_id'];

    /**
     * Проверяет учётные данные и возвращает данные пользователя при успехе.
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public static function authenticate(string $email, string $password): ?array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Загружаем роль
            $role = Role::find($user['role_id']);
            if ($role) {
                $user['role_name'] = $role['name'];
            }
            return $user;
        }
        return null;
    }

    /**
     * Получает роль пользователя.
     *
     * @return array|null
     */
    public function getRole(): ?array
    {
        return Role::find($this->role_id ?? 0);
    }

    /**
     * Получает профиль водителя (если пользователь – водитель).
     *
     * @return array|null
     */
    public function getDriverProfile(): ?array
    {
        if (($this->role_id ?? 0) == 3) { // role_id = 3 для водителя
            return Driver::findByUserId($this->id ?? 0);
        }
        return null;
    }
}