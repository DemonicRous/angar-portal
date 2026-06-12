<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Expense;

class HomeController extends Controller
{
    public function index(array $params = []): void
    {
        if (!Auth::isLoggedIn()) {
            $this->redirect('/login');
        }

        $stats = [];

        if (Auth::isAdmin() || Auth::isDispatcher()) {
            $trips = Trip::all();
            $vehicles = Vehicle::all();
            $drivers = Driver::all();
            $expenses = Expense::all();

            $stats['totalTrips'] = count($trips);
            $stats['totalVehicles'] = count($vehicles);
            $stats['totalDrivers'] = count($drivers);
            $stats['totalExpenses'] = array_sum(array_column($expenses, 'amount'));

            // Все рейсы с деталями для таблицы последних рейсов
            $allTrips = Trip::getAllWithDetails([]);
            $stats['allTrips'] = $allTrips;
        } else {
            // Водитель – его рейсы
            $driver = Driver::findByUserId(Auth::userId());
            if ($driver) {
                $stats['myTrips'] = Trip::getAllWithDetails(['driver_id' => $driver['id']]);
            } else {
                $stats['myTrips'] = [];
            }
        }

        $this->render('home', ['stats' => $stats]);
    }
}