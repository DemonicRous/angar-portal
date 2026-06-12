<?php

namespace App\Models;

use App\Core\Model;

/**
 * Модель затрат на транспортные средства.
 * Таблица expenses.
 */
class Expense extends Model
{
    protected static string $table = 'expenses';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['vehicle_id', 'date', 'amount', 'category', 'comment'];

    /**
     * Возвращает список затрат с названием автомобиля.
     *
     * @param array $filters
     * @return array
     */
    public static function getAllWithVehicles(array $filters = []): array
    {
        $db = self::db();
        $sql = "
            SELECT e.*, v.brand, v.model, v.license_plate
            FROM expenses e
            JOIN vehicles v ON e.vehicle_id = v.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND e.date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        if (!empty($filters['vehicle_id'])) {
            $sql .= " AND e.vehicle_id = :vehicle_id";
            $params[':vehicle_id'] = $filters['vehicle_id'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND e.category = :category";
            $params[':category'] = $filters['category'];
        }

        $sql .= " ORDER BY e.date DESC, e.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Возвращает сумму затрат по автомобилю за период.
     *
     * @param int $vehicleId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return float
     */
    public static function sumByVehicle(int $vehicleId, ?string $dateFrom = null, ?string $dateTo = null): float
    {
        $db = self::db();
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE vehicle_id = :vehicle_id";
        $params = [':vehicle_id' => $vehicleId];
        if ($dateFrom) {
            $sql .= " AND date >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND date <= :date_to";
            $params[':date_to'] = $dateTo;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (float)$stmt->fetch()['total'];
    }
}