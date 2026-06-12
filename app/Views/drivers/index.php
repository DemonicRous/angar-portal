<div class="card">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-users text-purple-500"></i> 
            Водители
        </h1>
        <?php if (\App\Core\Auth::isAdmin()): ?>
            <a href="/drivers/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-plus text-sm"></i> 
                Добавить водителя
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ФИО</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Телефон</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Тариф, ₽/км</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php foreach ($drivers as $driver): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($driver['name']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($driver['email']) ?></td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($driver['phone'] ?? '—') ?></td>
                    <td class="px-4 py-3 text-sm font-semibold text-blue-700"><?= number_format($driver['tariff_per_km'], 2) ?> ₽/км</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <?php if (\App\Core\Auth::isAdmin()): ?>
                                <a href="/drivers/<?= $driver['id'] ?>/edit" class="text-green-600 hover:text-green-800 transition" title="Редактировать">
                                    <i class="fas fa-edit text-lg"></i>
                                </a>
                                <form method="POST" action="/drivers/<?= $driver['id'] ?>/delete" class="inline delete-confirm" onsubmit="return confirm('Удалить водителя? Действие необратимо.');">
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
                <?php if (empty($drivers)): ?>
                <tr>
                    <td colspan="5" class="text-center py-12 text-gray-400">
                        <i class="fas fa-users-slash text-4xl mb-2 block"></i>
                        Нет водителей
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>