<?php

namespace App\Core;

/**
 * Базовый контроллер.
 * Предоставляет методы для рендеринга представлений и перенаправлений.
 */
class Controller
{
    /**
     * Рендерит представление с переданными данными.
     *
     * @param string $view Имя представления (относительно папки Views, без .php)
     * @param array $data Ассоциативный массив данных для передачи в шаблон
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        // Используем глобальную константу с префиксом \
        $viewPath = \VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Представление не найдено: {$view}";
        }
        $content = ob_get_clean();
        $layoutPath = \VIEWS_PATH . '/layout.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Выполняет перенаправление на указанный URL.
     *
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Устанавливает flash-сообщение в сессию.
     *
     * @param string $type Тип сообщения (success, error, warning)
     * @param string $message Текст сообщения
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Получает flash-сообщение и удаляет его из сессии.
     *
     * @param string $type
     * @return string|null
     */
    protected function getFlash(string $type): ?string
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
}