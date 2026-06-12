<?php

namespace App\Models;

use App\Core\Model;

/**
 * Модель водителя.
 * Таблица drivers.
 */
class Driver extends Model
{
    protected static string $table = 'drivers';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['user_id', 'phone', 'tariff_per_km'];

    /**
     * Находит водителя по ID пользователя.
     *
     * @param int $userId
     * @return array|null
     */
    public static function findByUserId(int $userId): ?array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM drivers WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Возвращает всех водителей с данными пользователя.
     *
     * @return array
     */
    public static function getAllWithUsers(): array
    {
        $db = self::db();
        $stmt = $db->query("
            SELECT d.*, u.name, u.email
            FROM drivers d
            JOIN users u ON d.user_id = u.id
            ORDER BY u.name
        ");
        return $stmt->fetchAll();
    }
}