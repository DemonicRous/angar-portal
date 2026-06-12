<div class="card">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-coins text-amber-500"></i> 
            Затраты на ТС
        </h1>
        <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
            <a href="/expenses/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus text-sm"></i> 
                Добавить затрату
            </a>
        <?php endif; ?>
    </div>

    <!-- Форма фильтров -->
    <form method="GET" class="bg-gray-50/90 rounded-xl p-5 mb-6 border border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" 
                   class="form-input text-sm" placeholder="Дата от">
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" 
                   class="form-input text-sm" placeholder="Дата до">
            <select name="vehicle_id" class="form-select text-sm">
                <option value="">Все автомобили</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= $vehicle['id'] ?>" <?= ($filters['vehicle_id'] ?? '') == $vehicle['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vehicle['license_plate']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="category" class="form-select text-sm">
                <option value="">Все категории</option>
                <option value="fuel" <?= ($filters['category'] ?? '') == 'fuel' ? 'selected' : '' ?>>Топливо</option>
                <option value="repair" <?= ($filters['category'] ?? '') == 'repair' ? 'selected' : '' ?>>Ремонт</option>
                <option value="maintenance" <?= ($filters['category'] ?? '') == 'maintenance' ? 'selected' : '' ?>>Обслуживание</option>
                <option value="other" <?= ($filters['category'] ?? '') == 'other' ? 'selected' : '' ?>>Прочее</option>
            </select>
            <button type="submit" class="col-span-full lg:col-span-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all shadow-sm">
                <i class="fas fa-search text-sm"></i> 
                Применить фильтры
            </button>
        </div>
    </form>

    <!-- Таблица затрат -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Дата</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Автомобиль</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Сумма, ₽</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Категория</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Комментарий</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php foreach ($expenses as $expense): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm"><?= $expense['date'] ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($expense['license_plate']) ?></td>
                    <td class="px-4 py-3 text-sm font-semibold text-red-700"><?= number_format($expense['amount'], 2) ?> ₽</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="badge <?= $expense['category'] == 'fuel' ? 'badge-blue' : ($expense['category'] == 'repair' ? 'badge-yellow' : ($expense['category'] == 'maintenance' ? 'badge-green' : 'badge-red')) ?>">
                            <?= $expense['category'] == 'fuel' ? 'Топливо' : ($expense['category'] == 'repair' ? 'Ремонт' : ($expense['category'] == 'maintenance' ? 'Обслуживание' : 'Прочее')) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate"><?= htmlspecialchars($expense['comment'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
                                <a href="/expenses/<?= $expense['id'] ?>/edit" class="text-green-600 hover:text-green-800 transition" title="Редактировать">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (\App\Core\Auth::isAdmin()): ?>
                                <form method="POST" action="/expenses/<?= $expense['id'] ?>/delete" class="inline delete-confirm" onsubmit="return confirm('Удалить запись о затрате? Действие необратимо.');">
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
                <?php if (empty($expenses)): ?>
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <i class="fas fa-receipt text-4xl mb-2 block"></i>
                        Нет записей о затратах
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>