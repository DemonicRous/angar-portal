<?php

namespace App\Models;

use App\Core\Model;

/**
 * Модель автомобиля.
 * Таблица vehicles.
 */
class Vehicle extends Model
{
    protected static string $table = 'vehicles';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['brand', 'model', 'license_plate', 'current_mileage', 'status'];

    /**
     * Возвращает только доступные автомобили (не в рейсе, не на ремонте).
     *
     * @return array
     */
    public static function getAvailable(): array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM vehicles WHERE status = 'available' ORDER BY brand, model");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Обновляет текущий пробег автомобиля.
     *
     * @param int $vehicleId
     * @param int $newMileage
     * @return bool
     */
    public static function updateMileage(int $vehicleId, int $newMileage): bool
    {
        $db = self::db();
        $stmt = $db->prepare("UPDATE vehicles SET current_mileage = :mileage WHERE id = :id");
        return $stmt->execute([':mileage' => $newMileage, ':id' => $vehicleId]);
    }

    /**
     * Изменяет статус автомобиля.
     *
     * @param int $vehicleId
     * @param string $status
     * @return bool
     */
    public static function setStatus(int $vehicleId, string $status): bool
    {
        $allowed = ['available', 'on_trip', 'maintenance'];
        if (!in_array($status, $allowed)) {
            return false;
        }
        $db = self::db();
        $stmt = $db->prepare("UPDATE vehicles SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $vehicleId]);
    }
}