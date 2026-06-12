<?php

namespace App\Core;

/**
 * Класс маршрутизатора.
 * Обрабатывает входящие URL и вызывает соответствующие контроллеры.
 */
class Router
{
    private array $routes = [];
    private array $routeParams = [];

    /**
     * Добавляет маршрут.
     *
     * @param string $method HTTP метод (GET, POST и т.д.)
     * @param string $path Путь с возможными плейсхолдерами {param}
     * @param callable|string $handler Обработчик (контроллер@метод или замыкание)
     */
    public function add(string $method, string $path, $handler): void
    {
        // Преобразуем плейсхолдеры в регулярное выражение
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#i';
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * Диспетчеризует запрос: сопоставляет URI с маршрутом и вызывает обработчик.
     *
     * @param string $requestUri URI запроса
     * @param string $requestMethod HTTP метод
     */
    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Извлекаем именованные параметры
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $this->routeParams[$key] = $value;
                    }
                }

                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func($handler, $this->routeParams);
                } elseif (is_string($handler)) {
                    $this->callController($handler, $this->routeParams);
                }
                return;
            }
        }

        // Маршрут не найден
        http_response_code(404);
        echo "404 - Страница не найдена";
    }

    /**
     * Вызывает метод контроллера.
     *
     * @param string $handler Строка вида "ControllerName@method"
     * @param array $params Параметры из маршрута
     */
    private function callController(string $handler, array $params): void
    {
        [$controllerName, $method] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Контроллер {$controllerClass} не найден";
            return;
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo "Метод {$method} не найден в контроллере {$controllerClass}";
            return;
        }

        call_user_func_array([$controller, $method], [$params]);
    }
}