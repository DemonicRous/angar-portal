<?php

/**
 * Список глобальных middleware.
 */

return [
    'csrf' => \App\Middleware\CsrfMiddleware::class,
    'auth' => \App\Middleware\AuthMiddleware::class,
    'role' => \App\Middleware\RoleMiddleware::class,
];