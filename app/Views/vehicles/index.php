<div class="card">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-truck text-green-500"></i> 
            Автопарк
        </h1>
        <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
            <a href="/vehicles/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus text-sm"></i> 
                Добавить авто
            </a>
        <?php endif; ?>
    </div>

    <!-- Таблица автомобилей -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Марка / Модель</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Госномер</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Пробег, км</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Статус</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php foreach ($vehicles as $vehicle): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($vehicle['license_plate']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= number_format($vehicle['current_mileage']) ?> km</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="badge <?= $vehicle['status'] === 'available' ? 'badge-green' : ($vehicle['status'] === 'on_trip' ? 'badge-yellow' : 'badge-red') ?>">
                            <?= $vehicle['status'] === 'available' ? 'Доступен' : ($vehicle['status'] === 'on_trip' ? 'В рейсе' : 'Ремонт') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
                                <a href="/vehicles/<?= $vehicle['id'] ?>/edit" class="text-green-600 hover:text-green-800 transition" title="Редактировать">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::isAdmin()): ?>
                                <form method="POST" action="/vehicles/<?= $vehicle['id'] ?>/delete" class="inline delete-confirm" onsubmit="return confirm('Удалить автомобиль? Действие необратимо.');">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Удалить">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($vehicles)): ?>
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400">
                        <i class="fas fa-truck-empty text-4xl mb-2 block"></i>
                        Нет автомобилей
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>