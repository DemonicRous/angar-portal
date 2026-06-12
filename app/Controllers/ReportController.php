<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Trip;
use App\Models\Expense;
use App\Models\Vehicle;
use App\Models\Driver;

class ReportController extends Controller
{
    public function index(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/');
        }
        $this->render('reports/index');
    }

    /**
     * API-эндпоинт для получения статистики в формате JSON (для графиков).
     */
    public function apiStats(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            http_response_code(403);
            echo json_encode(['error' => 'Доступ запрещён']);
            return;
        }

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');

        // Статистика по рейсам
        $tripsData = Trip::getAllWithDetails(['date_from' => $dateFrom, 'date_to' => $dateTo]);
        $totalTrips = count($tripsData);
        $totalDistance = array_sum(array_column($tripsData, 'distance'));
        $totalSalary = array_sum(array_column($tripsData, 'calculated_salary'));

        // Затраты за период
        $expensesData = Expense::getAllWithVehicles(['date_from' => $dateFrom, 'date_to' => $dateTo]);
        $totalExpenses = array_sum(array_column($expensesData, 'amount'));

        // Данные по водителям (топ по пробегу)
        $driverStats = [];
        foreach ($tripsData as $trip) {
            $driverId = $trip['driver_id'];
            $driverName = $trip['driver_name'];
            if (!isset($driverStats[$driverId])) {
                $driverStats[$driverId] = ['name' => $driverName, 'distance' => 0, 'salary' => 0];
            }
            $driverStats[$driverId]['distance'] += $trip['distance'];
            $driverStats[$driverId]['salary'] += $trip['calculated_salary'];
        }
        $driverStats = array_values($driverStats);
        usort($driverStats, fn($a, $b) => $b['distance'] <=> $a['distance']);

        // Данные по автомобилям (затраты и пробег)
        $vehicleStats = [];
        foreach ($vehicles = Vehicle::all() as $vehicle) {
            $vehicleId = $vehicle['id'];
            $vehicleName = $vehicle['brand'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')';
            $vehicleTrips = array_filter($tripsData, fn($t) => $t['vehicle_id'] == $vehicleId);
            $distance = array_sum(array_column($vehicleTrips, 'distance'));
            $expenses = Expense::sumByVehicle($vehicleId, $dateFrom, $dateTo);
            $vehicleStats[] = [
                'name' => $vehicleName,
                'distance' => $distance,
                'expenses' => $expenses
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'summary' => [
                'total_trips' => $totalTrips,
                'total_distance' => $totalDistance,
                'total_salary' => $totalSalary,
                'total_expenses' => $totalExpenses,
                'balance' => $totalSalary - $totalExpenses // условный баланс
            ],
            'driver_stats' => $driverStats,
            'vehicle_stats' => $vehicleStats
        ]);
    }
}