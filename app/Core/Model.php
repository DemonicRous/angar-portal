<?php

namespace App\Core;

use PDO;

/**
 * Абстрактная базовая модель.
 * Предоставляет общие методы CRUD.
 */
abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];

    /**
     * Возвращает соединение с БД.
     *
     * @return PDO
     */
    protected static function db(): PDO
    {
        return Database::getConnection();
    }

    /**
     * Находит запись по первичному ключу.
     *
     * @param int $id
     * @return array|null
     */
    public static function find(int $id): ?array
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s = :id LIMIT 1", static::$table, static::$primaryKey);
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Возвращает все записи (с возможной сортировкой).
     *
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public static function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $sql = sprintf("SELECT * FROM %s ORDER BY %s %s", static::$table, $orderBy, $direction);
        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Создаёт новую запись.
     *
     * @param array $data
     * @return int|false ID вставленной записи или false при ошибке
     */
    public static function create(array $data): int|false
    {
        $filteredData = array_intersect_key($data, array_flip(static::$fillable));
        if (empty($filteredData)) {
            return false;
        }
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", static::$table, $columns, $placeholders);
        
        $stmt = self::db()->prepare($sql);
        if ($stmt->execute($filteredData)) {
            return (int)self::db()->lastInsertId();
        }
        return false;
    }

    /**
     * Обновляет запись по первичному ключу.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function update(int $id, array $data): bool
    {
        $filteredData = array_intersect_key($data, array_flip(static::$fillable));
        if (empty($filteredData)) {
            return false;
        }
        
        $setClause = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($filteredData)));
        $sql = sprintf("UPDATE %s SET %s WHERE %s = :id", static::$table, $setClause, static::$primaryKey);
        $filteredData['id'] = $id;
        
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($filteredData);
    }

    /**
     * Удаляет запись по первичному ключу.
     *
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $sql = sprintf("DELETE FROM %s WHERE %s = :id", static::$table, static::$primaryKey);
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}