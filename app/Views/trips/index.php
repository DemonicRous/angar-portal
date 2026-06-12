<div class="card">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-route text-blue-500"></i> 
            Рейсы
        </h1>
        <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
            <a href="/trips/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus text-sm"></i> 
                Новый рейс
            </a>
        <?php endif; ?>
    </div>

    <!-- Форма фильтров -->
    <form method="GET" class="bg-gray-50/90 rounded-xl p-5 mb-6 border border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" 
                   class="form-input text-sm" placeholder="Дата от">
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" 
                   class="form-input text-sm" placeholder="Дата до">
            <select name="driver_id" class="form-select text-sm">
                <option value="">Все водители</option>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?= $driver['id'] ?>" <?= ($filters['driver_id'] == $driver['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($driver['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="vehicle_id" class="form-select text-sm">
                <option value="">Все авто</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= $vehicle['id'] ?>" <?= ($filters['vehicle_id'] == $vehicle['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vehicle['license_plate']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-select text-sm">
                <option value="">Все статусы</option>
                <option value="planned" <?= ($filters['status'] ?? '') == 'planned' ? 'selected' : '' ?>>Запланирован</option>
                <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Завершён</option>
            </select>
            <button type="submit" class="col-span-full lg:col-span-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all shadow-sm">
                <i class="fas fa-search text-sm"></i> 
                Применить фильтры
            </button>
        </div>
    </form>

    <!-- Таблица рейсов -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Дата</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Водитель</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Авто</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Маршрут</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Пробег, км</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Зарплата, ₽</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Статус</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php foreach ($trips as $trip): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm"><?= $trip['date'] ?></td>
                    <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($trip['driver_name']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($trip['license_plate']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($trip['start_location']) ?> → <?= htmlspecialchars($trip['end_location']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= $trip['distance'] ?? '—' ?></td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-700"><?= $trip['calculated_salary'] ? number_format($trip['calculated_salary'], 2) : '—' ?></td>
                    <td class="px-4 py-3 text-sm">
                        <span class="badge <?= $trip['status'] === 'completed' ? 'badge-green' : 'badge-yellow' ?>">
                            <?= $trip['status'] === 'completed' ? 'Завершён' : 'Запланирован' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <?php if ($trip['status'] === 'planned' && (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher())): ?>
                                <button onclick="showCompleteModal(<?= $trip['id'] ?>, <?= $trip['start_mileage'] ?>, <?= $trip['tariff_per_km'] ?>)" 
                                        class="text-blue-600 hover:text-blue-800 transition" title="Завершить рейс">
                                    <i class="fas fa-flag-checkered text-lg"></i>
                                </button>
                                <a href="/trips/<?= $trip['id'] ?>/edit" class="text-green-600 hover:text-green-800 transition" title="Редактировать">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                                <form method="POST" action="/trips/<?= $trip['id'] ?>/delete" class="inline delete-confirm" onsubmit="return confirm('Удалить рейс? Действие необратимо.');">
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
                <?php if (empty($trips)): ?>
                <tr>
                    <td colspan="8" class="text-center py-12 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2 block"></i>
                        Нет рейсов
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Модальное окно завершения рейса -->
<div id="completeModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-flag-checkered text-green-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Завершить рейс</h3>
            </div>
            <form id="completeForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="mb-4">
                    <label class="form-label">Текущий пробег (км)</label>
                    <input type="number" id="endMileage" name="end_mileage" required class="form-input" placeholder="Введите пробег">
                </div>
                <div class="mb-5 p-3 bg-gray-50 rounded-lg text-center">
                    <span class="text-gray-600">Рассчитанная зарплата:</span>
                    <span class="text-xl font-bold text-green-700 ml-2" id="salaryPreview">0</span> ₽
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="btn-secondary">Отмена</button>
                    <button type="submit" class="btn-primary">Подтвердить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCompleteModal(tripId, startMileage, tariff) {
    const modal = document.getElementById('completeModal');
    if (!modal) return;
    modal.dataset.startMileage = startMileage;
    modal.dataset.tariff = tariff;
    document.getElementById('completeForm').action = '/trips/' + tripId + '/complete';
    modal.style.display = 'flex';
    document.getElementById('endMileage').value = '';
    document.getElementById('salaryPreview').innerText = '0';
}

function closeModal() {
    const modal = document.getElementById('completeModal');
    if (modal) modal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const mileageInput = document.getElementById('endMileage');
    if (mileageInput) {
        mileageInput.addEventListener('input', function() {
            const modal = document.getElementById('completeModal');
            const start = parseInt(modal?.dataset.startMileage);
            const tariff = parseFloat(modal?.dataset.tariff);
            const end = parseInt(this.value);
            if (!isNaN(start) && !isNaN(tariff) && !isNaN(end) && end > start) {
                const salary = (end - start) * tariff;
                document.getElementById('salaryPreview').innerText = salary.toFixed(2);
            } else {
                document.getElementById('salaryPreview').innerText = '0';
            }
        });
    }
});
</script>