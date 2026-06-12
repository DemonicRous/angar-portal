<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Expense;
use App\Models\Vehicle;
use App\Middleware\CsrfMiddleware;

class ExpenseController extends Controller
{
    public function index(array $params = []): void
    {
        if (!Auth::isLoggedIn()) {
            $this->redirect('/login');
        }
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'vehicle_id' => $_GET['vehicle_id'] ?? '',
            'category' => $_GET['category'] ?? ''
        ];
        $expenses = Expense::getAllWithVehicles($filters);
        $vehicles = Vehicle::all();
        $this->render('expenses/index', [
            'expenses' => $expenses,
            'vehicles' => $vehicles,
            'filters' => $filters
        ]);
    }

    public function store(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/expenses');
        }
        CsrfMiddleware::validate();

        $data = [
            'vehicle_id' => $_POST['vehicle_id'] ?? null,
            'date' => $_POST['date'] ?? '',
            'amount' => $_POST['amount'] ?? 0,
            'category' => $_POST['category'] ?? '',
            'comment' => $_POST['comment'] ?? ''
        ];
        if (!$data['vehicle_id'] || !$data['date'] || $data['amount'] <= 0 || !$data['category']) {
            $this->setFlash('error', 'Заполните все обязательные поля');
            $this->redirect('/expenses');
        }
        if (Expense::create($data)) {
            $this->setFlash('success', 'Затрата добавлена');
        } else {
            $this->setFlash('error', 'Ошибка добавления');
        }
        $this->redirect('/expenses');
    }

    public function edit(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/expenses');
        }
        $id = (int)($params['id'] ?? 0);
        $expense = Expense::find($id);
        if (!$expense) {
            $this->setFlash('error', 'Затрата не найдена');
            $this->redirect('/expenses');
        }
        $vehicles = Vehicle::all();
        $this->render('expenses/edit', [
            'expense' => $expense,
            'vehicles' => $vehicles,
            'csrf_token' => CsrfMiddleware::generateToken()
        ]);
    }

    public function update(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/expenses');
        }
        CsrfMiddleware::validate();
        $id = (int)($params['id'] ?? 0);
        $data = [
            'vehicle_id' => $_POST['vehicle_id'] ?? null,
            'date' => $_POST['date'] ?? '',
            'amount' => $_POST['amount'] ?? 0,
            'category' => $_POST['category'] ?? '',
            'comment' => $_POST['comment'] ?? ''
        ];
        if (Expense::update($id, $data)) {
            $this->setFlash('success', 'Затрата обновлена');
        } else {
            $this->setFlash('error', 'Ошибка обновления');
        }
        $this->redirect('/expenses');
    }

    public function delete(array $params = []): void
    {
        if (!Auth::isAdmin()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/expenses');
        }
        $id = (int)($params['id'] ?? 0);
        if (Expense::delete($id)) {
            $this->setFlash('success', 'Затрата удалена');
        } else {
            $this->setFlash('error', 'Ошибка удаления');
        }
        $this->redirect('/expenses');
    }

    public function create(array $params = []): void
    {
        if (!Auth::isAdmin() && !Auth::isDispatcher()) {
            $this->setFlash('error', 'Доступ запрещён');
            $this->redirect('/expenses');
        }
        
        $vehicles = Vehicle::all();
        $this->render('expenses/create', [
            'vehicles' => $vehicles,
            'csrf_token' => CsrfMiddleware::generateToken()
        ]);
    }
}