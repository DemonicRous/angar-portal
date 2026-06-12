<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Trip;

class ApiController extends Controller
{
    /**
     * Возвращает тариф водителя для указанного рейса (используется в форме завершения).
     */
    public function getDriverTariffForTrip(array $params = []): void
    {
        $tripId = (int)($params['tripId'] ?? 0);
        $tariff = Trip::getDriverTariffForTrip($tripId);
        header('Content-Type: application/json');
        echo json_encode(['tariff' => $tariff ?? 0]);
    }
}