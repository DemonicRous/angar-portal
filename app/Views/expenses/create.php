<div class="card max-w-xl mx-auto">
    <div class="card-header">
        <i class="fas fa-plus-circle text-amber-600 text-xl"></i>
        Добавление затраты
    </div>
    <form method="POST" action="/expenses">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Дата</label>
                    <input type="date" name="date" required class="form-input" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Автомобиль</label>
                    <select name="vehicle_id" required class="form-select">
                        <option value="">Выберите автомобиль</option>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= $vehicle['id'] ?>"><?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Сумма (₽)</label>
                    <input type="number" step="0.01" name="amount" required class="form-input" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Категория</label>
                    <select name="category" required class="form-select">
                        <option value="fuel">Топливо</option>
                        <option value="repair">Ремонт</option>
                        <option value="maintenance">Обслуживание</option>
                        <option value="other">Прочее</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Комментарий (опционально)</label>
                <textarea name="comment" rows="3" class="form-textarea" placeholder="Например: замена масла, покупка топлива и т.д."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="/expenses" class="btn-secondary inline-flex items-center gap-1">
                    <i class="fas fa-times"></i> Отмена
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-1">
                    <i class="fas fa-save"></i> Сохранить
                </button>
            </div>
        </div>
    </form>
</div>