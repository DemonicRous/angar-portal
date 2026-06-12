<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Middleware\CsrfMiddleware;

/**
 * Контроллер аутентификации.
 */
class AuthController extends Controller
{
    /**
     * Показывает форму входа.
     */
    public function showLogin(array $params = []): void
    {
        if (Auth::isLoggedIn()) {
            $this->redirect('/');
        }
        $csrfToken = CsrfMiddleware::generateToken();
        $this->render('auth/login', ['csrf_token' => $csrfToken]);
    }

    /**
     * Обрабатывает попытку входа.
     */
    public function login(array $params = []): void
    {
        CsrfMiddleware::validate();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Заполните email и пароль');
            $this->redirect('/login');
        }

        $user = User::authenticate($email, $password);
        if ($user) {
            Auth::login($user['id'], $user['name'], $user['role_name']);
            $this->setFlash('success', 'Добро пожаловать, ' . $user['name']);
            $this->redirect('/');
        } else {
            $this->setFlash('error', 'Неверный email или пароль');
            $this->redirect('/login');
        }
    }

    /**
     * Выход из системы.
     */
    public function logout(array $params = []): void
    {
        Auth::logout();
        $this->setFlash('success', 'Вы вышли из системы');
        $this->redirect('/login');
    }
}