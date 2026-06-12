<div class="card max-w-xl mx-auto">
    <div class="card-header">
        <i class="fas fa-edit text-amber-600 text-xl"></i>
        Редактирование затраты
    </div>
    <form method="POST" action="/expenses/<?= $expense['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Дата</label>
                    <input type="date" name="date" value="<?= $expense['date'] ?>" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Автомобиль</label>
                    <select name="vehicle_id" required class="form-select">
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= $vehicle['id'] ?>" <?= $vehicle['id'] == $expense['vehicle_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Сумма (₽)</label>
                    <input type="number" step="0.01" name="amount" value="<?= $expense['amount'] ?>" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Категория</label>
                    <select name="category" required class="form-select">
                        <option value="fuel" <?= $expense['category'] == 'fuel' ? 'selected' : '' ?>>Топливо</option>
                        <option value="repair" <?= $expense['category'] == 'repair' ? 'selected' : '' ?>>Ремонт</option>
                        <option value="maintenance" <?= $expense['category'] == 'maintenance' ? 'selected' : '' ?>>Обслуживание</option>
                        <option value="other" <?= $expense['category'] == 'other' ? 'selected' : '' ?>>Прочее</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Комментарий</label>
                <textarea name="comment" rows="3" class="form-textarea" placeholder="Комментарий"><?= htmlspecialchars($expense['comment'] ?? '') ?></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="/expenses" class="btn-secondary inline-flex items-center gap-1">
                    <i class="fas fa-times"></i> Отмена
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-1">
                    <i class="fas fa-save"></i> Обновить
                </button>
            </div>
        </div>
    </form>
</div>