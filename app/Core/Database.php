<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Класс для работы с базой данных (PDO Singleton).
 */
class Database
{
    private static ?PDO $connection = null;

    /**
     * Возвращает соединение с БД.
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            $dsn = sprintf(
                "%s:host=%s;dbname=%s;charset=%s",
                $config['driver'],
                $config['host'],
                $config['database'],
                $config['charset']
            );
            
            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                die("Ошибка подключения к базе данных: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}