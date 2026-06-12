<?php

function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

loadEnv(__DIR__ . '/.env');

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'angar_portal';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    $migrationFiles = [
        __DIR__ . '/migrations/001_create_tables.sql',
        __DIR__ . '/migrations/002_seed_data.sql',
        __DIR__ . '/migrations/003_seed_demo_data.sql',
    ];

    foreach ($migrationFiles as $file) {
        if (!file_exists($file)) {
            echo "Файл не найден: " . basename($file) . "\n";
            continue;
        }
        $sql = file_get_contents($file);
        if ($sql === false) {
            echo "Ошибка чтения файла: " . basename($file) . "\n";
            continue;
        }
        // Удаляем BOM, если есть
        if (substr($sql, 0, 3) == "\xEF\xBB\xBF") {
            $sql = substr($sql, 3);
        }
        // Разбиваем на запросы по ";" вне кавычек
        $queries = [];
        $len = strlen($sql);
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $start = 0;
        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            if ($ch == "'" && !$inDoubleQuote) {
                $inSingleQuote = !$inSingleQuote;
            } elseif ($ch == '"' && !$inSingleQuote) {
                $inDoubleQuote = !$inDoubleQuote;
            } elseif ($ch == ';' && !$inSingleQuote && !$inDoubleQuote) {
                $query = trim(substr($sql, $start, $i - $start));
                if (!empty($query)) {
                    $queries[] = $query;
                }
                $start = $i + 1;
            }
        }
        // последний запрос без точки с запятой
        $lastQuery = trim(substr($sql, $start));
        if (!empty($lastQuery)) {
            $queries[] = $lastQuery;
        }

        foreach ($queries as $query) {
            if (empty($query)) continue;
            try {
                $pdo->exec($query);
            } catch (PDOException $e) {
                echo "Ошибка в файле " . basename($file) . ": " . $e->getMessage() . "\n";
                echo "Запрос: " . substr($query, 0, 200) . "\n";
                exit(1);
            }
        }
        echo "Выполнен: " . basename($file) . "\n";
    }

    echo "Миграция завершена успешно.\n";

} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}