<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Vehicle;
use App\Middleware\CsrfMiddleware;

class VehicleController extends Controller
{
    public function index(array $params = []): void
    {
        if (!Auth::isLoggedIn()) {
            $this->redirect('/login');
        }
        $vehicles = Vehicle::all();
        $this->render('vehicles/index', ['vehicles' => $vehicles]);
    }

    public function create(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/vehicles');
        }
        $this->render('vehicles/create', ['csrf_token' => CsrfMiddleware::generateToken()]);
    }

    public function store(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/vehicles');
        }
        CsrfMiddleware::validate();

        $data = [
            'brand' => $_POST['brand'] ?? '',
            'model' => $_POST['model'] ?? '',
            'license_plate' => $_POST['license_plate'] ?? '',
            'current_mileage' => $_POST['current_mileage'] ?? 0,
            'status' => $_POST['status'] ?? 'available'
        ];

        if (!$data['brand'] || !$data['model'] || !$data['license_plate']) {
            $this->setFlash('error', 'Заполните марку, модель и госномер');
            $this->redirect('/vehicles/create');
        }

        if (Vehicle::create($data)) {
            $this->setFlash('success', 'Автомобиль добавлен');
        } else {
            $this->setFlash('error', 'Ошибка добавления');
        }
        $this->redirect('/vehicles');
    }

    public function edit(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/vehicles');
        }
        $id = (int)($params['id'] ?? 0);
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            $this->setFlash('error', 'Автомобиль не найден');
            $this->redirect('/vehicles');
        }
        $this->render('vehicles/edit', [
            'vehicle' => $vehicle,
            'csrf_token' => CsrfMiddleware::generateToken()
        ]);
    }

    public function update(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/vehicles');
        }
        CsrfMiddleware::validate();
        $id = (int)($params['id'] ?? 0);
        $data = [
            'brand' => $_POST['brand'] ?? '',
            'model' => $_POST['model'] ?? '',
            'license_plate' => $_POST['license_plate'] ?? '',
            'current_mileage' => $_POST['current_mileage'] ?? 0,
            'status' => $_POST['status'] ?? 'available'
        ];
        if (Vehicle::update($id, $data)) {
            $this->setFlash('success', 'Автомобиль обновлён');
        } else {
            $this->setFlash('error', 'Ошибка обновления');
        }
        $this->redirect('/vehicles');
    }

    public function delete(array $params = []): void
    {
        if (!Auth::isAdmin()) { // только админ
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/vehicles');
        }
        $id = (int)($params['id'] ?? 0);
        if (Vehicle::delete($id)) {
            $this->setFlash('success', 'Автомобиль удалён');
        } else {
            $this->setFlash('error', 'Ошибка удаления');
        }
        $this->redirect('/vehicles');
    }
}