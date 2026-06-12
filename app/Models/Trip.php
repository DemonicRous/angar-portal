<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Модель рейса.
 * Таблица trips.
 */
class Trip extends Model
{
    protected static string $table = 'trips';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'driver_id', 'vehicle_id', 'date', 'start_location', 'end_location',
        'client_organization', 'start_mileage', 'end_mileage', 'distance',
        'calculated_salary', 'status'
    ];

    /**
     * Создаёт новый рейс со статусом 'planned'.
     *
     * @param array $data
     * @return int|false
     */
    public static function create(array $data): int|false
    {
        $data['status'] = 'planned';
        // Убедимся, что начальный пробег корректен
        if (isset($data['start_mileage'])) {
            $data['start_mileage'] = (int)$data['start_mileage'];
        }
        return parent::create($data);
    }

    /**
     * Завершает рейс: рассчитывает расстояние, зарплату, обновляет пробег авто.
     *
     * @param int $tripId
     * @param int $endMileage
     * @return bool
     */
    public static function complete(int $tripId, int $endMileage): bool
    {
        $db = self::db();
        $trip = self::find($tripId);
        if (!$trip || $trip['status'] !== 'planned') {
            return false;
        }

        $start = (int)$trip['start_mileage'];
        if ($endMileage <= $start) {
            return false; // некорректный пробег
        }
        $distance = $endMileage - $start;

        // Получаем тариф водителя
        $driver = Driver::find($trip['driver_id']);
        if (!$driver) {
            return false;
        }
        $salary = $distance * $driver['tariff_per_km'];

        // Обновляем запись рейса
        $stmt = $db->prepare("
            UPDATE trips 
            SET end_mileage = :end, distance = :dist, calculated_salary = :salary, status = 'completed'
            WHERE id = :id AND status = 'planned'
        ");
        $result = $stmt->execute([
            ':end' => $endMileage,
            ':dist' => $distance,
            ':salary' => $salary,
            ':id' => $tripId
        ]);

        if ($result) {
            // Обновляем пробег автомобиля
            Vehicle::updateMileage($trip['vehicle_id'], $endMileage);
            // Меняем статус авто на available
            Vehicle::setStatus($trip['vehicle_id'], 'available');
        }
        return $result;
    }

    /**
     * Возвращает список рейсов с фильтрацией и связанными данными.
     *
     * @param array $filters
     * @return array
     */
    public static function getAllWithDetails(array $filters = []): array
    {
        $db = self::db();
        $sql = "
            SELECT t.*, 
                   u.name as driver_name, 
                   v.brand, v.model, v.license_plate,
                   d.tariff_per_km
            FROM trips t
            JOIN drivers d ON t.driver_id = d.id
            JOIN users u ON d.user_id = u.id
            JOIN vehicles v ON t.vehicle_id = v.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql .= " AND t.date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND t.date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        if (!empty($filters['driver_id'])) {
            $sql .= " AND t.driver_id = :driver_id";
            $params[':driver_id'] = $filters['driver_id'];
        }
        if (!empty($filters['vehicle_id'])) {
            $sql .= " AND t.vehicle_id = :vehicle_id";
            $params[':vehicle_id'] = $filters['vehicle_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY t.date DESC, t.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Возвращает тариф водителя для указанного рейса (через driver_id).
     *
     * @param int $tripId
     * @return float|null
     */
    public static function getDriverTariffForTrip(int $tripId): ?float
    {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT d.tariff_per_km 
            FROM trips t 
            JOIN drivers d ON t.driver_id = d.id 
            WHERE t.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $tripId]);
        $result = $stmt->fetch();
        return $result ? (float)$result['tariff_per_km'] : null;
    }
}