<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Trip;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Expense; // не используется напрямую, но может понадобиться
use App\Middleware\CsrfMiddleware;

/**
 * Контроллер рейсов.
 */
class TripController extends Controller
{
    /**
     * Список рейсов с фильтрацией.
     */
    public function index(array $params = []): void
    {
        if (!Auth::isLoggedIn()) {
            $this->redirect('/login');
        }

        // Водитель видит только свои рейсы
        $filters = [];
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $driver = Driver::findByUserId(Auth::userId());
            if (!$driver) {
                $this->setFlash('error', 'Профиль водителя не найден');
                $this->redirect('/');
            }
            $filters['driver_id'] = $driver['id'];
        } else {
            $filters = [
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'driver_id' => $_GET['driver_id'] ?? '',
                'vehicle_id' => $_GET['vehicle_id'] ?? '',
                'status' => $_GET['status'] ?? ''
            ];
        }

        $trips = Trip::getAllWithDetails($filters);
        $drivers = Driver::getAllWithUsers();
        $vehicles = Vehicle::all();

        $this->render('trips/index', [
            'trips' => $trips,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'filters' => $filters
        ]);
    }

    /**
     * Форма создания рейса.
     */
    public function create(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/trips');
        }

        $drivers = Driver::getAllWithUsers();
        $vehicles = Vehicle::getAvailable();
        $this->render('trips/create', [
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'csrf_token' => CsrfMiddleware::generateToken()
        ]);
    }

    /**
     * Сохранение нового рейса.
     */
    public function store(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/trips');
        }

        CsrfMiddleware::validate();

        $data = [
            'driver_id' => $_POST['driver_id'] ?? null,
            'vehicle_id' => $_POST['vehicle_id'] ?? null,
            'date' => $_POST['date'] ?? '',
            'start_location' => $_POST['start_location'] ?? '',
            'end_location' => $_POST['end_location'] ?? '',
            'client_organization' => $_POST['client_organization'] ?? '',
            'start_mileage' => $_POST['start_mileage'] ?? 0,
        ];

        // Простая валидация
        if (!$data['driver_id'] || !$data['vehicle_id'] || !$data['date'] || !$data['start_location'] || !$data['end_location']) {
            $this->setFlash('error', 'Заполните все обязательные поля');
            $this->redirect('/trips/create');
        }

        $tripId = Trip::create($data);
        if ($tripId) {
            // Обновляем статус автомобиля на "в рейсе"
            Vehicle::setStatus($data['vehicle_id'], 'on_trip');
            $this->setFlash('success', 'Рейс успешно создан');
        } else {
            $this->setFlash('error', 'Ошибка при создании рейса');
        }
        $this->redirect('/trips');
    }

    /**
     * Форма редактирования рейса (только planned).
     */
    public function edit(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/trips');
        }

        $tripId = (int)($params['id'] ?? 0);
        $trip = Trip::find($tripId);
        if (!$trip || $trip['status'] !== 'planned') {
            $this->setFlash('error', 'Редактирование недоступно');
            $this->redirect('/trips');
        }

        $drivers = Driver::getAllWithUsers();
        $vehicles = Vehicle::all();
        $this->render('trips/edit', [
            'trip' => $trip,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'csrf_token' => CsrfMiddleware::generateToken()
        ]);
    }

    /**
     * Обновление рейса.
     */
    public function update(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/trips');
        }

        CsrfMiddleware::validate();

        $tripId = (int)($params['id'] ?? 0);
        $trip = Trip::find($tripId);
        if (!$trip || $trip['status'] !== 'planned') {
            $this->setFlash('error', 'Редактирование недоступно');
            $this->redirect('/trips');
        }

        $data = [
            'driver_id' => $_POST['driver_id'] ?? $trip['driver_id'],
            'vehicle_id' => $_POST['vehicle_id'] ?? $trip['vehicle_id'],
            'date' => $_POST['date'] ?? $trip['date'],
            'start_location' => $_POST['start_location'] ?? $trip['start_location'],
            'end_location' => $_POST['end_location'] ?? $trip['end_location'],
            'client_organization' => $_POST['client_organization'] ?? $trip['client_organization'],
            'start_mileage' => $_POST['start_mileage'] ?? $trip['start_mileage'],
        ];

        $oldVehicleId = $trip['vehicle_id'];
        if (Trip::update($tripId, $data)) {
            // Если сменили авто, обновляем статусы
            if ($oldVehicleId != $data['vehicle_id']) {
                Vehicle::setStatus($oldVehicleId, 'available');
                Vehicle::setStatus($data['vehicle_id'], 'on_trip');
            }
            $this->setFlash('success', 'Рейс обновлён');
        } else {
            $this->setFlash('error', 'Ошибка обновления');
        }
        $this->redirect('/trips');
    }

    /**
     * Завершение рейса (AJAX или обычный POST).
     */
    public function complete(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/trips');
        }

        $tripId = (int)($params['id'] ?? 0);
        $endMileage = (int)($_POST['end_mileage'] ?? 0);

        if (Trip::complete($tripId, $endMileage)) {
            $this->setFlash('success', 'Рейс завершён, зарплата рассчитана');
        } else {
            $this->setFlash('error', 'Не удалось завершить рейс: проверьте пробег или статус');
        }
        $this->redirect('/trips');
    }

    /**
     * Удаление рейса (только planned).
     */
    public function delete(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/trips');
        }

        $tripId = (int)($params['id'] ?? 0);
        $trip = Trip::find($tripId);
        if (!$trip || $trip['status'] !== 'planned') {
            $this->setFlash('error', 'Удаление невозможно');
            $this->redirect('/trips');
        }

        // Возвращаем автомобиль в доступные
        Vehicle::setStatus($trip['vehicle_id'], 'available');
        if (Trip::delete($tripId)) {
            $this->setFlash('success', 'Рейс удалён');
        } else {
            $this->setFlash('error', 'Ошибка удаления');
        }
        $this->redirect('/trips');
    }
}