<?php

/**
 * Точка входа приложения.
 * Загружает автозагрузчик, инициализирует сессию, переменные окружения,
 * создаёт маршрутизатор и диспетчеризует запрос.
 */

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constants.php';

// Загрузка переменных окружения из .env (если файл существует)
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Пропускаем комментарии
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            putenv(trim($parts[0]) . '=' . trim($parts[1]));
        }
    }
}

use App\Core\Router;

$router = new Router();

// Маршруты
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');

// Рейсы
$router->add('GET', '/trips', 'TripController@index');
$router->add('GET', '/trips/create', 'TripController@create');
$router->add('POST', '/trips', 'TripController@store');
$router->add('GET', '/trips/{id}/edit', 'TripController@edit');
$router->add('POST', '/trips/{id}', 'TripController@update');
$router->add('POST', '/trips/{id}/complete', 'TripController@complete');
$router->add('POST', '/trips/{id}/delete', 'TripController@delete');

// Автопарк
$router->add('GET', '/vehicles', 'VehicleController@index');
$router->add('GET', '/vehicles/create', 'VehicleController@create');
$router->add('POST', '/vehicles', 'VehicleController@store');
$router->add('GET', '/vehicles/{id}/edit', 'VehicleController@edit');
$router->add('POST', '/vehicles/{id}', 'VehicleController@update');
$router->add('POST', '/vehicles/{id}/delete', 'VehicleController@delete');

// Водители (управление профилями)
$router->add('GET', '/drivers', 'DriverController@index');
$router->add('GET', '/drivers/create', 'DriverController@create');
$router->add('POST', '/drivers', 'DriverController@store');
$router->add('GET', '/drivers/{id}/edit', 'DriverController@edit');
$router->add('POST', '/drivers/{id}', 'DriverController@update');
$router->add('POST', '/drivers/{id}/delete', 'DriverController@delete');

// Затраты
$router->add('GET', '/expenses', 'ExpenseController@index');
$router->add('GET', '/expenses/create', 'ExpenseController@create');
$router->add('POST', '/expenses', 'ExpenseController@store');
$router->add('GET', '/expenses/{id}/edit', 'ExpenseController@edit');
$router->add('POST', '/expenses/{id}', 'ExpenseController@update');
$router->add('POST', '/expenses/{id}/delete', 'ExpenseController@delete');

// Отчёты и аналитика
$router->add('GET', '/reports', 'ReportController@index');
$router->add('GET', '/api/stats', 'ReportController@apiStats');

// API для получения тарифа водителя по ID рейса
$router->add('GET', '/api/trip-driver-tariff/{tripId}', 'ApiController@getDriverTariffForTrip');

// Обслуживание статических файлов (CSS, JS) – .htaccess обрабатывает
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);