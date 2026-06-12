<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Driver;
use App\Models\User;
use App\Models\Role;
use App\Middleware\CsrfMiddleware;

class DriverController extends Controller
{
    public function index(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/');
        }
        $drivers = Driver::getAllWithUsers();
        $this->render('drivers/index', ['drivers' => $drivers]);
    }

    public function create(array $params = []): void
    {
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Только администратор может создавать водителей');
            $this->redirect('/drivers');
        }
        $this->render('drivers/create', ['csrf_token' => CsrfMiddleware::generateToken()]);
    }

    public function store(array $params = []): void
    {
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/drivers');
        }
        CsrfMiddleware::validate();

        // Сначала создаём пользователя с ролью driver (role_id = 3)
        $userData = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT),
            'role_id' => 3 // driver
        ];
        if (!$userData['name'] || !$userData['email'] || empty($_POST['password'])) {
            $this->setFlash('error', 'Заполните имя, email и пароль');
            $this->redirect('/drivers/create');
        }

        $userId = User::create($userData);
        if (!$userId) {
            $this->setFlash('error', 'Ошибка создания пользователя');
            $this->redirect('/drivers/create');
        }

        // Создаём профиль водителя
        $driverData = [
            'user_id' => $userId,
            'phone' => $_POST['phone'] ?? '',
            'tariff_per_km' => $_POST['tariff_per_km'] ?? 10.00
        ];
        if (Driver::create($driverData)) {
            $this->setFlash('success', 'Водитель добавлен');
        } else {
            // Откат: удалим созданного пользователя
            User::delete($userId);
            $this->setFlash('error', 'Ошибка создания профиля водителя');
        }
        $this->redirect('/drivers');
    }

    public function edit(array $params = []): void
    {
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/drivers');
        }
        $id = (int)($params['id'] ?? 0);
        $driver = Driver::find($id);
        if (!$driver) {
            $this->setFlash('error', 'Водитель не найден');
            $this->redirect('/drivers');
        }
        $user = User::find($driver['user_id']);
        $this->render('drivers/edit', [
            'driver' => $driver,
            'user' => $user,
            'csrf_token' => CsrfMiddleware::generateToken()
        ]);
    }

    public function update(array $params = []): void
    {
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/drivers');
        }
        CsrfMiddleware::validate();
        $id = (int)($params['id'] ?? 0);
        $driver = Driver::find($id);
        if (!$driver) {
            $this->setFlash('error', 'Водитель не найден');
            $this->redirect('/drivers');
        }

        // Обновляем профиль водителя
        $driverData = [
            'phone' => $_POST['phone'] ?? '',
            'tariff_per_km' => $_POST['tariff_per_km'] ?? 10.00
        ];
        Driver::update($id, $driverData);

        // Обновляем данные пользователя (имя, email, опционально пароль)
        $userData = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];
        if (!empty($_POST['password'])) {
            $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        User::update($driver['user_id'], $userData);

        $this->setFlash('success', 'Данные водителя обновлены');
        $this->redirect('/drivers');
    }

    public function delete(array $params = []): void
    {
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/drivers');
        }
        $id = (int)($params['id'] ?? 0);
        $driver = Driver::find($id);
        if (!$driver) {
            $this->setFlash('error', 'Водитель не найден');
            $this->redirect('/drivers');
        }
        // Удаляем сначала профиль водителя, затем пользователя
        Driver::delete($id);
        User::delete($driver['user_id']);
        $this->setFlash('success', 'Водитель удалён');
        $this->redirect('/drivers');
    }
}