<div class="space-y-6">
    <!-- Приветствие -->
    <div class="card flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-circle text-blue-600 text-3xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Добро пожаловать, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
                <p class="text-gray-500">Вы вошли как <span class="badge badge-blue"><?= $_SESSION['role_name'] ?></span></p>
            </div>
        </div>
        <div class="text-sm text-gray-400">
            <i class="far fa-calendar-alt mr-1"></i> <?= date('d.m.Y') ?>
        </div>
    </div>

    <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
        <!-- Панель администратора/диспетчера: статистика -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="card flex items-center gap-4 hover:shadow-lg transition">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <i class="fas fa-route text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-800"><?= number_format($stats['totalTrips'] ?? 0) ?></div>
                    <div class="text-sm text-gray-500">Всего рейсов</div>
                </div>
            </div>
            <div class="card flex items-center gap-4 hover:shadow-lg transition">
                <div class="p-3 bg-green-100 rounded-xl">
                    <i class="fas fa-truck text-green-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-800"><?= number_format($stats['totalVehicles'] ?? 0) ?></div>
                    <div class="text-sm text-gray-500">Автомобилей</div>
                </div>
            </div>
            <div class="card flex items-center gap-4 hover:shadow-lg transition">
                <div class="p-3 bg-orange-100 rounded-xl">
                    <i class="fas fa-users text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-800"><?= number_format($stats['totalDrivers'] ?? 0) ?></div>
                    <div class="text-sm text-gray-500">Водителей</div>
                </div>
            </div>
            <div class="card flex items-center gap-4 hover:shadow-lg transition">
                <div class="p-3 bg-red-100 rounded-xl">
                    <i class="fas fa-coins text-red-600 text-2xl"></i>
                </div>
                <div>
                    <div class="text-3xl font-bold text-gray-800"><?= number_format($stats['totalExpenses'] ?? 0, 0, ',', ' ') ?> ₽</div>
                    <div class="text-sm text-gray-500">Общие затраты</div>
                </div>
            </div>
        </div>

        <!-- Быстрые ссылки -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/trips/create" class="card flex items-center gap-3 justify-center text-center hover:bg-blue-50 transition group">
                <i class="fas fa-plus-circle text-blue-500 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium">Новый рейс</span>
            </a>
            <a href="/vehicles/create" class="card flex items-center gap-3 justify-center text-center hover:bg-green-50 transition group">
                <i class="fas fa-truck text-green-500 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium">Добавить авто</span>
            </a>
            <a href="/drivers/create" class="card flex items-center gap-3 justify-center text-center hover:bg-purple-50 transition group">
                <i class="fas fa-user-plus text-purple-500 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium">Новый водитель</span>
            </a>
            <a href="/expenses/create" class="card flex items-center gap-3 justify-center text-center hover:bg-amber-50 transition group">
                <i class="fas fa-coins text-amber-500 text-xl group-hover:scale-110 transition"></i>
                <span class="font-medium">Добавить затрату</span>
            </a>
        </div>

        <!-- Последние 5 рейсов (краткий список) -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-history text-gray-500"></i> Последние рейсы
                </h2>
                <a href="/trips" class="text-blue-500 hover:text-blue-700 text-sm">Все рейсы →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Дата</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Водитель</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Маршрут</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recentTrips = array_slice($stats['allTrips'] ?? [], 0, 5);
                        foreach ($recentTrips as $trip): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm"><?= $trip['date'] ?></td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($trip['driver_name']) ?></td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($trip['start_location']) ?> → <?= htmlspecialchars($trip['end_location']) ?></td>
                            <td class="px-4 py-2 text-sm">
                                <span class="badge <?= $trip['status'] === 'completed' ? 'badge-green' : 'badge-yellow' ?>">
                                    <?= $trip['status'] === 'completed' ? 'Завершён' : 'Запланирован' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentTrips)): ?>
                        <tr><td colspan="4" class="text-center py-6 text-gray-400">Нет рейсов</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Водитель: его личные рейсы -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <i class="fas fa-truck-fast text-blue-500"></i> Мои рейсы
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Дата</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Автомобиль</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Маршрут</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Пробег (км)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Зарплата (₽)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['myTrips'] ?? [] as $trip): ?>
                        <tr>
                            <td class="px-4 py-2 text-sm"><?= $trip['date'] ?></td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($trip['license_plate']) ?></td>
                            <td class="px-4 py-2 text-sm"><?= htmlspecialchars($trip['start_location']) ?> → <?= htmlspecialchars($trip['end_location']) ?></td>
                            <td class="px-4 py-2 text-sm"><?= $trip['distance'] ?? '—' ?></td>
                            <td class="px-4 py-2 text-sm font-semibold text-green-700"><?= $trip['calculated_salary'] ? number_format($trip['calculated_salary'], 2) : '—' ?> ₽</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stats['myTrips'])): ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">У вас пока нет рейсов</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>