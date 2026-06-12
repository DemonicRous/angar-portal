<?php

/**
 * Конфигурация ролей и прав доступа.
 */

return [
    'roles' => [
        'admin' => 1,
        'dispatcher' => 2,
        'driver' => 3,
    ],

    'permissions' => [
        'admin' => ['*'], // все права
        'dispatcher' => [
            'trips.view', 'trips.create', 'trips.edit', 'trips.complete',
            'vehicles.view', 'drivers.view',
            'expenses.view', 'expenses.create',
            'reports.view'
        ],
        'driver' => [
            'trips.view_own',
            'profile.edit'
        ],
    ],
];